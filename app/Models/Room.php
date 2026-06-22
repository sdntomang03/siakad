<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use BelongsToSchool;

    protected $fillable = ['school_id', 'nama_ruangan', 'deskripsi'];

    public function placements()
    {
        return $this->hasMany(AssetPlacement::class);
    }
}
