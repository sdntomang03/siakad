<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_pikets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('hari'); // Senin, Selasa, Rabu, Kamis, Jumat, Sabtu

            // Opsional: Jika sistem Anda menggunakan tahun ajaran
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();

            $table->timestamps();

            // Mencegah 1 anak dijadwalkan 2 kali di kelas yang sama pada hari yang sama
            $table->unique(['classroom_id', 'student_id', 'hari', 'academic_year_id'], 'jadwal_piket_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pikets');
    }
};
