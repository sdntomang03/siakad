<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukti_dukung', function (Blueprint $table) {
            $table->id();
            // Foreign key agar kepemilikannya jelas
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Foreign key untuk mengaitkan bukti dengan output tertentu
            $table->foreignId('output_target_id')->constrained('output_target')->onDelete('cascade');

            // Informasi file bukti dukung
            $table->string('nama_bukti'); // Contoh: "Sertifikat Pelatihan", "SK Mengajar"
            $table->string('file_path');  // Lokasi file disimpan (storage/app/public/...) atau URL Google Drive
            table->text('tautan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_dukung');
    }
};
