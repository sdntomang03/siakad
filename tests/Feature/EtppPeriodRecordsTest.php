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
            'link_referensi' => 'https://example.com/realisasi-awal',
        ])
        ->assertSessionHasNoErrors();

    $this->actingAs($user)
        ->post(route('etpp.realisasi.store'), [
            'output_target_id' => $outputTargetId,
            'triwulan' => 'TW 1',
            'tahun' => 2026,
            'realisasi' => 'Realisasi diperbarui.',
            'link_referensi' => 'https://example.com/realisasi-diperbarui',
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseCount('etpp_realisasi', 1);
    $this->assertDatabaseHas('etpp_realisasi', [
        'user_id' => $user->id,
        'output_target_id' => $outputTargetId,
        'triwulan' => 'TW 1',
        'tahun' => 2026,
        'realisasi' => 'Realisasi diperbarui.',
        'link_referensi' => 'https://example.com/realisasi-diperbarui',
    ]);
});

test('an employee can save and update one monthly performance dialogue', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('etpp.dialog-kinerja.store'), [
            'tahun' => 2026,
            'bulan' => 9,
            'uraian' => 'Dialog awal.',
            'link_referensi' => 'https://example.com/dialog-awal',
        ])
        ->assertSessionHasNoErrors();

    $this->actingAs($user)
        ->post(route('etpp.dialog-kinerja.store'), [
            'tahun' => 2026,
            'bulan' => 9,
            'uraian' => 'Dialog diperbarui.',
            'link_referensi' => 'https://example.com/dialog-diperbarui',
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseCount('dialog_kinerja', 1);
    $this->assertDatabaseHas('dialog_kinerja', [
        'user_id' => $user->id,
        'tahun' => 2026,
        'bulan' => 9,
        'uraian' => 'Dialog diperbarui.',
        'link_referensi' => 'https://example.com/dialog-diperbarui',
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

test('an employee can update and delete their stored quarterly realization', function () {
    $user = User::factory()->create();
    $outputTargetId = createEtppOutputTargetFor($user);
    $realisasiId = DB::table('etpp_realisasi')->insertGetId([
        'user_id' => $user->id,
        'output_target_id' => $outputTargetId,
        'nama_output' => 'Output uji',
        'triwulan' => 'TW 1',
        'tahun' => 2026,
        'bulan' => 3,
        'realisasi' => 'Realisasi sebelumnya.',
        'link_referensi' => 'https://example.com/sebelumnya',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($user)
        ->put(route('etpp.realisasi.update', $realisasiId), [
            'realisasi' => 'Realisasi hasil edit.',
            'link_referensi' => 'https://example.com/profil-realisasi',
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('etpp_realisasi', [
        'id' => $realisasiId,
        'realisasi' => 'Realisasi hasil edit.',
        'link_referensi' => 'https://example.com/profil-realisasi',
    ]);

    $this->actingAs($user)
        ->delete(route('etpp.realisasi.destroy', $realisasiId))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('etpp_realisasi', ['id' => $realisasiId]);
});

test('an employee cannot update another employee dialogue', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $dialogKinerjaId = DB::table('dialog_kinerja')->insertGetId([
        'user_id' => $otherUser->id,
        'tahun' => 2026,
        'bulan' => 9,
        'uraian' => 'Dialog pemilik lain.',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($user)
        ->put(route('etpp.dialog-kinerja.update', $dialogKinerjaId), [
            'uraian' => 'Tidak boleh diperbarui.',
            'link_referensi' => 'https://example.com/tidak-boleh',
        ])
        ->assertNotFound();

    $this->assertDatabaseHas('dialog_kinerja', [
        'id' => $dialogKinerjaId,
        'uraian' => 'Dialog pemilik lain.',
    ]);
});

test('an employee can save realization and dialogue rows linked to output targets', function () {
    $user = User::factory()->create();
    $outputTargetId = createEtppOutputTargetFor($user);

    $this->actingAs($user)
        ->post(route('etpp.realisasi.batch'), [
            'tahun' => 2026,
            'bulan' => 1,
            'items' => [
                ['output_target_id' => $outputTargetId, 'link_referensi' => 'https://example.com/realisasi'],
            ],
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('etpp_realisasi', [
        'user_id' => $user->id,
        'output_target_id' => $outputTargetId,
        'nama_output' => 'Output uji',
    ]);

    $this->actingAs($user)
        ->post(route('etpp.dialog-kinerja.batch'), [
            'tahun' => 2026,
            'bulan' => 1,
            'uraian' => 'Ringkasan dialog',
            'items' => [
                ['output_target_id' => $outputTargetId, 'uraian' => 'Pembahasan output', 'link_referensi' => 'https://example.com/dialog'],
            ],
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('dialog_kinerja_items', [
        'output_target_id' => $outputTargetId,
        'uraian' => '-',
        'link_referensi' => 'https://example.com/realisasi',
    ]);
});

test('realisasi only shows output targets from the selected month quarter', function () {
    $user = User::factory()->create();
    $twOneOutputId = createEtppOutputTargetFor($user);
    DB::table('output_target')->where('id', $twOneOutputId)->update([
        'deskripsi_output' => 'Output TW 1',
    ]);
    $rencanaAksiId = DB::table('output_target')->where('id', $twOneOutputId)->value('rencana_aksi_id');
    DB::table('output_target')->insert([
        'user_id' => $user->id,
        'rencana_aksi_id' => $rencanaAksiId,
        'deskripsi_output' => 'Output TW 2',
        'target_waktu' => 'TW 2',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('etpp.realisasi.index', ['tahun' => 2026, 'bulan' => 1]))
        ->assertOk()
        ->assertSee('Output TW 1')
        ->assertDontSee('Output TW 2');
});

test('public monthly recaps can be accessed without authentication', function () {
    $user = User::factory()->create();
    $outputTargetId = createEtppOutputTargetFor($user);
    DB::table('etpp_realisasi')->insert([
        'user_id' => $user->id,
        'output_target_id' => $outputTargetId,
        'nama_output' => 'Output uji',
        'triwulan' => 'TW 1',
        'tahun' => 2026,
        'bulan' => 1,
        'realisasi' => '-',
        'link_referensi' => 'https://example.com/realisasi',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $dialogKinerjaId = DB::table('dialog_kinerja')->insertGetId([
        'user_id' => $user->id,
        'tahun' => 2026,
        'bulan' => 1,
        'uraian' => '-',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    DB::table('dialog_kinerja_items')->insert([
        'dialog_kinerja_id' => $dialogKinerjaId,
        'output_target_id' => $outputTargetId,
        'nama_output' => 'Output uji',
        'uraian' => '-',
        'link_referensi' => 'https://example.com/dialog',
        'urutan' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('etpp.realisasi.recap', [$user, 2026, 1]))
        ->assertOk()
        ->assertSee('https://example.com/realisasi');
    $this->get(route('etpp.dialog-kinerja.recap', [$user, 2026, 1]))
        ->assertOk()
        ->assertSee('https://example.com/dialog');
});
