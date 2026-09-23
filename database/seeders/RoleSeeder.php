<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat daftar role
        $roles = [
            'superadmin',
            'operator',
            'kepsek',
            'guru',
            'siswa',
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
