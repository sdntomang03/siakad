<?php

namespace App\Http\Controllers;

use App\Imports\KelulusanImport;
use App\Models\Kelulusan;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
            $salt = 'TOMANG03PAGI_SECURE_KEY';
            $hash = hash('sha256', $data->nisn.$data->tanggal_lahir.$salt);
            $year = date('Y');
            $secureNumber = "SKL-{$year}-".strtoupper(substr($hash, 0, 4)).'-'.strtoupper(substr($hash, 4, 8));

            $token = Crypt::encryptString($data->nisn);

            return redirect()->route('kelulusan.hasil')->with([
                'studentData' => $data,
                'secureNumber' => $secureNumber,
                'token' => $token,
            ]);
        }

        return redirect()->back()->with('error', 'NISN atau Tanggal Lahir tidak terdaftar.');
    }

    /**
     * MENAMPILKAN HALAMAN HASIL
     */
    public function halamanHasil()
    {
        // Cegah akses langsung URL jika belum mengisi form
        if (! session('studentData')) {
            return redirect()->route('pengumuman.index')->with('error', 'Silakan isi form pencarian terlebih dahulu untuk melihat hasilnya.');
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

    /**
     * FUNGSI UNTUK DOWNLOAD PDF
     */
    public function downloadSKL($token, PDF $pdf) // <-- Tambahkan parameter PDF $pdf di sini
    {
        try {
            $nisn = Crypt::decryptString($token);
            $data = Kelulusan::withoutGlobalScope('school')->where('nisn', $nisn)->firstOrFail();

            if ($data->keterangan !== 'LULUS') {
                abort(403, 'Akses Ditolak. Dokumen hanya untuk siswa yang lulus.');
            }

            $urlValidasi = route('kelulusan.validasi', $token);
            $qrCode = base64_encode(QrCode::format('svg')->size(100)->generate($urlValidasi));

            // Gunakan variabel $pdf langsung dari parameter (TIDAK ADA LAGI PANGGILAN STATIC :: )
            $dokumen = $pdf->loadView('kelulusan.pdf', compact('data', 'qrCode', 'urlValidasi'));

            $kertasF4 = [0, 0, 609.4488, 935.433];
            $dokumen->setPaper($kertasF4, 'portrait');

            return $dokumen->download('SKL_'.$data->nisn.'_'.$data->nama.'.pdf');

        } catch (\Exception $e) {
            abort(404, 'Dokumen tidak valid atau token kedaluwarsa.');
        }
    }

    /**
     * FUNGSI UNTUK HALAMAN VALIDASI QR CODE
     */
    public function validasiSKL($token)
    {
        try {
            $nisn = Crypt::decryptString($token);
            $data = Kelulusan::withoutGlobalScope('school')->where('nisn', $nisn)->firstOrFail();

            // Generate ulang ID Validasi yang sama persis seperti di PDF & Halaman Hasil
            $salt = 'TOMANG03PAGI_SECURE_KEY';
            $hash = hash('sha256', $data->nisn.$data->tanggal_lahir.$salt);
            $year = date('Y');
            $secureNumber = "SKL-{$year}-".strtoupper(substr($hash, 0, 4)).'-'.strtoupper(substr($hash, 4, 8));

            // Kirim variabel $secureNumber ke view
            return view('kelulusan.validasi', compact('data', 'secureNumber'));
        } catch (\Exception $e) {
            abort(404, 'Dokumen tidak valid atau tidak terdaftar di sistem kami.');
        }
    }
}
