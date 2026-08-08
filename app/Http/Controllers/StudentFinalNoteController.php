<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentCriteriaScore;
use App\Models\AssessmentNote;
use App\Models\AssessmentScore;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\JurnalPiket;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentFinalNote;
use App\Models\TeacherNote;
use Illuminate\Http\Request;

class StudentFinalNoteController extends Controller
{
    /**
     * Menampilkan daftar siswa dalam satu kelas untuk diisi catatan akhirnya
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        abort_if(! $user->hasAnyRole(['superadmin', 'operator', 'guru', 'kepsek']), 403, 'Akses ditolak.');

        $selectedSchoolId = $user->hasRole('superadmin') ? $request->query('school_id') : $user->school_id;
        $schools = $user->hasRole('superadmin') ? School::orderBy('nama_sekolah')->get() : collect();

        $activeYear = null;
        $allClassrooms = collect();
        $students = collect();
        $selectedClassroom = null;

        if ($selectedSchoolId) {
            $activeYear = AcademicYear::where('school_id', $selectedSchoolId)
                ->where('is_active', true)
                ->first();

            if ($activeYear) {
                $classroomQuery = Classroom::where('school_id', $selectedSchoolId)
                    ->where('academic_year_id', $activeYear->id);

                if ($user->hasRole('guru')) {
                    $employeeId = $user->employee->id ?? 0;
                    $classroomQuery->where('homeroom_teacher_id', $employeeId);
                }

                $allClassrooms = $classroomQuery->get();

                if ($request->classroom_id) {
                    $selectedClassroom = Classroom::with(['students' => function ($q) {
                        $q->orderBy('nama_lengkap', 'asc');
                    }])->find($request->classroom_id);

                    if ($selectedClassroom && $allClassrooms->contains('id', $selectedClassroom->id)) {
                        $students = $selectedClassroom->students;
                    }
                }
            }
        }

        return view('catatan_akhir.index', compact(
            'schools', 'selectedSchoolId', 'activeYear', 'allClassrooms', 'selectedClassroom', 'students'
        ));
    }

    /**
     * Menampilkan form pengisian catatan akhir per siswa dengan data REAL
     */
    public function edit($student_id, $classroom_id)
    {
        $student = Student::findOrFail($student_id);
        $classroom = Classroom::findOrFail($classroom_id);

        // 1. Ambil Tahun Ajaran Aktif berdasarkan KELASNYA (Sangat aman untuk Superadmin & Multi-Tenant)
        $activeYear = AcademicYear::where('school_id', $classroom->school_id)
            ->where('is_active', true)
            ->first();

        if (! $activeYear) {
            return back()->with('error', 'Tahun ajaran aktif belum diatur oleh Admin!');
        }

        $active_academic_year_id = $activeYear->id;

        // 2. Tarik Rekap Catatan Guru (Memastikan ID Tahun Ajaran Sama persis)
        $teacherNotes = TeacherNote::where('student_id', $student_id)
            ->where('academic_year_id', $active_academic_year_id)
            ->orderBy('tanggal', 'desc')
            ->get();

        // 3. Tarik Rekap Jurnal Piket
        $piketTerlaksana = JurnalPiket::where('student_id', $student_id)
            ->where('academic_year_id', $active_academic_year_id)
            ->where('status', 'terlaksana')
            ->count();

        $piketTidak = JurnalPiket::where('student_id', $student_id)
            ->where('academic_year_id', $active_academic_year_id)
            ->where('status', 'tidak_terlaksana')
            ->count();

        $catatanPiket = JurnalPiket::where('student_id', $student_id)
            ->where('academic_year_id', $active_academic_year_id)
            ->where('status', 'tidak_terlaksana')
            ->whereNotNull('catatan')
            ->where('catatan', '!=', '')
            ->get();

        // 4. Tarik Rekap Absensi (Direct Database Query agar akurat)
        $sakit = Attendance::where('student_id', $student_id)
            ->where('academic_year_id', $active_academic_year_id)
            ->where('status', 'sakit')
            ->count();

        $izin = Attendance::where('student_id', $student_id)
            ->where('academic_year_id', $active_academic_year_id)
            ->where('status', 'izin')
            ->count();

        $alpha = Attendance::where('student_id', $student_id)
            ->where('academic_year_id', $active_academic_year_id)
            ->where('status', 'alfa')
            ->count();

        // 5. Tarik Rekap Nilai TES (Rata-rata per Mapel)
        $tesAssessments = Assessment::where('academic_year_id', $active_academic_year_id)
            ->where('classroom_id', $classroom_id)
            ->where(function ($q) {
                $q->where('format', '!=', 'non-tes')->orWhereNull('format');
            })->with('subject')->get();

        $testScores = AssessmentScore::where('student_id', $student_id)
            ->whereIn('assessment_id', $tesAssessments->pluck('id'))->get();

        $rekapNilaiTesMap = [];
        foreach ($testScores as $score) {
            $ass = $tesAssessments->where('id', $score->assessment_id)->first();
            if ($ass && $ass->subject) {
                $mapel = $ass->subject->nama_mapel;
                if (! isset($rekapNilaiTesMap[$mapel])) {
                    $rekapNilaiTesMap[$mapel] = ['total' => 0, 'count' => 0];
                }
                $rekapNilaiTesMap[$mapel]['total'] += $score->score;
                $rekapNilaiTesMap[$mapel]['count']++;
            }
        }

        $rekapNilai = collect();
        foreach ($rekapNilaiTesMap as $mapel => $data) {
            $rekapNilai->push((object) [
                'nama_mapel' => $mapel,
                'nilai_akhir' => round($data['total'] / $data['count'], 1),
            ]);
        }

        // 6. Tarik Rekap Penilaian NON-TES / OBSERVASI (Predikat & Catatan)
        $nonTesAssessments = Assessment::where('academic_year_id', $active_academic_year_id)
            ->where('classroom_id', $classroom_id)
            ->where('format', 'non-tes')
            ->with('subject', 'criteria')->get();

        $nonTestScores = AssessmentCriteriaScore::where('student_id', $student_id)
            ->whereIn('assessment_criterion_id', $nonTesAssessments->pluck('criteria')->flatten()->pluck('id'))
            ->get();

        $nonTestNotes = AssessmentNote::where('student_id', $student_id)
            ->whereIn('assessment_id', $nonTesAssessments->pluck('id'))
            ->get();

        $rekapObservasi = collect();
        foreach ($nonTesAssessments as $ass) {
            $mapel = $ass->subject->nama_mapel ?? 'Mata Pelajaran';
            $keterangan = $ass->keterangan;
            $note = $nonTestNotes->where('assessment_id', $ass->id)->first();

            $criteriaIds = $ass->criteria->pluck('id');
            $scores = $nonTestScores->whereIn('assessment_criterion_id', $criteriaIds);

            if ($scores->count() > 0) {
                $avg = $scores->sum('score') / $scores->count();
                $scale = $ass->scale ?? 4;
                $persentase = ($avg / $scale) * 100;

                $predikat = 'Perlu Bimbingan';
                if ($persentase >= 85) {
                    $predikat = 'Sangat Baik';
                } elseif ($persentase >= 70) {
                    $predikat = 'Baik';
                } elseif ($persentase >= 55) {
                    $predikat = 'Cukup';
                }

                $rekapObservasi->push((object) [
                    'nama_mapel' => $mapel,
                    'kegiatan' => $keterangan,
                    'predikat' => $predikat,
                    'catatan' => $note ? $note->catatan : '-',
                ]);
            }
        }

        // 7. Cek Riwayat Final Note Sebelumnya
        $finalNote = StudentFinalNote::where('student_id', $student_id)
            ->where('classroom_id', $classroom_id)
            ->where('academic_year_id', $active_academic_year_id)
            ->first();

        return view('catatan_akhir.edit', compact(
            'student', 'classroom', 'teacherNotes',
            'piketTerlaksana', 'piketTidak', 'catatanPiket',
            'sakit', 'izin', 'alpha', 'finalNote', 'active_academic_year_id',
            'rekapNilai', 'rekapObservasi'
        ));
    }

    /**
     * Memproses penyimpanan catatan akhir
     */
    public function update(Request $request, $student_id, $classroom_id)
    {
        $request->validate([
            'academic_year_id' => 'required',
            'catatan_akhir' => 'required|string',
            'sakit' => 'required|integer|min:0',
            'izin' => 'required|integer|min:0',
            'alpha' => 'required|integer|min:0',
        ]);

        $employee_id = auth()->user()->employee->id ?? null;

        StudentFinalNote::updateOrCreate(
            [
                'student_id' => $student_id,
                'classroom_id' => $classroom_id,
                'academic_year_id' => $request->academic_year_id,
            ],
            [
                'employee_id' => $employee_id,
                'sakit' => $request->sakit,
                'izin' => $request->izin,
                'alpha' => $request->alpha,
                'piket_terlaksana' => $request->piket_terlaksana,
                'piket_tidak_terlaksana' => $request->piket_tidak_terlaksana,
                'ringkasan_catatan_guru' => $request->ringkasan_catatan_guru,
                'catatan_akhir' => $request->catatan_akhir,
            ]
        );

        return back()->with('success', 'Catatan Akhir Siswa berhasil disimpan!');
    }
}
