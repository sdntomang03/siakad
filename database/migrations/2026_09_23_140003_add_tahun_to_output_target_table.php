<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('output_target', function (Blueprint $table) {
            $table->unsignedSmallInteger('tahun')->default((int) date('Y'))->after('target_waktu');
        });

        DB::table('output_target')
            ->select(['id', 'created_at'])
            ->whereNotNull('created_at')
            ->get()
            ->each(function ($output) {
                DB::table('output_target')
                    ->where('id', $output->id)
                    ->update(['tahun' => Carbon::parse($output->created_at)->year]);
            });

        Schema::table('output_target', function (Blueprint $table) {
            $table->index(['user_id', 'tahun', 'target_waktu'], 'output_target_period_index');
        });
    }

    public function down(): void
    {
        Schema::table('output_target', function (Blueprint $table) {
            $table->dropIndex('output_target_period_index');
            $table->dropColumn('tahun');
        });
    }
};
