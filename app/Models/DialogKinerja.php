<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DialogKinerja extends Model
{
    protected $table = 'dialog_kinerja';

    protected $fillable = [
        'user_id',
        'tahun',
        'bulan',
        'uraian',
        'link_referensi',
    ];

    public function items()
    {
        return $this->hasMany(DialogKinerjaItem::class);
    }
}
