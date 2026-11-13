<?php

namespace App\Http\Controllers;

use App\Models\Extracurricular;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExtracurricularController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isSuperadmin = $user->hasRole('superadmin');

        if ($isSuperadmin) {
            $request->validate([
                'school_id' => 'nullable|integer|exists:schools,id',
            ]);
        }

        $selectedSchoolId = $isSuperadmin ? $request->query('school_id') : $user->school_id;
        $schools = $isSuperadmin ? School::orderBy('nama_sekolah')->get() : collect();
        $extracurriculars = $selectedSchoolId
            ? Extracurricular::where('school_id', $selectedSchoolId)->orderBy('nama_ekskul')->get()
            : collect();

        return view('extracurriculars.index', compact('extracurriculars', 'schools', 'selectedSchoolId'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $isSuperadmin = $user->hasRole('superadmin');

        if ($isSuperadmin) {
            $request->validate([
                'school_id' => 'required|integer|exists:schools,id',
            ]);
            $schoolId = $request->input('school_id');
        } else {
            $schoolId = $user->school_id;
            abort_unless($schoolId, 403);
        }

        $validated = $request->validate([
            'nama_ekskul' => [
                'required',
                'string',
                'max:255',
                Rule::unique('extracurriculars', 'nama_ekskul')->where('school_id', $schoolId),
            ],
        ]);

        Extracurricular::create([
            'school_id' => $schoolId,
            'nama_ekskul' => $validated['nama_ekskul'],
        ]);

        return redirect()
            ->route('extracurriculars.index', $isSuperadmin ? ['school_id' => $schoolId] : [])
            ->with('success', 'Ekskul berhasil ditambahkan.');
    }

    public function destroy(Request $request, $id)
    {
        $user = auth()->user();
        $isSuperadmin = $user->hasRole('superadmin');

        if ($isSuperadmin) {
            $request->validate([
                'school_id' => 'required|integer|exists:schools,id',
            ]);
            $schoolId = $request->input('school_id');
        } else {
            $schoolId = $user->school_id;
            abort_unless($schoolId, 403);
        }

        $extracurricular = Extracurricular::where('school_id', $schoolId)->findOrFail($id);
        $extracurricular->delete();

        return redirect()
            ->route('extracurriculars.index', $isSuperadmin ? ['school_id' => $schoolId] : [])
            ->with('success', 'Ekskul berhasil dihapus.');
    }
}
