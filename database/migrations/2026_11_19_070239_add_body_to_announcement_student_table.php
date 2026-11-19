<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('announcement_student') && ! Schema::hasColumn('announcement_student', 'body')) {
            Schema::table('announcement_student', function (Blueprint $table) {
                $table->text('body')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('announcement_student', 'body')) {
            return;
        }

        if (DB::table('announcement_student')->whereNotNull('body')->exists()) {
            throw new RuntimeException('Cannot remove personalized announcement messages.');
        }

        Schema::table('announcement_student', function (Blueprint $table) {
            $table->dropColumn('body');
        });
    }
};
