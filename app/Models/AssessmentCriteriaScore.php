<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentCriteriaScore extends Model
{
    protected $fillable = ['assessment_id', 'assessment_criterion_id', 'student_id', 'score'];
}
