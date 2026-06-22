<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportSubmission extends Model
{
    // Pastikan academic_year_id ada di sini!
    protected $fillable = [
        'school_id',
        'student_id',
        'classroom_id',
        'academic_year_id',
        'posisi',
        'waktu_dibagikan',
        'waktu_dikembalikan',
        'notes',
    ];

    // Jika kamu menggunakan $guarded kosong, pastikan penulisannya seperti ini:
    // protected $guarded = [];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}
