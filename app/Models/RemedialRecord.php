<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemedialRecord extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'average_score' => 'float',
        'remedial_score' => 'float',
        'is_completed' => 'boolean',
    ];

    public function program()
    {
        return $this->belongsTo(RemedialProgram::class, 'remedial_program_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
