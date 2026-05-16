<?php

use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\TeacherNoteController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route Publik (Tanpa Token)
Route::post('/login', [AuthController::class, 'login']);

// Route Terlindungi (Wajib pakai Token dari Mobile App)
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', [DashboardController::class, 'index']);

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

    // Mengambil daftar riwayat ujian
    Route::get('/assessments', [AssessmentController::class, 'index']);

    // Mengambil data untuk dropdown (Kelas & Mapel) saat membuat ujian baru
    Route::get('/assessments/create', [AssessmentController::class, 'create']);

    // Menyimpan wadah ujian baru
    Route::post('/assessments', [AssessmentController::class, 'store']);

    // Menghapus ujian
    Route::delete('/assessments/{assessment}', [AssessmentController::class, 'destroy']);

    // Menampilkan daftar siswa untuk input nilai maraton
    Route::get('/assessments/{assessment}/input', [AssessmentController::class, 'input']);

    // Menyimpan massal nilai siswa
    Route::post('/assessments/{assessment}/scores', [AssessmentController::class, 'updateScores']);

    // Mengambil matriks/rekapitulasi nilai
    Route::get('/assessments/recap', [AssessmentController::class, 'recap']);

    // Mengambil data kelas, daftar siswa, dan riwayat catatan terbaru
    Route::get('/teacher-notes', [TeacherNoteController::class, 'index']);

    // Menyimpan catatan baru (mendukung Multipart/Form-Data untuk Foto)
    Route::post('/teacher-notes', [TeacherNoteController::class, 'store']);

    // Menghapus catatan (Gunakan parameter ?mode=kejadian untuk hapus massal)
    Route::delete('/teacher-notes/{id}', [TeacherNoteController::class, 'destroy']);

    // Mengambil rekapitulasi catatan seluruh siswa di satu kelas
    Route::get('/teacher-notes/report', [TeacherNoteController::class, 'report']);
});
