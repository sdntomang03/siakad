<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('report_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->nullOnDelete();

            // Mengganti period menjadi foreignId ke tabel academic_years
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->cascadeOnDelete();

            $table->enum('posisi', ['Di Sekolah', 'Dibawa Siswa'])->default('Di Sekolah');
            $table->dateTime('waktu_dibagikan')->nullable();
            $table->dateTime('waktu_dikembalikan')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_submissions');
    }
};
