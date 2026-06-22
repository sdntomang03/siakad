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

    /**
     * Ambil data SISWA yang memiliki tanggungan peminjaman (belum dikembalikan)
     */
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

        $this->maxBooks = $students->max(function ($student) {
            return $student->bookLoans->count();
        });

        return $students;
    }

    /**
     * Pembuatan Header Kolom secara Dinamis
     */
    public function headings(): array
    {
        $headings = [
            'Nama Siswa',
            'Kelas',
            'Total Tanggungan',
        ];

        for ($i = 1; $i <= $this->maxBooks; $i++) {
            $headings[] = 'Judul Buku '.$i;
        }

        return $headings;
    }

    /**
     * Memasukkan data ke masing-masing baris & kolom
     */
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
     * Styling tampilan Excel agar lebih rapi dan profesional
     */
    public function styles(Worksheet $sheet)
    {
        // Mendapatkan baris dan kolom terakhir yang berisi data
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        // 1. Style untuk Header (Baris 1)
        $sheet->getStyle('A1:'.$lastColumn.'1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // Teks putih
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4F46E5'], // Background warna indigo-600
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

        // 2. Jika ada datanya, berikan border dan perataan pada isi tabel (Baris 2 ke bawah)
        if ($lastRow > 1) {
            // Style garis pembatas (border) untuk seluruh cell data
            $sheet->getStyle('A2:'.$lastColumn.$lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFB0BEC5'], // Warna border abu-abu
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // 3. Rata Tengah khusus untuk kolom "Kelas" (B) dan "Total Tanggungan" (C)
            $sheet->getStyle('B2:C'.$lastRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ]);
        }

        // 4. Ubah tinggi baris header agar lebih lega
        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }
}
