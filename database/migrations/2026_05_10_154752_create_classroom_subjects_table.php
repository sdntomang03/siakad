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
        Schema::create('classroom_subjects', function (Blueprint $table) {
            $table->id();
            // Kelas mana yang diajar? (Cth: Kelas 1A)
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();

            // Mapel apa yang diajarkan? (Cth: Pendidikan Agama Islam)
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();

            // Siapa guru mapelnya? (Cth: Guru A)
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();

            $table->timestamps();

            // Mencegah duplikasi: 1 Kelas hanya bisa punya 1 Guru untuk 1 Mapel yang sama
            $table->unique(['classroom_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classroom_subjects');
    }
};
