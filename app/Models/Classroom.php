<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use App\Traits\Hashidable;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use BelongsToSchool, Hashidable;

    protected $guarded = ['id'];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function homeroomTeacher()
    {
        return $this->belongsTo(Employee::class, 'homeroom_teacher_id');
    }

    // Relasi ke banyak Siswa
    public function students()
    {
        return $this->belongsToMany(Student::class)->withTimestamps();
    }

    // Tambahkan ini di dalam model Classroom
    public function subjectTeachers()
    {
        return $this->hasMany(ClassroomSubject::class);
    }
}
