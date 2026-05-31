<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kelulusans', function (Blueprint $table) {
            // Tambahkan kolom tempat_lahir setelah nama
            $table->string('tempat_lahir')->nullable()->after('nama');
            // Tambahkan kolom nomor_skl setelah keterangan
            $table->string('nomor_skl')->nullable()->after('keterangan');
        });
    }

    public function down()
    {
        Schema::table('kelulusans', function (Blueprint $table) {
            $table->dropColumn(['tempat_lahir', 'nomor_skl']);
        });
    }
};
