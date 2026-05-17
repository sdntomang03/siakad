<?php

// app/Http/Middleware/EnsureSchoolAccess.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSchoolAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. Bypass untuk Superadmin (Pengecekan disesuaikan dengan standar Anda)
        if ($user->hasRole('superadmin')) {
            return $next($request);
        }

        // 2. Cegah user yang tidak terikat dengan sekolah manapun
        // KEMBALIKAN JSON agar Flutter bisa membaca pesan errornya dengan rapi
        if (! $user->school_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak: Akun Anda belum didaftarkan pada sekolah manapun.',
            ], 403);
        }

        // 3. Cegah akses jika sekolah sedang diblokir/dinonaktifkan
        if (! $user->school?->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak: Sekolah Anda saat ini sedang dinonaktifkan oleh sistem.',
            ], 403);
        }

        return $next($request);
    }
}
