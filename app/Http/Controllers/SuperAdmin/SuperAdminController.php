<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\School;
use App\Models\Student;
use App\Models\User;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        // Statistik Global
        $stats = [
            'total_sekolah' => School::count(),
            'total_pengguna' => User::count(),
            'total_guru_nasional' => Employee::where('kategori_pegawai', 'guru')->count(),
            'total_siswa_nasional' => Student::count(),
        ];

        // Mengambil daftar sekolah terbaru
        $schools = School::latest()->take(5)->get();

        // Mengirim data $stats dan $schools ke tampilan Blade
        return view('superadmin.dashboard', compact('stats', 'schools'));
    }

    public function manageSchools()
    {
        // Fitur khusus Super Admin untuk melihat semua sekolah
        $allSchools = School::withCount(['employees', 'students'])->get();

        return response()->json([
            'status' => 'success',
            'data_sekolah' => $allSchools,
        ]);
    }
}
