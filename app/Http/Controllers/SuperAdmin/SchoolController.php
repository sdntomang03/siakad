<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::latest()->paginate(10);

        return view('superadmin.schools.index', compact('schools'));
    }

    // 3. Menyimpan data sekolah baru ke database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'npsn' => 'required|unique:schools,npsn',
            'nama_sekolah' => 'required|string|max:255',
            'kepala_sekolah' => 'nullable|string|max:255',
            'nip' => 'nullable|string|max:255',
            'tingkat' => 'required|in:SD,SMP,SMA,SMK',
            'alamat' => 'nullable|string',
            'kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kota' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);

        // Set default status ke true jika tidak dikirim dari form
        $validated['status'] = $request->has('status') ? true : false;

        School::create($validated);

        return redirect()->route('superadmin.schools.index')->with('success', 'Sekolah berhasil ditambahkan!');
    }

    // 5. Memperbarui data sekolah di database
    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'npsn' => 'required|unique:schools,npsn,'.$school->id,
            'nama_sekolah' => 'required|string|max:255',
            'kepala_sekolah' => 'nullable|string|max:255',
            'nip' => 'nullable|string|max:255',
            'tingkat' => 'required|in:SD,SMP,SMA,SMK',
            'alamat' => 'nullable|string',
            'kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kota' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);

        $validated['status'] = $request->has('status') ? true : false;

        $school->update($validated);

        return redirect()->route('superadmin.schools.index')->with('success', 'Data sekolah berhasil diperbarui!');
    }

    // 6. Menghapus data sekolah
    public function destroy(School $school)
    {
        $school->delete();

        return redirect()->route('superadmin.schools.index')->with('success', 'Sekolah berhasil dihapus!');
    }
}
