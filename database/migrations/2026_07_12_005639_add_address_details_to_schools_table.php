<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // Menambahkan 'kepala_sekolah' setelah 'nama_sekolah'
            $table->string('kepala_sekolah')->nullable()->after('nama_sekolah');
            $table->string('nip')->nullable()->after('kepala_sekolah');

            // Menambahkan detail alamat setelah kolom 'alamat' yang sudah ada
            $table->string('kelurahan')->nullable()->after('alamat');
            $table->string('kecamatan')->nullable()->after('kelurahan');
            $table->string('kota')->nullable()->after('kecamatan');
            $table->string('provinsi')->nullable()->after('kota');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // Hapus kolom-kolom baru tersebut jika melakukan rollback
            $table->dropColumn(['nip', 'kelurahan', 'kecamatan', 'kota', 'provinsi']);
        });
    }
};
