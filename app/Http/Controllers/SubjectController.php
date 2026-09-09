<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $selectedSchoolId = $user->hasRole('superadmin') ? $request->query('school_id') : $user->school_id;
        $schools = $user->hasRole('superadmin') ? School::orderBy('nama_sekolah')->get() : collect();

        $subjects = collect();

        if ($selectedSchoolId) {
            $subjects = Subject::where('school_id', $selectedSchoolId)
                ->orderBy('tingkat', 'asc')
                ->orderBy('nama_mapel', 'asc')
                ->get()
                ->groupBy('tingkat');
        }

        return view('subjects.index', compact('subjects', 'schools', 'selectedSchoolId'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->hasRole('superadmin') ? $request->school_id : $user->school_id;

        $request->validate([
            'tingkat' => 'required|array|min:1',
            'tingkat.*' => 'integer|min:1|max:6',
            'mapel_default' => 'nullable|array',
            'nama_mapel_kustom' => 'nullable|string|max:255',
            'kode_mapel_kustom' => 'nullable|string|max:20',
            'pengampu' => 'required|in:guru_kelas,guru_mapel',
            'kkm' => 'required|integer|min:0|max:100',
        ]);

        if (empty($request->mapel_default) && empty($request->nama_mapel_kustom)) {
            return back()->withErrors(['mapel_error' => 'Pilih minimal satu mata pelajaran dari daftar, atau ketik nama mapel kustom.'])->withInput();
        }

        $mapelsToCreate = [];

        // 1. Kumpulkan mapel dari daftar pilihan yang dicentang (Generate kode otomatis)
        if (! empty($request->mapel_default)) {
            foreach ($request->mapel_default as $mapelName) {
                $mapelsToCreate[] = [
                    'nama_mapel' => $mapelName,
                    'kode_mapel' => $this->generateKodeMapel($mapelName),
                ];
            }
        }

        // 2. Kumpulkan mapel kustom (Jika kode kosong, generate otomatis dari nama)
        if (! empty($request->nama_mapel_kustom)) {
            $mapelsToCreate[] = [
                'nama_mapel' => $request->nama_mapel_kustom,
                'kode_mapel' => $request->kode_mapel_kustom ?? $this->generateKodeMapel($request->nama_mapel_kustom),
            ];
        }

        // 3. Simpan ke database
        foreach ($request->tingkat as $t) {
            foreach ($mapelsToCreate as $mapel) {
                Subject::create([
                    'school_id' => $schoolId,
                    'tingkat' => $t,
                    'nama_mapel' => $mapel['nama_mapel'],
                    'kode_mapel' => $mapel['kode_mapel'],
                    'pengampu' => $request->pengampu,
                    'kkm' => $request->kkm,
                ]);
            }
        }

        return back()->with('success', count($mapelsToCreate).' Mata Pelajaran berhasil ditambahkan ke tingkat yang dipilih.');
    }

    // FUNGSI UNTUK MENGHAPUS SATUAN
    public function destroy(Subject $subject)
    {
        $subject->delete();

        return back()->with('success', 'Mata Pelajaran berhasil dihapus.');
    }

    // FUNGSI UNTUK HAPUS MASSAL (BULK DELETE)
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        Subject::whereIn('id', $request->subject_ids)->delete();

        return back()->with('success', count($request->subject_ids).' Mata Pelajaran berhasil dihapus secara massal.');
    }

    /**
     * Helper: Generate Kode Mapel secara otomatis.
     */
    private function generateKodeMapel($namaMapel)
    {
        // 1. Cek dari kamus mapel baku terlebih dahulu
        $kamusBaku = [
            'Pendidikan Agama dan Budi Pekerti' => 'PABP',
            'Pendidikan Pancasila' => 'PKn',
            'Bahasa Indonesia' => 'BIND',
            'Matematika' => 'MTK',
            'Pendidikan Jasmani Olahraga dan Kesehatan' => 'PJOK',
            'Ilmu Pengetahuan Alam dan Sosial' => 'IPAS',
            'Seni dan Budaya' => 'SB',
            'Pendidikan Lingkungan dan Budaya Jakarta' => 'PLBJ',
            'Bahasa Inggris' => 'BING',
        ];

        if (array_key_exists($namaMapel, $kamusBaku)) {
            return $kamusBaku[$namaMapel];
        }

        // 2. Fallback untuk mapel kustom: Ambil huruf kapital pertama dari setiap kata
        // Mengabaikan kata sambung seperti 'dan' agar lebih rapi.
        $words = explode(' ', $namaMapel);
        $kode = '';
        foreach ($words as $word) {
            if (strtolower($word) !== 'dan' && strtolower($word) !== 'yang') {
                $kode .= strtoupper(substr($word, 0, 1));
            }
        }

        // Batasi panjang kode maksimal 6 karakter
        return substr($kode, 0, 6);
    }

    // ==============================================
    // FUNGSI MENGUBAH STATUS SIDANIRA (Tanpa Reload)
    // ==============================================
    public function toggleSidanira(Subject $subject, Request $request)
    {
        // Validasi Keamanan Sekolah
        if ($subject->school_id != auth()->user()->school_id && ! auth()->user()->hasRole('superadmin')) {
            return response()->json(['success' => false, 'message' => 'Tidak ada izin aksi (Unauthorized).'], 403);
        }

        // Pastikan Sidanira hanya untuk kelas 4, 5, dan 6
        if (in_array($subject->tingkat, [4, 5, 6])) {
            $subject->update([
                'is_sidanira' => ! $subject->is_sidanira, // Balikkan status
            ]);

            return response()->json([
                'success' => true,
                'is_sidanira' => $subject->is_sidanira,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Hanya berlaku untuk kelas 4, 5, dan 6.'], 400);
    }

    // ==============================================
    // FUNGSI SIMPAN URUTAN MASSAL (AJAX)
    // ==============================================
    public function bulkUpdateUrutan(Request $request)
    {
        $request->validate([
            'urutan' => 'required|array',
            'urutan.*' => 'required|integer|min:1',
        ]);

        $user = auth()->user();
        $isSuperadmin = $user->hasRole('superadmin');

        // Looping semua data urutan yang dikirim
        foreach ($request->urutan as $subjectId => $nilaiUrutan) {
            $subject = Subject::find($subjectId);

            // Keamanan: Pastikan mapel ada dan milik sekolah tersebut
            if ($subject && ($isSuperadmin || $subject->school_id == $user->school_id)) {
                $subject->update(['urutan' => $nilaiUrutan]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan mata pelajaran berhasil diperbarui!',
        ]);
    }
}
