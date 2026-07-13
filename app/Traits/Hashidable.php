<?php

namespace App\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait Hashidable
{
    /**
     * Encode otomatis untuk Route Model Binding (misal: /classrooms/{classroom})
     */
    public function getRouteKey()
    {
        return Hashids::encode($this->getKey());
    }

    /**
     * Decode otomatis saat menangkap dari Route Model Binding
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $decoded = Hashids::decode($value);
        $id = $decoded[0] ?? null;

        return $this->where($field ?? $this->getRouteKeyName(), $id)->firstOrFail();
    }

    /**
     * Akses manual ID yang sudah ter-encode (Dipanggil dengan: $model->hashid)
     * Tambahkan fungsi ini!
     */
    public function getHashidAttribute()
    {
        return Hashids::encode($this->getKey());
    }
}
