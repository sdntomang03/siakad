<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'student_id', 'classroom_id', 'period', 'is_submitted', 'submitted_at', 'notes', 'is_returned', 'returned_at', 'location',
    ];

    protected $casts = [
        'is_submitted' => 'boolean',
        'is_returned' => 'boolean',
    ];

    protected $dates = ['submitted_at', 'returned_at'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
