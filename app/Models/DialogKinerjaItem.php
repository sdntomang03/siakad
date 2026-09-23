<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DialogKinerjaItem extends Model
{
    protected $fillable = [
        'dialog_kinerja_id',
        'output_target_id',
        'nama_output',
        'uraian',
        'link_referensi',
        'urutan',
    ];

    public function dialogKinerja()
    {
        return $this->belongsTo(DialogKinerja::class);
    }

    public function outputTarget()
    {
        return $this->belongsTo(OutputTarget::class);
    }
}
