<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UnreturnedBooksExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $schoolId;

    protected $maxBooks = 0; // Variabel untuk menyimpan jumlah maksimal buku terbanyak

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

        // Query dari model Student, bukan BookLoan
        $query = Student::with(['classrooms', 'bookLoans' => function ($q) {
            $q->whereNull('returned_at')->orderBy('borrowed_at', 'desc');
        }])->whereHas('bookLoans', function ($q) {
            $q->whereNull('returned_at'); // Hanya ambil siswa yang punya tanggungan
        });

        if (! $user->hasRole('superadmin')) {
            $query->where('school_id', $this->schoolId);
        }

        // Jika Guru, batasi hanya siswa di kelasnya
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

        // Cari jumlah buku terbanyak yang dipinjam oleh satu siswa
        // Ini berguna untuk menentukan seberapa panjang kolom ke kanannya
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

        // Cetak kolom Buku 1, Buku 2, dst ke samping
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

        // 3 Kolom utama di awal
        $row = [
            $student->nama_lengkap ?? '—',
            $namaKelas,
            $student->bookLoans->count().' Buku',
        ];

        // Iterasi buku yang dipinjam, letakkan mendatar ke samping
        $loanCount = 0;
        foreach ($student->bookLoans as $loan) {
            $row[] = $loan->book_title;
            $loanCount++;
        }

        // Sisipkan string kosong di sisa kolom jika siswa ini meminjam
        // lebih sedikit buku dari nilai $maxBooks agar struktur kolom Excel tidak bergeser
        for ($i = $loanCount; $i < $this->maxBooks; $i++) {
            $row[] = '';
        }

        return $row;
    }
}
