<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentNote extends Model
{
    protected $fillable = ['assessment_id', 'student_id', 'catatan'];
}
