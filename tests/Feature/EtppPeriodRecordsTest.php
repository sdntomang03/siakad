<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

function createEtppOutputTargetFor(User $user): int
{
    $kategoriId = DB::table('kategori')->insertGetId([
        'user_id' => $user->id,
        'nama_kategori' => 'Kategori Uji',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $rhkId = DB::table('rhk')->insertGetId([
        'user_id' => $user->id,
        'kategori_id' => $kategoriId,
        'deskripsi_rhk' => 'RHK Uji',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $rencanaAksiId = DB::table('rencana_aksi')->insertGetId([
        'user_id' => $user->id,
        'rhk_id' => $rhkId,
        'deskripsi_ra' => 'Rencana aksi uji',
        'kriteria_keberhasilan' => 'Kriteria uji',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return DB::table('output_target')->insertGetId([
        'user_id' => $user->id,
        'rencana_aksi_id' => $rencanaAksiId,
        'deskripsi_output' => 'Output uji',
        'target_waktu' => 'TW 1',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

test('an employee can save and update a quarterly realization for their own output', function () {
    $user = User::factory()->create();
    $outputTargetId = createEtppOutputTargetFor($user);

    $this->actingAs($user)
        ->post(route('etpp.realisasi.store'), [
            'output_target_id' => $outputTargetId,
            'triwulan' => 'TW 1',
            'tahun' => 2026,
            'realisasi' => 'Realisasi awal.',
        ])
        ->assertSessionHasNoErrors();

    $this->actingAs($user)
        ->post(route('etpp.realisasi.store'), [
            'output_target_id' => $outputTargetId,
            'triwulan' => 'TW 1',
            'tahun' => 2026,
            'realisasi' => 'Realisasi diperbarui.',
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseCount('etpp_realisasi', 1);
    $this->assertDatabaseHas('etpp_realisasi', [
        'user_id' => $user->id,
        'output_target_id' => $outputTargetId,
        'triwulan' => 'TW 1',
        'tahun' => 2026,
        'realisasi' => 'Realisasi diperbarui.',
    ]);
});

test('an employee can save and update one monthly performance dialogue', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('etpp.dialog-kinerja.store'), [
            'tahun' => 2026,
            'bulan' => 9,
            'uraian' => 'Dialog awal.',
        ])
        ->assertSessionHasNoErrors();

    $this->actingAs($user)
        ->post(route('etpp.dialog-kinerja.store'), [
            'tahun' => 2026,
            'bulan' => 9,
            'uraian' => 'Dialog diperbarui.',
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseCount('dialog_kinerja', 1);
    $this->assertDatabaseHas('dialog_kinerja', [
        'user_id' => $user->id,
        'tahun' => 2026,
        'bulan' => 9,
        'uraian' => 'Dialog diperbarui.',
    ]);
});

test('an employee cannot save a realization for another employee output', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $outputTargetId = createEtppOutputTargetFor($otherUser);

    $this->actingAs($user)
        ->post(route('etpp.realisasi.store'), [
            'output_target_id' => $outputTargetId,
            'triwulan' => 'TW 1',
            'tahun' => 2026,
            'realisasi' => 'Tidak boleh disimpan.',
        ])
        ->assertNotFound();

    $this->assertDatabaseCount('etpp_realisasi', 0);
});
