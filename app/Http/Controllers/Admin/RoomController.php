<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetPlacement;
use App\Models\Classroom;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Menampilkan DAFTAR SELURUH RUANGAN (Gabungan Kelas & Ruangan Lain)
    public function index()
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;

        // 1. Ambil Data Ruangan Non-Kelas
        $normalRooms = Room::where('school_id', $schoolId)
            ->withCount('placements')
            ->get()
            ->map(function ($room) {
                return [
                    'id' => $room->id,
                    'nama_ruangan' => $room->nama_ruangan,
                    'deskripsi' => $room->deskripsi ?? 'Ruangan Fasilitas',
                    'tipe' => 'Ruangan Lain',
                    'placements_count' => $room->placements_count,
                    'raw_data' => $room, // disimpan untuk modal edit nanti
                ];
            });

        // 2. Ambil Data Ruang Kelas
        $classroomRooms = Classroom::where('school_id', $schoolId)
            ->get()
            ->map(function ($class) {
                // Hitung jumlah aset yang ditempatkan di kelas ini
                $assetsCount = AssetPlacement::where('classroom_id', $class->id)->sum('jumlah');

                return [
                    'id' => $class->id,
                    'nama_ruangan' => 'Kelas '.$class->tingkat.' '.$class->nama_kelas,
                    'deskripsi' => 'Ruang Kelas Pembelajaran Aktif',
                    'tipe' => 'Kelas',
                    'placements_count' => $assetsCount,
                    'raw_data' => $class,
                ];
            });

        // 3. Gabungkan Kedua Koleksi & Urutkan Berdasarkan Nama
        $allRooms = $normalRooms->concat($classroomRooms)->sortBy('nama_ruangan')->values();

        return view('admin.rooms.index', ['rooms' => $allRooms]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;

        Room::create([
            'school_id' => $schoolId,
            'nama_ruangan' => $request->nama_ruangan,
            'deskripsi' => $request->deskripsi,
        ]);

        return back()->with('success', 'Ruangan baru berhasil ditambahkan.');
    }

    public function showAssets($type, $id)
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;

        if ($type === 'kelas') {
            $roomDetails = Classroom::where('school_id', $schoolId)->findOrFail($id);
            $namaRuangan = 'Kelas '.$roomDetails->tingkat.' '.$roomDetails->nama_kelas;
            $deskripsi = 'Ruang Kelas Pembelajaran Aktif';

            // Ambil barang-barang yang ada di kelas ini
            $placements = AssetPlacement::with('asset')
                ->where('classroom_id', $id)
                ->get();
        } else {
            $roomDetails = Room::where('school_id', $schoolId)->findOrFail($id);
            $namaRuangan = $roomDetails->nama_ruangan;
            $deskripsi = $roomDetails->deskripsi ?? 'Ruangan Fasilitas';

            // Ambil barang-barang yang ada di ruangan ini
            $placements = AssetPlacement::with('asset')
                ->where('room_id', $id)
                ->get();
        }

        return view('admin.rooms.show_assets', compact('namaRuangan', 'deskripsi', 'placements', 'type', 'id'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
        ]);

        $room->update([
            'nama_ruangan' => $request->nama_ruangan,
            'deskripsi' => $request->deskripsi,
        ]);

        return back()->with('success', 'Data ruangan berhasil diperbarui.');
    }

    public function destroy(Room $room)
    {
        $room->delete();

        return back()->with('success', 'Ruangan berhasil dihapus.');
    }
}
