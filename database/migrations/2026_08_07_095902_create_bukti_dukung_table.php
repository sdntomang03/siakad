<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukti_dukung', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('output_target_id')->constrained('output_target')->onDelete('cascade');

            $table->string('nama_bukti');

          
            $table->enum('jenis_bukti', ['file', 'link'])->default('file');

        
            $table->string('file_path')->nullable();

       
            $table->text('tautan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_dukung');
    }
};
