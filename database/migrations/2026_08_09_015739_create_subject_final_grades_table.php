<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_final_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();

            $table->decimal('nilai_asli', 5, 2)->default(0); // Nilai mentah sebelum dikatrol
            $table->decimal('nilai_akhir', 5, 2)->default(0); // Nilai final untuk e-Rapor
            $table->string('predikat', 2)->nullable();

            $table->timestamps();

            // Mencegah duplikasi nilai akhir pada siswa, mapel, dan tahun ajaran yang sama
            $table->unique(['academic_year_id', 'student_id', 'subject_id'], 'unique_subject_final_grade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_final_grades');
    }
};
