<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetPlacement;
use App\Models\Classroom;
use App\Models\Room;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;

        $query = AssetPlacement::with(['asset', 'classroom', 'room'])
            ->whereHas('asset', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId)->where('status_persetujuan', 'disetujui');
            });

        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }
        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        $placements = $query->latest()->get();
        $classrooms = Classroom::where('school_id', $schoolId)->orderBy('tingkat')->get();
        $rooms = Room::where('school_id', $schoolId)->orderBy('nama_ruangan')->get();

        $pendingAssets = Asset::with('pengaju')
            ->where('school_id', $schoolId)
            ->where('status_persetujuan', 'pending')
            ->get();

        return view('admin.assets.index', compact('placements', 'classrooms', 'rooms', 'pendingAssets'));
    }

    // 1. INPUT MASTER ASET DARI ADMIN (MENGISI TOTAL STOK SEKOLAH)
    public function store(Request $request)
    {
        $request->validate([
            'nama_aset' => 'required|string|max:255',
            'kode_aset' => 'required|string|max:50',
            'total_stok' => 'required|integer|min:0',
        ]);

        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;

        Asset::create([
            'school_id' => $schoolId,
            'nama_aset' => $request->nama_aset,
            'kode_aset' => $request->kode_aset,
            'total_stok' => $request->total_stok,
            'status_persetujuan' => 'disetujui',
            'diajukan_oleh' => $user->id,
        ]);

        return back()->with('success', 'Master aset baru dengan kapasitas total stok berhasil didaftarkan.');
    }

    // 2. LOGIKA PENEMPATAN FASILITAS (TRACKING SISA GUDANG)
    public function storePlacement(Request $request)
    {
        $request->validate([
            'jenis_input' => 'required|in:database,baru',
            'jumlah' => 'required|integer|min:1',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'classroom_id' => 'nullable|integer|exists:classrooms,id',
            'room_id' => 'nullable|integer|exists:rooms,id',
            'keterangan' => 'nullable|string',
        ]);

        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;

        if ($request->jenis_input === 'baru') {
            $request->validate(['nama_aset_baru' => 'required|string|max:255']);

            // Jika diajukan baru oleh guru, total stok awal otomatis senilai jumlah penempatan pertama
            $asset = Asset::create([
                'school_id' => $schoolId,
                'nama_aset' => $request->nama_aset_baru,
                'total_stok' => $request->jumlah,
                'status_persetujuan' => 'pending',
                'diajukan_oleh' => $user->id,
            ]);
        } else {
            $request->validate(['asset_id' => 'required|exists:assets,id']);
            $asset = Asset::find($request->asset_id);

            // LOGIKA CRITICAL: Hitung sisa kapasitas gudang
            $totalTersebar = AssetPlacement::where('asset_id', $asset->id)->sum('jumlah');
            $sisaGudang = $asset->total_stok - $totalTersebar;

            if ($request->jumlah > $sisaGudang) {
                return back()->withErrors(['asset_id' => 'Gagal menempatkan barang. Stok di gudang tidak mencukupi (Sisa di gudang saat ini: '.$sisaGudang.' Unit).'])->withInput();
            }
        }

        AssetPlacement::create([
            'asset_id' => $asset->id,
            'classroom_id' => $request->classroom_id,
            'room_id' => $request->room_id,
            'jumlah' => $request->jumlah,
            'kondisi' => $request->kondisi,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Data inventaris berhasil ditempatkan ke ruangan.');
    }

    public function listMasterAssets(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;

        // Mengambil sum total kuantitas penempatan secara dinamis
        $query = Asset::where('school_id', $schoolId)
            ->where('status_persetujuan', 'disetujui')
            ->withCount(['placements as total_tersebar' => function ($q) {
                $q->select(\DB::raw('coalesce(sum(jumlah), 0)'));
            }]);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_aset', 'like', '%'.$request->search.'%')
                    ->orWhere('kode_aset', 'like', '%'.$request->search.'%');
            });
        }

        $assets = $query->orderBy('nama_aset')->paginate(25);

        return view('admin.assets.list', compact('assets'));
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'nama_aset' => 'required|string|max:255',
            'kode_aset' => 'required|string|max:50',
            'total_stok' => 'required|integer|min:0',
        ]);

        $totalTersebar = AssetPlacement::where('asset_id', $asset->id)->sum('jumlah');
        if ($request->total_stok < $totalTersebar) {
            return back()->with('error', 'Total stok tidak boleh lebih rendah dari jumlah aset yang sudah tersebar di kelas/ruangan ('.$totalTersebar.' Unit).');
        }

        $asset->update([
            'nama_aset' => $request->nama_aset,
            'kode_aset' => $request->kode_aset,
            'total_stok' => $request->total_stok,
        ]);

        return back()->with('success', 'Informasi master aset dan total kuantitas berhasil diupdate.');
    }

    public function approve(Request $request, Asset $asset)
    {
        $request->validate(['kode_aset' => 'required|string|max:50']);
        $asset->update(['status_persetujuan' => 'disetujui', 'kode_aset' => $request->kode_aset]);

        return back()->with('success', 'Aset baru disetujui.');
    }

    public function reject(Asset $asset)
    {
        $asset->update(['status_persetujuan' => 'ditolak']);
        AssetPlacement::where('asset_id', $asset->id)->delete();

        return back()->with('success', 'Pengajuan aset ditolak.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();

        return back()->with('success', 'Master aset dihapus permanen.');
    }

    // HALAMAN GURU/STAF: Form Input Aset & Daftar Inventaris Terinput
    public function createPlacement(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? optional($user->employee)->school_id;

        // Ambil data untuk opsi pilihan lokasi di form
        $classrooms = Classroom::where('school_id', $schoolId)->orderBy('tingkat')->orderBy('nama_kelas')->get();
        $rooms = Room::where('school_id', $schoolId)->orderBy('nama_ruangan')->get();

        // Ambil daftar master aset yang sudah disetujui agar bisa dipilih di dropdown
        $masterAssets = Asset::where('school_id', $schoolId)
            ->where('status_persetujuan', 'disetujui')
            ->orderBy('nama_aset')
            ->get();

        // Riwayat penempatan barang (log input)
        $myPlacements = AssetPlacement::with(['asset', 'classroom', 'room'])
            ->whereHas('asset', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->latest()
            ->paginate(15);

        return view('assets.placement', compact('classrooms', 'rooms', 'masterAssets', 'myPlacements'));
    }

    // Mengubah kondisi barang langsung dari detail ruangan
    public function updatePlacementCondition(Request $request, AssetPlacement $placement)
    {
        $request->validate([
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $placement->update([
            'kondisi' => $request->kondisi,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Data kondisi barang berhasil diperbarui.');
    }

    // Menarik/menghapus barang keluar dari ruangan tersebut
    public function destroyPlacement(AssetPlacement $placement)
    {
        $placement->delete();

        return back()->with('success', 'Barang berhasil dikeluarkan dari inventaris ruangan ini.');
    }
}
