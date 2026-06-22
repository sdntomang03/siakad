<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetPlacement extends Model
{
    protected $fillable = ['asset_id', 'classroom_id', 'room_id', 'jumlah', 'kondisi', 'keterangan'];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
