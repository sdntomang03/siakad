<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // 1. CEK OTORISASI: Pastikan yang mengakses adalah 'superadmin'
        if (! $request->user()->hasRole('superadmin')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses Ditolak! Endpoint ini khusus untuk Super Admin.',
            ], 403); // 403 = Forbidden
        }

        // 2. AMBIL DATA: Mengambil semua user beserta relasi role-nya
        // Menggunakan paginate(20) lebih disarankan daripada get() agar aplikasi Flutter tidak lag/berat jika data mencapai ribuan.
        $users = User::with('roles')->latest()->paginate(20);

        // 3. FORMAT RESPON (Opsional tapi disarankan agar data lebih rapi)
        $formattedUsers = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username ?? null,
                'school_id' => $user->school_id,
                'role' => $user->getRoleNames()->first() ?? 'Tidak ada role',
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            ];
        });

        // 4. KEMBALIKAN RESPON JSON
        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil daftar seluruh pengguna',
            'data' => $formattedUsers,
            // Jika Anda menggunakan paginate, sertakan meta data paginasi untuk Flutter
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'total_data' => $users->total(),
            ],
        ], 200);
    }
}
