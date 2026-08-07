<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rhk', function (Blueprint $table) {
            $table->id();
            // Foreign key ke tabel kategori
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kategori_id')->constrained('kategori')->onDelete('cascade');
            $table->text('deskripsi_rhk');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rhk');
    }
};
