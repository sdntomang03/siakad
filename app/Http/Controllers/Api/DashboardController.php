<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->school_id;
        $today = date('Y-m-d'); // Tanggal hari ini

        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first();
        $activeYearId = $activeYear ? $activeYear->id : 0;

        $totalSiswa = 0;
        $absenHariIni = [];
        $penilaianTerbaru = [];

        // JIKA SUPERADMIN (Lihat seluruh sekolah)
        if ($user->hasRole('superadmin')) {
            $totalSiswa = Student::where('school_id', $schoolId)->count();

            $absenHariIni = Attendance::with(['student:id,nama_lengkap', 'classroom:id,tingkat,nama_kelas'])
                ->where('academic_year_id', $activeYearId)
                ->where('tanggal', $today)
                ->whereIn('status', ['sakit', 'izin', 'alfa'])
                ->get();

            $penilaianTerbaru = Assessment::with(['classroom:id,tingkat,nama_kelas', 'subject:id,nama_mapel'])
                ->where('school_id', $schoolId)
                ->orderBy('created_at', 'desc')
                ->take(3) // Ambil 3 terbaru
                ->get();
        }
        // JIKA GURU (Lihat kelasnya sendiri)
        else {
            $employeeId = $user->employee->id ?? 0;
            $waliKelasIds = Classroom::where('homeroom_teacher_id', $employeeId)->where('academic_year_id', $activeYearId)->pluck('id');

            // Hitung siswa di kelas yang dia wali-kan
            $totalSiswa = Student::whereHas('classrooms', function ($q) use ($waliKelasIds) {
                $q->whereIn('classrooms.id', $waliKelasIds);
            })->count();

            $absenHariIni = Attendance::with(['student:id,nama_lengkap', 'classroom:id,tingkat,nama_kelas'])
                ->whereIn('classroom_id', $waliKelasIds)
                ->where('tanggal', $today)
                ->whereIn('status', ['sakit', 'izin', 'alfa'])
                ->get();

            $penilaianTerbaru = Assessment::with(['classroom:id,tingkat,nama_kelas', 'subject:id,nama_mapel'])
                ->where('employee_id', $employeeId)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        }

        // Format data agar bersih diterima Flutter
        $formattedAbsen = $absenHariIni->map(function ($a) {
            return [
                'nama' => $a->student->nama_lengkap ?? 'Anonim',
                'kelas' => ($a->classroom->tingkat ?? '').' '.($a->classroom->nama_kelas ?? ''),
                'status' => $a->status,
                'keterangan' => $a->keterangan,
            ];
        });

        $formattedPenilaian = $penilaianTerbaru->map(function ($p) {
            return [
                'keterangan' => $p->keterangan,
                'mapel' => $p->subject->nama_mapel ?? '',
                'kelas' => ($p->classroom->tingkat ?? '').' '.($p->classroom->nama_kelas ?? ''),
                'tanggal' => $p->tanggal,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_siswa' => $totalSiswa,
                'absen_hari_ini' => $formattedAbsen,
                'penilaian_terbaru' => $formattedPenilaian,
            ],
        ], 200);
    }
}
