<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';

    public function rhk()
    {
        return $this->hasMany(Rhk::class, 'kategori_id');
    }
}
