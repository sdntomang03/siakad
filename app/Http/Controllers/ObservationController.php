<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentCriteriaScore;
use App\Models\AssessmentCriterion;
use App\Models\AssessmentNote;
use App\Models\AssessmentType;
use App\Models\Classroom;
use App\Models\ClassroomSubject;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class ObservationController extends Controller
{
    // TAHAP 1: FORM BUAT PENILAIAN NON-TES & DESKRIPTOR
    public function create()
    {
        $employeeId = auth()->user()->employee->id ?? 0;
        $schoolId = auth()->user()->school_id;

        // Ambil ID Tahun Ajaran secara dinamis
        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->first();

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

            if (! isset($classesData[$kelasId])) {
                $classesData[$kelasId] = [
                    'nama_kelas' => $mk->classroom->tingkat.' - '.$mk->classroom->nama_kelas,
                    'subjects' => [],
                ];
            }
            $classesData[$kelasId]['subjects'][] = [
                'id' => $mk->subject->id,
                'nama' => $mk->subject->nama_mapel,
            ];
        }

        $assessmentTypes = AssessmentType::where('school_id', $schoolId)->get();

        return view('observations.create', compact('classesData', 'assessmentTypes'));
    }

    // TAHAP 2: SIMPAN NAMA, SKALA, DAN DESKRIPTOR KRITERIA
    public function store(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required',
            'subject_id' => 'required',
            'keterangan' => 'required',
            'tanggal' => 'required|date',
            'scale' => 'required|in:1,2,3,4,5',
            'descriptors' => 'required|array|min:1',
            'descriptors.*' => 'required|string|max:255',
        ]);

        $activeYear = AcademicYear::where('school_id', auth()->user()->school_id)
            ->where('is_active', true)
            ->first();

        // 1. Simpan Header Penilaian (Format Non-Tes)
        $assessment = Assessment::create([
            'school_id' => auth()->user()->school_id,
            'academic_year_id' => $activeYear->id,
            'classroom_id' => $request->classroom_id,
            'subject_id' => $request->subject_id,
            'employee_id' => auth()->user()->employee->id ?? 0,
            'assessment_type_id' => $request->assessment_type_id,
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal,
            'format' => 'non-tes',
            'scale' => $request->scale,
        ]);

        // 2. Simpan Kriteria Deskriptor
        foreach ($request->descriptors as $desc) {
            AssessmentCriterion::create([
                'assessment_id' => $assessment->id,
                'descriptor' => $desc,
            ]);
        }

        // Lempar ke halaman input
        return redirect()->route('observations.input', $assessment->id);
    }

    // TAHAP 3: MENAMPILKAN MATRIKS INPUT PENILAIAN OBSERVASI
    public function input(Assessment $assessment)
    {
        $assessment->load(['classroom', 'subject', 'criteria']);

        $students = Student::whereHas('classrooms', function ($query) use ($assessment) {
            $query->where('classrooms.id', $assessment->classroom_id);
        })->orderBy('nama_lengkap', 'asc')->get();

        // Ambil nilai existing jika ada
        $rawScores = AssessmentCriteriaScore::where('assessment_id', $assessment->id)->get();
        $existingScores = [];
        foreach ($rawScores as $score) {
            $existingScores[$score->student_id][$score->assessment_criterion_id] = $score->score;
        }

        // === TAMBAHAN UNTUK MEMANGGIL DATA CATATAN ===
        $rawNotes = AssessmentNote::where('assessment_id', $assessment->id)->get();
        $existingNotes = $rawNotes->pluck('catatan', 'student_id')->toArray();

        // Jangan lupa tambahkan $existingNotes ke dalam compact()
        return view('observations.input', compact('assessment', 'students', 'existingScores', 'existingNotes'));
    }

    // TAHAP 4: SIMPAN HASIL PENILAIAN OBSERVASI
    // TAHAP 4: SIMPAN HASIL PENILAIAN OBSERVASI
    public function updateScores(Request $request, Assessment $assessment)
    {
        $request->validate([
            'scores' => 'required|array',
            'notes' => 'nullable|array',
            'criteria' => 'nullable|array', // Validasi input kriteria baru
            'criteria.*' => 'required|string|max:255',
        ]);

        // 0. Update Teks Kriteria (Jika ada editan)
        if ($request->has('criteria')) {
            foreach ($request->criteria as $criterionId => $newDescriptor) {
                AssessmentCriterion::where('id', $criterionId)
                    ->where('assessment_id', $assessment->id)
                    ->update(['descriptor' => $newDescriptor]);
            }
        }

        // 1. Simpan dan Hapus Skor Matriks (Kriteria)
        foreach ($request->scores as $studentId => $criteriaScores) {
            foreach ($criteriaScores as $criterionId => $scoreValue) {
                if ($scoreValue !== null && trim($scoreValue) !== '') {
                    // Jika ada nilainya, update atau create[cite: 27]
                    AssessmentCriteriaScore::updateOrCreate(
                        [
                            'assessment_id' => $assessment->id,
                            'student_id' => $studentId,
                            'assessment_criterion_id' => $criterionId,
                        ],
                        ['score' => $scoreValue]
                    );
                } else {
                    // JIKA NILAI DIKOSONGKAN/DIHAPUS, hapus record dari database
                    AssessmentCriteriaScore::where('assessment_id', $assessment->id)
                        ->where('student_id', $studentId)
                        ->where('assessment_criterion_id', $criterionId)
                        ->delete();
                }
            }
        }

        // 2. Simpan Catatan (Baru)
        if ($request->has('notes')) {
            foreach ($request->notes as $studentId => $noteText) {
                if (! empty(trim($noteText))) {
                    AssessmentNote::updateOrCreate(
                        [
                            'assessment_id' => $assessment->id,
                            'student_id' => $studentId,
                        ],
                        ['catatan' => $noteText]
                    );
                } else {
                    // Hapus record catatan jika dikosongkan[cite: 27]
                    AssessmentNote::where('assessment_id', $assessment->id)
                        ->where('student_id', $studentId)
                        ->delete();
                }
            }
        }

        return redirect()->route('assessments.index')->with('success', 'Nilai, Catatan, dan Kriteria observasi berhasil disimpan!');
    }

    public function showReport(Assessment $assessment)
    {
        // Pastikan ini adalah format observasi
        if ($assessment->format !== 'non-tes') {
            return redirect()->route('assessments.index')->with('error', 'Format tidak valid.');
        }

        $assessment->load(['classroom', 'subject', 'criteria']);

        // Ambil daftar siswa di kelas tersebut
        $students = Student::whereHas('classrooms', function ($query) use ($assessment) {
            $query->where('classrooms.id', $assessment->classroom_id);
        })->orderBy('nama_lengkap', 'asc')->get();

        // Ambil semua nilai yang sudah diinput untuk observasi ini
        $rawScores = AssessmentCriteriaScore::where('assessment_id', $assessment->id)->get();

        // === TAMBAHAN: Ambil data catatan ===
        $rawNotes = AssessmentNote::where('assessment_id', $assessment->id)->get();
        $existingNotes = $rawNotes->pluck('catatan', 'student_id')->toArray();

        $reportData = [];

        foreach ($students as $siswa) {
            $siswaScores = $rawScores->where('student_id', $siswa->id);

            $totalScore = 0;
            $criteriaCount = 0;
            $scoresByCriteria = [];

            foreach ($assessment->criteria as $kriteria) {
                $scoreRecord = $siswaScores->where('assessment_criterion_id', $kriteria->id)->first();
                $nilai = $scoreRecord ? $scoreRecord->score : null;

                $scoresByCriteria[$kriteria->id] = $nilai;

                if ($nilai !== null) {
                    $totalScore += $nilai;
                    $criteriaCount++;
                }
            }

            // Hitung rata-rata
            $average = $criteriaCount > 0 ? round($totalScore / $criteriaCount, 2) : 0;

            // Tentukan Predikat berdasarkan skala (Contoh untuk Skala 1-4)
            $predikat = '-';
            if ($criteriaCount > 0) {
                $persentase = ($average / $assessment->scale) * 100;
                if ($persentase >= 85) {
                    $predikat = 'Sangat Baik';
                } elseif ($persentase >= 70) {
                    $predikat = 'Baik';
                } elseif ($persentase >= 55) {
                    $predikat = 'Cukup';
                } else {
                    $predikat = 'Perlu Bimbingan';
                }
            }

            $reportData[$siswa->id] = [
                'scores' => $scoresByCriteria,
                'average' => $average,
                'predikat' => $predikat,
                'is_assessed' => $criteriaCount > 0,
                // === TAMBAHAN: Masukkan catatan ke dalam data siswa ===
                'catatan' => $existingNotes[$siswa->id] ?? '-',
            ];
        }

        return view('observations.report', compact('assessment', 'students', 'reportData'));
    }

    // TAHAP 5: EXPORT EXCEL (CSV FORMAT)
    public function exportExcel(Assessment $assessment)
    {
        // Pastikan ini adalah format observasi[cite: 1]
        if ($assessment->format !== 'non-tes') {
            return redirect()->route('assessments.index')->with('error', 'Format tidak valid.');
        }

        $assessment->load(['classroom', 'subject', 'criteria']);

        // Ambil daftar siswa di kelas tersebut[cite: 1]
        $students = Student::whereHas('classrooms', function ($query) use ($assessment) {
            $query->where('classrooms.id', $assessment->classroom_id);
        })->orderBy('nama_lengkap', 'asc')->get();

        // Ambil semua nilai dan catatan yang sudah diinput[cite: 1]
        $rawScores = AssessmentCriteriaScore::where('assessment_id', $assessment->id)->get();
        $rawNotes = AssessmentNote::where('assessment_id', $assessment->id)->get();
        $existingNotes = $rawNotes->pluck('catatan', 'student_id')->toArray();

        // Siapkan nama file
        $fileName = 'Export_Observasi_'.str_replace(' ', '_', $assessment->classroom->nama_kelas).'_'.date('Y-m-d').'.csv';

        // Header HTTP untuk memaksa download
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // Buat baris header kolom Excel
        $columns = ['No', 'Nama Siswa'];
        foreach ($assessment->criteria as $index => $kriteria) {
            $columns[] = 'K'.($index + 1).' ('.$kriteria->descriptor.')';
        }
        $columns[] = 'Rata-rata';
        $columns[] = 'Predikat';
        $columns[] = 'Catatan AI/Tambahan';

        // Eksekusi penulisan ke output stream
        $callback = function () use ($students, $assessment, $rawScores, $existingNotes, $columns) {
            $file = fopen('php://output', 'w');

            // Tulis UTF-8 BOM agar Excel bisa membaca karakter khusus/spasi dengan benar
            fwrite($file, $bom = (chr(0xEF).chr(0xBB).chr(0xBF)));

            fputcsv($file, $columns, ';'); // Gunakan separator ';' agar rapi di Excel format Indonesia

            $no = 1;
            foreach ($students as $siswa) {
                $row = [
                    $no++,
                    $siswa->nama_lengkap,
                ];

                $siswaScores = $rawScores->where('student_id', $siswa->id);
                $totalScore = 0;
                $criteriaCount = 0;

                // Hitung skor per kriteria[cite: 1]
                foreach ($assessment->criteria as $kriteria) {
                    $scoreRecord = $siswaScores->where('assessment_criterion_id', $kriteria->id)->first();
                    $nilai = $scoreRecord ? $scoreRecord->score : '';
                    $row[] = $nilai;

                    if ($nilai !== '') {
                        $totalScore += $nilai;
                        $criteriaCount++;
                    }
                }

                // Kalkulasi Rata-rata dan Predikat[cite: 1]
                $average = $criteriaCount > 0 ? round($totalScore / $criteriaCount, 2) : 0;
                $predikat = '-';
                if ($criteriaCount > 0) {
                    $persentase = ($average / $assessment->scale) * 100;
                    if ($persentase >= 85) {
                        $predikat = 'Sangat Baik';
                    } elseif ($persentase >= 70) {
                        $predikat = 'Baik';
                    } elseif ($persentase >= 55) {
                        $predikat = 'Cukup';
                    } else {
                        $predikat = 'Perlu Bimbingan';
                    }
                }

                $row[] = $average;
                $row[] = $predikat;
                $row[] = $existingNotes[$siswa->id] ?? '-';

                fputcsv($file, $row, ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
