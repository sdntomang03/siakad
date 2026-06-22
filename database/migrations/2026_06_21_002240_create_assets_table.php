<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('nama_aset');
            $table->string('kode_aset')->nullable(); // Diisi oleh admin (cth: INV/2026/001)
            $table->integer('total_stok')->default(0);
            // Alur Approval
            $table->enum('status_persetujuan', ['pending', 'disetujui', 'ditolak'])->default('disetujui');
            $table->foreignId('diajukan_oleh')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assets');
    }
};
