<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\School;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $roleName = str_replace('_', ' ', $user->roles->first()->name ?? 'Pengguna');

        // 1. Ambil Tahun Ajaran Aktif
        // Sesuaikan query ini jika nama kolom penanda aktif di tabel Anda berbeda
        // (misal menggunakan 'is_active' => 1, atau 'status' => 'aktif')
        $activeYear = AcademicYear::where('status', 'aktif')->first() ?? AcademicYear::latest()->first();
        $activeYearId = $activeYear->id ?? 0;

        $data = [
            'user' => $user,
            'roleName' => $roleName,
            'activeYear' => $activeYear,
        ];

        // 2. Filter data berdasarkan Role pengguna
        if ($user->hasRole('superadmin')) {
            $data['totalSchools'] = School::count();
            $data['totalUsers'] = User::count();
        } elseif ($user->hasRole('guru')) {
            $employeeId = $user->employee->id ?? 0;

            // Cari kelas WALI KELAS yang sesuai dengan sekolah dan tahun ajaran aktif
            $data['myClass'] = Classroom::where('school_id', $user->school_id)
                ->where('academic_year_id', $activeYearId)
                ->where('homeroom_teacher_id', $employeeId)
                ->first();

            // OPSIONAL: Jika Anda ingin mengambil data guru mengajar mapel di kelas mana saja
            // $data['taughtClasses'] = \App\Models\ClassroomSubject::with('classroom')
            //    ->where('employee_id', $employeeId)
            //    ->whereHas('classroom', function($q) use ($user, $activeYearId) {
            //        $q->where('school_id', $user->school_id)->where('academic_year_id', $activeYearId);
            //    })->get();
        } elseif ($user->hasRole('siswa')) {
            $studentId = $user->student->id ?? 0;

            // Cari KELAS SISWA SAAT INI berdasarkan sekolah dan tahun ajaran aktif
            $data['myClassroom'] = Classroom::where('school_id', $user->school_id)
                ->where('academic_year_id', $activeYearId)
                ->whereHas('students', function ($query) use ($studentId) {
                    $query->where('students.id', $studentId); // Menggunakan relasi Many-to-Many
                })->first();
        }

        return view('dashboard', $data);
    }
}
