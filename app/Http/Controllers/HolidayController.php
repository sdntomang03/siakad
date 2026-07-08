<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    // Menampilkan halaman dan daftar hari libur
    public function index()
    {
        // Mengambil semua data hari libur, diurutkan dari yang paling baru
        $holidays = Holiday::orderBy('tanggal', 'desc')->get();

        return view('holidays.index', compact('holidays'));
    }

    // Menyimpan hari libur baru
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|unique:holidays,tanggal',
            'keterangan' => 'required|string|max:255',
        ], [
            'tanggal.unique' => 'Tanggal ini sudah pernah didaftarkan sebagai hari libur.',
        ]);

        Holiday::create([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Hari Libur berhasil ditambahkan.');
    }

    // Menghapus hari libur jika terjadi kesalahan
    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return back()->with('success', 'Hari Libur berhasil dihapus.');
    }
}
