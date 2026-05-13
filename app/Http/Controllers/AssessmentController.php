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

        // Asumsi Anda punya cara ambil Tahun Ajaran Aktif. Misal ID-nya 1:
        $activeYearId = 1; // Ganti dengan logika ActiveYear Anda
        $schoolId = auth()->user()->school_id;

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

        $assessment = Assessment::create([
            'school_id' => auth()->user()->school_id,
            'academic_year_id' => 1, // Ganti ke ActiveYear ID Anda
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

        // PERBAIKAN: Ambil ID Tahun Ajaran secara dinamis (Otomatis melacak yang aktif)
        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->first();

        // Jika tidak ada tahun ajaran aktif, fallback ke 0 untuk mencegah error
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

        // 2. JIKA GURU SUDAH MEMILIH KELAS & MAPEL, AMBIL DATA NILAINYA
        $students = collect();
        $assessments = collect();
        $matrixScores = [];
        $assessmentTypes = AssessmentType::where('school_id', $schoolId)->get();

        if ($request->filled('classroom_id') && $request->filled('subject_id')) {

            // Kueri dasar untuk mengambil penilaian
            $queryUjian = Assessment::where('classroom_id', $request->classroom_id)
                ->where('subject_id', $request->subject_id);

            // Jika filter Jenis Penilaian dipilih, tambahkan ke kueri
            if ($request->filled('assessment_type_id')) {
                $queryUjian->where('assessment_type_id', $request->assessment_type_id);
            }

            // Eksekusi kueri
            $assessments = $queryUjian->orderBy('tanggal', 'asc')->get();

            // Ambil daftar siswa di kelas tersebut (baris tabel)
            $students = Student::whereHas('classrooms', function ($q) use ($request) {
                $q->where('classrooms.id', $request->classroom_id);
            })->orderBy('nama_lengkap', 'asc')->get();

            // Ambil nilainya jika ada penilaian yang sesuai
            if ($assessments->count() > 0) {
                $rawScores = AssessmentScore::whereIn('assessment_id', $assessments->pluck('id'))->get();
                foreach ($rawScores as $score) {
                    $matrixScores[$score->student_id][$score->assessment_id] = $score->score;
                }
            }
        }

        return view('assessments.recap', compact('classesData', 'students', 'assessments', 'matrixScores', 'assessmentTypes'));
    }
}
