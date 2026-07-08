<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            // Relasi ke sekolah (Multi-Tenant)
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();

            // Atribut Mapel
            $table->tinyInteger('tingkat'); // Contoh: 1, 2, 3, 4, 5, 6
            $table->integer('urutan')->default(0);
            $table->boolean('is_sidanira')->default(false);
            $table->string('kode_mapel', 20)->nullable(); // Opsional: misal 'B-IND'
            $table->string('nama_mapel'); // Misal: 'Bahasa Indonesia'
            $table->integer('kkm')->default(75); // Nilai Kriteria Ketuntasan Minimal
            $table->enum('pengampu', ['guru_kelas', 'guru_mapel'])->default('guru_kelas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
