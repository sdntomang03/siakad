<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\SubjectFinalGrade;
use App\Services\FinalGradeService;
use Exception;
use Illuminate\Http\Request;

class FinalGradeController extends Controller
{
    protected $finalGradeService;

    // Dependency Injection Service ke dalam Controller
    public function __construct(FinalGradeService $finalGradeService)
    {
        $this->finalGradeService = $finalGradeService;
    }

    /**
     * Menampilkan antarmuka halaman Katrol Nilai
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? ($user->employee->school_id ?? 0);

        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first();
        if (! $activeYear) {
            return back()->with('error', 'Tahun ajaran aktif belum diatur.');
        }

        $classrooms = Classroom::where('school_id', $schoolId)
            ->where('academic_year_id', $activeYear->id)
            ->get();

        $subjects = collect();
        $grades = collect();
        $selectedClassroom = null;

        if ($request->filled('classroom_id')) {
            $selectedClassroom = Classroom::find($request->classroom_id);
            if ($selectedClassroom) {
                $subjects = Subject::where('school_id', $schoolId)
                    ->where('tingkat', $selectedClassroom->tingkat)
                    ->get();
            }
        }

        if ($request->filled('classroom_id') && $request->filled('subject_id')) {
            $grades = SubjectFinalGrade::with(['student' => function ($q) {
                $q->orderBy('nama_lengkap', 'asc');
            }])
                ->where('classroom_id', $request->classroom_id)
                ->where('subject_id', $request->subject_id)
                ->where('academic_year_id', $activeYear->id)
                ->get()
                ->sortBy('student.nama_lengkap');
        }

        return view('final_grades.katrol', compact('activeYear', 'classrooms', 'subjects', 'grades', 'selectedClassroom'));
    }

    /**
     * MENARIK RATA-RATA NILAI UJIAN & OBSERVASI (Generate Nilai Asli)
     */
    public function fetchRawScores(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ], [
            'classroom_id.required' => 'Pilihan Kelas tidak boleh kosong.',
            'subject_id.required' => 'Mata Pelajaran tidak boleh kosong.',
            'academic_year_id.required' => 'Tahun Ajaran aktif belum terdeteksi.',
        ]);

        $schoolId = auth()->user()->school_id ?? (auth()->user()->employee->school_id ?? 0);

        try {
            // Meneruskan eksekusi ke Service
            $this->finalGradeService->calculateAndSaveRawScores(
                $schoolId,
                $request->academic_year_id,
                $request->classroom_id,
                $request->subject_id
            );

            return back()->with('success', 'Nilai Asli berhasil ditarik dan dihitung dari rekapitulasi ujian. Silakan lakukan proses Katrol jika diperlukan.');
        } catch (Exception $e) {
            // Menangkap pesan error dari Service
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Memproses Katrol Nilai secara massal menggunakan Transformasi Linier
     */
    public function katrolNilai(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'target_min' => 'required|numeric|min:0|max:100',
            'target_max' => 'required|numeric|min:0|max:100|gt:target_min',
        ], [
            'required' => ':attribute tidak boleh dibiarkan kosong.',
            'exists' => ':attribute tidak valid di database kami.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal bernilai :min.',
            'max' => ':attribute maksimal bernilai :max.',
            'gt' => ':attribute harus diisi dengan angka yang lebih besar dari KKM.',
        ], [
            'classroom_id' => 'Data Kelas pada Form',
            'subject_id' => 'Mata Pelajaran',
            'academic_year_id' => 'Data Tahun Ajaran',
            'target_min' => 'Target Nilai Terendah (KKM)',
            'target_max' => 'Target Nilai Maksimal',
        ]);

        try {
            // Meneruskan eksekusi ke Service
            $this->finalGradeService->applyGradeCurve(
                $request->academic_year_id,
                $request->classroom_id,
                $request->subject_id,
                $request->target_min,
                $request->target_max
            );

            return back()->with('success', 'Nilai berhasil dikatrol secara proporsional. Peringkat siswa tetap terjaga!');
        } catch (Exception $e) {
            // Menangkap pesan error dari Service
            return back()->with('error', $e->getMessage());
        }
    }
}
