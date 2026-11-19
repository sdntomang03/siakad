<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'school_id',
        'user_id',
        'classroom_id',
        'recipient_student_id',
        'audience',
        'title',
        'body',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function recipient()
    {
        return $this->belongsTo(Student::class, 'recipient_student_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'announcement_student')->withPivot('body');
    }
}
