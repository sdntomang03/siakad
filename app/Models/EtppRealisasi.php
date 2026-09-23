<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EtppRealisasi extends Model
{
    protected $table = 'etpp_realisasi';

    protected $fillable = [
        'user_id',
        'output_target_id',
        'triwulan',
        'tahun',
        'realisasi',
    ];

    public function outputTarget()
    {
        return $this->belongsTo(OutputTarget::class, 'output_target_id');
    }
}
