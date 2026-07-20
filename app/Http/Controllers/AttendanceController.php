<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Holiday;
use App\Models\School;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // 1. Menampilkan Form Absensi berdasarkan Kelas dan Tanggal
    public function show(Request $request, Classroom $classroom)
    {
        $user = auth()->user();

        // Isolasi Tenant (Kecuali Superadmin)
        if (! $user->hasRole('superadmin') && $classroom->school_id !== $user->school_id) {
            abort(403);
        }

        // Tentukan tanggal absensi (Default: Hari ini)
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        // Ambil daftar siswa di kelas tersebut (diurutkan abjad)
        $classroom->load(['students' => function ($query) {
            $query->orderBy('nama_lengkap', 'asc');
        }]);

        // Ambil data absensi yang sudah ada pada tanggal tersebut (jika sudah pernah diisi)
        $existingAttendances = Attendance::where('classroom_id', $classroom->id)
            ->where('tanggal', $tanggal)
            ->get()
            ->keyBy('student_id'); // Jadikan student_id sebagai kunci array agar mudah dicocokkan

        // TAMBAHAN: Cek jika guru adalah wali kelas di lebih dari 1 kelas
        $myClassrooms = collect();
        if ($user->hasRole('guru')) {
            $employeeId = $user->employee->id ?? 0;
            $myClassrooms = Classroom::where('homeroom_teacher_id', $employeeId)
                ->where('academic_year_id', $classroom->academic_year_id)
                ->get();
        }

        // Jangan lupa tambahkan 'myClassrooms' ke dalam compact
        return view('attendances.show', compact('classroom', 'tanggal', 'existingAttendances', 'myClassrooms'));
    }

    // 2. Menyimpan Absensi Massal

    public function store(Request $request, Classroom $classroom)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:hadir,sakit,izin,alfa',
        ]);

        // PERBAIKAN: Sesuaikan dengan nama kolom 'is_active' di Migration
        $activeYear = AcademicYear::where('school_id', $classroom->school_id)
            ->where('is_active', true) // Menggunakan boolean true
            ->first();

        if (! $activeYear) {
            return back()->with('error', 'Gagal menyimpan: Tahun ajaran aktif untuk '.($classroom->school->nama_sekolah ?? 'sekolah ini').' belum ditentukan.');
        }

        $tanggal = $request->tanggal;

        foreach ($request->attendance as $studentId => $data) {
            if ($data['status'] === 'hadir') {
                Attendance::where('classroom_id', $classroom->id)
                    ->where('academic_year_id', $activeYear->id)
                    ->where('student_id', $studentId)
                    ->where('tanggal', $tanggal)
                    ->delete();
            } else {
                Attendance::updateOrCreate(
                    [
                        'classroom_id' => $classroom->id,
                        'academic_year_id' => $activeYear->id,
                        'student_id' => $studentId,
                        'tanggal' => $tanggal,
                    ],
                    [
                        'status' => $data['status'],
                        'keterangan' => $data['keterangan'] ?? null,
                    ]
                );
            }
        }

        return back()->with('success', 'Data absensi berhasil disimpan.');
    }

    public function studentReport($studentId)
    {
        $user = auth()->user();
        $student = Student::with('classrooms')->findOrFail($studentId);

        // Proteksi Multi-Tenant
        if (! $user->hasRole('superadmin') && $student->school_id !== $user->school_id) {
            abort(403);
        }

        // Ambil kelas aktif siswa saat ini (Tahun Ajaran terbaru)
        $classroom = $student->classrooms()->latest()->first();

        if (! $classroom) {
            return back()->with('error', 'Siswa belum terdaftar di kelas manapun.');
        }

        // 1. Hitung Total Hari Efektif (Berapa kali guru sudah melakukan absensi di kelas ini)
        $totalHariEfektif = Attendance::where('classroom_id', $classroom->id)
            ->distinct('tanggal')
            ->count('tanggal');

        // 2. Ambil semua rekaman ketidakhadiran siswa ini di kelas tersebut
        $absences = Attendance::where('student_id', $student->id)
            ->where('classroom_id', $classroom->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        // 3. Hitung Rekap (S, I, A)
        $rekap = [
            'sakit' => $absences->where('status', 'sakit')->count(),
            'izin' => $absences->where('status', 'izin')->count(),
            'alfa' => $absences->where('status', 'alfa')->count(),
        ];

        // 4. Kalkulasi Hadir = Total Hari Efektif - (Total Tidak Hadir)
        $totalTidakHadir = $rekap['sakit'] + $rekap['izin'] + $rekap['alfa'];
        $rekap['hadir'] = max(0, $totalHariEfektif - $totalTidakHadir);

        // 5. Hitung Persentase Kehadiran
        $persentase = $totalHariEfektif > 0
            ? round(($rekap['hadir'] / $totalHariEfektif) * 100, 1)
            : 0;

        return view('attendances.student-report', compact('student', 'classroom', 'rekap', 'absences', 'totalHariEfektif', 'persentase'));
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        abort_if(! $user->hasAnyRole(['superadmin', 'operator', 'guru', 'kepsek']), 403, 'Anda tidak memiliki akses ke halaman ini.');

        // 1. Tangani Multi-Tenant (Filter Sekolah untuk Superadmin)
        $selectedSchoolId = $user->hasRole('superadmin') ? $request->query('school_id') : $user->school_id;
        $schools = $user->hasRole('superadmin') ? School::orderBy('nama_sekolah')->get() : collect();

        $activeYear = null;
        $reportData = [];
        $allClassrooms = collect(); // Koleksi untuk dropdown kelas

        if ($selectedSchoolId) {
            $activeYear = AcademicYear::where('school_id', $selectedSchoolId)
                ->where('is_active', true)
                ->first();

            if ($activeYear) {
                // Query dasar untuk mengambil kelas
                $classroomQuery = Classroom::with(['students' => function ($query) {
                    $query->orderBy('nama_lengkap', 'asc');
                }])
                    ->where('school_id', $selectedSchoolId)
                    ->where('academic_year_id', $activeYear->id);

                // ==========================================
                // LOGIKA BARU: PENGISIAN DROPDOWN & FILTER
                // ==========================================
                if ($user->hasRole('guru')) {
                    $employeeId = $user->employee->id ?? 0;

                    // Isi dropdown HANYA dengan kelas-kelas milik guru ini
                    $allClassrooms = Classroom::where('school_id', $selectedSchoolId)
                        ->where('academic_year_id', $activeYear->id)
                        ->where('homeroom_teacher_id', $employeeId)
                        ->get();

                    // Filter kueri utama agar data tabel aman (hanya kelas miliknya)
                    $classroomQuery->where('homeroom_teacher_id', $employeeId);

                    // Jika guru memilih kelas spesifik dari dropdown
                    if ($request->classroom_id) {
                        $classroomQuery->where('id', $request->classroom_id);
                    }

                } else {
                    // Jika Operator/Kepsek/Superadmin, isi dropdown dengan seluruh kelas di sekolah
                    $allClassrooms = Classroom::where('school_id', $selectedSchoolId)
                        ->where('academic_year_id', $activeYear->id)
                        ->get();

                    if ($request->classroom_id) {
                        $classroomQuery->where('id', $request->classroom_id);
                    }
                }

                $classrooms = $classroomQuery->get();

                // Olah data statistik per siswa di setiap kelas
                foreach ($classrooms as $classroom) {
                    $totalDays = Attendance::where('classroom_id', $classroom->id)
                        ->where('academic_year_id', $activeYear->id)
                        ->distinct('tanggal')
                        ->count('tanggal');

                    foreach ($classroom->students as $student) {
                        $absences = Attendance::where('student_id', $student->id)
                            ->where('classroom_id', $classroom->id)
                            ->where('academic_year_id', $activeYear->id)
                            ->get();

                        $s = $absences->where('status', 'sakit')->count();
                        $i = $absences->where('status', 'izin')->count();
                        $a = $absences->where('status', 'alfa')->count();
                        $h = max(0, $totalDays - ($s + $i + $a));

                        $percentage = $totalDays > 0 ? round(($h / $totalDays) * 100, 1) : 100;

                        // Perbaikan: gunakan nama lengkap kelas (Tingkat + Nama) agar jelas
                        $namaLengkapKelas = $classroom->tingkat.' - '.$classroom->nama_kelas;
                        $reportData[$namaLengkapKelas][] = [
                            'id' => $student->id,
                            'nama' => $student->nama_lengkap,
                            'nisn' => $student->nisn,
                            'sakit' => $s,
                            'izin' => $i,
                            'alfa' => $a,
                            'hadir' => $h,
                            'persentase' => $percentage,
                        ];
                    }
                }
            }
        }

        return view('attendances.index', compact('reportData', 'activeYear', 'allClassrooms', 'schools', 'selectedSchoolId'));
    }

    // ==========================================
    // FUNGSI REKAP ABSENSI BULANAN
    // ==========================================
    public function monthlyRecap(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $classroomId = $request->input('classroom_id');

        $classrooms = Classroom::where('school_id', $schoolId)->orderBy('nama_kelas')->get();

        // 1. CARA BARU: Mengambil data hari libur dan memformat ulang tanggalnya secara manual (Foolproof)
        $holidaysData = Holiday::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->get();

        $holidays = [];
        foreach ($holidaysData as $holiday) {
            $formattedDate = Carbon::parse($holiday->tanggal)->format('Y-m-d');
            $holidays[$formattedDate] = $holiday->keterangan;
        }

        // 2. Siapkan array tanggal (Tetap Sama)
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $dates = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = Carbon::createFromDate($year, $month, $i);
            $dateString = $date->format('Y-m-d'); // Ini juga murni "YYYY-MM-DD"

            $dates[$i] = [
                'date' => $dateString,
                'is_weekend' => $date->isWeekend(),
                // Pengecekan sekarang pasti cocok karena formatnya sudah persis sama
                'is_holiday' => isset($holidays[$dateString]),
                'holiday_name' => $holidays[$dateString] ?? null,
                'day_name' => $date->locale('id')->isoFormat('dd'),
            ];
        }

        $students = collect();
        $attendanceData = [];
        $selectedClassroom = null;

        if ($classroomId) {
            $selectedClassroom = Classroom::with(['students' => function ($q) {
                $q->orderBy('nama_lengkap');
            }])->where('school_id', $schoolId)->find($classroomId);

            if ($selectedClassroom) {
                $students = $selectedClassroom->students;

                $attendances = Attendance::where('classroom_id', $classroomId)
                    ->whereYear('tanggal', $year)
                    ->whereMonth('tanggal', $month)
                    ->get();

                foreach ($attendances as $att) {
                    $day = Carbon::parse($att->tanggal)->format('j');
                    $attendanceData[$att->student_id][$day] = $att->status;
                }
            }
        }

        return view('attendances.monthly', compact(
            'classrooms', 'month', 'year', 'dates', 'students', 'attendanceData', 'classroomId', 'selectedClassroom'
        ));
    }

    // ==========================================
    // FUNGSI DOWNLOAD PDF REKAP ABSENSI BULANAN
    // ==========================================
    public function downloadPdf(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $sekolah = School::findOrFail($schoolId);
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $classroomId = $request->input('classroom_id');

        if (! $classroomId) {
            return back()->with('error', 'Silakan pilih kelas terlebih dahulu.');
        }

        $holidaysData = Holiday::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->get();

        $holidays = [];
        foreach ($holidaysData as $holiday) {
            $formattedDate = Carbon::parse($holiday->tanggal)->format('Y-m-d');
            $holidays[$formattedDate] = $holiday->keterangan;
        }

        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $dates = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = Carbon::createFromDate($year, $month, $i);
            $dateString = $date->format('Y-m-d');

            $dates[$i] = [
                'date' => $dateString,
                'is_weekend' => $date->isWeekend(),
                'is_holiday' => isset($holidays[$dateString]),
                'day_name' => $date->locale('id')->isoFormat('dd'),
            ];
        }

        $selectedClassroom = Classroom::with(['students' => function ($q) {
            $q->orderBy('nama_lengkap');
        }])->where('school_id', $schoolId)->findOrFail($classroomId);

        $students = $selectedClassroom->students;
        $attendanceData = [];

        $attendances = Attendance::where('classroom_id', $classroomId)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->get();

        foreach ($attendances as $att) {
            $day = Carbon::parse($att->tanggal)->format('j');
            $attendanceData[$att->student_id][$day] = $att->status;
        }

        $periode = Carbon::createFromDate($year, $month, 1)->locale('id')->isoFormat('MMMM YYYY');

        $pdf = Pdf::loadView('attendances.pdf', compact(
            'month', 'year', 'dates', 'students', 'attendanceData', 'selectedClassroom', 'periode', 'sekolah'
        ))->setPaper('a4', 'landscape');

        return $pdf->download("Rekap_Absensi_{$selectedClassroom->nama_kelas}_{$periode}.pdf");
    }
}
