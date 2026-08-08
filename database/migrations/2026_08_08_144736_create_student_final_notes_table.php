<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_final_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete(); // Wali kelas penyusun

            // Rekap Kehadiran (Bisa diisi manual atau ditarik otomatis dari tabel absensi)
            $table->integer('sakit')->default(0);
            $table->integer('izin')->default(0);
            $table->integer('alpha')->default(0);

            // Rekap Kedisiplinan Piket
            $table->integer('piket_terlaksana')->default(0);
            $table->integer('piket_tidak_terlaksana')->default(0);

            // Teks Kesimpulan
            $table->text('ringkasan_catatan_guru')->nullable();
            $table->text('catatan_akhir')->nullable(); // Kesimpulan final untuk raport

            $table->timestamps();

            // Mencegah duplikasi catatan akhir untuk siswa yang sama di semester yang sama
            $table->unique(['student_id', 'classroom_id', 'academic_year_id'], 'student_final_note_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_final_notes');
    }
};
