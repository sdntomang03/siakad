<?php

namespace App\Exports;

use App\Models\BookLoan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UnreturnedBooksExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $schoolId;

    public function __construct($schoolId)
    {
        $this->schoolId = $schoolId;
    }

    /**
     * Ambil data peminjaman yang BELUM dikembalikan (returned_at NULL)
     */
    public function collection()
    {
        $user = auth()->user();

        $query = BookLoan::with(['student'])->whereNull('returned_at');

        if (! $user->hasRole('superadmin')) {
            $query->where('school_id', $this->schoolId);
        }

        // Jika Guru, batasi hanya mengekspor siswa di kelas yang diampunya
        if ($user->hasRole('guru')) {
            $employee = $user->employee;
            if ($employee) {
                $query->whereHas('student.classrooms', function ($q) use ($employee) {
                    $q->where('homeroom_teacher_id', $employee->id)
                        ->whereHas('academicYear', function ($q2) {
                            $q2->where('is_active', true);
                        });
                });
            }
        }

        return $query->orderBy('borrowed_at', 'desc')->get();
    }

    /**
     * Header baris pertama di Excel
     */
    public function headings(): array
    {
        return [
            'Nama Siswa',
            'Kelas',
            'Judul Buku',
            'Tanggal Pinjam',
            'Jatuh Tempo',
            'Status Tanggungan',
        ];
    }

    /**
     * Mapping data per baris kolom Excel
     */
    public function map($loan): array
    {
        $kelas = $loan->student ? $loan->student->kelasAktif() : null;
        $namaKelas = $kelas ? 'Kelas '.$kelas->tingkat.' '.$kelas->nama_kelas : '—';

        return [
            $loan->student->nama_lengkap ?? '—',
            $namaKelas,
            $loan->book_title,
            $loan->borrowed_at ? Carbon::parse($loan->borrowed_at)->format('d M Y, H:i') : '—',
            $loan->due_at ? Carbon::parse($loan->due_at)->format('d M Y') : '—',
            'Belum Dikembalikan',
        ];
    }
}
