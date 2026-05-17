<?php

namespace App\Traits;

use App\Models\School;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToSchool
{
    /**
     * Boot the trait (dijalankan otomatis oleh Laravel saat model dipanggil).
     */
    protected static function bootBelongsToSchool()
    {
        // --- 1. FITUR AUTO-FILTER (SAAT MEMBACA DATA) ---
        static::addGlobalScope('school', function (Builder $builder) {
            // Cek apakah ada user yang login
            if (auth()->check()) {
                $user = auth()->user();

                // Jika user BUKAN superadmin, batasi data sesuai school_id miliknya
                if (! $user->hasRole('superadmin')) {
                    // MENGHINDARI AMBIGU: Gunakan nama tabel secara dinamis
                    $tableName = $builder->getModel()->getTable();
                    $builder->where($tableName.'.school_id', $user->school_id);
                }
            }
        });

        // --- 2. FITUR AUTO-SET (SAAT MENYIMPAN DATA BARU) ---
        static::creating(function ($model) {
            if (auth()->check()) {
                $user = auth()->user();

                // Jika user BUKAN superadmin dan school_id masih kosong,
                // maka isikan otomatis dengan school_id milik user yang login.
                if (! $user->hasRole('superadmin') && empty($model->school_id)) {
                    $model->school_id = $user->school_id;
                }
            }
        });
    }

    /**
     * Tambahkan relasi otomatis ke tabel School.
     * Karena semua model yang memakai trait ini pasti punya school_id.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
