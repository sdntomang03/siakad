<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use BelongsToSchool;

    protected $guarded = ['id'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teacherNotes()
    {
        return $this->hasMany(TeacherNote::class, 'employee_id');
    }
}
