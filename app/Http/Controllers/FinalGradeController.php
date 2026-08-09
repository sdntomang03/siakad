<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\SubjectFinalGrade;
use Illuminate\Http\Request;

class FinalGradeController extends Controller
{
    /**
     * Menampilkan antarmuka halaman Katrol Nilai
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? ($user->employee->school_id ?? 0);

        // Ambil Tahun Ajaran Aktif
        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first();
        if (! $activeYear) {
            return back()->with('error', 'Tahun ajaran aktif belum diatur.');
        }

        // Ambil daftar kelas (Sesuaikan dengan hak akses guru/admin)
        $classrooms = Classroom::where('school_id', $schoolId)
            ->where('academic_year_id', $activeYear->id)
            ->get();

        $subjects = collect();
        $grades = collect();
        $selectedClassroom = null;

        // Jika user memilih kelas, tampilkan mata pelajaran yang relevan
        if ($request->filled('classroom_id')) {
            $selectedClassroom = Classroom::find($request->classroom_id);
            if ($selectedClassroom) {
                // Ambil daftar mapel berdasarkan tingkat kelas
                $subjects = Subject::where('school_id', $schoolId)
                    ->where('tingkat', $selectedClassroom->tingkat)
                    ->get();
            }
        }

        // Jika user juga memilih mata pelajaran, tarik data nilainya
        if ($request->filled('classroom_id') && $request->filled('subject_id')) {
            $grades = SubjectFinalGrade::with(['student' => function ($q) {
                $q->orderBy('nama_lengkap', 'asc');
            }])
                ->where('classroom_id', $request->classroom_id)
                ->where('subject_id', $request->subject_id)
                ->where('academic_year_id', $activeYear->id)
                ->get()
                ->sortBy('student.nama_lengkap'); // Urutkan berdasarkan nama siswa
        }

        return view('final_grades.katrol', compact('activeYear', 'classrooms', 'subjects', 'grades', 'selectedClassroom'));
    }

    /**
     * Memproses Katrol Nilai secara massal menggunakan Transformasi Linier
     */
    public function katrolNilai(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'target_min' => 'required|numeric|min:0|max:100', // KKM (Misal: 75)
            'target_max' => 'required|numeric|min:0|max:100|gt:target_min', // Batas Atas (Misal: 100)
        ]);

        // 1. Tarik semua nilai siswa di kelas dan mapel tersebut
        $grades = SubjectFinalGrade::where('classroom_id', $request->classroom_id)
            ->where('subject_id', $request->subject_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->get();

        if ($grades->isEmpty()) {
            return back()->with('error', 'Tidak ada data nilai asli yang tersimpan untuk dikatrol.');
        }

        // 2. Cari Nilai Asli Terendah dan Tertinggi di kelas tersebut
        $nilaiAsliList = $grades->pluck('nilai_asli');
        $nilaiMinAsli = $nilaiAsliList->min();
        $nilaiMaxAsli = $nilaiAsliList->max();

        $targetMin = $request->target_min;
        $targetMax = $request->target_max;

        // 3. Eksekusi Rumus Katrol untuk setiap siswa
        foreach ($grades as $grade) {
            if ($nilaiMaxAsli == $nilaiMinAsli) {
                $nilaiBaru = $targetMax;
            } else {
                $nilaiBaru = (($grade->nilai_asli - $nilaiMinAsli) / ($nilaiMaxAsli - $nilaiMinAsli)) * ($targetMax - $targetMin) + $targetMin;
            }

            // 4. Update data ke database (Timpa nilai_akhir saja)
            $grade->update([
                'nilai_akhir' => round($nilaiBaru, 2),
                'predikat' => $this->hitungPredikat($nilaiBaru),
            ]);
        }

        return back()->with('success', 'Nilai berhasil dikatrol secara proporsional. Peringkat siswa tetap terjaga!');
    }

    private function hitungPredikat($nilai)
    {
        if ($nilai >= 90) {
            return 'A';
        }
        if ($nilai >= 80) {
            return 'B';
        }
        if ($nilai >= 70) {
            return 'C';
        }

        return 'D';
    }
}
