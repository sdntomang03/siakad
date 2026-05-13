<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan Cache Spatie di awal
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Definisi daftar permission
        $permissions = [
            'view-academic-years',
            'create-academic-years',
            'edit-academic-years',
            'delete-academic-years',
            // Tambahkan ini:
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'view-classes',
            'edit-classes',
        ];

        // 3. Buat Permission (Gunakan updateOrCreate agar lebih aman)
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        // 4. Berikan ke SuperAdmin
        $superAdmin = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        // Gunakan syncPermissions agar Spatie menarik ulang data dari DB
        $superAdmin->syncPermissions($permissions);

        // 5. Berikan ke Operator
        $operator = Role::firstOrCreate(['name' => 'operator', 'guard_name' => 'web']);
        $operator->syncPermissions([
            'view-academic-years',
            'create-academic-years',
            'edit-academic-years',
            'view-users',
            'create-users',
            'edit-users',
            'view-classes',
            'edit-classes',
        ]);
    }
}
