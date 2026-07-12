<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ModulAjar;
use App\Models\School;
use Illuminate\Http\Request;

class ModulAjarController extends Controller
{
    // Menampilkan halaman Generator
    public function index()
    {
        $user = auth()->user();

        // Mengambil data sekolah tempat guru tersebut bernaung
        $sekolah = School::find($user->school_id);

        // Mengambil tahun ajaran aktif
        $activeYear = AcademicYear::where('school_id', $user->school_id)
            ->where('is_active', true)
            ->first();

        // Mengambil data guru (Asumsi relasi user ke tabel employee/karyawan)
        $namaGuru = $user->nama_lengkap ?? $user->name;
        $nipGuru = $user->employee->nip ?? '-';

        return view('modul-ajar.generator', compact('sekolah', 'activeYear', 'namaGuru', 'nipGuru'));
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
}
