<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentCriteriaScore;
use App\Models\AssessmentScore;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectFinalGrade;
use Exception;

class FinalGradeService
{
    /**
     * Menghitung dan menyimpan Nilai Asli dari rekapitulasi ujian/observasi
     */
    public function calculateAndSaveRawScores($schoolId, $academicYearId, $classroomId, $subjectId)
    {
        // Ambil semua daftar penilaian untuk Mapel & Kelas ini
        $assessments = Assessment::where('classroom_id', $classroomId)
            ->where('subject_id', $subjectId)
            ->where('academic_year_id', $academicYearId)
            ->with('criteria')
            ->get();

        if ($assessments->isEmpty()) {
            throw new Exception('Belum ada satupun jadwal/data penilaian untuk mata pelajaran ini di kelas yang dipilih.');
        }

        // Ambil daftar siswa di kelas tersebut
        $students = Student::whereHas('classrooms', function ($query) use ($classroomId) {
            $query->where('classrooms.id', $classroomId);
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
                'academic_year_id' => $academicYearId,
                'classroom_id' => $classroomId,
                'student_id' => $student->id,
                'subject_id' => $subjectId,
            ]);

            $grade->nilai_asli = $nilaiAsli;

            // Jika nilai akhir masih 0 (baru pertama kali ditarik), samakan dengan nilai asli.
            if (! $grade->exists || $grade->nilai_akhir == 0) {
                $grade->nilai_akhir = $nilaiAsli;
                $grade->predikat = $this->calculatePredicate($nilaiAsli);
            }

            $grade->save();
        }
    }

    /**
     * Memproses Transformasi Linier (Katrol Nilai)
     */
    public function applyGradeCurve($academicYearId, $classroomId, $subjectId, $targetMin, $targetMax)
    {
        $grades = SubjectFinalGrade::where('classroom_id', $classroomId)
            ->where('subject_id', $subjectId)
            ->where('academic_year_id', $academicYearId)
            ->get();

        if ($grades->isEmpty()) {
            throw new Exception('Silakan tarik Nilai Asli terlebih dahulu sebelum mengkatrol.');
        }

        $nilaiAsliList = $grades->pluck('nilai_asli');
        $nilaiMinAsli = $nilaiAsliList->min();
        $nilaiMaxAsli = $nilaiAsliList->max();

        foreach ($grades as $grade) {
            // Transformasi Linier: menghindari division by zero
            if ($nilaiMaxAsli == $nilaiMinAsli) {
                $nilaiBaru = $targetMax;
            } else {
                $nilaiBaru = (($grade->nilai_asli - $nilaiMinAsli) / ($nilaiMaxAsli - $nilaiMinAsli)) * ($targetMax - $targetMin) + $targetMin;
            }

            $grade->update([
                'nilai_akhir' => round($nilaiBaru, 2),
                'predikat' => $this->calculatePredicate($nilaiBaru),
            ]);
        }
    }

    /**
     * Helper penghitung predikat
     */
    private function calculatePredicate($nilai)
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

    /**
     * Mengambil data untuk halaman antarmuka Katrol Nilai
     */
    public function getKatrolData($request, $schoolId)
    {
        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first();

        if (! $activeYear) {
            throw new Exception('Tahun ajaran aktif belum diatur.');
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
            // Menggunakan eager loading (with) untuk mencegah N+1 Query Problem
            $grades = SubjectFinalGrade::with(['student' => function ($q) {
                $q->orderBy('nama_lengkap', 'asc');
            }])
                ->where('classroom_id', $request->classroom_id)
                ->where('subject_id', $request->subject_id)
                ->where('academic_year_id', $activeYear->id)
                ->get()
                ->sortBy('student.nama_lengkap');
        }

        return compact('activeYear', 'classrooms', 'subjects', 'grades', 'selectedClassroom');
    }
}
