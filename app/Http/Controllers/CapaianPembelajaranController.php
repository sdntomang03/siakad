<?php

namespace App\Http\Controllers;

use App\Models\CapaianPembelajaran;
use Illuminate\Http\Request;

class CapaianPembelajaranController extends Controller
{
    // Menampilkan halaman form upload JSON
    public function importForm()
    {
        return view('cp.import');
    }

    // Memproses file JSON yang diunggah
    public function importProcess(Request $request)
    {
        // Validasi wajib upload file
        $request->validate([
            'file_json' => 'required|file',
        ]);

        $file = $request->file('file_json');

        // Baca isi file JSON
        $jsonContent = file_get_contents($file->getRealPath());
        $data = json_decode($jsonContent, true);

        if (! $data) {
            return back()->with('error', 'Gagal membaca file. Pastikan format JSON sudah benar.');
        }

        $jumlahDiimpor = 0;

        // Looping data utama (Key berupa "Mapel_Fase")
        foreach ($data as $kategori => $daftarCp) {
            // Pecah string (Contoh: "Pendidikan Pancasila_Fase A")
            $pecahKategori = explode('_', $kategori);

            // Lewati jika format kunci tidak sesuai (tidak mengandung underscore)
            if (count($pecahKategori) < 2) {
                continue;
            }

            $mataPelajaran = trim($pecahKategori[0]); // Mendapatkan "Pendidikan Pancasila"
            $fase = trim($pecahKategori[1]);          // Mendapatkan "Fase A"

            // Looping isi array di dalam kategori tersebut
            foreach ($daftarCp as $item) {
                CapaianPembelajaran::updateOrCreate(
                    ['kode_cp' => $item['id']], // Cari berdasarkan ID unik dari JSON
                    [
                        'mata_pelajaran' => $mataPelajaran,
                        'fase' => $fase,
                        'elemen' => $item['elemen'],
                        'deskripsi_cp' => $item['cp'],
                    ]
                );
                $jumlahDiimpor++;
            }
        }

        return back()->with('success', "Berhasil mengimpor {$jumlahDiimpor} data Capaian Pembelajaran!");
    }

    // 1. Tampilkan Daftar CP (Dengan Filter)
    public function index(Request $request)
    {
        // Ambil daftar unik untuk dropdown filter
        $mapelList = CapaianPembelajaran::select('mata_pelajaran')
            ->distinct()
            ->orderBy('mata_pelajaran')
            ->pluck('mata_pelajaran');

        $faseList = CapaianPembelajaran::select('fase')
            ->distinct()
            ->orderBy('fase')
            ->pluck('fase');

        $query = CapaianPembelajaran::query();

        // Filter Pencarian Teks (Mencari di elemen atau deskripsi)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('elemen', 'like', "%{$search}%")
                    ->orWhere('deskripsi_cp', 'like', "%{$search}%");
            });
        }

        // Filter Mata Pelajaran
        if ($request->filled('mata_pelajaran')) {
            $query->where('mata_pelajaran', $request->mata_pelajaran);
        }

        // Filter Fase
        if ($request->filled('fase')) {
            $query->where('fase', $request->fase);
        }

        // Tambahkan withQueryString() agar filter tidak hilang saat pindah halaman (pagination)
        $cps = $query->orderBy('mata_pelajaran')->orderBy('fase')->paginate(15)->withQueryString();

        return view('cp.index', compact('cps', 'mapelList', 'faseList'));
    }

    // 2. Form Tambah Data
    public function create()
    {
        return view('cp.create');
    }

    // 3. Simpan Data Baru
    public function store(Request $request)
    {
        $request->validate([
            'kode_cp' => 'required|string|unique:capaian_pembelajarans,kode_cp',
            'mata_pelajaran' => 'required|string|max:255',
            'fase' => 'required|string|max:50',
            'elemen' => 'required|string|max:255',
            'deskripsi_cp' => 'required|string',
        ]);

        CapaianPembelajaran::create($request->all());

        return redirect()->route('cp.index')->with('success', 'Data Capaian Pembelajaran berhasil ditambahkan.');
    }

    // 4. Form Edit Data
    public function edit(CapaianPembelajaran $cp)
    {
        return view('cp.edit', compact('cp'));
    }

    // 5. Update Data
    public function update(Request $request, CapaianPembelajaran $cp)
    {
        $request->validate([
            'kode_cp' => 'required|string|unique:capaian_pembelajarans,kode_cp,'.$cp->id,
            'mata_pelajaran' => 'required|string|max:255',
            'fase' => 'required|string|max:50',
            'elemen' => 'required|string|max:255',
            'deskripsi_cp' => 'required|string',
        ]);

        $cp->update($request->all());

        return redirect()->route('cp.index')->with('success', 'Data Capaian Pembelajaran berhasil diperbarui.');
    }

    // 6. Hapus Data
    public function destroy(CapaianPembelajaran $cp)
    {
        $cp->delete();

        return redirect()->route('cp.index')->with('success', 'Data berhasil dihapus.');
    }
}
