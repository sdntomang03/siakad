<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\ClassroomSubject;
use App\Models\JadwalPiket;
use App\Models\JurnalPiket;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PiketController extends Controller
{
    /**
     * 1. HALAMAN PENGATURAN JADWAL PIKET
     */
    public function jadwal(Request $request)
    {
        // Ganti dengan tahun ajaran aktif dari session/sistem Anda
        $employeeId = auth()->user()->employee->id ?? 0;
        $schoolId = auth()->user()->school_id;

        // Ambil ID Tahun Ajaran aktif
        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->first();
        $academicYearId = $activeYear ? $activeYear->id : 0;

        // 1. Ambil Kelas di mana dia adalah WALI KELAS
        $waliKelas = Classroom::where('homeroom_teacher_id', $employeeId)
            ->where('academic_year_id', $academicYearId)
            ->get();

        // 2. Ambil Kelas di mana dia adalah GURU MAPEL
        $mapelKhusus = ClassroomSubject::where('employee_id', $employeeId)
            ->whereHas('classroom', function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->with('classroom')
            ->get()
            ->pluck('classroom');

        // 3. Gabungkan kelas, hilangkan duplikasi, lalu urutkan
        $classrooms = $waliKelas->merge($mapelKhusus)
            ->unique('id')
            ->sortBy([
                ['tingkat', 'asc'],
                ['nama_kelas', 'asc'],
            ])->values();
        $classroomId = $request->classroom_id ?? $classrooms->first()->id ?? null;

        $students = collect();
        $jadwalTersimpan = [];

        if ($classroomId) {
            $students = Student::whereHas('classrooms', function ($q) use ($classroomId) {
                $q->where('classrooms.id', $classroomId);
            })->orderBy('nama_lengkap')->get();

            // Ambil jadwal yang sudah ada, kelompokkan berdasarkan student_id dan hari
            $jadwalTersimpan = JadwalPiket::where('classroom_id', $classroomId)
                ->where('academic_year_id', $academicYearId)
                ->get()
                ->groupBy(['student_id', 'hari'])
                ->toArray();
        }

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('piket.jadwal', compact('classrooms', 'classroomId', 'students', 'jadwalTersimpan', 'hariList'));
    }

    public function storeJadwal(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'jadwal' => 'array', // format: jadwal[student_id][] = hari
        ]);

        $employeeId = auth()->user()->employee->id ?? 0;
        $schoolId = auth()->user()->school_id;

        // Ambil ID Tahun Ajaran aktif
        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->first();
        $academicYearId = $activeYear ? $activeYear->id : 0;

        // Hapus jadwal lama untuk kelas ini
        JadwalPiket::where('classroom_id', $request->classroom_id)
            ->where('academic_year_id', $academicYearId)
            ->delete();

        $dataInsert = [];
        $now = now();

        if ($request->has('jadwal')) {
            foreach ($request->jadwal as $studentId => $hariList) {
                foreach ($hariList as $hari) {
                    $dataInsert[] = [
                        'classroom_id' => $request->classroom_id,
                        'student_id' => $studentId,
                        'hari' => $hari,
                        'academic_year_id' => $academicYearId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        JadwalPiket::insert($dataInsert);

        return redirect()->back()->with('success', 'Jadwal piket berhasil diperbarui.');
    }

    /**
     * 2. HALAMAN PENCATATAN JURNAL PIKET HARIAN
     */
    public function jurnal(Request $request)
    {
        $employeeId = auth()->user()->employee->id ?? 0;
        $schoolId = auth()->user()->school_id;

        // Ambil ID Tahun Ajaran aktif
        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->first();
        $academicYearId = $activeYear ? $activeYear->id : 0;

        // 1. Ambil Kelas di mana dia adalah WALI KELAS
        $waliKelas = Classroom::where('homeroom_teacher_id', $employeeId)
            ->where('academic_year_id', $academicYearId)
            ->get();

        // 2. Ambil Kelas di mana dia adalah GURU MAPEL
        $mapelKhusus = ClassroomSubject::where('employee_id', $employeeId)
            ->whereHas('classroom', function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->with('classroom')
            ->get()
            ->pluck('classroom');

        // 3. Gabungkan kelas, hilangkan duplikasi, lalu urutkan
        $classrooms = $waliKelas->merge($mapelKhusus)
            ->unique('id')
            ->sortBy([
                ['tingkat', 'asc'],
                ['nama_kelas', 'asc'],
            ])->values();
        $classroomId = $request->classroom_id ?? $classrooms->first()->id ?? null;

        $tanggal = $request->tanggal ?? now()->toDateString();

        // Terjemahkan nama hari ke bahasa Indonesia
        Carbon::setLocale('id');
        $namaHari = Carbon::parse($tanggal)->translatedFormat('l'); // Senin, Selasa...

        $siswaPiket = [];
        $absensiHariIni = [];
        $jurnalTersimpan = [];

        if ($classroomId) {
            // Ambil daftar siswa yang piket hari ini
            $siswaPiket = JadwalPiket::with('student')
                ->where('classroom_id', $classroomId)
                ->where('academic_year_id', $academicYearId)
                ->where('hari', $namaHari)
                ->get();

            // Cek absensi siswa di tanggal tersebut
            $absensiHariIni = Attendance::where('classroom_id', $classroomId)
                ->where('tanggal', $tanggal)
                ->get()
                ->keyBy('student_id');

            // Cek apakah jurnal sudah pernah disimpan sebelumnya
            $jurnalTersimpan = JurnalPiket::where('classroom_id', $classroomId)
                ->where('tanggal', $tanggal)
                ->get()
                ->keyBy('student_id');
        }

        return view('piket.jurnal', compact(
            'classrooms', 'classroomId', 'tanggal', 'namaHari', 'siswaPiket', 'absensiHariIni', 'jurnalTersimpan'
        ));
    }

    public function storeJurnal(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'tanggal' => 'required|date',
            'status' => 'array',
            'catatan' => 'array',
        ]);

        $employeeId = auth()->user()->employee->id ?? 0;
        $schoolId = auth()->user()->school_id;

        // Ambil ID Tahun Ajaran aktif
        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->first();
        $academicYearId = $activeYear ? $activeYear->id : 0;

        $tanggal = $request->tanggal;
        $classroomId = $request->classroom_id;

        // Ambil data absensi
        $absensiHariIni = Attendance::where('classroom_id', $classroomId)
            ->where('tanggal', $tanggal)
            ->get()
            ->keyBy('student_id');

        if ($request->has('status')) {
            foreach ($request->status as $studentId => $statusPiket) {

                $catatanGuru = $request->catatan[$studentId] ?? null;
                $statusAkhir = $statusPiket;
                $catatanAkhir = $catatanGuru;

                // INTEGRASI ABSENSI: Timpa status jika siswa tidak hadir
                if ($absensiHariIni->has($studentId)) {
                    $statusAbsen = $absensiHariIni[$studentId]->status;
                    if ($statusAbsen !== 'hadir') {
                        $statusAkhir = 'tidak_terlaksana';
                        $catatanAkhir = 'Siswa tidak masuk ('.ucfirst($statusAbsen).')';
                    }
                }

                JurnalPiket::updateOrCreate(
                    [
                        'classroom_id' => $classroomId,
                        'student_id' => $studentId,
                        'tanggal' => $tanggal,
                    ],
                    [
                        'status' => $statusAkhir,
                        'catatan' => $catatanAkhir,
                        'academic_year_id' => $academicYearId,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Jurnal piket harian berhasil disimpan.');
    }

    /**
     * 3. HALAMAN LAPORAN PIKET SISWA
     */
    public function laporan(Request $request)
    {
        $employeeId = auth()->user()->employee->id ?? 0;
        $schoolId = auth()->user()->school_id;

        // Ambil ID Tahun Ajaran aktif
        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->first();
        $academicYearId = $activeYear ? $activeYear->id : 0;

        // 1. Ambil Kelas di mana dia adalah WALI KELAS
        $waliKelas = Classroom::where('homeroom_teacher_id', $employeeId)
            ->where('academic_year_id', $academicYearId)
            ->get();

        // 2. Ambil Kelas di mana dia adalah GURU MAPEL
        $mapelKhusus = ClassroomSubject::where('employee_id', $employeeId)
            ->whereHas('classroom', function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->with('classroom')
            ->get()
            ->pluck('classroom');

        // 3. Gabungkan kelas, hilangkan duplikasi, lalu urutkan
        $classrooms = $waliKelas->merge($mapelKhusus)
            ->unique('id')
            ->sortBy([
                ['tingkat', 'asc'],
                ['nama_kelas', 'asc'],
            ])->values();
        $classroomId = $request->classroom_id ?? $classrooms->first()->id ?? null;

        // Default filter bulan dan tahun saat ini
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $students = collect();
        $rekapPiket = [];

        if ($classroomId) {
            $students = Student::whereHas('classrooms', function ($q) use ($classroomId) {
                $q->where('classrooms.id', $classroomId);
            })->orderBy('nama_lengkap')->get();

            // Ambil semua data jurnal piket di bulan dan tahun yang dipilih
            $jurnals = JurnalPiket::where('classroom_id', $classroomId)
                ->where('academic_year_id', $academicYearId)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get();

            // Hitung rekapitulasi untuk tiap siswa
            foreach ($students as $siswa) {
                $jurnalSiswa = $jurnals->where('student_id', $siswa->id);

                $terlaksana = $jurnalSiswa->where('status', 'terlaksana')->count();
                $tidakTerlaksana = $jurnalSiswa->where('status', 'tidak_terlaksana')->count();
                $totalJadwal = $terlaksana + $tidakTerlaksana;

                // Ambil daftar catatan pelanggaran/ketidakhadiran jika ada
                $catatan = $jurnalSiswa->where('status', 'tidak_terlaksana')
                    ->whereNotNull('catatan')
                    ->pluck('catatan', 'tanggal')
                    ->toArray();

                $rekapPiket[$siswa->id] = [
                    'terlaksana' => $terlaksana,
                    'tidak_terlaksana' => $tidakTerlaksana,
                    'total' => $totalJadwal,
                    'catatan_pelanggaran' => $catatan,
                ];
            }
        }

        $bulanList = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        return view('piket.laporan', compact(
            'classrooms', 'classroomId', 'students', 'rekapPiket', 'bulan', 'tahun', 'bulanList'
        ));
    }
}
