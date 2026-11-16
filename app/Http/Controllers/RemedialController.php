<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\Classroom;
use App\Models\ClassroomSubject;
use App\Models\RemedialProgram;
use App\Models\RemedialRecord;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RemedialController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $employeeId = $user->employee->id ?? 0;
        $programs = RemedialProgram::with(['classroom', 'subject'])
            ->withCount('records')
            ->where('school_id', $user->school_id)
            ->where('employee_id', $employeeId)
            ->latest()
            ->paginate(12);

        return view('remedials.index', compact('programs'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $employeeId = $user->employee->id ?? 0;
        $activeYear = AcademicYear::where('school_id', $user->school_id)
            ->where('is_active', true)
            ->first();
        $classesData = $activeYear
            ? $this->teacherClasses($user->school_id, $activeYear->id, $employeeId)
            : [];
        $classroomId = $request->query('classroom_id');
        $subjectId = $request->query('subject_id');
        $classroom = null;
        $subject = null;
        $kkm = null;
        $assessments = collect();
        $rows = collect();
        $selectedAssessmentIds = [];

        if ($classroomId !== null || $subjectId !== null) {
            if (! $activeYear || ! $classroomId || ! $subjectId
                || ! isset($classesData[$classroomId]['subjects'][$subjectId])) {
                abort(403, 'Kelas atau mata pelajaran tidak termasuk tugas mengajar Anda.');
            }

            $classroom = Classroom::findOrFail($classroomId);
            $subject = Subject::findOrFail($subjectId);
            $kkm = (int) $subject->kkm;
            if ($request->query->has('kkm')) {
                $validatedKkm = $request->validate([
                    'kkm' => 'required|integer|min:0|max:100',
                ]);
                $kkm = (int) $validatedKkm['kkm'];
            }
            $assessments = $this->assessmentQuery($user->school_id, $activeYear->id, $classroomId, $subjectId)
                ->with('assessmentType')
                ->withCount('scores')
                ->orderBy('tanggal')
                ->get();

            if ($request->query->has('assessment_ids')) {
                $validated = $request->validate([
                    'assessment_ids' => 'required|array|min:1',
                    'assessment_ids.*' => 'required|integer|distinct',
                ]);
                $selectedAssessmentIds = array_map('intval', $validated['assessment_ids']);
                $availableIds = $assessments->modelKeys();

                if (array_diff($selectedAssessmentIds, $availableIds)) {
                    abort(403, 'Penilaian yang dipilih tidak sesuai dengan kelas dan mata pelajaran.');
                }

                $rows = $this->calculateRows($classroom, $selectedAssessmentIds, $kkm);
            }
        }

        return view('remedials.create', compact(
            'classesData',
            'activeYear',
            'classroom',
            'subject',
            'kkm',
            'assessments',
            'rows',
            'selectedAssessmentIds'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'assessment_ids' => 'required|array|min:1',
            'assessment_ids.*' => 'required|integer|distinct',
            'kkm' => 'sometimes|required|integer|min:0|max:100',
            'records' => 'required|array',
            'records.*.completed' => 'sometimes|boolean',
            'records.*.score' => 'nullable|numeric|min:0|max:100',
        ]);
        [$classroom, $subject, $activeYear] = $this->authorizedSelection(
            (int) $validated['classroom_id'],
            (int) $validated['subject_id']
        );
        $kkm = (int) ($validated['kkm'] ?? $subject->kkm);
        $assessmentIds = array_map('intval', $validated['assessment_ids']);
        $assessments = $this->assessmentQuery(
            auth()->user()->school_id,
            $activeYear->id,
            $classroom->id,
            $subject->id
        )->whereIn('id', $assessmentIds)->get();

        if ($assessments->count() !== count($assessmentIds)) {
            throw ValidationException::withMessages([
                'assessment_ids' => 'Penilaian yang dipilih sudah tidak tersedia untuk kelas dan mata pelajaran ini.',
            ]);
        }

        $eligibleRows = $this->calculateRows($classroom, $assessmentIds, $kkm)
            ->filter(fn ($row) => $row->eligible)
            ->keyBy('student_id');
        $submittedStudentIds = array_map('intval', array_keys($validated['records']));

        if (array_diff($submittedStudentIds, $eligibleRows->keys()->all())) {
            throw ValidationException::withMessages([
                'records' => 'Data siswa yang dikirim tidak sesuai dengan hasil perhitungan nilai.',
            ]);
        }

        $program = DB::transaction(function () use ($validated, $classroom, $subject, $activeYear, $assessmentIds, $eligibleRows, $kkm) {
            $program = RemedialProgram::create([
                'school_id' => auth()->user()->school_id,
                'academic_year_id' => $activeYear->id,
                'classroom_id' => $classroom->id,
                'subject_id' => $subject->id,
                'employee_id' => auth()->user()->employee->id,
            ]);
            $program->assessments()->attach($assessmentIds);

            foreach ($eligibleRows as $studentId => $result) {
                $input = $validated['records'][$studentId] ?? $validated['records'][(string) $studentId] ?? [];
                $remedialScore = $input['score'] ?? null;

                RemedialRecord::create([
                    'remedial_program_id' => $program->id,
                    'student_id' => $studentId,
                    'average_score' => $result->average,
                    'kkm' => $kkm,
                    'is_completed' => ! empty($input['completed']) || $remedialScore !== null,
                    'remedial_score' => $remedialScore,
                ]);
            }

            return $program;
        });

        return redirect()->route('remedials.show', $program)
            ->with('success', 'Program remedial dan status siswa berhasil disimpan.');
    }

    public function show(RemedialProgram $remedialProgram)
    {
        $this->authorizeProgram($remedialProgram);
        $remedialProgram->load([
            'classroom',
            'subject',
            'assessments.assessmentType',
            'records.student',
        ]);

        return view('remedials.show', compact('remedialProgram'));
    }

    public function update(Request $request, RemedialProgram $remedialProgram)
    {
        $this->authorizeProgram($remedialProgram);
        $validated = $request->validate([
            'records' => 'required|array',
            'records.*.completed' => 'sometimes|boolean',
            'records.*.score' => 'nullable|numeric|min:0|max:100',
        ]);
        $recordIds = $remedialProgram->records()->pluck('id')->all();
        $submittedIds = array_map('intval', array_keys($validated['records']));

        if (array_diff($submittedIds, $recordIds)) {
            throw ValidationException::withMessages([
                'records' => 'Data siswa tidak termasuk dalam program remedial ini.',
            ]);
        }

        DB::transaction(function () use ($validated, $remedialProgram) {
            foreach ($validated['records'] as $recordId => $input) {
                $record = $remedialProgram->records()->findOrFail($recordId);
                $remedialScore = $input['score'] ?? null;

                $record->update([
                    'is_completed' => ! empty($input['completed']) || $remedialScore !== null,
                    'remedial_score' => $remedialScore,
                ]);
            }
        });

        return redirect()->route('remedials.show', $remedialProgram)
            ->with('success', 'Status remedial berhasil diperbarui.');
    }

    public function destroy(RemedialProgram $remedialProgram)
    {
        $this->authorizeProgram($remedialProgram);
        $remedialProgram->delete();

        return redirect()->route('remedials.index')
            ->with('success', 'Program remedial berhasil dihapus.');
    }

    private function teacherClasses(int $schoolId, int $activeYearId, int $employeeId): array
    {
        $classesData = [];

        $homeroomClasses = Classroom::where('school_id', $schoolId)
            ->where('academic_year_id', $activeYearId)
            ->where('homeroom_teacher_id', $employeeId)
            ->get();

        foreach ($homeroomClasses as $classroom) {
            $subjects = Subject::where('school_id', $schoolId)
                ->where('tingkat', $classroom->tingkat)
                ->where('pengampu', 'guru_kelas')
                ->get();
            $classesData[$classroom->id] = [
                'nama_kelas' => $classroom->tingkat.' - '.$classroom->nama_kelas,
                'subjects' => $subjects->mapWithKeys(fn ($item) => [
                    $item->id => ['id' => $item->id, 'nama' => $item->nama_mapel, 'kkm' => $item->kkm],
                ])->all(),
            ];
        }

        $classroomSubjects = ClassroomSubject::with(['classroom', 'subject'])
            ->where('employee_id', $employeeId)
            ->whereHas('classroom', fn ($query) => $query
                ->where('school_id', $schoolId)
                ->where('academic_year_id', $activeYearId))
            ->whereHas('subject', fn ($query) => $query->where('school_id', $schoolId))
            ->get();

        foreach ($classroomSubjects as $assignment) {
            $classroom = $assignment->classroom;
            $subject = $assignment->subject;
            $classesData[$classroom->id] ??= [
                'nama_kelas' => $classroom->tingkat.' - '.$classroom->nama_kelas,
                'subjects' => [],
            ];
            $classesData[$classroom->id]['subjects'][$subject->id] = [
                'id' => $subject->id,
                'nama' => $subject->nama_mapel,
                'kkm' => $subject->kkm,
            ];
        }

        return $classesData;
    }

    private function authorizedSelection(int $classroomId, int $subjectId): array
    {
        $user = auth()->user();
        $activeYear = AcademicYear::where('school_id', $user->school_id)
            ->where('is_active', true)
            ->first();

        if (! $activeYear) {
            throw ValidationException::withMessages([
                'classroom_id' => 'Tahun ajaran aktif belum ditentukan.',
            ]);
        }

        $classesData = $this->teacherClasses($user->school_id, $activeYear->id, $user->employee->id ?? 0);

        if (! isset($classesData[$classroomId]['subjects'][$subjectId])) {
            abort(403, 'Kelas atau mata pelajaran tidak termasuk tugas mengajar Anda.');
        }

        return [
            Classroom::findOrFail($classroomId),
            Subject::findOrFail($subjectId),
            $activeYear,
        ];
    }

    private function assessmentQuery(int $schoolId, int $activeYearId, int $classroomId, int $subjectId)
    {
        return Assessment::where('school_id', $schoolId)
            ->where('academic_year_id', $activeYearId)
            ->where('classroom_id', $classroomId)
            ->where('subject_id', $subjectId)
            ->where(function ($query) {
                $query->where('format', '!=', 'non-tes')->orWhereNull('format');
            });
    }

    private function calculateRows(Classroom $classroom, array $assessmentIds, int $kkm)
    {
        $students = $classroom->students()
            ->where('students.school_id', auth()->user()->school_id)
            ->orderBy('nama_lengkap')
            ->get();
        $scores = AssessmentScore::whereIn('assessment_id', $assessmentIds)->get();
        $scoreMap = [];

        foreach ($scores as $score) {
            $scoreMap[$score->student_id][$score->assessment_id] = $score->score;
        }

        return $students->map(function (Student $student) use ($assessmentIds, $scoreMap, $kkm) {
            $studentScores = [];

            foreach ($assessmentIds as $assessmentId) {
                if (isset($scoreMap[$student->id][$assessmentId])) {
                    $studentScores[] = $scoreMap[$student->id][$assessmentId];
                }
            }

            $isComplete = count($studentScores) === count($assessmentIds);
            $average = $isComplete
                ? round(array_sum($studentScores) / count($assessmentIds), 2)
                : null;

            return (object) [
                'student' => $student,
                'student_id' => $student->id,
                'score_count' => count($studentScores),
                'assessment_count' => count($assessmentIds),
                'average' => $average,
                'is_complete' => $isComplete,
                'eligible' => $isComplete && $average < $kkm,
            ];
        });
    }

    private function authorizeProgram(RemedialProgram $program): void
    {
        abort_unless(
            $program->school_id === auth()->user()->school_id
                && $program->employee_id === (auth()->user()->employee->id ?? 0),
            403,
            'Program remedial ini bukan milik Anda.'
        );
    }
}
