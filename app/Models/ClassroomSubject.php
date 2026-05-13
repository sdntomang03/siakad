<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomSubject extends Model
{
    protected $guarded = ['id'];

    // Relasi ke tabel Kelas
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    // Relasi ke tabel Mapel
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // Relasi ke tabel Pegawai (Guru)
    public function teacher()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
