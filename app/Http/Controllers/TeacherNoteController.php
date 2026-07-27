<?php

namespace App\Http\Controllers;

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
    public function index(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->school_id;

        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first();

        $myClassrooms = collect();
        $students = collect();
        $riwayatCatatan = collect();
        $selectedClassroom = null;

        if ($activeYear) {
            if ($user->hasRole('guru')) {
                $employeeId = $user->employee->id ?? 0;
                $myClassrooms = Classroom::where('school_id', $schoolId)
                    ->where('academic_year_id', $activeYear->id)
                    ->where('homeroom_teacher_id', $employeeId)
                    ->get();
            } else {
                $myClassrooms = Classroom::where('school_id', $schoolId)
                    ->where('academic_year_id', $activeYear->id)
                    ->get();
            }

            if ($request->filled('classroom_id')) {
                $selectedClassroom = Classroom::with(['students' => function ($q) {
                    $q->orderBy('nama_lengkap', 'asc');
                }])->find($request->classroom_id);

                if ($selectedClassroom) {
                    $students = $selectedClassroom->students;

                    // Ambil riwayat kejadian terbaru di kelas ini
                    $riwayatCatatan = TeacherNote::with('student')
                        ->where('classroom_id', $selectedClassroom->id)
                        ->where('academic_year_id', $activeYear->id)
                        ->latest()
                        ->limit(10) // Ambil 5 data terbaru saja
                        ->get();
                }
            }
        }

        return view('teacher_notes.index', compact('activeYear', 'myClassrooms', 'students', 'riwayatCatatan', 'selectedClassroom'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'jenis_catatan' => 'required|string',
            'catatan' => 'required|string',
            'student_ids' => 'required|array|min:1',
            // Update: Validasi foto sebagai array
            'foto' => 'nullable|array',
            'foto.*' => 'image|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'student_ids.required' => 'Pilih minimal satu siswa yang terlibat.',
            'foto.*.max' => 'Ukuran setiap foto tidak boleh lebih dari 2MB.',
            'foto.*.image' => 'File harus berupa gambar.',
            'foto.*.mimes' => 'Format foto harus jpeg, png, jpg, atau webp.', ]);

        $schoolId = auth()->user()->school_id;
        $employeeId = auth()->user()->employee->id ?? 0;
        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first();

        if (! $activeYear) {
            return back()->with('error', 'Tahun ajaran aktif tidak ditemukan.');
        }

        // 1. PROSES UPLOAD BANYAK FOTO
        $fotoPaths = [];
        if ($request->hasFile('foto')) {
            // 1. Inisialisasi ImageManager (v4)
            $manager = ImageManager::usingDriver(Driver::class);

            // Pastikan folder tujuan tersedia
            if (! Storage::disk('public')->exists('jurnal_foto')) {
                Storage::disk('public')->makeDirectory('jurnal_foto');
            }

            foreach ($request->file('foto') as $file) {
                // 2. Buat nama unik dengan ekstensi .webp
                // Ekstensi ini yang memberitahu Intervention v4 untuk mengonversi ke WebP
                $fileName = Str::random(40).'.webp';
                $destinationPath = storage_path('app/public/jurnal_foto/'.$fileName);

                // 3. Baca, Perkecil (opsional), dan Simpan
                $manager->decode($file)
                    ->scaleDown(width: 1000) // Batasi lebar max 1000px agar ringan
                    ->save($destinationPath, quality: 80); // Simpan sebagai WebP kualitas 80%

                // 4. Masukkan ke array path
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
                'is_for_report' => $request->has('is_for_report') ? 1 : 0,
                'foto' => $fotoPaths, // Simpan array path (pastikan model sudah di-cast ke array)
            ]);
        }

        return back()->with('success', 'Jurnal kejadian berhasil disimpan.');
    }

    // 3. MENGHAPUS CATATAN (INDIVIDU ATAU KELOMPOK)
    public function destroy(Request $request, $id)
    {
        $note = TeacherNote::findOrFail($id);

        if ($request->query('mode') === 'kejadian') {
            // Ambil semua foto dari catatan ini
            // Karena kita simpan array yang sama untuk kelompok, ambil satu contoh saja
            if ($note->foto && is_array($note->foto)) {
                foreach ($note->foto as $path) {
                    Storage::disk('public')->delete($path);
                }
            }

            TeacherNote::where('classroom_id', $note->classroom_id)
                ->where('catatan', $note->catatan)
                ->where('created_at', $note->created_at)
                ->delete();

            $pesan = 'Seluruh riwayat kejadian beserta semua fotonya berhasil dihapus.';
        } else {
            // Jika hapus individu, data foto di storage jangan dihapus karena
            // merujuk ke file yang sama dengan siswa lain dalam kelompok tersebut
            $note->delete();
            $pesan = 'Catatan kejadian untuk siswa tersebut berhasil dihapus.';
        }

        return back()->with('success', $pesan);
    }

    // Method baru untuk menampilkan Rekapitulasi Per Siswa
    public function report(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->school_id;
        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first();

        $myClassrooms = collect();
        $students = collect();
        $selectedClassroom = null;

        if ($activeYear) {
            // Filter kelas berdasarkan Role
            if ($user->hasRole('guru')) {
                $myClassrooms = Classroom::where('school_id', $schoolId)
                    ->where('academic_year_id', $activeYear->id)
                    ->where('homeroom_teacher_id', $user->employee->id ?? 0)
                    ->get();
            } else {
                $myClassrooms = Classroom::where('school_id', $schoolId)
                    ->where('academic_year_id', $activeYear->id)
                    ->get();
            }

            // Jika kelas dipilih, ambil siswa beserta riwayat catatannya
            if ($request->filled('classroom_id')) {
                $selectedClassroom = Classroom::with([
                    'students' => fn ($q) => $q->orderBy('nama_lengkap', 'asc'),
                    'students.notes' => fn ($q) => $q->where('academic_year_id', $activeYear->id)->latest(),
                ])->find($request->classroom_id);

                if ($selectedClassroom) {
                    $students = $selectedClassroom->students;
                }
            }
        }

        return view('teacher_notes.report', compact('activeYear', 'myClassrooms', 'students', 'selectedClassroom'));
    }
}
