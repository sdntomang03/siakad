<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('assessment_notes');
        Schema::create('assessment_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->text('catatan')->nullable(); // Kolom untuk menyimpan teks catatan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_notes');
    }
};
