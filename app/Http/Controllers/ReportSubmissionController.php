<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\ReportSubmission;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportSubmissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        // resolve school id: user may have school_id directly or via employee relation
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;

        $query = ReportSubmission::with('student', 'classroom');

        if (! $user->hasRole('superadmin')) {
            if ($schoolId) {
                $query->where('school_id', $schoolId);
            } else {
                // no school id available for this non-superadmin user -> no results
                $query->whereRaw('1 = 0');
            }
        }

        $submissions = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get active academic years for the school
        $academicYears = \App\Models\AcademicYear::where('school_id', $schoolId)
            ->orderBy('tahun_ajaran', 'desc')
            ->get();

        $activeYear = \App\Models\AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->first();

        // determine classrooms the current user (teacher) is responsible for
        if ($user->hasRole('superadmin')) {
            $myClassrooms = Classroom::when(true, fn($q) => $q->orderBy('tingkat')->orderBy('nama_kelas'))->get();
        } else {
            $employeeId = optional($user->employee)->id;
            $myClassrooms = collect();
            if ($schoolId && $employeeId) {
                // Only homeroom classes
                $myClassrooms = Classroom::where('school_id', $schoolId)
                    ->where('homeroom_teacher_id', $employeeId)
                    ->orderBy('tingkat')->orderBy('nama_kelas')
                    ->get();
            }
        }

        // students limited to classes the teacher handles
        if ($user->hasRole('superadmin')) {
            $students = Student::orderBy('nama_lengkap')->get();
        } else {
            $classIds = $myClassrooms->pluck('id')->all();
            if ($classIds) {
                $students = Student::whereHas('classrooms', fn($q) => $q->whereIn('classrooms.id', $classIds))
                    ->with('classrooms')
                    ->orderBy('nama_lengkap')
                    ->get();
            } else {
                $students = collect();
            }
        }

        $classrooms = $myClassrooms;

        return view('report_submissions.index', compact('submissions', 'students', 'classrooms', 'academicYears', 'activeYear'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;

        $data = $request->validate([
            'student_id' => [
                'required',
                Rule::exists('students', 'id')->where(fn ($query) => $query->where('school_id', $schoolId)),
            ],
            'classroom_id' => [
                'nullable',
                Rule::exists('classrooms', 'id')->where(fn ($query) => $query->where('school_id', $user->school_id)),
            ],
            'period' => 'nullable|string|max:50',
            'is_submitted' => 'sometimes|boolean',
            'location' => 'nullable|in:school,home',
            'notes' => 'nullable|string',
        ]);

        $data['school_id'] = $schoolId;
        // default location to 'school' if not provided
        $data['location'] = $data['location'] ?? 'school';
        if (! empty($data['is_submitted'])) {
            $data['submitted_at'] = now();
        }

        ReportSubmission::updateOrCreate(
            ['student_id' => $data['student_id'], 'period' => $data['period'], 'school_id' => $data['school_id']],
            $data
        );

        return back()->with('success', 'Status pengumpulan rapor disimpan.');
    }

    public function toggle(ReportSubmission $reportSubmission)
    {
        $this->ensureSchoolAccess($reportSubmission);

        $reportSubmission->is_submitted = ! $reportSubmission->is_submitted;
        $reportSubmission->submitted_at = $reportSubmission->is_submitted ? now() : null;
        // keep location consistent: if marked submitted but not returned, default to home; if returned, ensure school
        if ($reportSubmission->is_returned) {
            $reportSubmission->location = 'school';
        } else {
            $reportSubmission->location = $reportSubmission->is_submitted ? 'home' : ($reportSubmission->location ?? 'home');
        }
        $reportSubmission->save();

        return back()->with('success', 'Status pengumpulan rapor diperbarui.');
    }

    public function destroy(ReportSubmission $reportSubmission)
    {
        $this->ensureSchoolAccess($reportSubmission);

        $reportSubmission->delete();

        return back()->with('success', 'Catatan pengumpulan rapor dihapus.');
    }

    public function markReturned(ReportSubmission $reportSubmission)
    {
        $this->ensureSchoolAccess($reportSubmission);

        if ($reportSubmission->is_returned) {
            return back()->with('warning', 'Rapor sudah ditandai sebagai dikembalikan.');
        }

        $reportSubmission->is_returned = true;
        $reportSubmission->returned_at = now();
        $reportSubmission->location = 'school';
        $reportSubmission->save();

        return back()->with('success', 'Rapor ditandai sudah dikembalikan.');
    }

    public function returnMultiple(Request $request)
    {
        $ids = $request->input('submission_ids', []);
        if (empty($ids)) {
            return back()->with('warning', 'Tidak ada data yang dipilih.');
        }

        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;

        $subs = ReportSubmission::whereIn('id', $ids)->get();
        foreach ($subs as $s) {
            if (! $user->hasRole('superadmin') && $s->school_id !== $schoolId) {
                continue;
            }
            $s->is_returned = true;
            $s->returned_at = now();
            $s->location = 'school';
            $s->save();
        }

        return back()->with('success', 'Rapor terpilih ditandai sudah dikembalikan.');
    }

    public function setLocationMultiple(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'integer|exists:students,id',
            'location' => 'required|in:school,home',
            'period' => 'nullable|string|max:50',
            'is_submitted' => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;

        $studentIds = $request->input('student_ids', []);
        foreach ($studentIds as $studentId) {
            $data = [
                'student_id' => $studentId,
                'school_id' => $schoolId,
                'period' => $request->input('period'),
                'location' => $request->input('location'),
                'notes' => null,
            ];
            if ($request->filled('is_submitted')) {
                $data['is_submitted'] = (bool) $request->input('is_submitted');
                $data['submitted_at'] = $data['is_submitted'] ? now() : null;
            }
            // if location is school, mark returned
            if ($request->input('location') === 'school') {
                $data['is_returned'] = true;
                $data['returned_at'] = now();
            } else {
                $data['is_returned'] = $data['is_returned'] ?? false;
                $data['returned_at'] = $data['is_returned'] ? ($data['returned_at'] ?? now()) : null;
            }

            ReportSubmission::updateOrCreate(
                ['student_id' => $studentId, 'period' => $data['period'], 'school_id' => $schoolId],
                $data
            );
        }

        return back()->with('success', 'Lokasi rapor untuk siswa terpilih telah diperbarui.');
    }

    public function setReturnedMultiple(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'integer|exists:students,id',
            'period' => 'nullable|string|max:50',
        ]);

        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;

        $studentIds = $request->input('student_ids', []);
        foreach ($studentIds as $studentId) {
            $data = [
                'student_id' => $studentId,
                'school_id' => $schoolId,
                'period' => $request->input('period'),
                'location' => 'school',
                'is_returned' => true,
                'returned_at' => now(),
                'is_submitted' => true,
                'submitted_at' => now(),
            ];

            ReportSubmission::updateOrCreate(
                ['student_id' => $studentId, 'period' => $data['period'], 'school_id' => $schoolId],
                $data
            );
        }

        return back()->with('success', 'Siswa terpilih ditandai sebagai dikembalikan.');
    }

    public function updateLocationMultiple(Request $request)
    {
        $request->validate([
            'submission_ids' => 'required|array',
            'submission_ids.*' => 'integer|exists:report_submissions,id',
            'location' => 'required|in:school,home',
        ]);

        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;

        $subs = ReportSubmission::whereIn('id', $request->input('submission_ids', []))->get();
        foreach ($subs as $s) {
            if (! $user->hasRole('superadmin') && $s->school_id !== $schoolId) {
                continue;
            }
            $s->location = $request->input('location');
            $s->save();
        }

        return back()->with('success', 'Lokasi rapor untuk siswa terpilih telah diperbarui.');
    }

    public function setLocation(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'integer|exists:students,id',
            'location' => 'required|in:school,home',
            'period' => 'nullable|string|max:50',
        ]);

        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;

        $studentIds = $request->input('student_ids', []);
        foreach ($studentIds as $studentId) {
            $data = [
                'student_id' => $studentId,
                'school_id' => $schoolId,
                'period' => $request->input('period'),
                'location' => $request->input('location'),
            ];

            ReportSubmission::updateOrCreate(
                ['student_id' => $studentId, 'period' => $data['period'], 'school_id' => $schoolId],
                $data
            );
        }

        return back()->with('success', 'Lokasi rapor siswa telah diperbarui.');
    }

    private function ensureSchoolAccess(ReportSubmission $reportSubmission)
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;
        if (! $user->hasRole('superadmin') && $reportSubmission->school_id !== $schoolId) {
            abort(403);
        }
    }
}
