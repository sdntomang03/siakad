<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherNote extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'foto' => 'array', // Otomatis handle JSON ke Array
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
