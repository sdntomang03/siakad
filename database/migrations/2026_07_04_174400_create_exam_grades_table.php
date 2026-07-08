<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_grades', function (Blueprint $table) {
            $table->id();

            // Relasi Utama
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();

            // Kolom Kategori untuk membedakan jenis ujian
            $table->string('kategori_ujian', 50); // Contoh: 'Ujian Sekolah', 'Ujian Praktik'

            // Tingkat dan Semester (Opsional, tapi baik untuk rekam jejak)
            $table->tinyInteger('tingkat_kelas')->nullable();
            $table->tinyInteger('semester')->nullable();

            // Nilai
            $table->decimal('nilai', 5, 2)->nullable(); // Pakai decimal agar bisa simpan nilai koma (cth: 85.50)

            $table->timestamps();

            // Mencegah duplikasi data:
            // 1 Siswa hanya punya 1 Nilai pada 1 Mapel untuk 1 Kategori Ujian di periode tersebut
            $table->unique(
                ['school_id', 'student_id', 'subject_id', 'kategori_ujian', 'tingkat_kelas', 'semester'],
                'unique_exam_grade_entry'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_grades');
    }
};
