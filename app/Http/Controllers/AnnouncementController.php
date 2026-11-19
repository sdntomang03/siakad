<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AnnouncementController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        abort_unless($user->school_id, 403);

        if ($user->hasRole('guru')) {
            $announcements = Announcement::with('recipient')
                ->with('classroom')
                ->withCount('students')
                ->where('school_id', $user->school_id)
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(15);
            $students = Student::where('school_id', $user->school_id)
                ->where('status', 'aktif')
                ->orderBy('nama_lengkap')
                ->get(['id', 'nama_lengkap']);
            $classrooms = collect();
            $employeeId = $user->employee?->id;

            if ($employeeId) {
                $classrooms = Classroom::with(['students' => function ($query) use ($user) {
                    $query->where('students.school_id', $user->school_id)
                        ->where('students.status', 'aktif')
                        ->orderBy('students.nama_lengkap')
                        ->select(['students.id', 'students.school_id', 'students.nama_lengkap']);
                }])
                    ->where('school_id', $user->school_id)
                    ->whereHas('academicYear', fn ($query) => $query->where('is_active', true))
                    ->where(function ($query) use ($employeeId) {
                        $query->where('homeroom_teacher_id', $employeeId)
                            ->orWhereHas('subjectTeachers', fn ($subjectTeachers) => $subjectTeachers->where('employee_id', $employeeId));
                    })
                    ->orderBy('tingkat')
                    ->orderBy('nama_kelas')
                    ->get(['id', 'tingkat', 'nama_kelas']);
            }

            return view('announcements.teacher-index', compact('announcements', 'students', 'classrooms'));
        }

        abort_unless($user->hasRole('siswa'), 403);
        $student = $user->student;
        abort_unless($student && (int) $student->school_id === (int) $user->school_id, 403);

        $announcements = $student->announcements()
            ->where('announcements.school_id', $user->school_id)
            ->with('author.employee')
            ->latest('announcements.created_at')
            ->paginate(15);

        return view('announcements.student-index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->hasRole('guru') && $user->school_id, 403);

        $validated = $request->validate([
            'audience' => ['required', Rule::in(['all', 'individual', 'class_personalized'])],
            'student_id' => [
                'nullable',
                'required_if:audience,individual',
                'integer',
                Rule::exists('students', 'id')->where(
                    fn ($query) => $query->where('school_id', $user->school_id)->where('status', 'aktif')
                ),
            ],
            'classroom_id' => [
                'nullable',
                'required_if:audience,class_personalized',
                'integer',
                Rule::exists('classrooms', 'id')->where(
                    fn ($query) => $query->where('school_id', $user->school_id)
                ),
            ],
            'student_bodies' => ['required_if:audience,class_personalized', 'array', 'min:1'],
            'student_bodies.*' => ['required', 'string', 'max:5000'],
            'title' => ['required', 'string', 'max:180'],
            'body' => ['nullable', 'required_unless:audience,class_personalized', 'string', 'max:5000'],
        ], [
            'student_id.required_if' => 'Pilih siswa penerima pengumuman.',
            'student_id.exists' => 'Siswa yang dipilih tidak aktif atau bukan dari sekolah ini.',
            'classroom_id.required_if' => 'Pilih kelas yang Anda ampu.',
            'student_bodies.required_if' => 'Isi pesan untuk setiap siswa di kelas.',
            'student_bodies.*.required' => 'Isi pesan untuk setiap siswa di kelas.',
        ]);

        $classroom = null;
        $recipients = collect();

        if ($validated['audience'] === 'class_personalized') {
            $employeeId = $user->employee?->id;
            abort_unless($employeeId, 403);

            $classroom = Classroom::with(['students' => function ($query) use ($user) {
                $query->where('students.school_id', $user->school_id)
                    ->where('students.status', 'aktif')
                    ->orderBy('students.nama_lengkap');
            }])
                ->where('school_id', $user->school_id)
                ->whereHas('academicYear', fn ($query) => $query->where('is_active', true))
                ->whereKey($validated['classroom_id'])
                ->firstOrFail();

            abort_unless(
                $classroom->homeroom_teacher_id === $employeeId
                || $classroom->subjectTeachers()->where('employee_id', $employeeId)->exists(),
                403
            );

            $classStudentIds = $classroom->students->modelKeys();
            $submittedStudentIds = collect(array_keys($validated['student_bodies']))
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values();
            $expectedStudentIds = collect($classStudentIds)->sort()->values();

            if ($expectedStudentIds->isEmpty() || $submittedStudentIds->all() !== $expectedStudentIds->all()) {
                return back()
                    ->withInput()
                    ->withErrors(['student_bodies' => 'Isi pesan untuk seluruh siswa aktif di kelas yang dipilih.']);
            }

            $recipients = $classroom->students->mapWithKeys(
                fn ($student) => [$student->id => ['body' => $validated['student_bodies'][$student->id]]]
            );
        } else {
            $students = $validated['audience'] === 'all'
                ? Student::where('school_id', $user->school_id)->where('status', 'aktif')->pluck('id')
                : collect([(int) $validated['student_id']]);

            if ($students->isEmpty()) {
                return back()
                    ->withInput()
                    ->withErrors(['audience' => 'Belum ada siswa aktif yang dapat menerima pengumuman.']);
            }

            $recipients = $students;
        }

        DB::transaction(function () use ($validated, $user, $recipients, $classroom) {
            $announcement = Announcement::create([
                'school_id' => $user->school_id,
                'user_id' => $user->id,
                'recipient_student_id' => $validated['audience'] === 'individual'
                    ? $validated['student_id']
                    : null,
                'classroom_id' => $classroom?->id,
                'audience' => $validated['audience'],
                'title' => $validated['title'],
                'body' => $validated['body'] ?? null,
            ]);

            $announcement->students()->sync($recipients->all());
        });

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dikirim.');
    }
}
