<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuktiDukung extends Model
{
    protected $table = 'bukti_dukung';

    // Izinkan mass-assignment untuk kolom-kolom ini
    protected $fillable = [
        'user_id',
        'output_target_id',
        'nama_bukti',
        'file_path',
    ];

    // Relasi balik ke Output Target
    public function outputTarget()
    {
        return $this->belongsTo(OutputTarget::class, 'output_target_id');
    }

    // Relasi balik ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
