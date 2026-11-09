<?php

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

dataset('graduation results', [
    'lulus' => ['LULUS', 'Unduh Surat Keterangan Lulus (PDF)'],
    'tidak lulus' => ['TIDAK LULUS', 'Untuk mendapatkan penjelasan dan informasi tindak lanjut, silakan hubungi wali kelas.'],
    'ditunda' => ['DITUNDA', 'Keputusan kelulusan masih ditunda. Silakan hubungi wali kelas untuk memperoleh informasi lebih lanjut.'],
]);

test('the result page shows the decision and only offers the certificate to graduates', function ($status, $message) {
    $student = (object) [
        'nama' => 'Contoh Siswa Uji',
        'nisn' => '0000000000',
        'tempat_lahir' => 'Jakarta',
        'tanggal_lahir' => '2014-01-02',
        'keterangan' => $status,
    ];

    $response = $this->withSession([
        'studentData' => $student,
        'secureNumber' => 'SKL-2026-TEST-00000000',
        'token' => 'token-uji',
    ])->get(route('kelulusan.hasil'));

    $response->assertOk()
        ->assertSeeText($status)
        ->assertSeeText($student->nama)
        ->assertSeeText($student->nisn)
        ->assertSeeText($message)
        ->assertSeeText('SKL-2026-TEST-00000000')
        ->assertDontSeeText('Memverifikasi')
        ->assertDontSeeText('Menghubungkan ke database');

    if ($status === 'LULUS') {
        $response->assertSee(route('kelulusan.download', 'token-uji'))
            ->assertSee('graduation-fireworks');
    } else {
        $response->assertDontSee(route('kelulusan.download', 'token-uji'))
            ->assertDontSee('graduation-fireworks');
    }
})->with('graduation results');

test('the result page returns to the portal when there is no search result in session', function () {
    $this->get(route('kelulusan.hasil'))
        ->assertRedirect(route('pengumuman.index'))
        ->assertSessionHas('error', 'Silakan isi form pencarian terlebih dahulu untuk melihat hasilnya.');

    $this->get(route('pengumuman.index'))
        ->assertSeeText('Silakan isi form pencarian terlebih dahulu untuk melihat hasilnya.');
});

test('a graduate can download the linked certificate', function () {
    $nisn = '0000000000';
    $name = 'Contoh Siswa Uji';

    DB::table('kelulusans')->insert([
        'nama' => $name,
        'nisn' => $nisn,
        'tanggal_lahir' => '2014-01-02',
        'keterangan' => 'LULUS',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $token = Crypt::encryptString($nisn);

    $this->get(route('kelulusan.download', $token))
        ->assertOk()
        ->assertDownload("SKL_{$nisn}_{$name}.pdf");
});
