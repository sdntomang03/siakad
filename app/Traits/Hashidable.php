<?php

namespace App\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait Hashidable
{
    /**
     * Encode ID secara otomatis saat membuat URL (misal: route('classrooms.show', $classroom))
     */
    public function getRouteKey()
    {
        return Hashids::encode($this->getKey());
    }

    /**
     * Decode kode unik kembali menjadi ID saat menangkap parameter dari URL
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Decode string menjadi array. Jika gagal/tidak valid, hasilnya array kosong
        $decoded = Hashids::decode($value);

        // Ambil ID dari index pertama array
        $id = $decoded[0] ?? null;

        return $this->where($field ?? $this->getRouteKeyName(), $id)->firstOrFail();
    }
}
