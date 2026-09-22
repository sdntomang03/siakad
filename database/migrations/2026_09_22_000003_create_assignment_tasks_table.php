<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->foreignId('selected_by_group_id')->nullable()->constrained('assignment_groups')->nullOnDelete();
            $table->timestamp('selected_at')->nullable();
            $table->timestamps();

            $table->unique(['assignment_id', 'selected_by_group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_tasks');
    }
};
