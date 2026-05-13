<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function edit($id)
    {
        $currentUser = auth()->user();

        // 1. CEK PERMISSION: Boleh edit orang lain atau tidak?
        $canEditOthers = $currentUser->hasPermissionTo('edit-users');

        // Jika tidak punya izin DAN mencoba buka ID orang lain -> BLOKIR
        if (! $canEditOthers && $currentUser->id != $id) {
            abort(403, 'Akses Ditolak: Anda hanya dapat melihat dan mengedit profil Anda sendiri.');
        }

        // Mulai Query
        $query = User::with('employee');

        // Jika bukan Superadmin, batasi pencarian hanya pada sekolah user yang sedang login
        if (! $currentUser->hasRole('superadmin')) {
            $query->where('school_id', $currentUser->school_id);
        }

        $user = $query->findOrFail($id);

        // Inisialisasi data employee jika belum ada di tabel 'employees'
        if (! $user->employee) {
            $user->employee()->create([
                'school_id' => $user->school_id,
                'nama_lengkap' => $user->name,
                'kategori_pegawai' => $user->roles->first()->name ?? 'guru',
                'jenis_kelamin' => 'L', // Default
            ]);
            $user->load('employee');
        }

        return view('employees.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $currentUser = auth()->user();

        // 1. CEK PERMISSION: Boleh edit orang lain atau tidak?
        $canEditOthers = $currentUser->hasPermissionTo('edit-users');

        // LOGIKA KEAMANAN SAMA SEPERTI EDIT
        if (! $canEditOthers && $currentUser->id != $id) {
            abort(403, 'Akses Ditolak: Anda tidak dapat mengubah data pegawai lain.');
        }

        $query = User::query();
        if (! $currentUser->hasRole('superadmin')) {
            $query->where('school_id', $currentUser->school_id);
        }

        $user = $query->findOrFail($id);

        // Validasi dasar
        $request->validate([
            'name' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'kategori_pegawai' => 'required|string',
        ]);

        DB::transaction(function () use ($request, $user) {
            // 1. Update Akun User (Nama Login)
            $user->update(['name' => $request->name]);

            // 2. Update Data Detail Pegawai
            $user->employee()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'school_id' => $user->school_id, // Kunci ke sekolah user
                    'nama_lengkap' => $request->name,
                    'kategori_pegawai' => $request->kategori_pegawai,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'nip' => $request->nip,
                    'nuptk' => $request->nuptk,
                    'status_kepegawaian' => $request->status_kepegawaian,
                    'tugas_tambahan' => $request->tugas_tambahan,

                    // Alamat
                    'alamat' => $request->alamat,
                    'rt' => $request->rt,
                    'rw' => $request->rw,
                    'dusun' => $request->dusun,
                    'kelurahan' => $request->kelurahan,
                    'kecamatan' => $request->kecamatan,
                ]
            );
        });

        return back()->with('success', 'Data pegawai berhasil diperbarui.');
    }
}
