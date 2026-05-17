<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    protected $guarded = ['id']; // Melindungi kolom ID agar tidak sembarang diisi

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function teacherNotes()
    {
        return $this->hasMany(TeacherNote::class);
    }

    /**
     * Mengambil profil Kepala Sekolah untuk urusan cetak dokumen / laporan.
     */
    public function kepalaSekolah()
    {
        return $this->hasOne(Employee::class)->where('tugas_tambahan', 'Kepala Sekolah');
    }

    public function activeAcademicYear()
    {
        return $this->academicYears()->where('is_active', true)->first();
    }
}
