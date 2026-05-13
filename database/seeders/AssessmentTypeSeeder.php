<?php

namespace Database\Seeders;

use App\Models\AssessmentType;
use App\Models\School;
use Illuminate\Database\Seeder;

class AssessmentTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil sekolah pertama sebagai acuan
        $school = School::first();

        // Cegah error jika tabel sekolah masih kosong
        if (! $school) {
            $this->command->error('Data Sekolah belum ada! Silakan jalankan UserSeeder terlebih dahulu.');

            return;
        }

        // Daftar jenis penilaian standar beserta bobotnya
        $types = [
            // Kurikulum Standar / K13
            ['nama' => 'Penilaian Harian', 'singkatan' => 'PH', 'bobot' => 1],
            ['nama' => 'Tugas', 'singkatan' => 'Tgs', 'bobot' => 1],
            ['nama' => 'Sumatif Tengah Semester', 'singkatan' => 'STS', 'bobot' => 1],
            ['nama' => 'Sumatif Akhir Semester', 'singkatan' => 'SAS', 'bobot' => 1],
        ];

        // Looping dan masukkan ke database
        // Looping dan masukkan ke database
        foreach ($types as $type) {
            // Gunakan firstOrCreate agar jika seeder dijalankan 2x, datanya tidak ganda (duplikat)
            AssessmentType::firstOrCreate(
                [
                    // Array pertama: parameter pencarian (jangan sampai duplikat)
                    'school_id' => $school->id,
                    'nama' => $type['nama'],
                ],
                [
                    // Array kedua: data yang akan diisi JIKA data baru dibuat
                    'singkatan' => $type['singkatan'], // <--- Tambahkan baris ini!
                    'bobot' => $type['bobot'],
                ]
            );
        }

        $this->command->info('Data Jenis Penilaian (Assessment Types) berhasil ditambahkan!');

    }
}
