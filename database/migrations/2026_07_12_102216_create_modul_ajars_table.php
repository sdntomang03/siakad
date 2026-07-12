<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modul_ajars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // ID Guru pembuat
            $table->foreignId('academic_year_id')->nullable()->constrained()->cascadeOnDelete(); // Tahun Pelajaran

            $table->string('tingkat');
            $table->string('mata_pelajaran');
            $table->text('topik');

            $table->longText('html_content'); // Untuk menyimpan hasil modul HTML
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modul_ajars');
    }
};
