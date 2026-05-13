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
        static::addGlobalScope('school', function (Builder $builder) {
            // Cek apakah ada user yang login
            if (auth()->check()) {
                $user = auth()->user();

                // Jika user BUKAN superadmin, batasi data sesuai school_id miliknya
                if (! $user->hasRole('superadmin')) {
                    $builder->where('school_id', $user->school_id);
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
