<?php

namespace App\Http\Controllers;

use App\Exports\ExtracurricularsExport;
use App\Models\AcademicYear;
use App\Models\Extracurricular;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;

class ExtracurricularController extends Controller
{
    public function export(Request $request)
    {
        $user = auth()->user();
        $isSuperadmin = $user->hasRole('superadmin');
        abort_unless($isSuperadmin || $user->hasAnyRole(['guru', 'operator']), 403);

        if ($isSuperadmin) {
            $validated = $request->validate([
                'school_id' => 'required|integer|exists:schools,id',
            ]);
            $school = School::findOrFail($validated['school_id']);
            $schoolId = $school->id;
        } else {
            abort_unless($user->school_id, 403);
            $school = $user->school;
            $schoolId = $user->school_id;
        }

        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->first()
            ?? AcademicYear::where('school_id', $schoolId)->latest()->first();

        return Excel::download(
            new ExtracurricularsExport($schoolId, $activeYear?->id),
            'Rekap_Ekskul_'.Str::slug($school->nama_sekolah).'_'.now()->format('Ymd').'.xlsx'
        );
    }

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
