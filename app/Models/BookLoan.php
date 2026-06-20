<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'student_id', 'book_id', 'book_title', 'notes', 'borrowed_at', 'due_at', 'returned_at',
    ];

    protected $dates = ['borrowed_at', 'due_at', 'returned_at'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
