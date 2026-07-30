<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\ClassroomSubject;
use App\Models\JadwalPelajaran;
use App\Models\Subject;
use Illuminate\Http\Request;

class JadwalPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $employeeId = auth()->user()->employee->id ?? 0;
        $schoolId = auth()->user()->school_id;

        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->first();
        $academicYearId = $activeYear ? $activeYear->id : 0;

        $waliKelas = Classroom::where('homeroom_teacher_id', $employeeId)
            ->where('academic_year_id', $academicYearId)
            ->get();

        $mapelKhusus = ClassroomSubject::where('employee_id', $employeeId)
            ->whereHas('classroom', function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->with('classroom')
            ->get()
            ->pluck('classroom');

        $classrooms = $waliKelas->merge($mapelKhusus)
            ->unique('id')
            ->sortBy([
                ['tingkat', 'asc'],
                ['nama_kelas', 'asc'],
            ])->values();
        $classroomId = $request->classroom_id ?? $classrooms->first()->id ?? null;

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $jadwal = collect();
        $subjects = collect();

        if ($classroomId) {
            $classroom = Classroom::find($classroomId);

            // Filter mata pelajaran berdasarkan 'tingkat' kelas yang dipilih
            $subjects = Subject::where('tingkat', $classroom->tingkat)
                ->orderBy('urutan') // Mengurutkan berdasarkan kolom urutan
                ->orderBy('nama_mapel')
                ->get();

            // Ambil jadwal dan kelompokkan berdasarkan hari
            $jadwal = JadwalPelajaran::with('subject')
                ->where('classroom_id', $classroomId)
                ->where('academic_year_id', $academicYearId)
                ->orderBy('hari')
                ->orderBy('urutan_jam')
                ->get()
                ->groupBy('hari');
        }

        return view('jadwal.index', compact('classrooms', 'classroomId', 'subjects', 'hariList', 'jadwal'));
    }

    // Untuk menyimpan banyak jadwal sekaligus (contoh sederhana)
    public function store(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'hari' => 'required|string',
            'urutan_jam' => 'required|array',
            'jam_mulai' => 'required|array',
            'jam_selesai' => 'required|array',
        ]);

        $employeeId = auth()->user()->employee->id ?? 0;
        $schoolId = auth()->user()->school_id;

        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->first();
        $academicYearId = $activeYear ? $activeYear->id : 0;

        // Hapus jadwal lama di hari & kelas tersebut sebelum update baru
        JadwalPelajaran::where('classroom_id', $request->classroom_id)
            ->where('hari', $request->hari)
            ->where('academic_year_id', $academicYearId)
            ->delete();

        $dataInsert = [];
        $now = now();

        foreach ($request->urutan_jam as $index => $urutan) {
            $dataInsert[] = [
                'classroom_id' => $request->classroom_id,
                'academic_year_id' => $academicYearId,
                'hari' => $request->hari,
                'urutan_jam' => $urutan,
                'jam_mulai' => $request->jam_mulai[$index],
                'jam_selesai' => $request->jam_selesai[$index],
                'subject_id' => $request->subject_id[$index] ?? null,
                'keterangan' => $request->keterangan[$index] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        JadwalPelajaran::insert($dataInsert);

        return redirect()->back()->with('success', "Jadwal hari {$request->hari} berhasil disimpan!");
    }

    public function edit($classroomId, $hari)
    {
        $employeeId = auth()->user()->employee->id ?? 0;
        $schoolId = auth()->user()->school_id;

        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->first();
        $academicYearId = $activeYear ? $activeYear->id : 0;
        $classroom = Classroom::findOrFail($classroomId);

        // Filter mata pelajaran berdasarkan 'tingkat' kelas yang diedit
        $subjects = Subject::where('tingkat', $classroom->tingkat)
            ->orderBy('urutan') // Mengurutkan berdasarkan kolom urutan
            ->orderBy('nama_mapel')
            ->get();

        // Ambil jadwal yang sudah ada untuk kelas dan hari tersebut
        $jadwal = JadwalPelajaran::where('classroom_id', $classroomId)
            ->where('hari', $hari)
            ->where('academic_year_id', $academicYearId)
            ->orderBy('urutan_jam')
            ->get();

        return view('jadwal.edit', compact('classroom', 'hari', 'subjects', 'jadwal'));
    }
}
