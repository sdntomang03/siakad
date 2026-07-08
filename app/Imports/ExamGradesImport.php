<?php

namespace App\Imports;

use App\Models\ExamGrade;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExamGradesImport implements ToCollection, WithHeadingRow
{
    protected $schoolId;

    protected $kategoriUjian;

    protected $tingkatKelas;

    protected $semester;

    protected $subjects;

    protected $studentsMap;

    public function __construct($schoolId, $kategoriUjian, $tingkatKelas, $semester)
    {
        $this->schoolId = $schoolId;
        $this->kategoriUjian = $kategoriUjian;
        $this->tingkatKelas = $tingkatKelas;
        $this->semester = $semester;

        // Ambil mapel berdasarkan sekolah & tingkat (gunakan is_sidanira jika perlu difilter)
        $this->subjects = Subject::where('school_id', $this->schoolId)
            ->where('tingkat', $this->tingkatKelas)
            ->whereNotNull('kode_mapel')
            ->get()
            ->keyBy(function ($subject) {
                return Str::slug($subject->kode_mapel, '_');
            });

        // Mapping NISN ke ID Siswa untuk efisiensi query
        $this->studentsMap = Student::where('school_id', $this->schoolId)
            ->whereNotNull('nisn')
            ->pluck('id', 'nisn')
            ->toArray();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['nisn'])) {
                continue;
            }

            $nisn = (string) $row['nisn'];

            // Lewati jika NISN tidak ada di database
            if (! array_key_exists($nisn, $this->studentsMap)) {
                continue;
            }

            $studentId = $this->studentsMap[$nisn];

            // Looping per mapel di file Excel
            foreach ($this->subjects as $headerKey => $subject) {

                // Pastikan sel berisi angka
                if (isset($row[$headerKey]) && is_numeric($row[$headerKey])) {

                    ExamGrade::updateOrCreate(
                        [
                            'school_id' => $this->schoolId,
                            'student_id' => $studentId,
                            'subject_id' => $subject->id,
                            'kategori_ujian' => $this->kategoriUjian,
                            'tingkat_kelas' => $this->tingkatKelas,
                            'semester' => $this->semester,
                        ],
                        [
                            'nilai' => $row[$headerKey],
                        ]
                    );

                }
            }
        }
    }
}
