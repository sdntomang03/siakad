<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Histori Kelas (Dari kelas 1 sampai lulus)
    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class)->withTimestamps();
    }

    /**
     * FUNGSI BANTUAN SUPER CANGGIH
     * Mendapatkan data kelas siswa pada Tahun Ajaran yang sedang Aktif
     */
    public function kelasAktif()
    {
        return $this->classrooms()->whereHas('academicYear', function ($query) {
            $query->where('is_active', true);
        })->first();
    }

    public function family()
    {
        return $this->hasOne(StudentFamily::class);
    }

    public function address()
    {
        return $this->hasOne(StudentAddress::class);
    }

    public function financial()
    {
        return $this->hasOne(StudentFinancial::class);
    }

    public function health()
    {
        return $this->hasOne(StudentHealth::class);
    }

    // Tambahkan di dalam model Student.php
    public function assessmentScores()
    {
        return $this->hasMany(AssessmentScore::class);
    }

    public function notes()
    {
        // Mengurutkan dari catatan terbaru
        return $this->hasMany(TeacherNote::class)->latest();
    }

    public function bookLoans()
    {
        return $this->hasMany(BookLoan::class, 'student_id');
    }

    public function jadwalPikets()
    {
        return $this->hasMany(JadwalPiket::class);
    }

    // Relasi ke Jurnal Piket Harian
    public function jurnalPikets()
    {
        return $this->hasMany(JurnalPiket::class);
    }
}
