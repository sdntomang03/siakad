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

        // 1. Tangani Multi-Tenant
        $selectedSchoolId = $user->hasRole('superadmin') ? $request->query('school_id') : $user->school_id;
        $schools = $user->hasRole('superadmin') ? School::orderBy('nama_sekolah')->get() : collect();

        $subjects = collect();

        if ($selectedSchoolId) {
            // Ambil mapel dan KELOMPOKKAN berdasarkan tingkat
            $subjects = Subject::where('school_id', $selectedSchoolId)
                ->orderBy('tingkat', 'asc')
                ->orderBy('nama_mapel', 'asc')
                ->get()
                ->groupBy('tingkat'); // Ini fungsi ajaib Eloquent untuk memisahkan data per-tingkat
        }

        return view('subjects.index', compact('subjects', 'schools', 'selectedSchoolId'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->hasRole('superadmin') ? $request->school_id : $user->school_id;

        $request->validate([
            'tingkat' => 'required|array|min:1', // Wajib pilih minimal 1 kotak
            'tingkat.*' => 'integer|min:1|max:6', // Setiap kotak yang dipilih harus valid (1-6)
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'nullable|string|max:20',
            'pengampu' => 'required|in:guru_kelas,guru_mapel',
            'kkm' => 'required|integer|min:0|max:100',
        ]);

        // Looping untuk setiap tingkat yang dicentang
        foreach ($request->tingkat as $t) {
            Subject::create([
                'school_id' => $schoolId,
                'tingkat' => $t,
                'nama_mapel' => $request->nama_mapel,
                'kode_mapel' => $request->kode_mapel ?? null,
                'pengampu' => $request->pengampu,
                'kkm' => $request->kkm,
            ]);
        }

        return back()->with('success', 'Mata Pelajaran berhasil ditambahkan ke tingkat yang dipilih.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return back()->with('success', 'Mata Pelajaran berhasil dihapus.');
    }
}
