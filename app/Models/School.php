<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $guarded = ['id']; // Melindungi kolom ID agar tidak sembarang diisi

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Mengambil profil Kepala Sekolah untuk urusan cetak dokumen / laporan.
     */
    public function kepalaSekolah()
    {
        return $this->hasOne(Employee::class)->where('tugas_tambahan', 'Kepala Sekolah');
    }
}
