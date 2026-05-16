<?php

namespace App\Http\Controllers\Api; // Pastikan memindahkannya ke folder Api

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\School;
use App\Models\Student;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // 1. Menampilkan Form Absensi berdasarkan Kelas dan Tanggal
    public function show(Request $request, Classroom $classroom)
    {
        $user = auth()->user();

        // Isolasi Tenant (Kecuali Superadmin)
        if (! $user->hasRole('superadmin') && $classroom->school_id !== $user->school_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak. Anda tidak berhak melihat absensi kelas ini.',
            ], 403);
        }

        // Tentukan tanggal absensi (Default: Hari ini)
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        // Ambil daftar siswa di kelas tersebut (diurutkan abjad)
        $classroom->load(['students' => function ($query) {
            $query->orderBy('nama_lengkap', 'asc');
        }]);

        // Ambil data absensi yang sudah ada pada tanggal tersebut
        $existingAttendances = Attendance::where('classroom_id', $classroom->id)
            ->where('tanggal', $tanggal)
            ->get()
            ->keyBy('student_id');

        // Mapping data siswa agar Flutter mudah merendernya beserta status saat ini
        $studentsData = $classroom->students->map(function ($student) use ($existingAttendances) {
            $attendance = $existingAttendances->get($student->id);

            return [
                'id' => $student->id,
                'nama_lengkap' => $student->nama_lengkap,
                'nisn' => $student->nisn,
                'status_absensi' => $attendance ? $attendance->status : 'hadir', // Default 'hadir' di UI Flutter nantinya
                'keterangan' => $attendance ? $attendance->keterangan : null,
            ];
        });

        // Cek jika guru adalah wali kelas di lebih dari 1 kelas (untuk dropdown di Flutter)
        $myClassrooms = [];
        if ($user->hasRole('guru')) {
            $employeeId = $user->employee->id ?? 0;
            $myClassrooms = Classroom::where('homeroom_teacher_id', $employeeId)
                ->where('academic_year_id', $classroom->academic_year_id)
                ->select('id', 'nama_kelas', 'tingkat')
                ->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'classroom' => [
                    'id' => $classroom->id,
                    'nama_kelas' => $classroom->nama_kelas,
                    'tingkat' => $classroom->tingkat,
                ],
                'tanggal' => $tanggal,
                'students' => $studentsData,
                'my_classrooms' => $myClassrooms,
            ],
        ], 200);
    }

    // 2. Menyimpan Absensi Massal
    public function store(Request $request, Classroom $classroom)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:hadir,sakit,izin,alfa',
        ]);

        $activeYear = AcademicYear::where('school_id', $classroom->school_id)
            ->where('is_active', true)
            ->first();

        if (! $activeYear) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan: Tahun ajaran aktif untuk sekolah ini belum ditentukan.',
            ], 400);
        }

        $tanggal = $request->tanggal;

        foreach ($request->attendance as $studentId => $data) {
            if ($data['status'] === 'hadir') {
                // Hapus record jika status diubah kembali menjadi hadir
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

        return response()->json([
            'status' => 'success',
            'message' => 'Data absensi massal berhasil disimpan.',
        ], 200);
    }

    // 3. Menampilkan Laporan Absensi Per Siswa
    public function studentReport($studentId)
    {
        $user = auth()->user();
        $student = Student::with('classrooms')->findOrFail($studentId);

        if (! $user->hasRole('superadmin') && $student->school_id !== $user->school_id) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $classroom = $student->classrooms()->latest()->first();

        if (! $classroom) {
            return response()->json([
                'status' => 'error',
                'message' => 'Siswa belum terdaftar di kelas manapun.',
            ], 404);
        }

        $totalHariEfektif = Attendance::where('classroom_id', $classroom->id)
            ->distinct('tanggal')
            ->count('tanggal');

        $absences = Attendance::where('student_id', $student->id)
            ->where('classroom_id', $classroom->id)
            ->orderBy('tanggal', 'desc')
            ->get(['tanggal', 'status', 'keterangan']); // Optimasi hanya ambil data penting

        $rekap = [
            'sakit' => $absences->where('status', 'sakit')->count(),
            'izin' => $absences->where('status', 'izin')->count(),
            'alfa' => $absences->where('status', 'alfa')->count(),
        ];

        $totalTidakHadir = array_sum($rekap);
        $rekap['hadir'] = max(0, $totalHariEfektif - $totalTidakHadir);

        $persentase = $totalHariEfektif > 0
            ? round(($rekap['hadir'] / $totalHariEfektif) * 100, 1)
            : 0;

        return response()->json([
            'status' => 'success',
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'nama' => $student->nama_lengkap,
                    'nisn' => $student->nisn,
                ],
                'classroom' => [
                    'nama_kelas' => $classroom->tingkat.' - '.$classroom->nama_kelas,
                ],
                'statistik' => [
                    'total_hari_efektif' => $totalHariEfektif,
                    'persentase_kehadiran' => $persentase,
                    'rekap' => $rekap,
                ],
                'riwayat_absen' => $absences,
            ],
        ], 200);
    }

    // 4. Rekapitulasi Absensi (Index)
    public function index(Request $request)
    {
        $user = auth()->user();

        if (! $user->hasAnyRole(['superadmin', 'operator', 'guru', 'kepsek'])) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $selectedSchoolId = $user->hasRole('superadmin') ? $request->query('school_id') : $user->school_id;
        $schools = $user->hasRole('superadmin') ? School::orderBy('nama_sekolah')->select('id', 'nama_sekolah')->get() : [];

        $activeYear = null;
        $reportData = [];
        $allClassrooms = [];

        if ($selectedSchoolId) {
            $activeYear = AcademicYear::where('school_id', $selectedSchoolId)
                ->where('is_active', true)
                ->first();

            if ($activeYear) {
                $classroomQuery = Classroom::with(['students' => function ($query) {
                    $query->orderBy('nama_lengkap', 'asc');
                }])
                    ->where('school_id', $selectedSchoolId)
                    ->where('academic_year_id', $activeYear->id);

                if ($user->hasRole('guru')) {
                    $employeeId = $user->employee->id ?? 0;
                    $classroomQuery->where('homeroom_teacher_id', $employeeId);
                    $allClassrooms = Classroom::where('school_id', $selectedSchoolId)
                        ->where('academic_year_id', $activeYear->id)
                        ->where('homeroom_teacher_id', $employeeId)
                        ->select('id', 'nama_kelas', 'tingkat')
                        ->get();
                } else {
                    $allClassrooms = Classroom::where('school_id', $selectedSchoolId)
                        ->where('academic_year_id', $activeYear->id)
                        ->select('id', 'nama_kelas', 'tingkat')
                        ->get();
                }

                if ($request->classroom_id) {
                    $classroomQuery->where('id', $request->classroom_id);
                }

                $classrooms = $classroomQuery->get();

                foreach ($classrooms as $classroom) {
                    $totalDays = Attendance::where('classroom_id', $classroom->id)
                        ->where('academic_year_id', $activeYear->id)
                        ->distinct('tanggal')
                        ->count('tanggal');

                    $namaLengkapKelas = $classroom->tingkat.' - '.$classroom->nama_kelas;

                    // Inisialisasi array untuk kelas ini
                    $reportData[$namaLengkapKelas] = [
                        'classroom_id' => $classroom->id,
                        'students' => [],
                    ];

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

                        $reportData[$namaLengkapKelas]['students'][] = [
                            'id' => $student->id,
                            'nama' => $student->nama_lengkap,
                            'nisn' => $student->nisn,
                            'rekap' => [
                                'hadir' => $h,
                                'sakit' => $s,
                                'izin' => $i,
                                'alfa' => $a,
                            ],
                            'persentase' => $percentage,
                        ];
                    }
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'active_year' => $activeYear ? $activeYear->tahun_ajaran : null,
                'schools_dropdown' => $schools, // Hanya terisi jika superadmin
                'classrooms_dropdown' => $allClassrooms, // Berisi daftar kelas untuk difilter
                'report' => $reportData,
            ],
        ], 200);
    }
}
