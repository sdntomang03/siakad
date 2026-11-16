<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('remedial_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('remedial_program_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remedial_program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['remedial_program_id', 'assessment_id'], 'rem_prog_assessment_unique');
        });

        Schema::create('remedial_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remedial_program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->decimal('average_score', 5, 2);
            $table->unsignedSmallInteger('kkm');
            $table->boolean('is_completed')->default(false);
            $table->decimal('remedial_score', 5, 2)->nullable();
            $table->timestamps();
            $table->unique(['remedial_program_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('remedial_records');
        Schema::dropIfExists('remedial_program_assessments');
        Schema::dropIfExists('remedial_programs');
    }
};
