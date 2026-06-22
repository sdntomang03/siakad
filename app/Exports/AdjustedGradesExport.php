<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdjustedGradesExport implements FromArray, WithStyles
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function styles(Worksheet $sheet)
    {
        // Membuat baris pertama (Header) menjadi cetak tebal (Bold)
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
