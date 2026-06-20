<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\School;
use Illuminate\Http\Request;
class AcademicYearController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-academic-years')->only('index');
        $this->middleware('permission:create-academic-years')->only('store');
        $this->middleware('permission:edit-academic-years')->only(['aktifkan', 'update']);
        $this->middleware('permission:delete-academic-years')->only('destroy');
    }

    public function index()
    {
        $user = auth()->user();
        $query = AcademicYear::with('school')->orderBy('tahun_ajaran', 'desc');

        if (! $user->hasRole('superadmin')) {
            $query->where('school_id', $user->school_id);
        }

        // Paginate 20 agar grouping di blade tetap rapi (10 tahun per halaman)
        $academicYears = $query->paginate(20);
        $schools = $user->hasRole('superadmin') ? School::all() : [];

        return view('academic_years.index', compact('academicYears', 'schools'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'tahun_ajaran' => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'school_id' => $user->hasRole('superadmin') ? 'required|exists:schools,id' : 'nullable',
        ]);

        $school_id = $user->hasRole('superadmin') ? $request->school_id : $user->school_id;

        $exists = AcademicYear::where('school_id', $school_id)
            ->where('tahun_ajaran', $request->tahun_ajaran)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Tahun pelajaran ini sudah ada.');
        }

        AcademicYear::insert([
            ['school_id' => $school_id, 'tahun_ajaran' => $request->tahun_ajaran, 'semester' => 'Ganjil', 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['school_id' => $school_id, 'tahun_ajaran' => $request->tahun_ajaran, 'semester' => 'Genap', 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        return back()->with('success', 'Tahun Pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string|regex:/^\d{4}\/\d{4}$/',
        ]);

        $oldTahun = $academicYear->tahun_ajaran;
        $schoolId = $academicYear->school_id;

        if (! auth()->user()->hasRole('superadmin') && auth()->user()->school_id !== $schoolId) {
            abort(403);
        }

        // Update semua semester (Ganjil & Genap) yang memiliki tahun ajaran sama di sekolah tersebut
        AcademicYear::where('school_id', $schoolId)
            ->where('tahun_ajaran', $oldTahun)
            ->update(['tahun_ajaran' => $request->tahun_ajaran]);

        return back()->with('success', 'Tahun pelajaran berhasil diperbarui.');
    }

    public function aktifkan(AcademicYear $academicYear)
    {
        if (! auth()->user()->hasRole('superadmin') && auth()->user()->school_id !== $academicYear->school_id) {
            abort(403);
        }

        AcademicYear::where('school_id', $academicYear->school_id)->update(['is_active' => false]);
        $academicYear->update(['is_active' => true]);

        return back()->with('success', "Semester $academicYear->semester $academicYear->tahun_ajaran diaktifkan.");
    }

    public function destroy(AcademicYear $academicYear)
    {
        if (! auth()->user()->hasRole('superadmin') && auth()->user()->school_id !== $academicYear->school_id) {
            abort(403);
        }

        AcademicYear::where('school_id', $academicYear->school_id)
            ->where('tahun_ajaran', $academicYear->tahun_ajaran)
            ->delete();

        return back()->with('success', 'Tahun pelajaran berhasil dihapus.');
    }
}
