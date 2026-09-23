<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etpp_realisasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('output_target_id')->constrained('output_target')->cascadeOnDelete();
            $table->string('triwulan', 4);
            $table->unsignedSmallInteger('tahun');
            $table->text('realisasi');
            $table->timestamps();

            $table->unique(['user_id', 'output_target_id', 'triwulan', 'tahun'], 'etpp_realisasi_periode_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etpp_realisasi');
    }
};
