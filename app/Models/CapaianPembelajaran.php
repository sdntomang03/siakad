<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapaianPembelajaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_cp',
        'mata_pelajaran',
        'fase',
        'elemen',
        'deskripsi_cp',
    ];
}
