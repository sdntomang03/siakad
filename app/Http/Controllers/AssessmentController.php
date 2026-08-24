<?php

namespace App\Http\Controllers;

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
    // TAHAP 1: FORM MEMBUAT WADAH PENILAIAN
    public function create()
    {
        $employeeId = auth()->user()->employee->id ?? 0;
        $schoolId = auth()->user()->school_id;

        // PERBAIKAN: Ambil ID Tahun Ajaran secara dinamis (Otomatis melacak yang aktif)
        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->first();

        // Jika tidak ada tahun ajaran aktif, kembalikan error
        if (! $activeYear) {
            return back()->with('error', 'Tidak ada Tahun Ajaran Aktif untuk sekolah Anda. Harap hubungi Admin.');
        }

        $activeYearId = $activeYear->id;

        $classesData = [];

        // 1. Ambil Kelas di mana dia adalah WALI KELAS
        $waliKelas = Classroom::where('homeroom_teacher_id', $employeeId)
            ->where('academic_year_id', $activeYearId)
            ->get();

        foreach ($waliKelas as $kelas) {
            // Jika wali kelas, ambil semua mapel khusus wali kelas di tingkat tersebut
            $subjects = Subject::where('school_id', $schoolId)
                ->where('tingkat', $kelas->tingkat)
                ->where('pengampu', 'guru_kelas')
                ->get();

            $classesData[$kelas->id] = [
                'nama_kelas' => $kelas->tingkat.' - '.$kelas->nama_kelas,
                'subjects' => $subjects->map(fn ($s) => ['id' => $s->id, 'nama' => $s->nama_mapel])->toArray(),
            ];
        }

        // 2. Ambil Kelas di mana dia adalah GURU MAPEL (Cth: Agama / PJOK)
        $mapelKhusus = ClassroomSubject::where('employee_id', $employeeId)
            ->with(['classroom', 'subject'])
            ->whereHas('classroom', function ($q) use ($activeYearId) {
                $q->where('academic_year_id', $activeYearId);
            })
            ->get();

        foreach ($mapelKhusus as $mk) {
            $kelasId = $mk->classroom->id;

            // Jika kelasnya belum ada di array (berarti dia bukan wali kelas di kelas ini), buat wadahnya
            if (! isset($classesData[$kelasId])) {
                $classesData[$kelasId] = [
                    'nama_kelas' => $mk->classroom->tingkat.' - '.$mk->classroom->nama_kelas,
                    'subjects' => [],
                ];
            }
            // Masukkan mapel khusus ini ke kelas tersebut
            $classesData[$kelasId]['subjects'][] = [
                'id' => $mk->subject->id,
                'nama' => $mk->subject->nama_mapel,
            ];
        }
        $assessmentTypes = AssessmentType::where('school_id', $schoolId)->get();

        return view('assessments.create', compact('classesData', 'assessmentTypes'));
    }

    // MENYIMPAN WADAH PENILAIAN & LEMPAR KE TAHAP 2
    public function store(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'assessment_type_id' => 'required|exists:assessment_types,id',
            'keterangan' => 'required|string|max:255',
            'tanggal' => 'required|date',
        ]);

        $schoolId = auth()->user()->school_id;

        // PERBAIKAN: Ambil ID Tahun Ajaran aktif saat menyimpan
        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->first();

        if (! $activeYear) {
            return back()->with('error', 'Gagal menyimpan: Tahun ajaran aktif belum ditentukan.');
        }

        $assessment = Assessment::create([
            'school_id' => $schoolId,
            'academic_year_id' => $activeYear->id,
            'classroom_id' => $request->classroom_id,
            'subject_id' => $request->subject_id,
            'employee_id' => auth()->user()->employee->id ?? 0,
            'assessment_type_id' => $request->assessment_type_id,
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal,
        ]);

        // Lempar ke halaman input nilai
        return redirect()->route('assessments.input', $assessment->id);
    }

    // TAHAP 2: MENAMPILKAN HALAMAN INPUT NILAI MARATON
    public function input(Assessment $assessment)
    {
        // Pastikan hanya pembuat penilaian atau superadmin yang bisa akses
        $user = auth()->user();
        if (! $user->hasRole('superadmin') && $assessment->employee_id !== ($user->employee->id ?? 0)) {
            abort(403, 'Akses ditolak: Anda tidak memiliki akses ke penilaian ini.');
        }

        // Load data relasi untuk ditampilkan di header
        $assessment->load(['classroom', 'subject', 'assessmentType']);

        // Ambil daftar siswa yang ada di kelas tersebut (diurutkan abjad)
        $students = Student::whereHas('classrooms', function ($query) use ($assessment) {
            $query->where('classrooms.id', $assessment->classroom_id);
        })->orderBy('nama_lengkap', 'asc')->get();

        // Ambil nilai yang sudah ada (jika guru sedang mengedit nilai)
        $existingScores = AssessmentScore::where('assessment_id', $assessment->id)
            ->pluck('score', 'student_id')
            ->toArray();

        return view('assessments.input', compact('assessment', 'students', 'existingScores'));
    }

    // MENYIMPAN SELURUH NILAI SISWA KE DATABASE
    public function updateScores(Request $request, Assessment $assessment)
    {
        $user = auth()->user();
        if (! $user->hasRole('superadmin') && $assessment->employee_id !== ($user->employee->id ?? 0)) {
            abort(403, 'Akses ditolak.');
        }

        // Validasi: pastikan data scores dikirim dalam bentuk array (student_id => score)
        $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0|max:100', // Nilai kosong diizinkan, maksimal 100
        ]);

        foreach ($request->scores as $studentId => $score) {
            // Jika guru mengisi nilai (tidak kosong)
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
                // Jika guru mengosongkan kotak input, hapus nilainya dari database
                AssessmentScore::where('assessment_id', $assessment->id)
                    ->where('student_id', $studentId)
                    ->delete();
            }
        }

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('assessments.index')->with('success', 'Nilai berhasil disimpan!');
    }

    // MENAMPILKAN RIWAYAT PENILAIAN
    public function index()
    {
        $user = auth()->user();

        // Mulai kueri ambil data penilaian beserta relasi tabelnya
        $query = Assessment::with(['classroom', 'subject'])
            ->withCount('scores'); // Ini akan otomatis menghitung berapa siswa yang sudah dinilai

        // Jika yang login bukan superadmin, tampilkan HANYA penilaian miliknya sendiri
        if (! $user->hasRole('superadmin')) {
            $query->where('employee_id', $user->employee->id ?? 0);
        }

        // Tampilkan 10 data terbaru per halaman
        $assessments = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('assessments.index', compact('assessments'));
    }

    // MENGHAPUS WADAH PENILAIAN
    public function destroy(Assessment $assessment)
    {
        $user = auth()->user();
        // Cek keamanan ganda
        if (! $user->hasRole('superadmin') && $assessment->employee_id !== ($user->employee->id ?? 0)) {
            abort(403, 'Akses ditolak.');
        }

        // Karena di tabel migration kita pakai cascadeOnDelete(),
        // menghapus assessment otomatis menghapus semua score siswanya juga!
        $assessment->delete();

        return redirect()->route('assessments.index')->with('success', 'Riwayat penilaian beserta nilainya berhasil dihapus secara permanen.');
    }

    // MENAMPILKAN MATRIKS REKAP NILAI
    public function recap(Request $request)
    {
        $employeeId = auth()->user()->employee->id ?? 0;
        $schoolId = auth()->user()->school_id;

        // Ambil ID Tahun Ajaran secara dinamis
        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->first();

        $activeYearId = $activeYear ? $activeYear->id : 0;

        // 1. SIAPKAN DATA UNTUK DROPDOWN
        $classesData = [];

        $waliKelas = Classroom::where('homeroom_teacher_id', $employeeId)
            ->where('academic_year_id', $activeYearId)
            ->get();

        foreach ($waliKelas as $kelas) {
            $subjects = Subject::where('school_id', $schoolId)->where('tingkat', $kelas->tingkat)->where('pengampu', 'guru_kelas')->get();
            $classesData[$kelas->id] = [
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
                $classesData[$kelasId] = ['nama_kelas' => $mk->classroom->tingkat.' - '.$mk->classroom->nama_kelas, 'subjects' => []];
            }
            $classesData[$kelasId]['subjects'][] = ['id' => $mk->subject->id, 'nama' => $mk->subject->nama_mapel];
        }

        // 2. AMBIL DATA NILAI
        $students = collect();
        $assessments = collect();
        $matrixScores = [];
        $assessmentTypes = AssessmentType::where('school_id', $schoolId)->get();

        if ($request->filled('classroom_id')) {
            $classroomId = $request->classroom_id;

            // Kueri dasar untuk mengambil penilaian
            $queryUjian = Assessment::where('classroom_id', $classroomId)
                ->with('subject') // Load nama mapel
                ->where(function ($query) {
                    // Kecualikan format non-tes (Izinkan 'tes' atau data lama yang formatnya null)
                    $query->where('format', '!=', 'non-tes')
                        ->orWhereNull('format');
                });

            // Jika pilih mapel spesifik
            if ($request->filled('subject_id') && $request->subject_id !== 'all') {
                $queryUjian->where('subject_id', $request->subject_id);
            }

            // Jika pilih jenis penilaian (Ulangan Harian, UTS, dll)
            if ($request->filled('assessment_type_id')) {
                $queryUjian->where('assessment_type_id', $request->assessment_type_id);
            }

            // Urutkan berdasarkan Mapel lalu Tanggal
            $assessments = $queryUjian->orderBy('subject_id', 'asc')->orderBy('tanggal', 'asc')->get();

            // Ambil siswa
            $students = Student::whereHas('classrooms', function ($q) use ($classroomId) {
                $q->where('classrooms.id', $classroomId);
            })->orderBy('nama_lengkap', 'asc')->get();

            // Ambil skor
            if ($assessments->count() > 0) {
                $rawScores = AssessmentScore::whereIn('assessment_id', $assessments->pluck('id'))->get();
                foreach ($rawScores as $score) {
                    $matrixScores[$score->student_id][$score->assessment_id] = $score->score;
                }
            }
        }

        return view('assessments.recap', compact('classesData', 'students', 'assessments', 'matrixScores', 'assessmentTypes'));
    }

    // MENAMPILKAN MATRIKS RATA-RATA PER JENIS PENILAIAN
    public function recapByType(Request $request)
    {
        $employeeId = auth()->user()->employee->id ?? 0;
        $schoolId = auth()->user()->school_id;

        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->first();

        $activeYearId = $activeYear ? $activeYear->id : 0;

        // 1. SIAPKAN DATA UNTUK DROPDOWN
        $classesData = [];

        $waliKelas = Classroom::where('homeroom_teacher_id', $employeeId)
            ->where('academic_year_id', $activeYearId)
            ->get();

        foreach ($waliKelas as $kelas) {
            $subjects = Subject::where('school_id', $schoolId)->where('tingkat', $kelas->tingkat)->where('pengampu', 'guru_kelas')->get();
            $classesData[$kelas->id] = [
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
                $classesData[$kelasId] = ['nama_kelas' => $mk->classroom->tingkat.' - '.$mk->classroom->nama_kelas, 'subjects' => []];
            }
            $classesData[$kelasId]['subjects'][] = ['id' => $mk->subject->id, 'nama' => $mk->subject->nama_mapel];
        }

        // 2. AMBIL DATA DAN HITUNG RATA-RATA & RANKING
        $students = collect();
        $subjects = collect();
        $averageScores = [];
        $usedTypesPerSubject = [];
        $studentAverages = []; // Array Rata-Rata Keseluruhan Siswa
        $studentRanks = [];    // Array Ranking Siswa

        if ($request->filled('classroom_id')) {
            $classroomId = $request->classroom_id;

            $students = Student::whereHas('classrooms', function ($q) use ($classroomId) {
                $q->where('classrooms.id', $classroomId);
            })->orderBy('nama_lengkap', 'asc')->get();

            // Kueri Penilaian - Filter berdasarkan Mapel jika di-request
            $queryAss = Assessment::where('classroom_id', $classroomId)
                ->with(['subject', 'assessmentType'])
                ->where(function ($query) {
                    $query->where('format', '!=', 'non-tes')
                        ->orWhereNull('format');
                });

            // Membatasi kueri jika dropdown Mapel tidak diset "Semua Mata Pelajaran"
            if ($request->filled('subject_id') && $request->subject_id !== 'all') {
                $queryAss->where('subject_id', $request->subject_id);
            }

            $assessments = $queryAss->get();
            $subjects = $assessments->pluck('subject')->unique('id')->sortBy('nama_mapel');

            if ($assessments->count() > 0) {
                $rawScores = AssessmentScore::whereIn('assessment_id', $assessments->pluck('id'))->get();
                $assessmentMap = $assessments->keyBy('id');

                $temp = [];

                foreach ($rawScores as $score) {
                    $ass = $assessmentMap[$score->assessment_id];
                    $subjId = $ass->subject_id;
                    $typeId = $ass->assessment_type_id;
                    $stuId = $score->student_id;

                    if ($ass->assessmentType) {
                        $usedTypesPerSubject[$subjId][$typeId] = $ass->assessmentType;
                    }

                    if (! isset($temp[$stuId][$subjId][$typeId])) {
                        $temp[$stuId][$subjId][$typeId] = ['total' => 0, 'count' => 0];
                    }
                    $temp[$stuId][$subjId][$typeId]['total'] += $score->score;
                    $temp[$stuId][$subjId][$typeId]['count'] += 1;
                }

                // Kalkulasi Rata-rata Jenis & Keseluruhan per Siswa
                // Kalkulasi Rata-rata Jenis & Keseluruhan per Siswa
                foreach ($students as $student) {
                    $stuId = $student->id;

                    // Siapkan variabel penampung untuk perhitungan bobot
                    $totalWeightedScore = 0;
                    $totalBobot = 0;

                    if (isset($temp[$stuId])) {
                        foreach ($temp[$stuId] as $subjId => $typeData) {
                            foreach ($typeData as $typeId => $data) {
                                // 1. Hitung rata-rata murni per jenis penilaian (Misal: Rata-rata UH)
                                $avg = round($data['total'] / $data['count'], 1);
                                $averageScores[$stuId][$subjId][$typeId] = $avg;

                                // 2. Ambil nilai bobot dari assessmentType yang bersangkutan (default 1 jika kosong)
                                $bobot = $usedTypesPerSubject[$subjId][$typeId]->bobot ?? 1;

                                // 3. Kalikan rata-rata jenis penilaian tersebut dengan bobotnya
                                $totalWeightedScore += ($avg * $bobot);

                                // 4. Kumpulkan total bobot yang digunakan
                                $totalBobot += $bobot;
                            }
                        }
                    }

                    // Rata-rata keseluruhan = Total Nilai Berbobot dibagi Total Bobot
                    $studentAverages[$stuId] = $totalBobot > 0 ? round($totalWeightedScore / $totalBobot, 1) : 0;
                }

                // Urutkan collection $students dari rata-rata tertinggi ke terendah
                $students = $students->sortByDesc(function ($student) use ($studentAverages) {
                    return $studentAverages[$student->id];
                })->values(); // reindex

                // Menentukan Ranking (dengan sistem peringkat padat untuk nilai seri)
                $currentRank = 1;
                $previousAvg = null;
                $actualPosition = 1;

                foreach ($students as $student) {
                    $avg = $studentAverages[$student->id];

                    if ($previousAvg !== null && $avg < $previousAvg) {
                        $currentRank = $actualPosition;
                    }

                    // Beri strip '-' jika nilainya 0 (kosong/belum ada nilai)
                    $studentRanks[$student->id] = $avg > 0 ? $currentRank : '-';

                    $previousAvg = $avg;
                    $actualPosition++;
                }
            }
        }

        return view('assessments.recap_types', compact('classesData', 'students', 'subjects', 'usedTypesPerSubject', 'averageScores', 'studentAverages', 'studentRanks'));
    }
}
