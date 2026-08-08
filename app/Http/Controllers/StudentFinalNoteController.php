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

        // 1. Tarik SEMUA Rekap Catatan Guru (Tanpa filter is_for_report)
        $teacherNotes = TeacherNote::where('student_id', $student_id)
            ->where('classroom_id', $classroom_id)
            ->where('academic_year_id', $active_academic_year_id)
            ->orderBy('tanggal', 'desc')
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

        // 3. Tarik Rekap Absensi (Otomatis dari tabel attendances)
        $absensi = Attendance::where('student_id', $student_id)
            ->where('classroom_id', $classroom_id)
            ->where('academic_year_id', $active_academic_year_id)
            ->get();

        // Hitung jumlah S, I, A dari data absensi
        $sakit = $absensi->where('status', 'sakit')->count();
        $izin = $absensi->where('status', 'izin')->count();
        $alpha = $absensi->where('status', 'alfa')->count(); // Perhatikan penulisan 'alfa' dari sistem Anda

        // 4. Tarik Rekap Nilai Siswa (Contoh Mockup Lengkap)
        // INGAT: Ganti ini dengan query ke tabel nilai/grade Anda yang sebenarnya nanti
        $rekapNilai = collect([
            (object) ['nama_mapel' => 'Pendidikan Agama & Budi Pekerti', 'nilai_akhir' => 88],
            (object) ['nama_mapel' => 'Pendidikan Pancasila', 'nilai_akhir' => 85],
            (object) ['nama_mapel' => 'Bahasa Indonesia', 'nilai_akhir' => 90],
            (object) ['nama_mapel' => 'Matematika', 'nilai_akhir' => 78],
            (object) ['nama_mapel' => 'Ilmu Pengetahuan Alam', 'nilai_akhir' => 82],
            (object) ['nama_mapel' => 'Ilmu Pengetahuan Sosial', 'nilai_akhir' => 84],
            (object) ['nama_mapel' => 'Bahasa Inggris', 'nilai_akhir' => 89],
            (object) ['nama_mapel' => 'Seni Budaya', 'nilai_akhir' => 92],
            (object) ['nama_mapel' => 'Pendidikan Jasmani, Olahraga, & Kesehatan', 'nilai_akhir' => 86],
            (object) ['nama_mapel' => 'Prakarya dan Kewirausahaan', 'nilai_akhir' => 88],
            (object) ['nama_mapel' => 'Muatan Lokal', 'nilai_akhir' => 90],
        ]);

        // Cari apakah sudah ada data catatan akhir sebelumnya
        $finalNote = StudentFinalNote::where('student_id', $student_id)
            ->where('classroom_id', $classroom_id)
            ->where('academic_year_id', $active_academic_year_id)
            ->first();

        return view('catatan_akhir.edit', compact(
            'student', 'classroom', 'teacherNotes',
            'piketTerlaksana', 'piketTidak',
            'sakit', 'izin', 'alpha', 'finalNote', 'active_academic_year_id',
            'rekapNilai'
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
