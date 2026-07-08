<?php

namespace App\Exports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GradesTemplateExport implements FromArray, ShouldAutoSize, WithHeadings
{
    protected $schoolId;

    protected $tingkat;

    protected $subjects;

    // Tambahkan $tingkat sebagai parameter
    public function __construct($schoolId, $tingkat)
    {
        $this->schoolId = $schoolId;
        $this->tingkat = $tingkat;

        // Filter mapel berdasarkan sekolah DAN tingkat kelas
        $this->subjects = Subject::where('school_id', $this->schoolId)
            ->where('tingkat', $this->tingkat)
            ->whereNotNull('kode_mapel')
            // ->where('is_sidanira', true) // Aktifkan ini jika Anda menggunakan fitur Sidanira sebelumnya
            ->pluck('kode_mapel')
            ->toArray();
    }

    public function headings(): array
    {
        return array_merge(['nisn', 'nama_siswa'], $this->subjects);
    }

    public function array(): array
    {
        // Jika tidak ada mapel di tingkat tersebut, cegah error dengan memberi kolom default
        if (empty($this->subjects)) {
            return [['0011223344', 'Budi Santoso']];
        }

        $exampleData = ['0011223344', 'Budi Santoso'];

        foreach ($this->subjects as $subj) {
            $exampleData[] = 85;
        }

        return [
            $exampleData,
        ];
    }
}
