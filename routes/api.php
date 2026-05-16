<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route Publik (Tanpa Token)
Route::post('/login', [AuthController::class, 'login']);

// Route Terlindungi (Wajib pakai Token dari Mobile App)
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // Contoh endpoint untuk mengambil Profil Siswa di HP
    Route::get('/profil', function (Request $request) {
        $user = $request->user();

        // Memanfaatkan Helper dari model Student yang kita buat sebelumnya
        $kelasAktif = $user->student ? $user->student->kelasAktif() : null;

        return response()->json([
            'status' => 'success',
            'data' => [
                'nama' => $user->name,
                'sekolah' => $user->school->nama_sekolah,
                'role' => $user->getRoleNames()->first(),
                'kelas_saat_ini' => $kelasAktif ? $kelasAktif->nama_kelas : 'Belum ada kelas',
            ],
        ]);
    });
    Route::get('/users', [UserController::class, 'index']);
    // 1. Ambil Data Rekap Absensi (Index)
    Route::get('/attendances', [AttendanceController::class, 'index']);

    // 2. Ambil Form/Daftar Siswa untuk Absen Harian (Show)
    Route::get('/attendances/classroom/{classroom}', [AttendanceController::class, 'show']);

    // 3. Simpan Data Absensi Massal (Store)
    Route::post('/attendances/classroom/{classroom}', [AttendanceController::class, 'store']);

    // 4. Laporan Absensi Per Siswa Khusus (Report)
    Route::get('/attendances/student/{studentId}', [AttendanceController::class, 'studentReport']);
});
