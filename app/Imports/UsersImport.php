<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToCollection, WithHeadingRow
{
    protected $schoolId;

    // Tangkap school_id dari Controller agar sesuai dengan sekolah operator
    public function __construct($schoolId)
    {
        $this->schoolId = $schoolId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Lewati baris jika nama atau email kosong
            if (empty($row['name']) || empty($row['email'])) {
                continue;
            }

            // Cegah error duplikasi: lewati jika email sudah terdaftar
            if (User::where('email', $row['email'])->exists()) {
                continue;
            }

            // 1. Buat Akun Login
            $user = User::create([
                'name' => $row['name'],
                'email' => $row['email'],
                'school_id' => $this->schoolId,
                'password' => Hash::make('12345678'), // Default password
            ]);

            // Validasi Role (pastikan hanya kepsek, guru, atau siswa). Default: siswa.
            $roleName = strtolower($row['role'] ?? 'siswa');
            if (! in_array($roleName, ['kepsek', 'guru', 'siswa'])) {
                $roleName = 'siswa';
            }
            $user->assignRole($roleName);

            // 2. Buat profil Employee jika role adalah guru atau kepsek
            if (in_array($roleName, ['guru', 'kepsek'])) {
                $user->employee()->create([
                    'school_id' => $this->schoolId,
                    'nama_lengkap' => $row['name'],
                    'kategori_pegawai' => $roleName,
                    'jenis_kelamin' => strtoupper($row['jenis_kelamin'] ?? 'L'), // L/P
                    'nip' => $row['nip'] ?? null,
                ]);
            }
        }
    }
}
