<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Exception;
use Illuminate\Http\Request; // Ditambahkan untuk proses Import JSON
use Illuminate\Support\Facades\DB; // Ditambahkan untuk menangkap error saat Import JSON

class EtppController extends Controller
{
    /**
     * Menangkap inputan dari Form, lalu mengubahnya menjadi URL bersih
     */
    public function search(Request $request)
    {
        // Tangkap input dari URL (GET request)
        $nip = $request->input('nip');
        $filter_tw = $request->input('tw', 'semua'); // Default 'semua' jika kosong

        $employee = null;
        $data_kategori = collect(); // Kosongkan data secara default

        if ($nip) {
            // Bersihkan spasi berlebih pada NIP
            $nip = trim($nip);

            // Cari data pegawai
            $employee = Employee::where('nip', $nip)->first();

            // Jika pegawai ditemukan, tarik data e-Kinerja
            if ($employee) {
                // Tarik Kategori beserta relasi di dalamnya (Eager Loading)
                $query = Kategori::with([
                    'rhk.rencanaAksi.outputTarget' => function ($q) use ($filter_tw) {
                        // Filter Output berdasarkan TW jika bukan 'semua'
                        if ($filter_tw !== 'semua') {
                            $q->where('target_waktu', $filter_tw);
                        }
                    },
                ]);

                // Sembunyikan Kategori/RHK yang tidak memiliki output sesuai filter TW
                if ($filter_tw !== 'semua') {
                    $query->whereHas('rhk.rencanaAksi.outputTarget', function ($q) use ($filter_tw) {
                        $q->where('target_waktu', $filter_tw);
                    });
                }

                $data_kategori = $query->get();
            }
        }

        // Lempar data ke view
        return view('etpp.show', compact('nip', 'employee', 'filter_tw', 'data_kategori'));
    }

    /**
     * Memproses NIP yang ada di dalam URL
     */
    public function show($nip = null)
    {
        $user = auth()->user();

        // Skenario 1: Jika URL hanya dipanggil "/etpp" (tanpa NIP di belakangnya)
        if (! $nip) {
            // Jika yang login adalah guru/pegawai yang punya NIP, otomatis redirect ke NIP dia sendiri
            if ($user->hasAnyRole(['guru', 'kepsek', 'operator']) && isset($user->employee->nip)) {
                return redirect()->route('etpp.show', ['nip' => $user->employee->nip]);
            }

            // Jika yang login admin/tidak punya NIP, tampilkan form kosong
            return view('etpp.show', ['employee' => null, 'nip' => null]);
        }

        // Skenario 2: Jika ada NIP di URL (Misal: /etpp/198502022010012004)
        // Cari data pegawai berdasarkan NIP tersebut
        $employee = Employee::where('nip', $nip)->first();

        // Lempar datanya ke tampilan HTML
        return view('etpp.show', compact('employee', 'nip'));
    }

    /**
     * Menampilkan form upload JSON
     */
    public function showImportForm()
    {
        return view('etpp.import');
    }

    /**
     * Memproses file JSON e-Kinerja yang diupload
     */
    public function importJson(Request $request)
    {
        // 1. Validasi file
        $request->validate([
            'json_file' => 'required|file|mimes:json,txt',
        ], [
            'json_file.required' => 'Silakan pilih file JSON terlebih dahulu.',
            'json_file.mimes' => 'Format file harus berupa .json',
        ]);

        try {
            // 2. Baca isi file JSON
            $file = $request->file('json_file');
            $jsonContent = file_get_contents($file->getRealPath());
            $data = json_decode($jsonContent, true);

            // Cek apakah JSON valid
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->with('error', 'Isi file JSON tidak valid!');
            }

            // 3. Gunakan DB Transaction agar aman (jika ada error di tengah jalan, data akan di-rollback)
            DB::transaction(function () use ($data) {
                foreach ($data as $kategoriData) {

                    // Insert Kategori
                    $kategoriId = DB::table('kategori')->insertGetId([
                        'nama_kategori' => $kategoriData['nama_kategori'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Cek apakah ada data RHK di dalam Kategori
                    if (isset($kategoriData['rhk']) && is_array($kategoriData['rhk'])) {
                        foreach ($kategoriData['rhk'] as $rhkData) {

                            // Insert RHK
                            $rhkId = DB::table('rhk')->insertGetId([
                                'kategori_id' => $kategoriId,
                                'deskripsi_rhk' => $rhkData['deskripsi_rhk'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);

                            // Cek apakah ada data Rencana Aksi di dalam RHK
                            if (isset($rhkData['rencana_aksi']) && is_array($rhkData['rencana_aksi'])) {
                                foreach ($rhkData['rencana_aksi'] as $raData) {

                                    // Insert Rencana Aksi
                                    $raId = DB::table('rencana_aksi')->insertGetId([
                                        'rhk_id' => $rhkId,
                                        'deskripsi_ra' => $raData['deskripsi_ra'],
                                        'kriteria_keberhasilan' => $raData['kriteria_keberhasilan'],
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);

                                    // Cek apakah ada data Output di dalam Rencana Aksi
                                    if (isset($raData['output_target']) && is_array($raData['output_target'])) {
                                        foreach ($raData['output_target'] as $outputData) {

                                            // Insert Output
                                            DB::table('output_target')->insert([
                                                'rencana_aksi_id' => $raId,
                                                'deskripsi_output' => $outputData['deskripsi_output'],
                                                'target_waktu' => $outputData['target_waktu'],
                                                'created_at' => now(),
                                                'updated_at' => now(),
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            });

            return back()->with('success', 'Data JSON e-Kinerja berhasil diimpor!');

        } catch (Exception $e) {
            // Jika ada error, tampilkan pesannya
            return back()->with('error', 'Terjadi kesalahan sistem: '.$e->getMessage());
        }
    }
}
