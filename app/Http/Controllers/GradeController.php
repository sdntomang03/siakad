<?php

namespace App\Http\Controllers;

use App\Exports\GradesTemplateExport;
use App\Imports\GradesImport;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GradeController extends Controller
{
    public function index()
    {
        return view('grades.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'tingkat_kelas' => 'required|in:4,5,6',
            'semester' => 'required|in:1,2',
            'file_excel' => 'required|mimes:xlsx,xls,csv',
        ]);

        $schoolId = auth()->user()->school_id;

        try {
            Excel::import(
                new GradesImport(
                    $schoolId,
                    $request->tingkat_kelas,
                    $request->semester
                ),
                $request->file('file_excel')
            );

            return back()->with('success', 'Seluruh data nilai mapel berhasil diimpor!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function recap(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Mengambil daftar siswa untuk dropdown pencarian
        $students = Student::where('school_id', $schoolId)->orderBy('nama_lengkap')->get();

        $selectedStudent = null;
        $recapData = [];
        $subjectNames = [];

        // Jika ada siswa yang dicari/dipilih
        if ($request->has('student_id') && $request->student_id != '') {
            $selectedStudent = Student::findOrFail($request->student_id);

            // Ambil NAMA MAPEL yang unik (karena tiap tingkat punya ID mapel berbeda)
            $subjectNames = Subject::where('school_id', $schoolId)
                ->whereNotNull('kode_mapel')
                // ->where('is_sidanira', true) // (Opsional) Aktifkan jika Anda pakai fitur is_sidanira
                ->select('nama_mapel')
                ->distinct()
                ->orderBy('nama_mapel')
                ->pluck('nama_mapel');

            // Ambil seluruh nilai siswa yang bersangkutan BESERTA relasi tabel subject
            $grades = Grade::with('subject')
                ->where('student_id', $selectedStudent->id)
                ->where('school_id', $schoolId)
                ->get();

            // Menyusun data nilai ke dalam format matriks menggunakan NAMA MAPEL:
            // $recapData['Nama Mapel'][tingkat_kelas][semester] = nilai
            foreach ($grades as $grade) {
                // Pastikan relasi subject tidak null
                if ($grade->subject) {
                    $namaMapel = $grade->subject->nama_mapel;
                    $recapData[$namaMapel][$grade->tingkat_kelas][$grade->semester] = $grade->nilai;
                }
            }
        }

        return view('grades.recap', compact('students', 'selectedStudent', 'subjectNames', 'recapData'));
    }

    public function ledger(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->school_id;
        $employeeId = $user->employee ? $user->employee->id : null;

        // Ambil daftar kelas yang diampu oleh wali kelas ini
        $myClassrooms = Classroom::where('school_id', $schoolId)
            ->where('homeroom_teacher_id', $employeeId)
            ->get();

        // Ambil nama mata pelajaran yang unik di sekolah ini (paling aman tanpa syarat kode_mapel)
        $subjects = Subject::where('school_id', $schoolId)
            ->select('nama_mapel')
            ->distinct()
            ->orderBy('nama_mapel')
            ->get();

        $classroomId = $request->classroom_id;
        $ledgerData = [];
        $students = collect();
        $selectedClassroom = null;

        if ($classroomId) {
            $selectedClassroom = Classroom::with(['students' => function ($query) {
                $query->orderBy('nama_lengkap');
            }])->where('school_id', $schoolId)->find($classroomId);

            if ($selectedClassroom) {
                $students = $selectedClassroom->students;
                $studentIds = $students->pluck('id');

                // Ambil semua data nilai siswa untuk tingkat kelas 4, 5, dan 6
                $grades = Grade::with('subject')
                    ->where('school_id', $schoolId)
                    ->whereIn('student_id', $studentIds)
                    ->whereIn('tingkat_kelas', [4, 5, 6])
                    ->get();

                // Susun ke dalam matriks data menggunakan nama_mapel sebagai Key
                foreach ($grades as $grade) {
                    if ($grade->subject) {
                        $namaMapel = $grade->subject->nama_mapel;
                        $periode = ($grade->tingkat_kelas * 10) + $grade->semester; // Hasil: 41, 42, 51, 52, 61, 62
                        $ledgerData[$grade->student_id][$namaMapel][$periode] = $grade->nilai;
                    }
                }
            }
        }

        return view('grades.ledger', compact(
            'subjects',
            'myClassrooms',
            'selectedClassroom',
            'ledgerData',
            'students'
        ));
    }

    public function downloadTemplate(Request $request)
    {
        $request->validate([
            'tingkat_kelas' => 'required|in:1,2,3,4,5,6',
        ], [
            'tingkat_kelas.required' => 'Silakan pilih Tingkat Kelas terlebih dahulu sebelum mengunduh template.',
        ]);

        $schoolId = auth()->user()->school_id;
        $tingkat = $request->tingkat_kelas;

        return Excel::download(
            new GradesTemplateExport($schoolId, $tingkat),
            'template_nilai_kelas_'.$tingkat.'.xlsx'
        );
    }
}
