<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    protected $schoolId;

    public function __construct($schoolId)
    {
        $this->schoolId = $schoolId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['name']) || empty($row['email'])) {
                continue;
            }
            if (User::where('email', $row['email'])->exists()) {
                continue;
            }

            // 1. Buat User
            $user = User::create([
                'name' => $row['name'],
                'email' => $row['email'],
                'school_id' => $this->schoolId,
                'password' => Hash::make('12345678'),
            ]);

            // Fix role selalu siswa
            $user->assignRole('siswa');

            // 2. Buat profil Student (Sesuai kebutuhan di StudentController)
            $user->student()->create([
                'school_id' => $this->schoolId,
                'nama_lengkap' => $row['name'],
                'jenis_kelamin' => strtoupper($row['jenis_kelamin'] ?? 'L'),
                'nisn' => $row['nisn'] ?? null,
                'nipd' => $row['nipd'] ?? null,
                'status' => 'aktif',
            ]);
        }
    }
}
