<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UnreturnedBooksExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $schoolId;

    protected $maxBooks = 0;

    public function __construct($schoolId)
    {
        $this->schoolId = $schoolId;
    }

    public function collection()
    {
        $user = auth()->user();

        $query = Student::with(['classrooms', 'bookLoans' => function ($q) {
            $q->whereNull('returned_at')->orderBy('borrowed_at', 'desc');
        }])->whereHas('bookLoans', function ($q) {
            $q->whereNull('returned_at');
        });

        if (! $user->hasRole('superadmin')) {
            $query->where('school_id', $this->schoolId);
        }

        if ($user->hasRole('guru')) {
            $employee = $user->employee;
            if ($employee) {
                $query->whereHas('classrooms', function ($q) use ($employee) {
                    $q->where('homeroom_teacher_id', $employee->id)
                        ->whereHas('academicYear', function ($q2) {
                            $q2->where('is_active', true);
                        });
                });
            }
        }

        $students = $query->get();

        // Cari jumlah buku terbanyak
        $this->maxBooks = $students->max(function ($student) {
            return $student->bookLoans->count();
        });

        return $students;
    }

    /**
     * Pembuatan Header Kolom
     */
    public function headings(): array
    {
        $headings = [
            'Nama Siswa',
            'Kelas',
            'Total Buku Dipinjam',
        ];

        if ($this->maxBooks > 0) {
            $headings[] = 'Judul Buku yang Dipinjam';

            // Tambahkan string kosong untuk sisa kolom buku agar border tetap sejajar
            for ($i = 1; $i < $this->maxBooks; $i++) {
                $headings[] = '';
            }
        }

        return $headings;
    }

    public function map($student): array
    {
        $kelas = $student->kelasAktif();
        $namaKelas = $kelas ? 'Kelas '.$kelas->tingkat.' '.$kelas->nama_kelas : '—';

        $row = [
            $student->nama_lengkap ?? '—',
            $namaKelas,
            $student->bookLoans->count().' Buku',
        ];

        $loanCount = 0;
        foreach ($student->bookLoans as $loan) {
            $row[] = $loan->book_title;
            $loanCount++;
        }

        for ($i = $loanCount; $i < $this->maxBooks; $i++) {
            $row[] = '';
        }

        return $row;
    }

    /**
     * Styling dan Merge Cell
     */
    /**
     * Styling dan Merge Cell
     */
    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        // PAKSA TULIS JUDUL DI KOLOM D1 AGAR TIDAK HILANG SAAT DI-MERGE
        if ($this->maxBooks > 0) {
            $sheet->setCellValue('D1', 'Judul Buku yang Dipinjam');

            // Lakukan merge jika buku maksimal lebih dari 1
            if ($this->maxBooks > 1) {
                $sheet->mergeCells('D1:'.$lastColumn.'1');
            }
        }

        // Style untuk Header (Baris 1)
        $sheet->getStyle('A1:'.$lastColumn.'1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4F46E5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Style untuk isi data (Baris 2 ke bawah)
        if ($lastRow > 1) {
            $sheet->getStyle('A2:'.$lastColumn.$lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFB0BEC5'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Rata Tengah khusus kolom Kelas (B) dan Total Tanggungan (C)
            $sheet->getStyle('B2:C'.$lastRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ]);
        }

        // Tinggi baris header
        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }
}
