<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extracurriculars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('nama_ekskul');
            $table->timestamps();

            $table->unique(['school_id', 'nama_ekskul']);
        });

        Schema::create('extracurricular_student', function (Blueprint $table) {
            $table->foreignId('extracurricular_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['extracurricular_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extracurricular_student');
        Schema::dropIfExists('extracurriculars');
    }
};
