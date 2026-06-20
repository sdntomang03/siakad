<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('report_submissions')) {
            // If table doesn't exist yet, create it with returned columns included
            Schema::create('report_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->nullOnDelete();
                $table->string('period')->nullable();
                $table->boolean('is_submitted')->default(false);
                $table->dateTime('submitted_at')->nullable();
                $table->boolean('is_returned')->default(false);
                $table->timestamp('returned_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
            return;
        }

        Schema::table('report_submissions', function (Blueprint $table) {
            if (! Schema::hasColumn('report_submissions', 'is_returned')) {
                $table->boolean('is_returned')->default(false)->after('is_submitted');
            }
            if (! Schema::hasColumn('report_submissions', 'returned_at')) {
                $table->timestamp('returned_at')->nullable()->after('submitted_at');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('report_submissions')) {
            return;
        }
        Schema::table('report_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('report_submissions', 'returned_at')) {
                $table->dropColumn('returned_at');
            }
            if (Schema::hasColumn('report_submissions', 'is_returned')) {
                $table->dropColumn('is_returned');
            }
        });
    }
};
