<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_pikets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('status', ['terlaksana', 'tidak_terlaksana'])->default('terlaksana');
            $table->string('catatan')->nullable(); // Untuk alasan tidak piket (Sakit, Kabur, dll)

            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->timestamps();

            // Mencegah input ganda untuk anak yang sama di tanggal yang sama
            $table->unique(['classroom_id', 'student_id', 'tanggal'], 'jurnal_piket_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_pikets');
    }
};
