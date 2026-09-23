<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EtppRealisasi extends Model
{
    protected $table = 'etpp_realisasi';

    protected $fillable = [
        'user_id',
        'output_target_id',
        'nama_output',
        'triwulan',
        'tahun',
        'bulan',
        'realisasi',
        'link_referensi',
    ];

    public function outputTarget()
    {
        return $this->belongsTo(OutputTarget::class, 'output_target_id');
    }
}
