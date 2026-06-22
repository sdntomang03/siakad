<?php

namespace App\Http\Controllers;

use App\Exports\UnreturnedBooksExport;
use App\Models\Classroom;
use App\Models\ReportSubmission;
use App\Models\Student;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportSubmissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Fungsi untuk mengubah status massal dari tabel riwayat bawah
    public function bulkUpdateHistory(Request $request)
    {
        $request->validate([
            'submission_ids' => 'required|array',
            'submission_ids.*' => 'integer|exists:report_submissions,id',
            'posisi' => 'required|in:Di Sekolah,Dibawa Siswa',
        ]);

        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;

        $submissions = ReportSubmission::whereIn('id', $request->submission_ids)
            ->when(! $user->hasRole('superadmin'), function ($query) use ($schoolId) {
                return $query->where('school_id', $schoolId);
            })->get();

        foreach ($submissions as $submission) {
            $submission->posisi = $request->posisi;
            if ($request->posisi === 'Dibawa Siswa') {
                $submission->waktu_dibagikan = now();
            } else {
                $submission->waktu_dikembalikan = now();
            }
            $submission->save();
        }

        return back()->with('success', 'Status rapor terpilih berhasil diperbarui.');
    }

    // Fungsi untuk menghapus massal dari tabel riwayat bawah
    public function bulkDestroyHistory(Request $request)
    {
        $request->validate([
            'submission_ids' => 'required|array',
            'submission_ids.*' => 'integer|exists:report_submissions,id',
        ]);

        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;

        ReportSubmission::whereIn('id', $request->submission_ids)
            ->when(! $user->hasRole('superadmin'), function ($query) use ($schoolId) {
                return $query->where('school_id', $schoolId);
            })->delete();

        return back()->with('success', 'Riwayat rapor terpilih berhasil dihapus permanen.');
    }

    public function index()
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;

        // Memuat relasi academicYear ke dalam query list riwayat
        $query = ReportSubmission::with('student', 'classroom', 'academicYear');

        if (! $user->hasRole('superadmin')) {
            if ($schoolId) {
                $query->where('school_id', $schoolId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $submissions = $query->orderBy('updated_at', 'desc')->paginate(20);

        // Ambil daftar rombel beserta data tahun ajaran aktifnya
        if ($user->hasRole('superadmin')) {
            $myClassrooms = Classroom::with('academicYear')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        } else {
            $employeeId = optional($user->employee)->id;
            $myClassrooms = collect();
            if ($schoolId && $employeeId) {
                $myClassrooms = Classroom::with('academicYear')
                    ->where('school_id', $schoolId)
                    ->where('homeroom_teacher_id', $employeeId)
                    ->orderBy('tingkat')->orderBy('nama_kelas')
                    ->get();
            }
        }

        if ($user->hasRole('superadmin')) {
            $students = Student::orderBy('nama_lengkap')->get();
        } else {
            $classIds = $myClassrooms->pluck('id')->all();
            $students = $classIds ? Student::whereHas('classrooms', fn ($q) => $q->whereIn('classrooms.id', $classIds))
                ->with('classrooms')
                ->orderBy('nama_lengkap')
                ->get() : collect();
        }

        return view('report_submissions.index', [
            'submissions' => $submissions,
            'students' => $students,
            'classrooms' => $myClassrooms,
        ]);
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'integer|exists:students,id',
            'posisi' => 'required|in:Di Sekolah,Dibawa Siswa',
            'classroom_id' => 'required|integer|exists:classrooms,id',
        ]);

        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;
        $posisi = $request->input('posisi');

        // Mengambil entitas rombel untuk mendapatkan id tahun ajaran yang melekat
        $classroom = Classroom::find($request->classroom_id);
        $academicYearId = $classroom ? $classroom->academic_year_id : null;

        foreach ($request->student_ids as $studentId) {
            $data = [
                'student_id' => $studentId,
                'school_id' => $schoolId,
                'classroom_id' => $request->classroom_id,
                'academic_year_id' => $academicYearId, // Menyimpan id referensi tahun ajaran
                'posisi' => $posisi,
            ];

            if ($posisi === 'Dibawa Siswa') {
                $data['waktu_dibagikan'] = now();
            } else {
                $data['waktu_dikembalikan'] = now();
            }

            ReportSubmission::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'academic_year_id' => $academicYearId, // Mengunci keunikan berdasarkan ID tahun ajaran
                    'school_id' => $schoolId,
                ],
                $data
            );
        }

        $pesan = $posisi === 'Dibawa Siswa' ? 'Rapor ditandai sedang dibawa siswa.' : 'Rapor ditandai telah kembali di sekolah.';

        return back()->with('success', $pesan);
    }

    public function toggleStatus(ReportSubmission $reportSubmission)
    {
        $this->ensureSchoolAccess($reportSubmission);

        if ($reportSubmission->posisi === 'Di Sekolah') {
            $reportSubmission->posisi = 'Dibawa Siswa';
            $reportSubmission->waktu_dibagikan = now();
        } else {
            $reportSubmission->posisi = 'Di Sekolah';
            $reportSubmission->waktu_dikembalikan = now();
        }

        $reportSubmission->save();

        return back()->with('success', 'Status posisi rapor berhasil diperbarui.');
    }

    public function destroy(ReportSubmission $reportSubmission)
    {
        $this->ensureSchoolAccess($reportSubmission);
        $reportSubmission->delete();

        return back()->with('success', 'Catatan rapor dihapus.');
    }

    private function ensureSchoolAccess(ReportSubmission $reportSubmission)
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;
        if (! $user->hasRole('superadmin') && $reportSubmission->school_id !== $schoolId) {
            abort(403);
        }
    }

    public function exportUnreturned()
    {
        $user = auth()->user();

        return Excel::download(
            new UnreturnedBooksExport($user->school_id),
            'daftar_buku_belum_dikembalikan_'.date('d_M_Y').'.xlsx'
        );
    }
}
