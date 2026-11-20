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
            $table->enum('audience', ['all', 'individual', 'class_personalized'])->change();
        });
    }

    public function down(): void
    {
        if (DB::table('announcements')->where('audience', 'class_personalized')->exists()) {
            throw new RuntimeException('Cannot remove class_personalized while announcements use it.');
        }

        Schema::table('announcements', function (Blueprint $table) {
            $table->enum('audience', ['all', 'individual'])->change();
        });
    }
};
