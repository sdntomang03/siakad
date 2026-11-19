<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->text('body')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (DB::table('announcements')->whereNull('body')->exists()) {
            throw new RuntimeException('Cannot make announcement body required while null values exist.');
        }

        Schema::table('announcements', function (Blueprint $table) {
            $table->text('body')->nullable(false)->change();
        });
    }
};
