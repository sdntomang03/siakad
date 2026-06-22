<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = ['school_id', 'nama_aset', 'kode_aset', 'status_persetujuan', 'diajukan_oleh', 'total_stok'];

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function placements()
    {
        return $this->hasMany(AssetPlacement::class);
    }
}
