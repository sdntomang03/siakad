<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\ClassroomSubject;
use App\Models\Employee;
use App\Models\School;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        abort_if(! $user->hasPermissionTo('view-classes'), 403, 'Akses Ditolak');

        $query = Classroom::with(['homeroomTeacher', 'academicYear'])->withCount('students');

        // Variabel tambahan untuk Filter Superadmin
        $schools = [];
        $selectedSchoolId = null;

        if ($user->hasRole('superadmin')) {
            $schools = School::orderBy('nama_sekolah')->get();
            $selectedSchoolId = $request->query('school_id'); // Tangkap sekolah dari URL

            if ($selectedSchoolId) {
                $query->where('school_id', $selectedSchoolId);
            } else {
                // Jika superadmin belum pilih sekolah, KOSONGKAN hasil pencarian
                $query->whereRaw('1 = 0');
            }
        } else {
            $query->where('school_id', $user->school_id);
            $selectedSchoolId = $user->school_id;
        }

        $classrooms = $query->orderBy('tingkat')->orderBy('nama_kelas')->get();

        // Data pendukung untuk Dropdown Modal
        $academicYears = AcademicYear::latest('tahun_ajaran')->get();

        $teachersQuery = Employee::whereIn('kategori_pegawai', ['guru', 'kepsek']);
        // Pastikan dropdown guru hanya berisi guru dari sekolah yang sedang dipilih
        if ($selectedSchoolId) {
            $teachersQuery->where('school_id', $selectedSchoolId);
        } else {
            $teachersQuery->whereRaw('1 = 0');
        }
        $teachers = $teachersQuery->get();

        return view('classrooms.index', compact('classrooms', 'academicYears', 'teachers', 'schools', 'selectedSchoolId'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        abort_if(! $user->hasPermissionTo('view-classes'), 403);

        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'homeroom_teacher_id' => 'nullable|exists:employees,id',
            'tingkat' => 'required|string|max:10',
            'nama_kelas' => 'required|string|max:50',
            'kapasitas' => 'required|integer|min:1|max:100',
            // Validasi tambahan khusus Superadmin
            'school_id' => $user->hasRole('superadmin') ? 'required|exists:schools,id' : 'nullable',
        ]);

        Classroom::create([
            // Superadmin ambil dari input form, selain itu ambil dari profilnya
            'school_id' => $user->hasRole('superadmin') ? $request->school_id : $user->school_id,
            'academic_year_id' => $request->academic_year_id,
            'homeroom_teacher_id' => $request->homeroom_teacher_id,
            'tingkat' => $request->tingkat,
            'nama_kelas' => $request->nama_kelas,
            'kapasitas' => $request->kapasitas,
        ]);

        return back()->with('success', 'Kelas baru berhasil ditambahkan.');
    }

    public function update(Request $request, Classroom $classroom)
    {
        $user = auth()->user();
        abort_if(! $user->hasPermissionTo('view-classes'), 403);

        // Cek keamanan tenant
        if (! $user->hasRole('superadmin') && $classroom->school_id !== $user->school_id) {
            abort(403);
        }

        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'homeroom_teacher_id' => 'nullable|exists:employees,id',
            'tingkat' => 'required|string|max:10',
            'nama_kelas' => 'required|string|max:50',
            'kapasitas' => 'required|integer|min:1|max:100',
        ]);

        $classroom->update($request->only([
            'academic_year_id', 'homeroom_teacher_id', 'tingkat', 'nama_kelas', 'kapasitas',
        ]));

        return back()->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Classroom $classroom)
    {
        $user = auth()->user();
        abort_if(! $user->hasPermissionTo('view-classes'), 403);

        if (! $user->hasRole('superadmin') && $classroom->school_id !== $user->school_id) {
            abort(403);
        }

        $classroom->delete();

        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    public function show(Classroom $classroom)
    {
        $user = auth()->user();

        // 1. Role-Based Access Control
        abort_if(! $user->hasAnyRole(['superadmin', 'operator', 'guru', 'kepsek']), 403, 'Anda tidak memiliki akses ke halaman ini.');

        // 2. Pengecekan Wilayah Sekolah (Multi-Tenant)
        if (! $user->hasRole('superadmin') && $classroom->school_id !== $user->school_id) {
            abort(403, 'Akses ditolak: Kelas ini berada di sekolah lain.');
        }

        // 3. Pengecekan Khusus Guru (Hanya boleh buka kelasnya sendiri)
        if ($user->hasRole('guru')) {
            $employeeId = $user->employee->id ?? 0;
            if ($classroom->homeroom_teacher_id !== $employeeId) {
                abort(403, 'Akses ditolak: Anda bukan wali kelas untuk rombel ini.');
            }
        }

        // Load data relasi dasar
        $classroom->load(['students' => function ($query) {
            $query->orderBy('nama_lengkap', 'asc');
        }, 'homeroomTeacher', 'academicYear']);

        $currentAcademicYearId = $classroom->academic_year_id;

        // A. DATA UNTUK MANAJEMEN SISWA
        $availableStudents = Student::where('school_id', $classroom->school_id)
            ->whereDoesntHave('classrooms', function ($query) use ($currentAcademicYearId) {
                $query->where('academic_year_id', $currentAcademicYearId);
            })
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        // B. DATA UNTUK PENUGASAN GURU MAPEL (TAMBAHAN BARU)
        // 1. Ambil Mapel kategori 'guru_mapel' yang sesuai tingkat kelas ini
        $availableSubjects = Subject::where('school_id', $classroom->school_id)
            ->where('tingkat', $classroom->tingkat)
            ->where('pengampu', 'guru_mapel')
            ->get();

        // 2. Ambil daftar guru di sekolah tersebut untuk pilihan di dropdown
        $teachers = Employee::where('school_id', $classroom->school_id)
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        // 3. Ambil penugasan yang sudah ada (untuk mengisi nilai default di select)
        $currentAssignments = ClassroomSubject::where('classroom_id', $classroom->id)
            ->pluck('employee_id', 'subject_id')
            ->toArray();

        return view('classrooms.show', compact(
            'classroom',
            'availableStudents',
            'availableSubjects',
            'teachers',
            'currentAssignments'
        ));
    }

    // TAMBAHKAN 2 FUNGSI INI DI BAWAH FUNGSI SHOW UNTUK PROSES TAMBAH & HAPUS

    public function assignStudent(Request $request, Classroom $classroom)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $newStudentsCount = count($request->student_ids);
        $currentStudentsCount = $classroom->students()->count();

        // Cek kapasitas kelas
        if (($currentStudentsCount + $newStudentsCount) > $classroom->kapasitas) {
            $sisaKuota = $classroom->kapasitas - $currentStudentsCount;

            return back()->with('error', "Kapasitas kelas tidak mencukupi! Anda mencoba memasukkan {$newStudentsCount} siswa, tapi sisa kuota hanya {$sisaKuota} siswa.");
        }

        // Masukkan semua siswa yang dipilih sekaligus (syncWithoutDetaching mencegah error jika ada data ganda)
        $classroom->students()->syncWithoutDetaching($request->student_ids);

        return back()->with('success', "{$newStudentsCount} Siswa berhasil ditambahkan ke kelas.");
    }

    public function removeMultipleStudents(Request $request, Classroom $classroom)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        // Keluarkan semua siswa yang dipilih dari kelas
        $classroom->students()->detach($request->student_ids);

        return back()->with('success', count($request->student_ids).' Siswa berhasil dikeluarkan dari kelas.');
    }

    public function assignSubjectTeacher(Request $request, Classroom $classroom)
    {
        $request->validate([
            'assignments' => 'required|array',
            'assignments.*.subject_id' => 'required|exists:subjects,id',
            'assignments.*.employee_id' => 'nullable|exists:employees,id',
        ]);

        foreach ($request->assignments as $assignment) {
            if (! empty($assignment['employee_id'])) {
                // Jika guru dipilih, simpan atau update
                ClassroomSubject::updateOrCreate(
                    [
                        'classroom_id' => $classroom->id,
                        'subject_id' => $assignment['subject_id'],
                    ],
                    ['employee_id' => $assignment['employee_id']]
                );
            } else {
                // Jika dikosongkan (pilih -- Kosongkan --), hapus penugasan
                ClassroomSubject::where('classroom_id', $classroom->id)
                    ->where('subject_id', $assignment['subject_id'])
                    ->delete();
            }
        }

        return back()->with('success', 'Penugasan Guru Mata Pelajaran berhasil diperbarui.');
    }
}
