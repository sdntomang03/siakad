<?php

namespace App\Exports;

use App\Models\Classroom;
use App\Models\Student;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $schoolId;

    protected $classroomId;

    /**
     * Parameter $classroomId bersifat opsional.
     * Jika diisi, Excel hanya menarik data siswa di kelas tersebut.
     * Jika dikosongkan, Excel menarik data seluruh siswa di sekolah.
     */
    public function __construct($schoolId, $classroomId = null)
    {
        $this->schoolId = $schoolId;
        $this->classroomId = $classroomId;
    }

    public function collection()
    {
        // 1. Jika ada ID Kelas yang dikirim, tarik siswa HANYA dari kelas tersebut
        if ($this->classroomId) {
            $classroom = Classroom::findOrFail($this->classroomId);

            return $classroom->students()
                ->with(['address', 'family', 'financial', 'health'])
                ->orderBy('nama_lengkap', 'asc')
                ->get();
        }

        // 2. Jika ID Kelas kosong, tarik SEMUA siswa di sekolah tersebut (Fungsi Default)
        return Student::with(['address', 'family', 'financial', 'health'])
            ->where('school_id', $this->schoolId)
            ->orderBy('nama_lengkap', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NISN',
            'NIPD',
            'Nama Lengkap',
            'L/P',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Agama',
            'Alamat Tinggal',
            'RT',
            'RW',
            'Kelurahan',
            'Kecamatan',
            'Kota',
            'Provinsi',
            'Kode Pos',
            'Nama Ayah',
            'Pekerjaan Ayah',
            'Nama Ibu',
            'Pekerjaan Ibu',
            'Penerima KJP',
            'Penerima PIP',
            'Tinggi (cm)',
            'Berat (kg)',
        ];
    }

    public function map($student): array
    {
        // Penomoran baris otomatis
        static $row = 0;
        $row++;

        return [
            $row,
            $student->nisn,
            $student->nipd,
            $student->nama_lengkap,
            $student->jenis_kelamin,
            $student->tempat_lahir,
            $student->tanggal_lahir ? Carbon::parse($student->tanggal_lahir)->format('d-m-Y') : '',
            $student->agama,

            // Relasi Alamat
            $student->address->alamat ?? '',
            $student->address->rt ?? '',
            $student->address->rw ?? '',
            $student->address->kelurahan ?? '',
            $student->address->kecamatan ?? '',
            $student->address->kota ?? '',
            $student->address->provinsi ?? '',
            $student->address->kode_pos ?? '',

            // Relasi Keluarga
            $student->family->nama_ayah ?? '',
            $student->family->pekerjaan_ayah ?? '',
            $student->family->nama_ibu ?? '',
            $student->family->pekerjaan_ibu ?? '',

            // Relasi Finansial
            (! empty($student->financial->penerima_kjp)) ? 'Ya' : 'Tidak',
            (! empty($student->financial->penerima_pip)) ? 'Ya' : 'Tidak',

            // Relasi Kesehatan
            $student->health->tinggi_badan ?? '',
            $student->health->berat_badan ?? '',
        ];
    }
}
