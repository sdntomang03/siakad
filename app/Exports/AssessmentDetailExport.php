<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssessmentDetailExport implements FromArray, ShouldAutoSize, WithStyles
{
    public function __construct(
        private Collection $students,
        private Collection $assessments,
        private array $matrixScores,
        private bool $allSubjects
    ) {}

    public function array(): array
    {
        $groupedAssessments = $this->assessments->groupBy('subject_id');
        $headings = ['No', 'Nama Siswa'];

        foreach ($groupedAssessments as $subjectAssessments) {
            foreach ($subjectAssessments as $assessment) {
                $type = $assessment->assessmentType->singkatan ?? 'Penilaian';
                $date = $assessment->tanggal ? $assessment->tanggal->format('d/m/Y') : '';
                $headings[] = ($this->allSubjects ? ($assessment->subject->nama_mapel.' - ') : '').$type.($date ? ' ('.$date.')' : '');
            }

            if ($this->allSubjects) {
                $headings[] = 'Rata-rata '.$subjectAssessments->first()->subject->nama_mapel;
            }
        }

        if (! $this->allSubjects) {
            $headings[] = 'Rata-rata';
        }

        $rows = [$headings];

        foreach ($this->students as $index => $student) {
            $row = [$index + 1, $student->nama_lengkap];
            $total = 0;

            foreach ($groupedAssessments as $subjectAssessments) {
                $subjectTotal = 0;

                foreach ($subjectAssessments as $assessment) {
                    $score = $this->matrixScores[$student->id][$assessment->id] ?? null;
                    $row[] = $score ?? '';
                    $subjectTotal += $score ?? 0;
                }

                if ($this->allSubjects) {
                    $row[] = $subjectAssessments->count() > 0
                        ? round($subjectTotal / $subjectAssessments->count(), 1)
                        : 0;
                } else {
                    $total += $subjectTotal;
                }
            }

            if (! $this->allSubjects) {
                $row[] = $this->assessments->count() > 0
                    ? round($total / $this->assessments->count(), 1)
                    : 0;
            }

            $rows[] = $row;
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
