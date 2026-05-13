<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_financials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();

            $table->boolean('penerima_kps')->default(false);
            $table->string('no_kps')->nullable();

            $table->boolean('penerima_kip')->default(false);
            $table->string('nomor_kip')->nullable();
            $table->string('nama_di_kip')->nullable();

            $table->string('nomor_kks')->nullable();

            $table->boolean('layak_pip')->default(false);
            $table->string('alasan_layak_pip')->nullable();

            $table->string('bank')->nullable();
            $table->string('nomor_rekening_bank')->nullable();
            $table->string('rekening_atas_nama')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_financials');
    }
};
