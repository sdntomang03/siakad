<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Extracurricular extends Model
{
    protected $fillable = [
        'school_id',
        'nama_ekskul',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'extracurricular_student')->withTimestamps();
    }
}
