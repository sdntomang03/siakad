<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RencanaAksi extends Model
{
    protected $table = 'rencana_aksi';

    public function outputTarget()
    {
        return $this->hasMany(OutputTarget::class, 'rencana_aksi_id');
    }

    public function rhk()
    {
        return $this->belongsTo(Rhk::class, 'rhk_id');
    }
}
