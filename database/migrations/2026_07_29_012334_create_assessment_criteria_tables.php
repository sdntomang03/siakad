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
        // Tabel untuk menyimpan deskriptor kriteria
        Schema::create('assessment_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->string('descriptor'); // Contoh: "Mampu bekerjasama dalam tim"
            $table->timestamps();
        });

        // Tabel untuk menyimpan skor spesifik per kriteria per siswa
        Schema::create('assessment_criteria_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_criterion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->integer('score')->default(0); // Bernilai 1 sampai sesuai 'scale'
            $table->timestamps();

            // 1 Siswa hanya punya 1 skor per kriteria spesifik
            $table->unique(['assessment_criterion_id', 'student_id'], 'crit_student_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_criteria_tables');
    }
};
