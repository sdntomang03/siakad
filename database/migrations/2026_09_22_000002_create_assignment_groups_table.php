<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('leader_student_id')->constrained('students')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('assignment_group_student', function (Blueprint $table) {
            $table->foreignId('assignment_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['assignment_group_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_group_student');
        Schema::dropIfExists('assignment_groups');
    }
};
