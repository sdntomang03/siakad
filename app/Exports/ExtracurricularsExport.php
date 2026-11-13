<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ExtracurricularsExport extends DefaultValueBinder implements FromCollection, ShouldAutoSize, WithCustomValueBinder, WithHeadings, WithMapping
{
    public function __construct(
        private readonly int $schoolId,
        private readonly ?int $academicYearId
    ) {
    }

    public function collection(): Collection
    {
        return Student::query()
            ->where('school_id', $this->schoolId)
            ->with([
                'extracurriculars' => fn ($query) => $query
                    ->where('school_id', $this->schoolId)
                    ->orderBy('nama_ekskul'),
                'classrooms' => fn ($query) => $query
                    ->where('school_id', $this->schoolId)
                    ->when(
                        $this->academicYearId,
                        fn ($classrooms) => $classrooms->where('academic_year_id', $this->academicYearId),
                        fn ($classrooms) => $classrooms->whereNull('classrooms.id')
                    )
                    ->orderBy('nama_kelas'),
            ])
            ->orderBy('nama_lengkap')
            ->get();
    }

    public function headings(): array
    {
        return [
            'NISN',
            'NIPD',
            'Nama Siswa',
            'Kelas',
            'Ekstrakurikuler',
        ];
    }

    public function map($student): array
    {
        $classroom = $student->classrooms->first();

        return [
            $student->nisn,
            $student->nipd,
            $student->nama_lengkap,
            $classroom ? trim($classroom->tingkat.' '.$classroom->nama_kelas) : '',
            $student->extracurriculars->pluck('nama_ekskul')->implode(', '),
        ];
    }

    public function bindValue(Cell $cell, $value): bool
    {
        if (is_string($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
