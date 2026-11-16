<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemedialProgram extends Model
{
    protected $guarded = ['id'];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function assessments()
    {
        return $this->belongsToMany(Assessment::class, 'remedial_program_assessments')
            ->withTimestamps();
    }

    public function records()
    {
        return $this->hasMany(RemedialRecord::class)->orderBy('id');
    }
}
