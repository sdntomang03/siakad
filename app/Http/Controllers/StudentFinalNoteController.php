<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\JurnalPiket;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentFinalNote;
use App\Models\TeacherNote;
use Illuminate\Http\Request;

class StudentFinalNoteController extends Controller
{
    /**
     * Menampilkan daftar siswa dalam satu kelas untuk diisi catatan akhirnya
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        abort_if(! $user->hasAnyRole(['superadmin', 'operator', 'guru', 'kepsek']), 403, 'Akses ditolak.');

        $selectedSchoolId = $user->hasRole('superadmin') ? $request->query('school_id') : $user->school_id;
        $schools = $user->hasRole('superadmin') ? School::orderBy('nama_sekolah')->get() : collect();

        $activeYear = null;
        $allClassrooms = collect();
        $students = collect();
        $selectedClassroom = null;

        if ($selectedSchoolId) {
            $activeYear = AcademicYear::where('school_id', $selectedSchoolId)
                ->where('is_active', true)
                ->first();

            if ($activeYear) {
                // Query dasar untuk mengambil kelas di tahun ajaran aktif
                $classroomQuery = Classroom::where('school_id', $selectedSchoolId)
                    ->where('academic_year_id', $activeYear->id);

                // Jika user adalah Guru, hanya tampilkan kelas di mana dia menjadi Wali Kelas
                if ($user->hasRole('guru')) {
                    $employeeId = $user->employee->id ?? 0;
                    $classroomQuery->where('homeroom_teacher_id', $employeeId);
                }

                $allClassrooms = $classroomQuery->get();

                // PERBAIKAN: Gunakan Eager Loading 'with' untuk mengambil data relasi students
                if ($request->classroom_id) {
                    $selectedClassroom = Classroom::with(['students' => function ($q) {
                        $q->orderBy('nama_lengkap', 'asc');
                    }])->find($request->classroom_id);

                    // Validasi kepemilikan kelas, lalu ambil dari relasi
                    if ($selectedClassroom && $allClassrooms->contains('id', $selectedClassroom->id)) {
                        $students = $selectedClassroom->students;
                    }
                }
            }
        }

        return view('catatan_akhir.index', compact(
            'schools', 'selectedSchoolId', 'activeYear', 'allClassrooms', 'selectedClassroom', 'students'
        ));
    }

    /**
     * Menampilkan form pengisian catatan akhir per siswa
     */
    public function edit($student_id, $classroom_id)
    {
        // Asumsi menggunakan tahun ajaran aktif dari session/pengaturan
        $active_academic_year_id = 1; // Ubah sesuai logika aplikasi Anda

        $student = Student::findOrFail($student_id);
        $classroom = Classroom::findOrFail($classroom_id);

        // 1. Tarik Rekap Catatan Guru (Hanya yang is_for_report = true)
        $teacherNotes = TeacherNote::where('student_id', $student_id)
            ->where('classroom_id', $classroom_id)
            ->where('academic_year_id', $active_academic_year_id)
            ->where('is_for_report', true)
            ->get();

        // 2. Tarik Rekap Jurnal Piket
        $piketTerlaksana = JurnalPiket::where('student_id', $student_id)
            ->where('classroom_id', $classroom_id)
            ->where('academic_year_id', $active_academic_year_id)
            ->where('status', 'terlaksana')
            ->count();

        $piketTidak = JurnalPiket::where('student_id', $student_id)
            ->where('classroom_id', $classroom_id)
            ->where('academic_year_id', $active_academic_year_id)
            ->where('status', 'tidak_terlaksana')
            ->count();

        // 3. Tarik Rekap Absensi
        // $sakit = Absensi::where('student_id', $student_id)->where('status', 'S')->count();
        // $izin = Absensi::where('student_id', $student_id)->where('status', 'I')->count();
        // $alpha = Absensi::where('student_id', $student_id)->where('status', 'A')->count();
        $sakit = 0;
        $izin = 0;
        $alpha = 0;

        // Cari apakah sudah ada data catatan akhir sebelumnya
        $finalNote = StudentFinalNote::where('student_id', $student_id)
            ->where('classroom_id', $classroom_id)
            ->where('academic_year_id', $active_academic_year_id)
            ->first();

        return view('catatan_akhir.edit', compact(
            'student', 'classroom', 'teacherNotes',
            'piketTerlaksana', 'piketTidak',
            'sakit', 'izin', 'alpha', 'finalNote', 'active_academic_year_id'
        ));
    }

    /**
     * Memproses penyimpanan catatan akhir
     */
    public function update(Request $request, $student_id, $classroom_id)
    {
        $request->validate([
            'academic_year_id' => 'required',
            'catatan_akhir' => 'required|string',
            'sakit' => 'required|integer|min:0',
            'izin' => 'required|integer|min:0',
            'alpha' => 'required|integer|min:0',
        ]);

        $employee_id = auth()->user()->employee->id ?? null;

        StudentFinalNote::updateOrCreate(
            [
                'student_id' => $student_id,
                'classroom_id' => $classroom_id,
                'academic_year_id' => $request->academic_year_id,
            ],
            [
                'employee_id' => $employee_id,
                'sakit' => $request->sakit,
                'izin' => $request->izin,
                'alpha' => $request->alpha,
                'piket_terlaksana' => $request->piket_terlaksana,
                'piket_tidak_terlaksana' => $request->piket_tidak_terlaksana,
                'ringkasan_catatan_guru' => $request->ringkasan_catatan_guru,
                'catatan_akhir' => $request->catatan_akhir,
            ]
        );

        return back()->with('success', 'Catatan Akhir Siswa berhasil disimpan!');
    }
}
