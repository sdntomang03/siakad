<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentCriteriaScore;
use App\Models\AssessmentCriterion;
use App\Models\Student;
use Illuminate\Http\Request;

class ObservationController extends Controller
{
    // TAHAP 1: FORM BUAT PENILAIAN NON-TES & DESKRIPTOR
    public function create()
    {
        // ... (Logika load mapel dan kelas sama seperti AssessmentController@create) ...
        return view('observations.create', compact('classesData', 'assessmentTypes'));
    }

    // TAHAP 2: SIMPAN NAMA, SKALA, DAN DESKRIPTOR KRITERIA
    public function store(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required',
            'subject_id' => 'required',
            'keterangan' => 'required', // Nama Penilaian
            'tanggal' => 'required|date',
            'scale' => 'required|in:3,4,5', // Pilihan Skala
            'descriptors' => 'required|array|min:1', // Minimal 1 deskriptor
            'descriptors.*' => 'required|string|max:255',
        ]);

        // 1. Simpan Header Penilaian (Format Non-Tes)
        $assessment = Assessment::create([
            'school_id' => auth()->user()->school_id,
            'academic_year_id' => $activeYearId, // Ambil dari logic active year
            'classroom_id' => $request->classroom_id,
            'subject_id' => $request->subject_id,
            'employee_id' => auth()->user()->employee->id,
            'assessment_type_id' => $request->assessment_type_id,
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal,
            'format' => 'non-tes',
            'scale' => $request->scale,
        ]);

        // 2. Simpan Kriteria Deskriptor
        foreach ($request->descriptors as $desc) {
            AssessmentCriterion::create([
                'assessment_id' => $assessment->id,
                'descriptor' => $desc,
            ]);
        }

        return redirect()->route('observations.input', $assessment->id);
    }

    // TAHAP 3: MENAMPILKAN MATRIKS INPUT PENILAIAN OBSERVASI
    public function input(Assessment $assessment)
    {
        $assessment->load(['classroom', 'subject', 'criteria']);

        $students = Student::whereHas('classrooms', function ($query) use ($assessment) {
            $query->where('classrooms.id', $assessment->classroom_id);
        })->orderBy('nama_lengkap', 'asc')->get();

        // Ambil nilai existing jika ada
        $rawScores = AssessmentCriteriaScore::where('assessment_id', $assessment->id)->get();
        $existingScores = [];
        foreach ($rawScores as $score) {
            $existingScores[$score->student_id][$score->assessment_criterion_id] = $score->score;
        }

        return view('observations.input', compact('assessment', 'students', 'existingScores'));
    }

    // TAHAP 4: SIMPAN HASIL PENILAIAN OBSERVASI
    public function updateScores(Request $request, Assessment $assessment)
    {
        // Validasi array multidimensi: scores[student_id][criterion_id] = nilai
        $request->validate([
            'scores' => 'required|array',
        ]);

        foreach ($request->scores as $studentId => $criteriaScores) {
            foreach ($criteriaScores as $criterionId => $scoreValue) {
                if ($scoreValue !== null) {
                    AssessmentCriteriaScore::updateOrCreate(
                        [
                            'assessment_id' => $assessment->id,
                            'student_id' => $studentId,
                            'assessment_criterion_id' => $criterionId,
                        ],
                        ['score' => $scoreValue]
                    );
                }
            }
        }

        return redirect()->route('assessments.index')->with('success', 'Nilai observasi berhasil disimpan!');
    }
}
