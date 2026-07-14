<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Carbon\Carbon;
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

    // Menyimpan hari libur baru (Mendukung Rentang Tanggal)
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string|max:255',
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);

        // Gunakan tanggal selesai jika diisi, jika tidak gunakan tanggal mulai (hanya 1 hari)
        $start = Carbon::parse($request->tanggal_mulai);
        $end = $request->tanggal_selesai ? Carbon::parse($request->tanggal_selesai) : $start->copy();

        $insertedCount = 0;

        // Looping dari tanggal mulai hingga tanggal selesai
        for ($date = $start; $date->lte($end); $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');

            // Cek apakah tanggal ini sudah ada di database agar tidak duplikat
            $exists = Holiday::where('tanggal', $formattedDate)->exists();

            if (! $exists) {
                Holiday::create([
                    'tanggal' => $formattedDate,
                    'keterangan' => $request->keterangan,
                ]);
                $insertedCount++;
            }
        }

        // Tampilkan pesan sesuai hasil
        if ($insertedCount > 0) {
            return back()->with('success', "Berhasil menambahkan $insertedCount hari libur.");
        }

        return back()->with('warning', 'Semua tanggal pada rentang tersebut sudah terdaftar sebagai hari libur.');
    }

    // Menghapus hari libur jika terjadi kesalahan
    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return back()->with('success', 'Hari Libur berhasil dihapus.');
    }
}
