<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeesImport implements ToCollection, WithHeadingRow
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

            $roleName = strtolower($row['role'] ?? 'guru');
            if (! in_array($roleName, ['kepsek', 'guru', 'operator'])) {
                $roleName = 'guru'; // Default jika salah isi
            }

            // 1. Buat User
            $user = User::create([
                'name' => $row['name'],
                'email' => $row['email'],
                'school_id' => $this->schoolId,
                'password' => Hash::make('12345678'),
            ]);
            $user->assignRole($roleName);

            // 2. Buat profil Employee
            $user->employee()->create([
                'school_id' => $this->schoolId,
                'nama_lengkap' => $row['name'],
                'kategori_pegawai' => $roleName,
                'jenis_kelamin' => strtoupper($row['jenis_kelamin'] ?? 'L'),
                'nip' => $row['nip'] ?? null,
                'nuptk' => $row['nuptk'] ?? null,
            ]);
        }
    }
}
