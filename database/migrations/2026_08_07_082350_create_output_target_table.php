<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('output_target', function (Blueprint $table) {
            $table->id();
            // Foreign key ke tabel rencana_aksi
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('rencana_aksi_id')->constrained('rencana_aksi')->onDelete('cascade');
            $table->text('deskripsi_output');
            $table->string('target_waktu', 10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('output_target');
    }
};
