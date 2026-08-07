<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rencana_aksi', function (Blueprint $table) {
            $table->id();
            // Foreign key ke tabel rhk
            $table->foreignId('rhk_id')->constrained('rhk')->onDelete('cascade');
            $table->text('deskripsi_ra');
            $table->text('kriteria_keberhasilan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rencana_aksi');
    }
};
