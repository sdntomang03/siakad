<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classroom_student', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel classrooms
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();

            // Relasi ke tabel students
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();

            // Mencatat kapan siswa tersebut dimasukkan ke kelas ini
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_student');
    }
};
