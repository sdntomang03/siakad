<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class Kelulusan extends Model
{
    use BelongsToSchool; // Pasang satpam otomatisnya di sini

    protected $fillable = [
        'nama', 'nisn', 'nipd', 'tanggal_lahir', 'kelas', 'keterangan', 'tempat_lahir', 'nomor_skl',
    ];
}
