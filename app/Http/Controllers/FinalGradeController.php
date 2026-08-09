<?php

namespace App\Http\Controllers;

use App\Http\Requests\FetchRawScoresRequest;
use App\Http\Requests\KatrolNilaiRequest;
use App\Services\FinalGradeService;
use Exception;
use Illuminate\Http\Request;

class FinalGradeController extends Controller
{
    protected $finalGradeService;

    // Dependency Injection
    public function __construct(FinalGradeService $finalGradeService)
    {
        $this->finalGradeService = $finalGradeService;
    }

    /**
     * Menampilkan antarmuka halaman Katrol Nilai
     */
    public function index(Request $request)
    {
        try {
            $schoolId = auth()->user()->school_id ?? (auth()->user()->employee->school_id ?? 0);

            // Logika bisnis dipindahkan ke Service
            $data = $this->finalGradeService->getKatrolData($request, $schoolId);

            return view('final_grades.katrol', $data);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * MENARIK RATA-RATA NILAI UJIAN & OBSERVASI (Generate Nilai Asli)
     */
    public function fetchRawScores(FetchRawScoresRequest $request)
    {
        // Validasi sudah ditangani secara otomatis oleh FetchRawScoresRequest

        try {
            $schoolId = auth()->user()->school_id ?? (auth()->user()->employee->school_id ?? 0);

            $this->finalGradeService->calculateAndSaveRawScores(
                $schoolId,
                $request->academic_year_id,
                $request->classroom_id,
                $request->subject_id
            );

            return back()->with('success', 'Nilai Asli berhasil ditarik dan dihitung dari rekapitulasi ujian. Silakan lakukan proses Katrol jika diperlukan.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Memproses Katrol Nilai secara massal menggunakan Transformasi Linier
     */
    public function katrolNilai(KatrolNilaiRequest $request)
    {
        // Validasi sudah ditangani secara otomatis oleh KatrolNilaiRequest

        try {
            $this->finalGradeService->applyGradeCurve(
                $request->academic_year_id,
                $request->classroom_id,
                $request->subject_id,
                $request->target_min,
                $request->target_max
            );

            return back()->with('success', 'Nilai berhasil dikatrol secara proporsional. Peringkat siswa tetap terjaga!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
