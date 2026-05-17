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
        Schema::create('kelulusans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->nullable(); // Untuk Multi-Tenant
            $table->string('nama');
            $table->string('nisn');
            $table->string('nipd')->nullable();
            $table->date('tanggal_lahir');
            $table->string('kelas')->nullable();
            $table->enum('keterangan', ['LULUS', 'TIDAK LULUS', 'DITUNDA']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelulusans');
    }
};
