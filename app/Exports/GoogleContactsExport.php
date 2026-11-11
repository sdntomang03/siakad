<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GoogleContactsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $students)
    {
    }

    public function collection(): Collection
    {
        return $this->students;
    }

    public function headings(): array
    {
        return [
            'Name',
            'Phone 1 - Type',
            'Phone 1 - Value',
        ];
    }

    public function map($student): array
    {
        return [
            $student->nama_lengkap,
            'Mobile',
            $student->hp,
        ];
    }
}
