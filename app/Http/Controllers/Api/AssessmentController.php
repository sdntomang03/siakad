<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\AssessmentType;
use App\Models\Classroom;
use App\Models\ClassroomSubject;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    // TAHAP 1: FORM MEMBUAT WADAH PENILAIAN (API Data Dropdown)
    public function create()
    {
        $user = auth()->user();
        $schoolId = $user->school_id;

        // Ambil ID Tahun Ajaran Aktif
        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->first();

        if (! $activeYear) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tahun ajaran aktif belum ditentukan untuk sekolah ini.',
            ], 400);
        }

        $classesData = [];

        // ==========================================
        // CEK ROLE: SUPERADMIN vs GURU
        // ==========================================
        if ($user->hasRole('superadmin')) {
            // JIKA SUPERADMIN: Ambil SEMUA kelas beserta SEMUA mapel di sekolah tersebut
            $allClasses = Classroom::where('school_id', $schoolId)
                ->where('academic_year_id', $activeYear->id)
                ->get();

            foreach ($allClasses as $kelas) {
                $subjects = Subject::where('school_id', $schoolId)
                    ->where('tingkat', $kelas->tingkat)
                    ->get(); // Ambil semua mapel tanpa peduli siapa gurunya

                $classesData[$kelas->id] = [
                    'id' => $kelas->id,
                    'nama_kelas' => $kelas->tingkat.' - '.$kelas->nama_kelas,
                    'subjects' => $subjects->map(fn ($s) => ['id' => $s->id, 'nama' => $s->nama_mapel])->toArray(),
                ];
            }
        } else {
            // JIKA GURU BIASA: Ambil HANYA kelas yang dia ajar
            $employeeId = $user->employee->id ?? 0;

            // 1. Kelas dimana dia jadi Wali Kelas
            $waliKelas = Classroom::where('homeroom_teacher_id', $employeeId)
                ->where('academic_year_id', $activeYear->id)
                ->get();

            foreach ($waliKelas as $kelas) {
                $subjects = Subject::where('school_id', $schoolId)
                    ->where('tingkat', $kelas->tingkat)
                    ->where('pengampu', 'guru_kelas')
                    ->get();

                $classesData[$kelas->id] = [
                    'id' => $kelas->id,
                    'nama_kelas' => $kelas->tingkat.' - '.$kelas->nama_kelas,
                    'subjects' => $subjects->map(fn ($s) => ['id' => $s->id, 'nama' => $s->nama_mapel])->toArray(),
                ];
            }

            // 2. Kelas dimana dia jadi Guru Mapel Khusus (Agama/PJOK)
            $mapelKhusus = ClassroomSubject::where('employee_id', $employeeId)
                ->with(['classroom', 'subject'])
                ->whereHas('classroom', function ($q) use ($activeYear) {
                    $q->where('academic_year_id', $activeYear->id);
                })
                ->get();

            foreach ($mapelKhusus as $mk) {
                $kelasId = $mk->classroom->id;

                if (! isset($classesData[$kelasId])) {
                    $classesData[$kelasId] = [
                        'id' => $kelasId,
                        'nama_kelas' => $mk->classroom->tingkat.' - '.$mk->classroom->nama_kelas,
                        'subjects' => [],
                    ];
                }
                $classesData[$kelasId]['subjects'][] = [
                    'id' => $mk->subject->id,
                    'nama' => $mk->subject->nama_mapel,
                ];
            }
        }

        // Susun format akhir
        $classesList = array_values($classesData);
        $assessmentTypes = AssessmentType::where('school_id', $schoolId)->get(['id', 'nama', 'bobot']);

        return response()->json([
            'status' => 'success',
            'data' => [
                'active_year' => $activeYear->tahun_ajaran,
                'classes' => $classesList,
                'assessment_types' => $assessmentTypes,
            ],
        ], 200);
    }

    // MENYIMPAN WADAH PENILAIAN
    public function store(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'assessment_type_id' => 'required|exists:assessment_types,id',
            'keterangan' => 'required|string|max:255',
            'tanggal' => 'required|date',
        ]);

        $activeYear = AcademicYear::where('school_id', auth()->user()->school_id)
            ->where('is_active', true)
            ->first();

        $assessment = Assessment::create([
            'school_id' => auth()->user()->school_id,
            'academic_year_id' => $activeYear->id,
            'classroom_id' => $request->classroom_id,
            'subject_id' => $request->subject_id,
            'employee_id' => auth()->user()->employee->id ?? 0,
            'assessment_type_id' => $request->assessment_type_id,
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Wadah penilaian berhasil dibuat.',
            'data' => [
                'assessment_id' => $assessment->id,
            ],
        ], 201);
    }

    // TAHAP 2: MENAMPILKAN HALAMAN INPUT NILAI MARATON
    public function input(Assessment $assessment)
    {
        $user = auth()->user();
        if (! $user->hasRole('superadmin') && $assessment->employee_id !== ($user->employee->id ?? 0)) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $assessment->load(['classroom', 'subject', 'assessmentType']);

        $students = Student::whereHas('classrooms', function ($query) use ($assessment) {
            $query->where('classrooms.id', $assessment->classroom_id);
        })->orderBy('nama_lengkap', 'asc')->get(['id', 'nama_lengkap', 'nisn']);

        $existingScores = AssessmentScore::where('assessment_id', $assessment->id)
            ->pluck('score', 'student_id')
            ->toArray();

        // Gabungkan nilai ke dalam data siswa
        $studentsData = $students->map(function ($student) use ($existingScores) {
            return [
                'id' => $student->id,
                'nama_lengkap' => $student->nama_lengkap,
                'nisn' => $student->nisn,
                'score' => $existingScores[$student->id] ?? null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'assessment_info' => [
                    'id' => $assessment->id,
                    'keterangan' => $assessment->keterangan,
                    'tanggal' => $assessment->tanggal,
                    'kelas' => $assessment->classroom->tingkat.' - '.$assessment->classroom->nama_kelas,
                    'mapel' => $assessment->subject->nama_mapel,
                    'tipe_penilaian' => $assessment->assessmentType->nama,
                ],
                'students' => $studentsData,
            ],
        ], 200);
    }

    // MENYIMPAN SELURUH NILAI SISWA KE DATABASE
    public function updateScores(Request $request, Assessment $assessment)
    {
        $user = auth()->user();
        if (! $user->hasRole('superadmin') && $assessment->employee_id !== ($user->employee->id ?? 0)) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($request->scores as $studentId => $score) {
            if ($score !== null) {
                AssessmentScore::updateOrCreate(
                    [
                        'assessment_id' => $assessment->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'score' => $score,
                    ]
                );
            } else {
                AssessmentScore::where('assessment_id', $assessment->id)
                    ->where('student_id', $studentId)
                    ->delete();
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Nilai berhasil disimpan!',
        ], 200);
    }

    // MENAMPILKAN RIWAYAT PENILAIAN
    // MENAMPILKAN RIWAYAT PENILAIAN
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Assessment::with(['classroom', 'subject', 'assessmentType'])
            ->withCount('scores');

        if (! $user->hasRole('superadmin')) {
            $query->where('employee_id', $user->employee->id ?? 0);
        }

        // ==========================================
        // TAMBAHAN: LOGIKA FILTER PENCARIAN
        // ==========================================
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('assessment_type_id')) {
            $query->where('assessment_type_id', $request->assessment_type_id);
        }

        $assessments = $query->orderBy('created_at', 'desc')->paginate(15);

        $formattedAssessments = collect($assessments->items())->map(function ($item) {
            return [
                'id' => $item->id,
                'keterangan' => $item->keterangan,
                'tanggal' => $item->tanggal,
                'kelas' => $item->classroom->tingkat.' - '.$item->classroom->nama_kelas,
                'mapel' => $item->subject->nama_mapel,
                'tipe_penilaian' => $item->assessmentType->nama,
                'jumlah_dinilai' => $item->scores_count,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $formattedAssessments,
            'meta' => [
                'current_page' => $assessments->currentPage(),
                'last_page' => $assessments->lastPage(),
                'total' => $assessments->total(),
            ],
        ], 200);
    }

    // MENGHAPUS WADAH PENILAIAN
    public function destroy(Assessment $assessment)
    {
        $user = auth()->user();
        if (! $user->hasRole('superadmin') && $assessment->employee_id !== ($user->employee->id ?? 0)) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $assessment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Riwayat penilaian beserta nilainya berhasil dihapus secara permanen.',
        ], 200);
    }

    // MENAMPILKAN MATRIKS REKAP NILAI
    public function recap(Request $request)
    {
        $employeeId = auth()->user()->employee->id ?? 0;
        $schoolId = auth()->user()->school_id;

        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->first();

        $activeYearId = $activeYear ? $activeYear->id : 0;

        // 1. SIAPKAN DATA UNTUK DROPDOWN (Sama seperti Create)
        $classesData = [];

        $waliKelas = Classroom::where('homeroom_teacher_id', $employeeId)->where('academic_year_id', $activeYearId)->get();
        foreach ($waliKelas as $kelas) {
            $subjects = Subject::where('school_id', $schoolId)->where('tingkat', $kelas->tingkat)->where('pengampu', 'guru_kelas')->get();
            $classesData[$kelas->id] = [
                'id' => $kelas->id,
                'nama_kelas' => $kelas->tingkat.' - '.$kelas->nama_kelas,
                'subjects' => $subjects->map(fn ($s) => ['id' => $s->id, 'nama' => $s->nama_mapel])->toArray(),
            ];
        }

        $mapelKhusus = ClassroomSubject::where('employee_id', $employeeId)
            ->with(['classroom', 'subject'])
            ->whereHas('classroom', fn ($q) => $q->where('academic_year_id', $activeYearId))
            ->get();

        foreach ($mapelKhusus as $mk) {
            $kelasId = $mk->classroom->id;
            if (! isset($classesData[$kelasId])) {
                $classesData[$kelasId] = ['id' => $kelasId, 'nama_kelas' => $mk->classroom->tingkat.' - '.$mk->classroom->nama_kelas, 'subjects' => []];
            }
            $classesData[$kelasId]['subjects'][] = ['id' => $mk->subject->id, 'nama' => $mk->subject->nama_mapel];
        }

        $classesList = array_values($classesData);
        $assessmentTypes = AssessmentType::where('school_id', $schoolId)->get(['id', 'nama', 'bobot']);

        // 2. DATA MATRIKS NILAI
        $matrixData = [];
        $headers = [];

        if ($request->filled('classroom_id') && $request->filled('subject_id')) {
            $queryUjian = Assessment::with('assessmentType')
                ->where('classroom_id', $request->classroom_id)
                ->where('subject_id', $request->subject_id);

            if ($request->filled('assessment_type_id')) {
                $queryUjian->where('assessment_type_id', $request->assessment_type_id);
            }

            $assessments = $queryUjian->orderBy('tanggal', 'asc')->get();

            // Susun header kolom (keterangan ujian)
            $headers = $assessments->map(function ($a) {
                return [
                    'id' => $a->id,
                    'keterangan' => $a->keterangan,
                    'tipe' => $a->assessmentType->nama,
                    'tanggal' => $a->tanggal,
                ];
            })->toArray();

            $students = Student::whereHas('classrooms', function ($q) use ($request) {
                $q->where('classrooms.id', $request->classroom_id);
            })->orderBy('nama_lengkap', 'asc')->get(['id', 'nama_lengkap', 'nisn']);

            if ($assessments->count() > 0) {
                $rawScores = AssessmentScore::whereIn('assessment_id', $assessments->pluck('id'))->get();

                // Indexkan score berdasarkan student_id dan assessment_id
                $scoresMap = [];
                foreach ($rawScores as $score) {
                    $scoresMap[$score->student_id][$score->assessment_id] = $score->score;
                }

                // Masukkan nilai ke masing-masing siswa
                foreach ($students as $student) {
                    $studentScores = [];
                    foreach ($assessments as $assessment) {
                        $studentScores[] = [
                            'assessment_id' => $assessment->id,
                            'score' => $scoresMap[$student->id][$assessment->id] ?? null,
                        ];
                    }

                    $matrixData[] = [
                        'student_id' => $student->id,
                        'nama_lengkap' => $student->nama_lengkap,
                        'nisn' => $student->nisn,
                        'scores' => $studentScores,
                    ];
                }
            } else {
                // Jika belum ada ujian tapi siswa ada
                foreach ($students as $student) {
                    $matrixData[] = [
                        'student_id' => $student->id,
                        'nama_lengkap' => $student->nama_lengkap,
                        'nisn' => $student->nisn,
                        'scores' => [],
                    ];
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'filters' => [
                    'classes' => $classesList,
                    'assessment_types' => $assessmentTypes,
                ],
                'matrix' => [
                    'headers' => $headers,
                    'students' => $matrixData,
                ],
            ],
        ], 200);
    }
}
