<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutputTarget extends Model
{
    protected $table = 'output_target';

    public function buktiDukung()
    {
        return $this->hasMany(BuktiDukung::class, 'output_target_id');
    }
}
