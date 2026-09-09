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

            // 1. Data KJP (Kartu Jakarta Pintar)
            $table->boolean('penerima_kjp')->default(false);

            // 2. Data PIP (Program Indonesia Pintar)
            $table->boolean('penerima_pip')->default(false);

            // 3. Data Bantuan Lainnya
            $table->boolean('penerima_bantuan_lain')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_financials');
    }
};
