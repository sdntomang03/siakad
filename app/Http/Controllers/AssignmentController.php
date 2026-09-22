<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentGroup;
use App\Models\Classroom;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use HTMLPurifier;
use HTMLPurifier_Config;

class AssignmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('siswa')) {
            $student = $user->student;
            abort_unless($student, 403);

            $assignments = Assignment::with(['subject', 'classroom'])
                ->where('school_id', $user->school_id)
                ->whereHas('classroom.students', fn ($query) => $query->whereKey($student->id))
                ->latest()
                ->get();

            return view('assignments.student-index', compact('assignments'));
        }

        abort_unless($user->hasRole('guru'), 403);
        $employeeId = $user->employee?->id;
        $assignments = Assignment::with(['classroom', 'subject'])
            ->where('employee_id', $employeeId)
            ->latest()
            ->get();

        return view('assignments.index', compact('assignments'));
    }

    public function create()
    {
        $user = auth()->user();
        abort_unless($user->hasRole('guru'), 403);

        $classrooms = Classroom::with('students')
            ->where('school_id', $user->school_id)
            ->where(function ($query) use ($user) {
                $query->where('homeroom_teacher_id', $user->employee?->id)
                    ->orWhereHas('subjectTeachers', fn ($q) => $q->where('employee_id', $user->employee?->id));
            })
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();
        $subjects = Subject::where('school_id', $user->school_id)->orderBy('nama_mapel')->get();

        return view('assignments.create', compact('classrooms', 'subjects'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->hasRole('guru'), 403);

        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['individual', 'group'])],
            'due_at' => ['nullable', 'date'],
            'task_titles' => ['required', 'array', 'min:1'],
            'task_titles.*' => ['required', 'string', 'max:255'],
            'task_descriptions' => ['required', 'array', 'min:1'],
            'task_descriptions.*' => ['required', 'string'],
            'groups' => ['required_if:type,group', 'array', 'min:1'],
            'groups.*.name' => ['required_if:type,group', 'string', 'max:100'],
            'groups.*.leader_student_id' => ['required_if:type,group', 'integer'],
            'groups.*.student_ids' => ['required_if:type,group', 'array', 'min:1'],
            'groups.*.student_ids.*' => ['integer'],
        ]);

        $classroom = Classroom::with('students')->findOrFail($validated['classroom_id']);
        abort_unless($classroom->school_id === $user->school_id, 403);
        abort_unless(
            $classroom->homeroom_teacher_id === $user->employee?->id
            || $classroom->subjectTeachers()->where('employee_id', $user->employee?->id)->exists(),
            403
        );

        $studentIds = $classroom->students->pluck('id')->all();
        $assignment = DB::transaction(function () use ($validated, $user, $classroom, $studentIds) {
            $assignment = Assignment::create([
                'school_id' => $user->school_id,
                'classroom_id' => $classroom->id,
                'subject_id' => $validated['subject_id'] ?? null,
                'employee_id' => $user->employee->id,
                'title' => $validated['title'],
                'description' => $this->cleanHtml($validated['description'] ?? null),
                'type' => $validated['type'],
                'due_at' => $validated['due_at'] ?? null,
            ]);

            foreach ($validated['task_titles'] as $index => $title) {
                $assignment->tasks()->create([
                    'title' => $title,
                    'description' => $this->cleanHtml($validated['task_descriptions'][$index] ?? ''),
                ]);
            }

            if ($validated['type'] === 'group') {
                $assignedStudentIds = [];
                foreach ($validated['groups'] as $groupData) {
                    $members = array_values(array_unique(array_map('intval', $groupData['student_ids'])));
                    abort_unless(
                        count($members) === count(array_intersect($members, $studentIds))
                        && in_array((int) $groupData['leader_student_id'], $members, true),
                        422,
                        'Anggota kelompok harus berasal dari kelas yang dipilih dan ketua harus menjadi anggota.'
                    );
                    abort_unless(
                        ! array_intersect($assignedStudentIds, $members),
                        422,
                        'Seorang siswa tidak boleh masuk ke lebih dari satu kelompok.'
                    );
                    $assignedStudentIds = array_merge($assignedStudentIds, $members);

                    $group = $assignment->groups()->create([
                        'name' => $groupData['name'],
                        'leader_student_id' => $groupData['leader_student_id'],
                    ]);
                    $group->students()->sync($members);
                }
            }

            return $assignment;
        });

        return redirect()->route('assignments.index')->with('success', 'Tugas berhasil dibuat.');
    }

    public function show(Assignment $assignment)
    {
        $user = auth()->user();
        abort_unless($assignment->school_id === $user->school_id, 403);

        if ($user->hasRole('guru')) {
            abort_unless($assignment->employee_id === $user->employee?->id, 403);
            $assignment->load(['classroom.students', 'subject', 'tasks.group.students', 'groups.leader', 'groups.students', 'tasks']);

            return view('assignments.show', compact('assignment'));
        }

        abort_unless($user->hasRole('siswa') && $user->student, 403);
        $student = $user->student;
        $assignment->load(['subject', 'classroom', 'tasks']);
        $group = $assignment->groups()->with(['leader', 'students', 'selectedTask'])->whereHas(
            'students',
            fn ($query) => $query->whereKey($student->id)
        )->first();

        abort_unless($assignment->type === 'individual' || $group, 403);
        $selectedTask = $assignment->type === 'individual'
            ? $assignment->tasks->first()
            : $group->selectedTask;

        return view('assignments.student-show', compact('assignment', 'group', 'selectedTask'));
    }

    public function edit(Assignment $assignment)
    {
        $user = auth()->user();
        $this->authorizeTeacher($assignment, $user);

        $assignment->load(['tasks', 'groups.students', 'groups.selectedTask', 'classroom.students']);
        $subjects = Subject::where('school_id', $user->school_id)->orderBy('nama_mapel')->get();

        return view('assignments.edit', compact('assignment', 'subjects'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $user = auth()->user();
        $this->authorizeTeacher($assignment, $user);

        $validated = $request->validate([
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_at' => ['nullable', 'date'],
            'task_ids' => ['nullable', 'array'],
            'task_ids.*' => ['nullable', 'integer', 'exists:assignment_tasks,id'],
            'task_titles' => ['required', 'array', 'min:1'],
            'task_titles.*' => ['required', 'string', 'max:255'],
            'task_descriptions' => ['required', 'array', 'min:1'],
            'task_descriptions.*' => ['required', 'string'],
            'groups' => [$assignment->type === 'group' ? 'required' : 'nullable', 'array'],
            'groups.*.id' => ['nullable', 'integer', 'exists:assignment_groups,id'],
            'groups.*.name' => ['required_if:type,group', 'string', 'max:100'],
            'groups.*.leader_student_id' => ['required_if:type,group', 'integer'],
            'groups.*.student_ids' => ['required_if:type,group', 'array', 'min:1'],
            'groups.*.student_ids.*' => ['integer'],
        ]);

        $classroom = $assignment->classroom()->with('students')->first();
        $studentIds = $classroom->students->pluck('id')->all();

        DB::transaction(function () use ($validated, $assignment, $studentIds) {
            $assignment->update([
                'subject_id' => $validated['subject_id'] ?? null,
                'title' => $validated['title'],
                'description' => $this->cleanHtml($validated['description'] ?? null),
                'due_at' => $validated['due_at'] ?? null,
            ]);

            $submittedTaskIds = [];
            foreach ($validated['task_titles'] as $index => $title) {
                $taskId = $validated['task_ids'][$index] ?? null;
                $task = $taskId
                    ? $assignment->tasks()->whereKey($taskId)->firstOrFail()
                    : $assignment->tasks()->create([]);
                $task->update([
                    'title' => $title,
                    'description' => $this->cleanHtml($validated['task_descriptions'][$index] ?? ''),
                ]);
                $submittedTaskIds[] = $task->id;
            }

            $assignment->tasks()
                ->whereNotIn('id', $submittedTaskIds)
                ->whereNull('selected_by_group_id')
                ->delete();

            if ($assignment->type === 'group') {
                $assignedStudentIds = [];
                $submittedGroupIds = [];
                foreach ($validated['groups'] ?? [] as $groupData) {
                    $members = array_values(array_unique(array_map('intval', $groupData['student_ids'])));
                    abort_unless(
                        count($members) === count(array_intersect($members, $studentIds))
                        && in_array((int) $groupData['leader_student_id'], $members, true)
                        && ! array_intersect($assignedStudentIds, $members),
                        422,
                        'Anggota kelompok harus berasal dari kelas, ketua harus menjadi anggota, dan siswa tidak boleh ganda.'
                    );
                    $assignedStudentIds = array_merge($assignedStudentIds, $members);

                    $group = ! empty($groupData['id'])
                        ? $assignment->groups()->whereKey($groupData['id'])->firstOrFail()
                        : $assignment->groups()->create(['name' => $groupData['name'], 'leader_student_id' => $groupData['leader_student_id']]);
                    $group->update([
                        'name' => $groupData['name'],
                        'leader_student_id' => $groupData['leader_student_id'],
                    ]);
                    $group->students()->sync($members);
                    $submittedGroupIds[] = $group->id;
                }

                $groupsToDelete = $assignment->groups()->whereNotIn('id', $submittedGroupIds)->get();
                abort_unless(
                    $groupsToDelete->every(fn ($group) => ! $group->selectedTask()->exists()),
                    422,
                    'Kelompok yang sudah memilih tugas tidak dapat dihapus.'
                );
                $assignment->groups()->whereNotIn('id', $submittedGroupIds)->delete();
            }
        });

        return redirect()->route('assignments.show', $assignment)->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Assignment $assignment)
    {
        $user = auth()->user();
        $this->authorizeTeacher($assignment, $user);
        $assignment->delete();

        return redirect()->route('assignments.index')->with('success', 'Tugas berhasil dihapus.');
    }

    public function destroyTask(Assignment $assignment, \App\Models\AssignmentTask $task)
    {
        $user = auth()->user();
        $this->authorizeTeacher($assignment, $user);
        abort_unless($task->assignment_id === $assignment->id, 404);
        abort_if($task->selected_by_group_id, 422, 'Tugas yang sudah dipilih kelompok tidak dapat dihapus.');
        $task->delete();

        return back()->with('success', 'Item tugas berhasil dihapus.');
    }

    private function authorizeTeacher(Assignment $assignment, $user): void
    {
        abort_unless($user->hasRole('guru'), 403);
        abort_unless($assignment->school_id === $user->school_id && $assignment->employee_id === $user->employee?->id, 403);
    }

    private function cleanHtml(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }

        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,br,strong,b,em,i,u,ol,ul,li,h2,h3,h4,blockquote,a[href|title|target]');
        $config->set('URI.DisableExternalResources', true);
        $config->set('Attr.AllowedFrameTargets', ['_blank']);

        return (new HTMLPurifier($config))->purify($html);
    }

    public function selectTask(Assignment $assignment)
    {
        $user = auth()->user();
        abort_unless($user->hasRole('siswa') && $user->student, 403);
        abort_unless($assignment->school_id === $user->school_id && $assignment->type === 'group', 403);

        $group = $assignment->groups()
            ->whereHas('students', fn ($query) => $query->whereKey($user->student->id))
            ->firstOrFail();

        DB::transaction(function () use ($assignment, $group) {
            $group->load('selectedTask');
            if ($group->selectedTask) {
                return;
            }

            $task = $assignment->tasks()
                ->whereNull('selected_by_group_id')
                ->lock('for update')
                ->inRandomOrder()
                ->first();

            abort_unless($task, 422, 'Semua tugas dalam kumpulan sudah dipilih.');
            $task->update([
                'selected_by_group_id' => $group->id,
                'selected_at' => now(),
            ]);
        });

        return redirect()->route('assignments.show', $assignment)->with('success', 'Tugas kelompok berhasil dipilih.');
    }
}
