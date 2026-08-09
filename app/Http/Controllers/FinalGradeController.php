<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentCriteriaScore;
use App\Models\AssessmentScore;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectFinalGrade;
use Illuminate\Http\Request;

class FinalGradeController extends Controller
{
    /**
     * Menampilkan antarmuka halaman Katrol Nilai
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? ($user->employee->school_id ?? 0);

        // Ambil Tahun Ajaran Aktif
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
            'classroom_id.required' => 'Data Kelas wajib dipilih.',
            'classroom_id.exists' => 'Kelas yang Anda pilih tidak terdaftar di sistem.',
            'subject_id.required' => 'Data Mata Pelajaran wajib dipilih.',
            'subject_id.exists' => 'Mata Pelajaran yang Anda pilih tidak terdaftar.',
            'academic_year_id.required' => 'Tahun Ajaran aktif belum terdeteksi.',
            'academic_year_id.exists' => 'Tahun Ajaran tidak valid.',
        ]);

        $schoolId = auth()->user()->school_id ?? (auth()->user()->employee->school_id ?? 0);

        // Ambil semua daftar penilaian untuk Mapel & Kelas ini
        $assessments = Assessment::where('classroom_id', $request->classroom_id)
            ->where('subject_id', $request->subject_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->with('criteria')
            ->get();

        if ($assessments->isEmpty()) {
            return back()->with('error', 'Belum ada satupun jadwal/data penilaian untuk mata pelajaran ini di kelas yang dipilih.');
        }

        // Ambil daftar siswa di kelas tersebut
        $students = Student::whereHas('classrooms', function ($query) use ($request) {
            $query->where('classrooms.id', $request->classroom_id);
        })->get();

        foreach ($students as $student) {
            $totalScore = 0;
            $count = 0;

            foreach ($assessments as $ass) {
                if ($ass->format === 'non-tes') {
                    // Nilai Observasi Praktik (Ubah ke Skala 100)
                    $critIds = $ass->criteria->pluck('id');
                    $scores = AssessmentCriteriaScore::where('assessment_id', $ass->id)
                        ->where('student_id', $student->id)
                        ->whereIn('assessment_criterion_id', $critIds)
                        ->get();

                    if ($scores->count() > 0) {
                        $avg = $scores->avg('score');
                        $scale = $ass->scale ?? 4;
                        $nilai100 = ($avg / $scale) * 100;
                        $totalScore += $nilai100;
                        $count++;
                    }
                } else {
                    // Nilai Ujian (Tes Tertulis)
                    $score = AssessmentScore::where('assessment_id', $ass->id)
                        ->where('student_id', $student->id)
                        ->first();

                    if ($score && $score->score !== null) {
                        $totalScore += $score->score;
                        $count++;
                    }
                }
            }

            // Hitung rata-rata keseluruhan (Skala 100)
            $nilaiAsli = $count > 0 ? round($totalScore / $count, 2) : 0;

            // Simpan ke SubjectFinalGrade
            $grade = SubjectFinalGrade::firstOrNew([
                'school_id' => $schoolId,
                'academic_year_id' => $request->academic_year_id,
                'classroom_id' => $request->classroom_id,
                'student_id' => $student->id,
                'subject_id' => $request->subject_id,
            ]);

            // Set nilai asli sesuai hasil tarikan database
            $grade->nilai_asli = $nilaiAsli;

            // Jika nilai akhir masih 0 (baru pertama kali ditarik), samakan dengan nilai asli.
            if (! $grade->exists || $grade->nilai_akhir == 0) {
                $grade->nilai_akhir = $nilaiAsli;
                $grade->predikat = $this->hitungPredikat($nilaiAsli);
            }

            $grade->save();
        }

        return back()->with('success', 'Nilai Asli berhasil ditarik dan dihitung dari rekapitulasi ujian. Silakan lakukan proses Katrol jika diperlukan.');
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
            'classroom_id.required' => 'Data Kelas terputus. Silakan pilih kelas kembali.',
            'classroom_id.exists' => 'Kelas yang dipilih tidak valid.',
            'subject_id.required' => 'Data Mata Pelajaran terputus. Silakan pilih mapel kembali.',
            'subject_id.exists' => 'Mata Pelajaran tidak valid.',
            'academic_year_id.required' => 'Tahun Ajaran aktif belum terdeteksi.',
            'academic_year_id.exists' => 'Tahun Ajaran tidak valid.',

            'target_min.required' => 'Target nilai terendah (KKM) wajib diisi.',
            'target_min.numeric' => 'Target KKM harus berupa angka.',
            'target_min.min' => 'Target KKM tidak boleh kurang dari 0.',
            'target_min.max' => 'Target KKM tidak boleh lebih dari 100.',

            'target_max.required' => 'Target nilai maksimal wajib diisi.',
            'target_max.numeric' => 'Target nilai maksimal harus berupa angka.',
            'target_max.min' => 'Target nilai maksimal tidak boleh kurang dari 0.',
            'target_max.max' => 'Target nilai maksimal tidak boleh melebihi 100.',
            'target_max.gt' => 'Target nilai maksimal harus lebih besar dari target KKM.',
        ]);

        $grades = SubjectFinalGrade::where('classroom_id', $request->classroom_id)
            ->where('subject_id', $request->subject_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->get();

        if ($grades->isEmpty()) {
            return back()->with('error', 'Silakan tarik Nilai Asli terlebih dahulu sebelum mengkatrol.');
        }

        $nilaiAsliList = $grades->pluck('nilai_asli');
        $nilaiMinAsli = $nilaiAsliList->min();
        $nilaiMaxAsli = $nilaiAsliList->max();

        $targetMin = $request->target_min;
        $targetMax = $request->target_max;

        foreach ($grades as $grade) {
            if ($nilaiMaxAsli == $nilaiMinAsli) {
                $nilaiBaru = $targetMax;
            } else {
                $nilaiBaru = (($grade->nilai_asli - $nilaiMinAsli) / ($nilaiMaxAsli - $nilaiMinAsli)) * ($targetMax - $targetMin) + $targetMin;
            }

            $grade->update([
                'nilai_akhir' => round($nilaiBaru, 2),
                'predikat' => $this->hitungPredikat($nilaiBaru),
            ]);
        }

        return back()->with('success', 'Nilai berhasil dikatrol secara proporsional. Peringkat siswa tetap terjaga!');
    }

    private function hitungPredikat($nilai)
    {
        if ($nilai >= 90) {
            return 'A';
        }
        if ($nilai >= 80) {
            return 'B';
        }
        if ($nilai >= 70) {
            return 'C';
        }

        return 'D';
    }
}
