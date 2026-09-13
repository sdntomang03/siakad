<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ModulAjar;
use App\Models\School;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ModulAjarController extends Controller
{
    // Menampilkan halaman Generator
    public function index()
    {
        $user = auth()->user();
        $sekolah = School::find($user->school_id);

        $activeYear = AcademicYear::where('school_id', $user->school_id)
            ->where('is_active', true)
            ->first();

        $namaGuru = $user->employee->nama_lengkap ?? 'Guru Tidak Ditemukan';
        $nipGuru = $user->employee->nip ?? '-';

        // BARU: Ambil riwayat modul ajar milik user yang login untuk ditampilkan di dropdown
        $savedModuls = ModulAjar::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('modul-ajar.generator', compact('sekolah', 'activeYear', 'namaGuru', 'nipGuru', 'savedModuls'));
    }

    // Menyimpan Modul ke Database (Menerima AJAX POST dari View)
    public function store(Request $request)
    {
        $request->validate([
            'tingkat' => 'required',
            'mata_pelajaran' => 'required',
            'topik' => 'required',
            'html_content' => 'required',
            'academic_year_id' => 'required',
        ]);

        $modul = ModulAjar::create([
            'school_id' => auth()->user()->school_id,
            'user_id' => auth()->id(),
            'academic_year_id' => $request->academic_year_id,
            'tingkat' => $request->tingkat,
            'mata_pelajaran' => $request->mata_pelajaran,
            'topik' => $request->topik,
            'html_content' => $request->html_content,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Modul berhasil disimpan ke database!',
            'modul_id' => $modul->id,
        ]);
    }

    // BARU: Fungsi untuk mengambil 1 modul via AJAX
    public function show($id)
    {
        $modul = ModulAjar::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $modul,
        ]);
    }

    // BARU: Fungsi untuk Update HTML modul via AJAX
    public function update(Request $request, $id)
    {
        $request->validate([
            'html_content' => 'required',
        ]);

        $modul = ModulAjar::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $modul->update([
            'html_content' => $request->html_content,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Modul berhasil diperbarui!',
        ]);
    }

    public function downloadPdf($id)
    {
        $modul = ModulAjar::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        // Load view khusus PDF (kita buat di langkah 4)
        $pdf = Pdf::loadView('modul-ajar.pdf', compact('modul'));

        // Atur ukuran kertas ke A4 Portrait
        $pdf->setPaper('a4', 'portrait');

        // Unduh otomatis
        return $pdf->download('Modul_Ajar_'.str_replace(' ', '_', $modul->mata_pelajaran).'.pdf');
    }
}
