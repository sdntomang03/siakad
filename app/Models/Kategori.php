<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rhk()
    {

        return $this->hasMany(Rhk::class, 'kategori_id');
    }
}
