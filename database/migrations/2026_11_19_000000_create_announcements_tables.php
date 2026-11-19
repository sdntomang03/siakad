<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('recipient_student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->enum('audience', ['all', 'individual', 'class_personalized']);
            $table->string('title', 180);
            $table->text('body')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'audience']);
        });

        Schema::create('announcement_student', function (Blueprint $table) {
            $table->foreignId('announcement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->text('body')->nullable();
            $table->primary(['announcement_id', 'student_id']);
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_student');
        Schema::dropIfExists('announcements');
    }
};
