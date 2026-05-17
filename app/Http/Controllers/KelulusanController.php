<?php

namespace App\Http\Controllers;

use App\Imports\KelulusanImport;
use App\Models\Kelulusan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class KelulusanController extends Controller
{
    /**
     * MENAMPILKAN HALAMAN IMPORT
     */
    public function showImportForm()
    {
        // Mengambil data kelulusan yang sudah di-import sebelumnya (untuk pratinjau/preview)
        // Global scope 'school' dari trait otomatis akan menyaring data sesuai sekolah user yang login
        $dataKelulusan = Kelulusan::latest()->paginate(10);

        return view('kelulusan.import', compact('dataKelulusan'));
    }

    /**
     * MEMPROSES IMPORT EXCEL VIA WEB FORM
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120', // Maksimal 5MB
        ], [
            'file.required' => 'Pilih file Excel terlebih dahulu!',
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv',
            'file.max' => 'Ukuran file tidak boleh lebih dari 5MB',
        ]);

        try {
            Excel::import(new KelulusanImport, $request->file('file'));

            return redirect()->back()->with('success', 'Selamat! Data kelulusan siswa berhasil diunggah ke sistem.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunggah data. Pastikan format kolom Excel Anda sudah sesuai aturan. Error: '.$e->getMessage());
        }
    }

    public function portalPengumuman()
    {
        return view('kelulusan.pengumuman');
    }

    /**
     * MENGECEK KELULUSAN VIA API / AJAX (UNTUK SISWA)
     */
    public function cekKelulusan(Request $request)
    {
        // 1. Validasi inputan dari form Alpine.js
        $request->validate([
            'nisn' => 'required',
            'tanggal_lahir' => 'required|date',
        ]);

        // 2. Cari data berdasarkan NISN dan Tanggal Lahir
        // Kita gunakan withoutGlobalScope('school') agar siswa dari luar
        // tetap bisa membaca datanya meskipun mereka tidak login.
        $data = Kelulusan::withoutGlobalScope('school')
            ->where('nisn', $request->nisn)
            ->where('tanggal_lahir', $request->tanggal_lahir)
            ->first();

        // 3. Jika data cocok, kirim JSON sukses ke halaman pengumuman
        if ($data) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'nama' => $data->nama,
                    'nisn' => $data->nisn,
                    'keterangan' => $data->keterangan, // LULUS / TIDAK LULUS / DITUNDA
                ],
            ]);
        }

        // 4. Jika data tidak ditemukan / salah ketik
        return response()->json([
            'status' => 'error',
            'message' => 'Data tidak ditemukan. Pastikan NISN dan Tanggal Lahir (Sesuai ijazah) sudah benar.',
        ], 404);
    }
}
