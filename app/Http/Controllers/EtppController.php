<?php

namespace App\Http\Controllers;

use App\Models\BuktiDukung;
use App\Models\DialogKinerja;
use App\Models\DialogKinerjaItem;
use App\Models\Employee;
use App\Models\EtppRealisasi;
use App\Models\Kategori; // Pastikan model Kategori di-import untuk fungsi search
use App\Models\OutputTarget;
use App\Models\User;
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
                                                'tahun' => $outputData['tahun'] ?? now()->year,
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
        return redirect()->route('etpp.realisasi.index', $request->only(['bulan', 'tahun']));
    }

    public function realisasiIndex(Request $request)
    {
        $tahun = (int) $request->input('tahun', now()->year);
        $filter_tw = $request->input('tw');
        if ($filter_tw === null && $request->filled('bulan')) {
            $bulan = (int) $request->input('bulan');
            $filter_tw = $bulan >= 1 && $bulan <= 12 ? 'TW '.(int) ceil($bulan / 3) : 'semua';
        }
        $filter_tw ??= 'semua';
        if (! in_array($filter_tw, ['semua', 'TW 1', 'TW 2', 'TW 3', 'TW 4'], true)) {
            $filter_tw = 'semua';
        }
        if ($tahun < 2000 || $tahun > 2100) {
            abort(422, 'Tahun realisasi tidak valid.');
        }

        $user = $request->user();
        $outputTargets = OutputTarget::with('buktiDukung')
            ->where('user_id', $user->id)
            ->where('tahun', $tahun)
            ->when($filter_tw !== 'semua', fn ($query) => $query->where('target_waktu', $filter_tw))
            ->orderBy('target_waktu')
            ->orderBy('id')
            ->get();

        $buktiDukung = BuktiDukung::with('outputTarget')
            ->where('user_id', $user->id)
            ->when($filter_tw !== 'semua', fn ($query) => $query->whereHas(
                'outputTarget',
                fn ($q) => $q->where('target_waktu', $filter_tw)
            ))
            ->get();
        $realisasiList = EtppRealisasi::where('user_id', $user->id)
            ->where('tahun', $tahun)
            ->when($filter_tw !== 'semua', fn ($query) => $query->where('triwulan', $filter_tw))
            ->get();
        $triwulan = $filter_tw === 'semua' ? null : $filter_tw;
        $bulan = $triwulan ? ((int) str_replace('TW ', '', $triwulan)) * 3 : null;

        return view('etpp.realisasi-index', compact(
            'outputTargets', 'buktiDukung', 'realisasiList', 'filter_tw', 'tahun', 'triwulan', 'bulan'
        ));
    }

    public function dialogKinerjaIndex(Request $request)
    {
        $tahun = (int) $request->input('tahun', now()->year);
        $bulan = (int) $request->input('bulan', now()->month);

        if ($tahun < 2000 || $tahun > 2100 || $bulan < 1 || $bulan > 12) {
            abort(422, 'Periode dialog kinerja tidak valid.');
        }

        $triwulan = 'TW '.(int) ceil($bulan / 3);
        $outputTargets = OutputTarget::where('user_id', $request->user()->id)
            ->where('tahun', $tahun)
            ->where('target_waktu', $triwulan)
            ->orderBy('id')
            ->get();
        $dialogKinerja = DialogKinerja::with('items.outputTarget')
            ->where('user_id', $request->user()->id)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->first();
        $realisasiByOutput = EtppRealisasi::where('user_id', $request->user()->id)
            ->where('tahun', $tahun)
            ->where('triwulan', $triwulan)
            ->whereNotNull('link_referensi')
            ->get()
            ->keyBy('output_target_id');

        return view('etpp.dialog-kinerja-index', compact(
            'tahun', 'bulan', 'triwulan', 'outputTargets', 'dialogKinerja', 'realisasiByOutput'
        ));
    }

    public function saveRealisasiBatch(Request $request)
    {
        if (! $request->filled('triwulan') && $request->filled('bulan')) {
            $request->merge(['triwulan' => 'TW '.(int) ceil((int) $request->input('bulan') / 3)]);
        }
        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'triwulan' => ['nullable', 'in:TW 1,TW 2,TW 3,TW 4'],
            'items' => ['array'],
            'items.*.output_target_id' => ['required', 'integer', 'distinct'],
            'items.*.triwulan' => ['nullable', 'in:TW 1,TW 2,TW 3,TW 4'],
            'items.*.link_referensi' => ['required', 'url', 'max:2048'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $triwulan = $validated['triwulan'] ?? null;
            $keptIds = [];
            foreach ($validated['items'] ?? [] as $item) {
                $itemTriwulan = $item['triwulan'] ?? $triwulan;
                if ($itemTriwulan === null) {
                    abort(422, 'Triwulan setiap output wajib diisi.');
                }
                $outputTarget = OutputTarget::where('id', $item['output_target_id'])
                    ->where('user_id', $request->user()->id)
                    ->where('target_waktu', $itemTriwulan)
                    ->firstOrFail();
                $record = EtppRealisasi::updateOrCreate(
                    [
                        'user_id' => $request->user()->id,
                        'output_target_id' => $outputTarget->id,
                        'tahun' => $validated['tahun'],
                        'triwulan' => $itemTriwulan,
                    ],
                    [
                        'nama_output' => $outputTarget->deskripsi_output,
                        'triwulan' => $itemTriwulan,
                        'realisasi' => '-',
                        'link_referensi' => $item['link_referensi'],
                    ]
                );
                $keptIds[] = $record->id;
            }
            if ($triwulan !== null) {
                EtppRealisasi::where('user_id', $request->user()->id)
                    ->where('triwulan', $triwulan)->where('tahun', $validated['tahun'])
                    ->when($keptIds, fn ($query) => $query->whereNotIn('id', $keptIds))
                    ->delete();
            }
        });

        return redirect()->route('etpp.realisasi.index', ['tw' => $validated['triwulan'] ?? 'semua', 'tahun' => $validated['tahun']])
            ->with('success', 'Realisasi triwulan berhasil disimpan.');
    }

    public function saveDialogKinerjaBatch(Request $request)
    {
        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'items' => ['array'],
            'items.*.output_target_id' => ['required', 'integer', 'distinct'],
            'items.*.link_referensi' => ['nullable', 'url', 'max:2048'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $triwulan = 'TW '.(int) ceil($validated['bulan'] / 3);
            $dialog = DialogKinerja::updateOrCreate(
                ['user_id' => $request->user()->id, 'tahun' => $validated['tahun'], 'bulan' => $validated['bulan']],
                ['uraian' => '-', 'link_referensi' => null]
            );
            $keptIds = [];
            foreach ($validated['items'] ?? [] as $order => $item) {
                $outputTarget = OutputTarget::where('id', $item['output_target_id'])
                    ->where('user_id', $request->user()->id)
                    ->where('target_waktu', $triwulan)
                    ->firstOrFail();
                $linkRealisasi = EtppRealisasi::where('user_id', $request->user()->id)
                    ->where('output_target_id', $outputTarget->id)
                    ->where('tahun', $validated['tahun'])
                    ->where('triwulan', $triwulan)
                    ->value('link_referensi');
                $record = DialogKinerjaItem::updateOrCreate(
                    ['dialog_kinerja_id' => $dialog->id, 'output_target_id' => $outputTarget->id],
                    ['nama_output' => $outputTarget->deskripsi_output, 'uraian' => '-', 'link_referensi' => $linkRealisasi, 'urutan' => $order]
                );
                $keptIds[] = $record->id;
            }
            $dialog->items()->when($keptIds, fn ($query) => $query->whereNotIn('id', $keptIds))->delete();
        });

        return redirect()->route('etpp.dialog-kinerja.index', ['tahun' => $validated['tahun'], 'bulan' => $validated['bulan']])
            ->with('success', 'Dialog kinerja bulanan berhasil disimpan.');
    }

    public function storeRealisasi(Request $request)
    {
        $validated = $request->validate([
            'output_target_id' => ['required', 'integer'],
            'triwulan' => ['required', 'in:TW 1,TW 2,TW 3,TW 4'],
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'realisasi' => ['required', 'string', 'max:10000'],
            'link_referensi' => ['nullable', 'url', 'max:2048'],
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
            [
                'nama_output' => $outputTarget->deskripsi_output,
                'bulan' => ((int) str_replace('TW ', '', $validated['triwulan'])) * 3,
                'realisasi' => $validated['realisasi'],
                'link_referensi' => $validated['link_referensi'] ?? null,
            ]
        );

        return back()->with('success', 'Realisasi triwulan berhasil disimpan.');
    }

    public function updateRealisasi(Request $request, EtppRealisasi $realisasi)
    {
        abort_unless($realisasi->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'realisasi' => ['required', 'string', 'max:10000'],
            'link_referensi' => ['nullable', 'url', 'max:2048'],
        ]);

        $realisasi->update($validated);

        return back()->with('success', 'Realisasi triwulan berhasil diperbarui.');
    }

    public function destroyRealisasi(Request $request, EtppRealisasi $realisasi)
    {
        abort_unless($realisasi->user_id === $request->user()->id, 404);

        $realisasi->delete();

        return back()->with('success', 'Realisasi triwulan berhasil dihapus.');
    }

    public function storeDialogKinerja(Request $request)
    {
        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'uraian' => ['required', 'string', 'max:10000'],
            'link_referensi' => ['nullable', 'url', 'max:2048'],
        ]);

        DialogKinerja::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'tahun' => $validated['tahun'],
                'bulan' => $validated['bulan'],
            ],
            [
                'uraian' => $validated['uraian'],
                'link_referensi' => $validated['link_referensi'] ?? null,
            ]
        );

        return back()->with('success', 'Dialog kinerja bulanan berhasil disimpan.');
    }

    public function updateDialogKinerja(Request $request, DialogKinerja $dialogKinerja)
    {
        abort_unless($dialogKinerja->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'uraian' => ['required', 'string', 'max:10000'],
            'link_referensi' => ['nullable', 'url', 'max:2048'],
        ]);

        $dialogKinerja->update($validated);

        return back()->with('success', 'Dialog kinerja bulanan berhasil diperbarui.');
    }

    public function destroyDialogKinerja(Request $request, DialogKinerja $dialogKinerja)
    {
        abort_unless($dialogKinerja->user_id === $request->user()->id, 404);

        $dialogKinerja->delete();

        return back()->with('success', 'Dialog kinerja bulanan berhasil dihapus.');
    }

    public function downloadRealisasiPdf(Request $request)
    {
        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'bulan' => ['nullable', 'integer', 'between:1,12', 'required_without:triwulan'],
            'triwulan' => ['nullable', 'in:TW 1,TW 2,TW 3,TW 4', 'required_without:bulan'],
        ]);
        if (! isset($validated['bulan'])) {
            $validated['bulan'] = ((int) str_replace('TW ', '', $validated['triwulan'])) * 3;
        }

        $user = $request->user();
        $employee = $user->employee;
        $school = $user->school;
        $validated['triwulan'] = 'TW '.(int) ceil($validated['bulan'] / 3);
        $realisasiList = EtppRealisasi::with('outputTarget.rencanaAksi.rhk.kategori')
            ->where('user_id', $user->id)
            ->where('tahun', $validated['tahun'])
            ->where('triwulan', $validated['triwulan'])
            ->orderBy('output_target_id')
            ->get();

        $recapUrl = route('etpp.realisasi.recap', ['user' => $user->id, 'tahun' => $validated['tahun'], 'bulan' => $validated['bulan']]);
        $pdf = Pdf::loadView('etpp.pdf-realisasi', compact('user', 'employee', 'school', 'realisasiList', 'validated', 'recapUrl'))
            ->setPaper('a4', 'portrait');

        $nama = $employee?->nama_lengkap ?? $user->name;
        $nip = $employee?->nip ?? '-';
        $namaSekolah = $school?->nama_sekolah ?? 'Sekolah';
        $fileName = Str::of("Realisasi Renkin {$validated['triwulan']} - Guru Kelas SD - {$nama} - NIP {$nip} - {$namaSekolah}")
            ->replaceMatches('/[\/\\\\:*?"<>|]/', '-')
            ->squish()
            ->append('.pdf')
            ->toString();

        return $pdf->download($fileName);
    }

    public function downloadDialogKinerjaPdf(Request $request)
    {
        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'bulan' => ['required', 'integer', 'between:1,12'],
        ]);

        $user = $request->user();
        $employee = $user->employee;
        $school = $user->school;
        $dialogKinerja = DialogKinerja::where('user_id', $user->id)
            ->where('tahun', $validated['tahun'])
            ->where('bulan', $validated['bulan'])
            ->with('items')
            ->first();

        $recapUrl = route('etpp.dialog-kinerja.recap', ['user' => $user->id, 'tahun' => $validated['tahun'], 'bulan' => $validated['bulan']]);
        $pdf = Pdf::loadView('etpp.pdf-dialog-kinerja', compact('user', 'employee', 'school', 'dialogKinerja', 'validated', 'recapUrl'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Dialog_Kinerja_{$validated['tahun']}_".str_pad((string) $validated['bulan'], 2, '0', STR_PAD_LEFT).'.pdf');
    }

    public function realisasiRecap(User $user, int $tahun, int $bulan)
    {
        abort_if($tahun < 2000 || $tahun > 2100 || $bulan < 1 || $bulan > 12, 404);

        $triwulan = 'TW '.(int) ceil($bulan / 3);
        $records = EtppRealisasi::with('outputTarget')->where('user_id', $user->id)
            ->where('tahun', $tahun)->where('triwulan', $triwulan)
            ->orderBy('output_target_id')->orderBy('id')->get();

        $employee = $user->employee;
        $school = $user->school;

        return view('etpp.recap-realisasi', compact(
            'user', 'employee', 'school', 'tahun', 'bulan', 'triwulan', 'records'
        ));
    }

    public function dialogKinerjaRecap(User $user, int $tahun, int $bulan)
    {
        abort_if($tahun < 2000 || $tahun > 2100 || $bulan < 1 || $bulan > 12, 404);

        $dialogKinerja = DialogKinerja::with(['items' => fn ($query) => $query->with('outputTarget')->orderBy('urutan')->orderBy('id')])
            ->where('user_id', $user->id)
            ->where('tahun', $tahun)->where('bulan', $bulan)->firstOrFail();

        $employee = $user->employee;
        $school = $user->school;
        $triwulan = 'TW '.(int) ceil($bulan / 3);

        return view('etpp.recap-dialog-kinerja', compact(
            'user', 'employee', 'school', 'tahun', 'bulan', 'triwulan', 'dialogKinerja'
        ));
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
