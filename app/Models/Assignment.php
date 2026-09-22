<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'due_at' => 'datetime',
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function tasks()
    {
        return $this->hasMany(AssignmentTask::class);
    }

    public function groups()
    {
        return $this->hasMany(AssignmentGroup::class);
    }
}
