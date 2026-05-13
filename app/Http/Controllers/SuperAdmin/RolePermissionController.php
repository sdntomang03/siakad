<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function index()
    {
        // Ambil semua role KECUALI superadmin (agar tidak dimodifikasi dan bikin error)
        $roles = Role::where('name', '!=', 'superadmin')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('superadmin.roles.index', compact('roles', 'permissions'));
    }

    public function edit(Role $role)
    {
        // Proteksi keamanan ganda
        if ($role->name === 'superadmin') {
            return redirect()->route('superadmin.roles.index')->with('error', 'Role Superadmin tidak dapat dimodifikasi.');
        }

        // Ambil semua permission yang ada di database
        $permissions = Permission::orderBy('name')->get();

        // Ambil ID permission yang saat ini dimiliki oleh role tersebut
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('superadmin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'superadmin') {
            abort(403, 'Aksi dilarang.');
        }

        // Validasi input berupa array
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        // MAGIC: Sinkronisasi permission!
        // Jika array kosong (tidak ada kotak yang dicentang), ia akan menghapus semua hak akses
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('superadmin.roles.index')->with('success', 'Hak akses untuk jabatan '.strtoupper($role->name).' berhasil diperbarui!');
    }

    // Fungsi baru untuk menyimpan permission ke database
    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ], [
            'name.unique' => 'Nama permission ini sudah ada di database!',
        ]);

        // Otomatis ubah spasi menjadi strip (slug) dan huruf kecil
        // Contoh input: "Edit Kelas" -> menjadi: "edit-kelas"
        $permissionName = strtolower(str_replace(' ', '-', trim($request->name)));

        Permission::create(['name' => $permissionName]);

        return back()->with('success', 'Permission "'.$permissionName.'" berhasil ditambahkan!');
    }

    // Fungsi baru untuk menghapus permission
    public function destroyPermission(Permission $permission)
    {
        $namaPermission = $permission->name;
        $permission->delete();

        return back()->with('success', 'Permission "'.$namaPermission.'" berhasil dihapus beserta seluruh kaitannya pada Role.');
    }
}
