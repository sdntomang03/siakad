<?php

namespace App\Imports;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GradesImport implements ToCollection, WithHeadingRow
{
    protected $schoolId;

    protected $tingkatKelas;

    protected $semester;

    protected $subjects;

    protected $studentsMap;

    public function __construct($schoolId, $tingkatKelas, $semester)
    {
        $this->schoolId = $schoolId;
        $this->tingkatKelas = $tingkatKelas;
        $this->semester = $semester;

        // Ambil mapel KHUSUS untuk tingkat kelas yang dipilih saat import
        $this->subjects = Subject::where('school_id', $this->schoolId)
            ->where('tingkat', $this->tingkatKelas)
            ->whereNotNull('kode_mapel')
            ->get()
            ->keyBy(function ($subject) {
                return Str::slug($subject->kode_mapel, '_');
            });

        // Ambil siswa untuk sekolah ini dan petakan NISN ke ID
        $this->studentsMap = Student::where('school_id', $this->schoolId)
            ->whereNotNull('nisn')
            ->pluck('id', 'nisn')
            ->toArray();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            // Lewati jika kolom nisn di excel kosong
            if (empty($row['nisn'])) {
                continue;
            }

            $nisn = (string) $row['nisn'];

            // Lewati jika NISN dari excel tidak ada di database (mencegah error 1452)
            if (! array_key_exists($nisn, $this->studentsMap)) {
                continue;
            }

            $studentId = $this->studentsMap[$nisn];

            // Looping untuk memproses setiap kolom mapel di Excel
            foreach ($this->subjects as $headerKey => $subject) {

                // Cek jika kolom ada dan isinya angka
                if (isset($row[$headerKey]) && is_numeric($row[$headerKey])) {

                    Grade::updateOrCreate(
                        [
                            'school_id' => $this->schoolId,
                            'student_id' => $studentId,
                            'subject_id' => $subject->id, // Menggunakan ID Mapel sesuai Tingkat
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
