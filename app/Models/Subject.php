<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_sidanira' => 'boolean', // Memastikan nilainya dikembalikan sebagai true/false
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
