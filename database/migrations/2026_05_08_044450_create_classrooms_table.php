<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();

            // Relasi ke tabel employees untuk menentukan Wali Kelas
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('employees')->nullOnDelete();

            $table->string('tingkat'); // Contoh: '4'
            $table->string('nama_kelas'); // Contoh: '4B'
            $table->integer('kapasitas')->default(30); // Maksimal jumlah siswa per kelas

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
