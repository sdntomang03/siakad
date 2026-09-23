<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutputTarget extends Model
{
    protected $table = 'output_target';

    protected $fillable = [
        'user_id',
        'rencana_aksi_id',
        'deskripsi_output',
        'target_waktu',
        'tahun',
    ];

    public function buktiDukung()
    {
        return $this->hasMany(BuktiDukung::class, 'output_target_id');
    }

    public function rencanaAksi()
    {
        return $this->belongsTo(RencanaAksi::class, 'rencana_aksi_id');
    }

    public function realisasi()
    {
        return $this->hasMany(EtppRealisasi::class, 'output_target_id');
    }
}
