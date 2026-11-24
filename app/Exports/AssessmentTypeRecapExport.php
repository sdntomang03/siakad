<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssessmentTypeRecapExport implements FromArray, ShouldAutoSize, WithStyles
{
    public function __construct(
        private Collection $students,
        private Collection $subjects,
        private array $usedTypesPerSubject,
        private array $averageScores,
        private array $studentAverages,
        private array $studentRanks
    ) {}

    public function array(): array
    {
        $columns = [];
        $headings = ['No', 'Nama Siswa'];

        foreach ($this->subjects as $subject) {
            $types = $this->usedTypesPerSubject[$subject->id] ?? [];
            foreach ($types as $type) {
                $columns[] = [$subject->id, $type->id];
                $headings[] = $subject->nama_mapel.' - '.$type->nama;
            }
        }

        $headings[] = 'Rata-rata Total';
        $headings[] = 'Peringkat';
        $rows = [$headings];

        foreach ($this->students as $index => $student) {
            $row = [$index + 1, $student->nama_lengkap];

            foreach ($columns as [$subjectId, $typeId]) {
                $row[] = $this->averageScores[$student->id][$subjectId][$typeId] ?? '';
            }

            $average = $this->studentAverages[$student->id] ?? 0;
            $row[] = $average > 0 ? $average : '';
            $row[] = $this->studentRanks[$student->id] ?? '-';
            $rows[] = $row;
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
