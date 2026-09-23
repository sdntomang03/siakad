<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dialog_kinerja_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dialog_kinerja_id')->constrained('dialog_kinerja')->cascadeOnDelete();
            $table->foreignId('output_target_id')->nullable()->constrained('output_target')->nullOnDelete();
            $table->string('nama_output');
            $table->text('uraian');
            $table->string('link_referensi', 2048)->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            $table->unique(['dialog_kinerja_id', 'output_target_id'], 'dialog_kinerja_item_output_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dialog_kinerja_items');
    }
};
