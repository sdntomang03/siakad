<?php

namespace App\Imports;

use App\Models\Kelulusan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // 1. Tambahkan library pembaca tanggal Excel
use PhpOffice\PhpSpreadsheet\Shared\Date; // 2. Tambahkan library format waktu

class KelulusanImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 3. Logika Penerjemah Tanggal
        $tanggalLahir = $row['tanggal_lahir'];

        // Jika formatnya angka serial Excel (contoh: 41783)
        if (is_numeric($tanggalLahir)) {
            $tanggalLahir = Date::excelToDateTimeObject($tanggalLahir)->format('Y-m-d');
        } else {
            // Jika guru sudah terlanjur mengubahnya jadi Teks (contoh: "2014-05-24")
            $tanggalLahir = Carbon::parse($tanggalLahir)->format('Y-m-d');
        }

        return new Kelulusan([
            'nama' => $row['nama'],
            'nisn' => $row['nisn'],
            'nipd' => $row['nipd'],
            'tanggal_lahir' => $tanggalLahir, // Gunakan variabel yang sudah diterjemahkan
            'kelas' => $row['kelas'],
            'keterangan' => strtoupper($row['keterangan']),
        ]);
    }
}
