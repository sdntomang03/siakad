<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class AnnouncementTemplateExport extends DefaultValueBinder implements FromCollection, ShouldAutoSize, WithCustomValueBinder, WithHeadings, WithMapping
{
    public function __construct(private readonly Collection $students) {}

    public function collection(): Collection
    {
        return $this->students;
    }

    public function headings(): array
    {
        return ['NISN', 'Nama Siswa', 'Pesan'];
    }

    public function map($student): array
    {
        return [
            $student->nisn,
            $student->nama_lengkap,
            '',
        ];
    }

    public function bindValue(Cell $cell, $value): bool
    {
        if ($cell->getColumn() === 'A' && is_string($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
