<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capaian_pembelajarans', function (Blueprint $table) {
            $table->id();
            // Kita simpan ID acak dari JSON (misal: cp_mrurmwuiw3uby) sebagai kode_cp unik
            $table->string('kode_cp')->unique();
            $table->string('mata_pelajaran');
            $table->string('fase');
            $table->string('elemen');
            $table->text('deskripsi_cp');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capaian_pembelajarans');
    }
};
