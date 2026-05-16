<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\TeacherNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class TeacherNoteController extends Controller
{
    // 1. MENGAMBIL DATA AWAL (KELAS, SISWA, & RIWAYAT TERBARU)
    public function index(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->school_id;

        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first();

        if (! $activeYear) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tahun ajaran aktif belum ditentukan untuk sekolah ini.',
            ], 400);
        }

        $myClassrooms = [];
        $students = [];
        $riwayatCatatan = [];
        $selectedClassroomInfo = null;

        // Ambil daftar kelas sesuai Role
        if ($user->hasRole('guru')) {
            $employeeId = $user->employee->id ?? 0;
            $myClassrooms = Classroom::where('school_id', $schoolId)
                ->where('academic_year_id', $activeYear->id)
                ->where('homeroom_teacher_id', $employeeId)
                ->select('id', 'tingkat', 'nama_kelas')
                ->get();
        } else {
            $myClassrooms = Classroom::where('school_id', $schoolId)
                ->where('academic_year_id', $activeYear->id)
                ->select('id', 'tingkat', 'nama_kelas')
                ->get();
        }

        // Jika user mengirimkan ID Kelas (Pilih Kelas di Flutter)
        if ($request->filled('classroom_id')) {
            $selectedClassroom = Classroom::with(['students' => function ($q) {
                $q->orderBy('nama_lengkap', 'asc')->select('students.id', 'nama_lengkap', 'nisn');
            }])->find($request->classroom_id);

            if ($selectedClassroom) {
                $selectedClassroomInfo = [
                    'id' => $selectedClassroom->id,
                    'nama_kelas' => $selectedClassroom->tingkat.' - '.$selectedClassroom->nama_kelas,
                ];

                $students = $selectedClassroom->students;

                // Ambil riwayat kejadian terbaru di kelas ini
                $rawCatatan = TeacherNote::with('student:id,nama_lengkap')
                    ->where('classroom_id', $selectedClassroom->id)
                    ->where('academic_year_id', $activeYear->id)
                    ->latest()
                    ->limit(10)
                    ->get();

                // Format catatan agar menyertakan URL Foto lengkap
                $riwayatCatatan = $rawCatatan->map(function ($note) {
                    $fotoUrls = [];
                    if ($note->foto && is_array($note->foto)) {
                        foreach ($note->foto as $path) {
                            $fotoUrls[] = asset('storage/'.$path);
                        }
                    }

                    return [
                        'id' => $note->id,
                        'student_id' => $note->student_id,
                        'nama_siswa' => $note->student->nama_lengkap ?? 'Anonim',
                        'jenis_catatan' => $note->jenis_catatan,
                        'catatan' => $note->catatan,
                        'is_for_report' => (bool) $note->is_for_report,
                        'foto_urls' => $fotoUrls,
                        'created_at' => $note->created_at->format('Y-m-d H:i:s'),
                    ];
                });
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'active_year' => $activeYear->tahun_ajaran,
                'classrooms' => $myClassrooms,
                'selected_classroom' => $selectedClassroomInfo,
                'students' => $students,
                'recent_notes' => $riwayatCatatan,
            ],
        ], 200);
    }

    // 2. MENYIMPAN CATATAN BARU (DENGAN UPLOAD FOTO MULTIPLE)
    public function store(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'jenis_catatan' => 'required|string',
            'catatan' => 'required|string',
            'student_ids' => 'required|array|min:1',
            'foto' => 'nullable|array',
            'foto.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $schoolId = auth()->user()->school_id;
        $employeeId = auth()->user()->employee->id ?? 0;
        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first();

        if (! $activeYear) {
            return response()->json(['status' => 'error', 'message' => 'Tahun ajaran aktif tidak ditemukan.'], 400);
        }

        // 1. PROSES UPLOAD BANYAK FOTO
        $fotoPaths = [];
        if ($request->hasFile('foto')) {
            $manager = ImageManager::usingDriver(Driver::class);

            if (! Storage::disk('public')->exists('jurnal_foto')) {
                Storage::disk('public')->makeDirectory('jurnal_foto');
            }

            foreach ($request->file('foto') as $file) {
                $fileName = Str::random(40).'.webp';
                $destinationPath = storage_path('app/public/jurnal_foto/'.$fileName);

                $manager->decode($file)
                    ->scaleDown(width: 1000)
                    ->save($destinationPath, quality: 80);

                $fotoPaths[] = 'jurnal_foto/'.$fileName;
            }
        }

        // 2. SIMPAN KE DATABASE
        foreach ($request->student_ids as $studentId) {
            TeacherNote::create([
                'school_id' => $schoolId,
                'academic_year_id' => $activeYear->id,
                'classroom_id' => $request->classroom_id,
                'student_id' => $studentId,
                'employee_id' => $employeeId,
                'jenis_catatan' => $request->jenis_catatan,
                'catatan' => $request->catatan,
                'is_for_report' => $request->has('is_for_report') && $request->is_for_report == 1 ? 1 : 0, // Disesuaikan untuk API
                'foto' => $fotoPaths,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Jurnal kejadian berhasil disimpan.',
        ], 201);
    }

    // 3. MENGHAPUS CATATAN (INDIVIDU ATAU KELOMPOK)
    public function destroy(Request $request, $id)
    {
        $note = TeacherNote::find($id);

        if (! $note) {
            return response()->json(['status' => 'error', 'message' => 'Catatan tidak ditemukan.'], 404);
        }

        // Validasi Akses (Hanya pembuat atau superadmin yang boleh hapus)
        $user = auth()->user();
        if (! $user->hasRole('superadmin') && $note->employee_id !== ($user->employee->id ?? 0)) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak memiliki hak untuk menghapus catatan ini.'], 403);
        }

        if ($request->query('mode') === 'kejadian') {
            // Ambil semua foto dari catatan ini
            if ($note->foto && is_array($note->foto)) {
                foreach ($note->foto as $path) {
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }

            TeacherNote::where('classroom_id', $note->classroom_id)
                ->where('catatan', $note->catatan)
                ->where('created_at', $note->created_at)
                ->delete();

            $pesan = 'Seluruh riwayat kejadian beserta semua fotonya berhasil dihapus.';
        } else {
            // Jika hapus individu, file fisik jangan dihapus (merujuk ke file kelompok)
            $note->delete();
            $pesan = 'Catatan kejadian untuk siswa tersebut berhasil dihapus.';
        }

        return response()->json([
            'status' => 'success',
            'message' => $pesan,
        ], 200);
    }

    // 4. REKAPITULASI CATATAN PER SISWA
    public function report(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->school_id;
        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first();

        if (! $activeYear) {
            return response()->json(['status' => 'error', 'message' => 'Tahun ajaran aktif tidak ditemukan.'], 400);
        }

        $myClassrooms = [];
        $studentsReport = [];

        // Filter kelas berdasarkan Role
        if ($user->hasRole('guru')) {
            $myClassrooms = Classroom::where('school_id', $schoolId)
                ->where('academic_year_id', $activeYear->id)
                ->where('homeroom_teacher_id', $user->employee->id ?? 0)
                ->select('id', 'tingkat', 'nama_kelas')
                ->get();
        } else {
            $myClassrooms = Classroom::where('school_id', $schoolId)
                ->where('academic_year_id', $activeYear->id)
                ->select('id', 'tingkat', 'nama_kelas')
                ->get();
        }

        // Jika kelas dipilih, ambil siswa beserta riwayat catatannya
        if ($request->filled('classroom_id')) {
            $selectedClassroom = Classroom::with([
                'students' => fn ($q) => $q->orderBy('nama_lengkap', 'asc')->select('students.id', 'nama_lengkap', 'nisn'),
                'students.notes' => fn ($q) => $q->where('academic_year_id', $activeYear->id)->latest(),
            ])->find($request->classroom_id);

            if ($selectedClassroom) {
                // Formatting data agar rapi dan foto berubah jadi URL
                $studentsReport = $selectedClassroom->students->map(function ($student) {
                    $formattedNotes = $student->notes->map(function ($note) {
                        $fotoUrls = [];
                        if ($note->foto && is_array($note->foto)) {
                            foreach ($note->foto as $path) {
                                $fotoUrls[] = asset('storage/'.$path);
                            }
                        }

                        return [
                            'id' => $note->id,
                            'jenis_catatan' => $note->jenis_catatan,
                            'catatan' => $note->catatan,
                            'is_for_report' => (bool) $note->is_for_report,
                            'foto_urls' => $fotoUrls,
                            'created_at' => $note->created_at->format('Y-m-d H:i:s'),
                        ];
                    });

                    return [
                        'id' => $student->id,
                        'nama_lengkap' => $student->nama_lengkap,
                        'nisn' => $student->nisn,
                        'total_catatan' => $student->notes->count(),
                        'notes' => $formattedNotes,
                    ];
                });
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'classrooms' => $myClassrooms,
                'students_report' => $studentsReport,
            ],
        ], 200);
    }
}
