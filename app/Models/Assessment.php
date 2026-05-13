<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relasi ke tabel Nilai Siswa (1 Penilaian punya Banyak Nilai)
    public function scores()
    {
        return $this->hasMany(AssessmentScore::class);
    }

    // --- RELASI KE TABEL MASTER ---

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // Guru yang membuat penilaian ini
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function assessmentType()
    {

        return $this->belongsTo(AssessmentType::class, 'assessment_type_id');
    }
}
