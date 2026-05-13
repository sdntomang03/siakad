<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\TeacherNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            // Validasi foto: harus berupa gambar, maksimal 2MB (2048 KB)
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'student_ids.required' => 'Pilih minimal satu siswa yang terlibat.',
            'foto.max' => 'Ukuran foto maksimal adalah 2MB agar server tetap ringan.',
        ]);

        $schoolId = auth()->user()->school_id;
        $employeeId = auth()->user()->employee->id ?? 0;
        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first();

        if (! $activeYear) {
            return back()->with('error', 'Tahun ajaran aktif tidak ditemukan.');
        }

        // 1. PROSES UPLOAD FOTO (Hanya 1 kali per kejadian)
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            // Simpan ke folder: storage/app/public/jurnal_foto
            $fotoPath = $request->file('foto')->store('jurnal_foto', 'public');
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
                'foto' => $fotoPath, // <--- Simpan path foto
            ]);
        }

        return back()->with('success', 'Jurnal kejadian berhasil disimpan.');
    }

    // 3. MENGHAPUS CATATAN (INDIVIDU ATAU KELOMPOK)
    public function destroy(Request $request, $id)
    {
        $note = TeacherNote::findOrFail($id);

        if ($request->query('mode') === 'kejadian') {
            // Hapus file foto dari storage fisik server JIKA ADA
            if ($note->foto) {
                Storage::disk('public')->delete($note->foto);
            }

            TeacherNote::where('classroom_id', $note->classroom_id)
                ->where('catatan', $note->catatan)
                ->where('created_at', $note->created_at)
                ->delete();

            $pesan = 'Seluruh riwayat kejadian beserta lampiran fotonya berhasil dihapus.';
        } else {
            // Jika hanya menghapus 1 siswa, jangan hapus file fotonya (karena masih dipakai siswa lain)
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
