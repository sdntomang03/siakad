<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFamily extends Model
{
    protected $guarded = []; // Izinkan mass assignment

    // Balikkan relasi ke Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
