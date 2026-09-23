<?php

namespace App\Http\Controllers;

use App\Models\DialogKinerja;
use App\Models\Employee;
use App\Models\EtppRealisasi;
use App\Models\Kategori; // Pastikan model Kategori di-import untuk fungsi search
use App\Models\OutputTarget;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EtppController extends Controller
{
    /**
     * Menangkap inputan dari Form, lalu mengubahnya menjadi URL bersih
     * sekaligus menangani logika pencarian dan Filter TW
     */
    /**
     * Menangkap inputan POST dari Form, lalu membuangnya (redirect) menjadi URL GET bersih
     */
    public function search(Request $request)
    {
        $request->validate([
            'nip' => 'required',
        ]);

        // Tangkap input dan alihkan ke method show (GET) dengan membawa parameter
        return redirect()->route('etpp.show', [
            'nip' => trim($request->nip),
            'tw' => $request->tw ?? 'semua',
        ]);
    }

    /**
     * Memproses NIP dari URL (GET) dan menampilkan hasilnya
     */
    public function show(Request $request, $nip = null)
    {
        $user = auth()->user();

        // Skenario 1: Jika URL diakses tanpa NIP di belakangnya
        if (! $nip) {
            // Jika yang login punya NIP, otomatis redirect ke pencarian NIP dia sendiri
            if ($user && $user->hasAnyRole(['guru', 'kepsek', 'operator']) && isset($user->employee->nip)) {
                return redirect()->route('etpp.show', ['nip' => $user->employee->nip]);
            }

            // Jika tidak ada NIP, tampilkan form pencarian kosong
            return view('etpp.show', [
                'employee' => null,
                'nip' => null,
                'filter_tw' => 'semua',
                'data_kategori' => collect(),
            ]);
        }

        // Skenario 2: Jika ada NIP, jalankan logika pencarian database di sini
        $filter_tw = $request->input('tw', 'semua'); // Tangkap filter TW dari URL
        $employee = Employee::where('nip', $nip)->first();
        $data_kategori = collect();

        // Jika pegawai ditemukan, tarik data e-Kinerja
        if ($employee && $employee->user_id) {
            $query = Kategori::where('user_id', $employee->user_id)
                ->with([
                    'rhk.rencanaAksi.outputTarget' => function ($q) use ($filter_tw) {
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

        // Lempar data ke view
        return view('etpp.show', compact('nip', 'employee', 'filter_tw', 'data_kategori'));
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

            // Ambil ID user yang sedang login
            $userId = auth()->id();

            // Pastikan user sedang login sebelum import
            if (! $userId) {
                return back()->with('error', 'Sesi login Anda telah habis. Silakan login kembali.');
            }

            // 3. Gunakan DB Transaction agar aman (jika ada error, data dibatalkan otomatis)
            DB::transaction(function () use ($data, $userId) {
                foreach ($data as $kategoriData) {

                    // Insert Kategori
                    $kategoriId = DB::table('kategori')->insertGetId([
                        'user_id' => $userId, // <-- Disisipkan di sini
                        'nama_kategori' => $kategoriData['nama_kategori'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Cek apakah ada data RHK di dalam Kategori
                    if (isset($kategoriData['rhk']) && is_array($kategoriData['rhk'])) {
                        foreach ($kategoriData['rhk'] as $rhkData) {

                            // Insert RHK
                            $rhkId = DB::table('rhk')->insertGetId([
                                'user_id' => $userId, // <-- Disisipkan di sini
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
                                        'user_id' => $userId, // <-- Disisipkan di sini
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
                                                'user_id' => $userId, // <-- Disisipkan di sini
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
            // Jika ada error, tampilkan pesannya (berguna untuk debugging jika masih error)
            return back()->with('error', 'Terjadi kesalahan sistem: '.$e->getMessage());
        }
    }

    /**
     * Menampilkan e-Kinerja milik user yang sedang login
     */
    /**
     * Menampilkan e-Kinerja milik user yang sedang login
     */
    public function myEkinerja(Request $request)
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $employee = $user->employee ?? null;
        $filter_tw = $request->input('tw', 'semua');
        $tahun = (int) $request->input('tahun', now()->year);
        $bulan = (int) $request->input('bulan', now()->month);

        if ($tahun < 2000 || $tahun > 2100) {
            abort(422, 'Tahun harus berada antara 2000 dan 2100.');
        }

        if ($bulan < 1 || $bulan > 12) {
            abort(422, 'Bulan tidak valid.');
        }

        $data_kategori = collect();

        // Tarik data e-Kinerja & tambahkan eager loading untuk buktiDukung
        $query = Kategori::where('user_id', $user->id)
            ->with([
                'rhk.rencanaAksi.outputTarget' => function ($q) use ($filter_tw) {
                    if ($filter_tw !== 'semua') {
                        $q->where('target_waktu', $filter_tw);
                    }
                },
                // DITAMBAHKAN: Tarik relasi buktiDukung sekaligus agar view bisa membacanya
                'rhk.rencanaAksi.outputTarget.buktiDukung',
            ]);

        if ($filter_tw !== 'semua') {
            $query->whereHas('rhk.rencanaAksi.outputTarget', function ($q) use ($filter_tw) {
                $q->where('target_waktu', $filter_tw);
            });
        }

        $data_kategori = $query->get();
        $outputTargets = OutputTarget::where('user_id', $user->id)
            ->when($filter_tw !== 'semua', function ($query) use ($filter_tw) {
                $query->where('target_waktu', $filter_tw);
            })
            ->orderBy('target_waktu')
            ->orderBy('deskripsi_output')
            ->get();
        $realisasiList = EtppRealisasi::with('outputTarget')
            ->where('user_id', $user->id)
            ->where('tahun', $tahun)
            ->orderBy('triwulan')
            ->latest('updated_at')
            ->get();
        $dialogKinerja = DialogKinerja::where('user_id', $user->id)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->first();

        return view('etpp.my-ekinerja', compact(
            'employee',
            'filter_tw',
            'data_kategori',
            'tahun',
            'bulan',
            'outputTargets',
            'realisasiList',
            'dialogKinerja'
        ));
    }

    public function storeRealisasi(Request $request)
    {
        $validated = $request->validate([
            'output_target_id' => ['required', 'integer'],
            'triwulan' => ['required', 'in:TW 1,TW 2,TW 3,TW 4'],
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'realisasi' => ['required', 'string', 'max:10000'],
        ]);

        $outputTarget = OutputTarget::where('id', $validated['output_target_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        EtppRealisasi::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'output_target_id' => $outputTarget->id,
                'triwulan' => $validated['triwulan'],
                'tahun' => $validated['tahun'],
            ],
            ['realisasi' => $validated['realisasi']]
        );

        return back()->with('success', 'Realisasi triwulan berhasil disimpan.');
    }

    public function storeDialogKinerja(Request $request)
    {
        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'uraian' => ['required', 'string', 'max:10000'],
        ]);

        DialogKinerja::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'tahun' => $validated['tahun'],
                'bulan' => $validated['bulan'],
            ],
            ['uraian' => $validated['uraian']]
        );

        return back()->with('success', 'Dialog kinerja bulanan berhasil disimpan.');
    }

    public function downloadRealisasiPdf(Request $request)
    {
        $validated = $request->validate([
            'triwulan' => ['required', 'in:TW 1,TW 2,TW 3,TW 4'],
            'tahun' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $user = $request->user();
        $employee = $user->employee;
        $realisasiList = EtppRealisasi::with('outputTarget.rencanaAksi.rhk.kategori')
            ->where('user_id', $user->id)
            ->where('triwulan', $validated['triwulan'])
            ->where('tahun', $validated['tahun'])
            ->orderBy('output_target_id')
            ->get();

        $pdf = Pdf::loadView('etpp.pdf-realisasi', compact('user', 'employee', 'realisasiList', 'validated'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Realisasi_{$validated['triwulan']}_{$validated['tahun']}.pdf");
    }

    public function downloadDialogKinerjaPdf(Request $request)
    {
        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'bulan' => ['required', 'integer', 'between:1,12'],
        ]);

        $user = $request->user();
        $employee = $user->employee;
        $dialogKinerja = DialogKinerja::where('user_id', $user->id)
            ->where('tahun', $validated['tahun'])
            ->where('bulan', $validated['bulan'])
            ->first();

        $pdf = Pdf::loadView('etpp.pdf-dialog-kinerja', compact('user', 'employee', 'dialogKinerja', 'validated'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Dialog_Kinerja_{$validated['tahun']}_".str_pad((string) $validated['bulan'], 2, '0', STR_PAD_LEFT).'.pdf');
    }

    /**
     * Method untuk memproses upload Bukti Dukung (Bisa File atau Link)
     */
    public function uploadBukti(Request $request)
    {
        // 1. Validasi dinamis berdasarkan jenis_bukti
        $request->validate([
            'output_target_id' => 'required|exists:output_target,id',
            'jenis_bukti' => 'required|in:file,link',
            // Jika pilih file, maka file_bukti wajib diisi
            'file_bukti' => 'nullable|required_if:jenis_bukti,file|file|mimes:pdf,jpg,jpeg,png|max:5120',
            // Jika pilih link, maka link_bukti wajib diisi dan formatnya harus URL
            'link_bukti' => 'nullable|required_if:jenis_bukti,link|url',
        ], [
            'file_bukti.required_if' => 'File dokumen wajib diunggah jika Anda memilih opsi File.',
            'link_bukti.required_if' => 'Tautan/URL wajib diisi jika Anda memilih opsi Link.',
            'link_bukti.url' => 'Format Tautan tidak valid. Pastikan diawali dengan http:// atau https://',
        ]);

        try {
            $user = auth()->user();
            $outputTarget = DB::table('output_target')->where('id', $request->output_target_id)->first();

            // Buat nama otomatis
            $namaOutputBersih = Str::limit($outputTarget->deskripsi_output, 150, '...');
            $namaBuktiOtomatis = $namaOutputBersih.' - '.$user->name;

            // Siapkan kerangka data yang akan disimpan
            $dataInsert = [
                'user_id' => $user->id,
                'output_target_id' => $request->output_target_id,
                'nama_bukti' => $namaBuktiOtomatis,
                'jenis_bukti' => $request->jenis_bukti,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // 2. Cek apakah File atau Link yang disimpan
            if ($request->jenis_bukti === 'file' && $request->hasFile('file_bukti')) {
                // Simpan File Fisik
                $dataInsert['file_path'] = $request->file('file_bukti')->store('bukti_dukung', 'public');
                $dataInsert['tautan'] = null;
            } elseif ($request->jenis_bukti === 'link') {
                // Simpan Tautan/URL
                $dataInsert['tautan'] = $request->link_bukti;
                $dataInsert['file_path'] = null;
            }

            // 3. Masukkan ke database
            DB::table('bukti_dukung')->insert($dataInsert);

            return back()->with('success', 'Bukti dukung berhasil ditambahkan!');

        } catch (Exception $e) {
            return back()->with('error', 'Gagal menambahkan bukti: '.$e->getMessage());
        }
    }

    /**
     * Menghapus 1 Bukti (File atau Link)
     */
    public function destroyBukti($id)
    {
        try {
            // Cari data bukti yang sesuai dengan ID dan dimiliki oleh user yang sedang login
            $bukti = DB::table('bukti_dukung')
                ->where('id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if (! $bukti) {
                return back()->with('error', 'Dokumen tidak ditemukan atau Anda tidak memiliki akses.');
            }

            // Hapus file fisik dari storage HANYA JIKA file_path tidak kosong (bukan link)
            if (! empty($bukti->file_path) && Storage::disk('public')->exists($bukti->file_path)) {
                Storage::disk('public')->delete($bukti->file_path);
            }

            // Hapus data dari database
            DB::table('bukti_dukung')->where('id', $id)->delete();

            return back()->with('success', 'Bukti berhasil dihapus.');

        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus data: '.$e->getMessage());
        }
    }

    /**
     * Menghapus Semua File/Link Bukti dalam 1 Output
     */
    public function destroyBuktiByOutput($output_id)
    {
        try {
            // Tarik semua bukti yang terikat dengan output tersebut dan milik user ini
            $buktiList = DB::table('bukti_dukung')
                ->where('output_target_id', $output_id)
                ->where('user_id', auth()->id())
                ->get();

            if ($buktiList->isEmpty()) {
                return back()->with('error', 'Tidak ada bukti yang bisa dihapus pada output ini.');
            }

            // Looping untuk menghapus semua file fisiknya
            foreach ($buktiList as $bukti) {
                // Hapus HANYA JIKA file_path tidak kosong (bukan link)
                if (! empty($bukti->file_path) && Storage::disk('public')->exists($bukti->file_path)) {
                    Storage::disk('public')->delete($bukti->file_path);
                }
            }

            // Hapus semua baris data dari database
            DB::table('bukti_dukung')
                ->where('output_target_id', $output_id)
                ->where('user_id', auth()->id())
                ->delete();

            return back()->with('success', 'Semua bukti pada output tersebut berhasil dibersihkan.');

        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus data: '.$e->getMessage());
        }
    }
}
