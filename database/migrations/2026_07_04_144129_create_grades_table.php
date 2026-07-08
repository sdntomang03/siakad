<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();

            // Relasi Best Practice (Menggunakan ID/BigInt)
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();

            $table->tinyInteger('tingkat_kelas'); // 4, 5, atau 6
            $table->tinyInteger('semester'); // 1 atau 2

            $table->integer('nilai')->nullable();

            $table->timestamps();

            // Kunci Unik: 1 Siswa hanya punya 1 Nilai di 1 Mapel pada Tingkat & Semester tersebut
            $table->unique(
                ['school_id', 'student_id', 'subject_id', 'tingkat_kelas', 'semester'],
                'unique_grade_entry'
            );

            // Indexing agar query rekap nilai Sidanira kilat
            $table->index(['student_id', 'tingkat_kelas', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
