<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Ensure all existing rows have location consistent with is_returned
        DB::table('report_submissions')->update(['location' => DB::raw("CASE WHEN is_returned = 1 THEN 'school' ELSE 'home' END")]);
    }

    public function down()
    {
        // no-op: leave locations as-is
    }
};
