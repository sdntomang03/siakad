<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('report_submissions')) {
            return;
        }

        Schema::table('report_submissions', function (Blueprint $table) {
            if (! Schema::hasColumn('report_submissions', 'location')) {
                $table->string('location')->default('school')->after('notes');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('report_submissions')) {
            return;
        }
        Schema::table('report_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('report_submissions', 'location')) {
                $table->dropColumn('location');
            }
        });
    }
};
