<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();

            // subject_id boleh kosong untuk menampung jam istirahat/upacara
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();

            $table->string('hari'); // Senin, Selasa, dst
            $table->integer('urutan_jam'); // Jam ke-1, ke-2, dst
            $table->time('jam_mulai'); // 07:00:00
            $table->time('jam_selesai'); // 07:35:00
            $table->string('keterangan')->nullable(); // Misal: "Istirahat", "Upacara Bendera"

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajarans');
    }
};
