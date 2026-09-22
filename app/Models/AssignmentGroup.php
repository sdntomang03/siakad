<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentGroup extends Model
{
    protected $guarded = ['id'];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function leader()
    {
        return $this->belongsTo(Student::class, 'leader_student_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'assignment_group_student')->withTimestamps();
    }

    public function selectedTask()
    {
        return $this->hasOne(AssignmentTask::class, 'selected_by_group_id');
    }
}
