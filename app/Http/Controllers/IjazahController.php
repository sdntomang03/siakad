<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\ExamGrade;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\Request;

class IjazahController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // 1. Tangkap Input Bobot dan Filter (Gunakan Default jika kosong)
        $bobotRapor = $request->input('bobot_rapor', 60); // Default 60%
        $bobotUjian = $request->input('bobot_ujian', 40); // Default 40%
        $classroomId = $request->input('classroom_id');

        // Tetapkan kategori ujian baku secara statis
        $kategoriUjian = 'Ujian Sekolah';

        // 2. Ambil SEMUA Mapel (Hanya ambil mapel kelas 6 sebagai acuan kolom)
        // Kita tidak menggunakan filter 'is_sidanira' karena Ijazah butuh semua mapel
        $ijazahSubjects = Subject::where('school_id', $schoolId)
            ->where('tingkat', 6)

            ->get();

        // 3. Ambil Kelas (Hanya Kelas 6)
        $classrooms = Classroom::where('school_id', $schoolId)
            ->where('tingkat', 6)
            ->orderBy('nama_kelas')
            ->get();

        $students = collect();
        $ijazahData = [];
        $selectedClassroom = null;

        // 4. Proses Kalkulasi jika Kelas Dipilih
        if ($classroomId) {
            $selectedClassroom = Classroom::with(['students' => function ($q) {
                $q->orderBy('nama_lengkap');
            }])->where('school_id', $schoolId)->find($classroomId);

            if ($selectedClassroom) {
                $students = $selectedClassroom->students;

                foreach ($students as $student) {
                    foreach ($ijazahSubjects as $subject) {

                        // A. CARI RATA-RATA RAPOR KELAS 4, 5, dan 6
                        // Ambil semua ID mapel dengan nama yang sama dari kelas 4 sampai 6
                        $subjectIds = Subject::where('school_id', $schoolId)
                            ->where('nama_mapel', $subject->nama_mapel)
                            ->pluck('id');

                        // Mengambil dari tabel grades biasa mencakup kelas 4, 5, 6
                        $raporGrades = Grade::where('student_id', $student->id)
                            ->whereIn('subject_id', $subjectIds)
                            ->whereIn('tingkat_kelas', [4, 5, 6]) // Ambil semua semester di kelas 4, 5, dan 6
                            ->get();

                        $avgRapor = $raporGrades->count() > 0 ? $raporGrades->avg('nilai') : 0;

                        // B. CARI NILAI UJIAN
                        // Mengambil dari tabel exam_grades khusus
                        $examGrade = ExamGrade::where('student_id', $student->id)
                            ->where('subject_id', $subject->id)
                            ->where('kategori_ujian', $kategoriUjian)
                            ->value('nilai') ?? 0;

                        // C. KALKULASI NILAI AKHIR (Rapor * Bobot + Ujian * Bobot)
                        $nilaiAkhir = (($avgRapor * $bobotRapor / 100) + ($examGrade * $bobotUjian / 100));

                        // Simpan ke dalam array matriks
                        $ijazahData[$student->id][$subject->id] = [
                            'avg_rapor' => round($avgRapor, 2),
                            'exam' => round($examGrade, 2),
                            'final' => round($nilaiAkhir, 2),
                        ];
                    }
                }
            }
        }

        return view('ijazah.index', compact(
            'ijazahSubjects', 'classrooms', 'bobotRapor',
            'bobotUjian', 'students', 'ijazahData',
            'classroomId', 'selectedClassroom', 'kategoriUjian'
        ));
    }
}
