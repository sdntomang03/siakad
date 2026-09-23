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
            $table->foreignId('output_target_id')->nullable()->constrained('output_target')->nullOnDelete();
            $table->string('nama_output')->nullable();
            $table->string('triwulan', 4)->nullable();
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('bulan')->nullable();
            $table->text('realisasi');
            $table->string('link_referensi', 2048)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'output_target_id', 'tahun', 'triwulan'], 'etpp_realisasi_triwulan_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etpp_realisasi');
    }
};
