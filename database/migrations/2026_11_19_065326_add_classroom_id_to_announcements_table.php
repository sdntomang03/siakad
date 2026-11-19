<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('announcements') && ! Schema::hasColumn('announcements', 'classroom_id')) {
            Schema::table('announcements', function (Blueprint $table) {
                $table->foreignId('classroom_id')->nullable()->constrained()->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('announcements', 'classroom_id')) {
            Schema::table('announcements', function (Blueprint $table) {
                $table->dropConstrainedForeignId('classroom_id');
            });
        }
    }
};
