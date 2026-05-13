<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'student_id',
        'score',
    ];

    protected $casts = [
        'score' => 'float', // Memastikan nilai dibaca sebagai angka desimal, bukan string
    ];

    // Relasi balik ke Wadah Penilaian
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    // Relasi ke tabel Siswa
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
