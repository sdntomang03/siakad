<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rhk extends Model
{
    protected $table = 'rhk';

    public function rencanaAksi()
    {
        return $this->hasMany(RencanaAksi::class, 'rhk_id');
    }
}
