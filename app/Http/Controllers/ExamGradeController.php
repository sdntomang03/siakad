<?php

namespace App\Http\Controllers;

use App\Exports\GradesTemplateExport;
use App\Imports\ExamGradesImport;
use App\Models\Classroom;
use App\Models\ExamGrade;
use App\Models\Student; // Kita bisa pakai class export yang sama dengan rapor
use App\Models\Subject;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExamGradeController extends Controller
{
    // ==========================================
    // 1. FUNGSI READ (Tampilkan Data Nilai Ujian)
    // ==========================================
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Tangkap parameter filter dari tampilan
        $tingkatKelas = $request->tingkat_kelas;
        $semester = $request->semester;
        $kategoriUjian = $request->kategori_ujian;
        $subjectId = $request->subject_id; // <-- Tambahan filter Mapel

        // Query Dinamis
        $examGrades = ExamGrade::with(['student', 'subject'])
            ->where('school_id', $schoolId)
            ->when($kategoriUjian, function ($query, $kategoriUjian) {
                return $query->where('kategori_ujian', $kategoriUjian);
            })
            ->when($tingkatKelas, function ($query, $tingkatKelas) {
                return $query->where('tingkat_kelas', $tingkatKelas);
            })
            ->when($semester, function ($query, $semester) {
                return $query->where('semester', $semester);
            })
            ->when($subjectId, function ($query, $subjectId) {
                return $query->where('subject_id', $subjectId); // <-- Logika pencarian Mapel
            })
            ->latest()
            ->limit(100)
            ->get();

        // Ambil data siswa & mapel untuk form dan filter
        $students = Student::where('school_id', $schoolId)->orderBy('nama_lengkap')->get();
        // Pastikan urutan mapel rapi
        $subjects = Subject::where('school_id', $schoolId)->where('tingkat', 6)->orderBy('tingkat')->get();

        return view('exam_grades.index', compact(
            'examGrades', 'students', 'subjects', 'tingkatKelas', 'semester', 'kategoriUjian', 'subjectId'
        ));
    }

    // ==========================================
    // 2. FUNGSI CREATE / UPDATE MANUAL
    // ==========================================
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'kategori_ujian' => 'required|string|max:50',
            'tingkat_kelas' => 'required|integer|min:1|max:6',
            'nilai' => 'required|numeric|min:0|max:100',
        ]);

        $schoolId = auth()->user()->school_id;

        ExamGrade::updateOrCreate(
            [
                'school_id' => $schoolId,
                'student_id' => $request->student_id,
                'subject_id' => $request->subject_id,
                'kategori_ujian' => $request->kategori_ujian,
                'tingkat_kelas' => $request->tingkat_kelas,
                'semester' => 2,
            ],
            [
                'nilai' => $request->nilai,
            ]
        );

        return back()->with('success', 'Data nilai ujian berhasil disimpan/diperbarui.');
    }

    // ==========================================
    // 3. FUNGSI DELETE
    // ==========================================
    public function destroy(ExamGrade $examGrade)
    {
        // Keamanan: pastikan sekolah sesuai
        if ($examGrade->school_id != auth()->user()->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $examGrade->delete();

        return back()->with('success', 'Nilai ujian berhasil dihapus.');
    }

    // ==========================================
    // 4. FUNGSI IMPORT EXCEL
    // ==========================================
    public function import(Request $request)
    {
        $request->validate([
            'kategori_ujian' => 'required|string|max:50',
            'file_excel' => 'required|mimes:xlsx,xls,csv',
        ]);

        $schoolId = auth()->user()->school_id;

        try {
            Excel::import(
                new ExamGradesImport(
                    $schoolId,
                    $request->kategori_ujian,
                    6,
                    2
                ),
                $request->file('file_excel')
            );

            return back()->with('success', 'Seluruh data nilai '.$request->kategori_ujian.' berhasil diimpor!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor file: '.$e->getMessage());
        }
    }

    // ==========================================
    // 5. FUNGSI UNDUH TEMPLATE
    // ==========================================
    public function downloadTemplate(Request $request)
    {
        $request->validate([
            'tingkat_kelas' => 'required|in:1,2,3,4,5,6',
        ], [
            'tingkat_kelas.required' => 'Silakan pilih Tingkat Kelas terlebih dahulu.',
        ]);

        $schoolId = auth()->user()->school_id;
        $tingkat = $request->tingkat_kelas;

        // Kita menggunakan class template export yang sama dengan rapor
        // karena format matriksnya (Siswa x Mapel) identik.
        return Excel::download(
            new GradesTemplateExport($schoolId, $tingkat),
            'template_ujian_kelas_'.$tingkat.'.xlsx'
        );
    }

    // ==========================================
    // FUNGSI TAMPILAN INPUT MASSAL PER KELAS
    // ==========================================
    public function createBulk(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Ambil daftar kelas dan mapel untuk form pilihan
        $classrooms = Classroom::where('school_id', $schoolId)
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();
        $subjects = Subject::where('school_id', $schoolId)
            ->where('tingkat', 6)
            ->get();

        $classroomId = $request->classroom_id;
        $subjectId = $request->subject_id;
        $kategoriUjian = $request->kategori_ujian;
        $semester = 2j;

        $students = collect();
        $existingGrades = [];
        $selectedClassroom = null;

        // Jika guru sudah menekan tombol "Tampilkan Form"
        if ($classroomId && $subjectId && $kategoriUjian && $semester) {
            $selectedClassroom = Classroom::with(['students' => function ($q) {
                $q->orderBy('nama_lengkap');
            }])->where('school_id', $schoolId)->find($classroomId);

            if ($selectedClassroom) {
                $students = $selectedClassroom->students;

                // Ambil nilai yang sudah pernah diinput sebelumnya (agar bisa di-edit massal)
                $grades = ExamGrade::where('school_id', $schoolId)
                    ->whereIn('student_id', $students->pluck('id'))
                    ->where('subject_id', $subjectId)
                    ->where('kategori_ujian', $kategoriUjian)
                    ->where('semester', $semester)
                    ->get();

                // Format ke dalam array: $existingGrades[student_id] = nilai
                foreach ($grades as $grade) {
                    $existingGrades[$grade->student_id] = $grade->nilai;
                }
            }
        }

        return view('exam_grades.create_bulk', compact(
            'classrooms', 'subjects', 'classroomId', 'subjectId',
            'kategoriUjian', 'semester', 'students', 'existingGrades', 'selectedClassroom'
        ));
    }

    // ==========================================
    // FUNGSI SIMPAN INPUT MASSAL
    // ==========================================
    public function storeBulk(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'kategori_ujian' => 'required|string|max:50',
            'semester' => 'required|integer|in:1,2',
            'nilai' => 'required|array',
            'nilai.*' => 'nullable|numeric|min:0|max:100', // Izinkan null jika kolom dikosongkan
        ]);

        $schoolId = auth()->user()->school_id;
        $classroom = Classroom::where('school_id', $schoolId)->findOrFail($request->classroom_id);

        $count = 0;

        // Looping data nilai dari form (key = student_id, value = nilai)
        foreach ($request->nilai as $studentId => $nilai) {
            if ($nilai !== null && $nilai !== '') {
                ExamGrade::updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'student_id' => $studentId,
                        'subject_id' => $request->subject_id,
                        'kategori_ujian' => $request->kategori_ujian,
                        'tingkat_kelas' => $classroom->tingkat, // Mengambil tingkat dari tabel classroom
                        'semester' => $request->semester,
                    ],
                    [
                        'nilai' => $nilai,
                    ]
                );
                $count++;
            }
        }

        return redirect()->route('exam-grades.index')->with('success', $count.' data nilai berhasil disimpan.');
    }
}
