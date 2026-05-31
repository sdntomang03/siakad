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
     * MEMPROSES FORM PENCARIAN
     */
    public function cekKelulusan(Request $request)
    {
        $request->validate([
            'nisn' => 'required',
            'tanggal_lahir' => 'required|date',
        ]);

        $data = Kelulusan::withoutGlobalScope('school')
            ->where('nisn', $request->nisn)
            ->where('tanggal_lahir', $request->tanggal_lahir)
            ->first();

        if ($data) {
            // Buat nomor verifikasi
            $salt = 'TOMANG03PAGI_SECURE_KEY';
            $hash = hash('sha256', $data->nisn.$data->tanggal_lahir.$salt);
            $year = date('Y');
            $part1 = strtoupper(substr($hash, 0, 4));
            $part2 = strtoupper(substr($hash, 4, 8));
            $secureNumber = "SKL-{$year}-{$part1}-{$part2}";

            // Simpan data di session (hanya berlaku 1x request) lalu arahkan ke halaman hasil
            return redirect()->route('kelulusan.hasil')->with([
                'studentData' => $data,
                'secureNumber' => $secureNumber,
            ]);
        }

        // Jika salah, kembali ke halaman input dengan pesan error
        return redirect()->back()->with('error', 'NISN atau Tanggal Lahir tidak terdaftar. Pastikan data sudah benar!');
    }

    /**
     * MENAMPILKAN HALAMAN HASIL
     */
    public function halamanHasil()
    {
        // Cegah akses langsung URL jika belum mengisi form
        if (! session('studentData')) {
            return redirect()->route('kelulusan.pengumuman');
        }

        return view('kelulusan.hasil');
    }

    public function deleteAll()
    {
        try {
            // Berkat Trait BelongsToSchool, query ini otomatis disaring
            // Hanya menghapus data dengan school_id milik user yang sedang login
            Kelulusan::query()->delete();

            return redirect()->back()->with('success', 'Seluruh data kelulusan berhasil dikosongkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: '.$e->getMessage());
        }
    }

    public function updateStatusAjax(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|in:LULUS,TIDAK LULUS,DITUNDA',
        ]);

        $siswa = Kelulusan::findOrFail($id); // Sesuaikan dengan model siswa Anda
        $siswa->keterangan = $request->keterangan;
        $siswa->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status kelulusan berhasil diperbarui.',
        ]);
    }
}
