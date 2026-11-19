<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AnnouncementController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        abort_unless($user->school_id, 403);

        if ($user->hasRole('guru')) {
            $announcements = Announcement::with('recipient')
                ->withCount('students')
                ->where('school_id', $user->school_id)
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(15);
            $students = Student::where('school_id', $user->school_id)
                ->where('status', 'aktif')
                ->orderBy('nama_lengkap')
                ->get(['id', 'nama_lengkap']);

            return view('announcements.teacher-index', compact('announcements', 'students'));
        }

        abort_unless($user->hasRole('siswa'), 403);
        $student = $user->student;
        abort_unless($student && (int) $student->school_id === (int) $user->school_id, 403);

        $announcements = $student->announcements()
            ->where('announcements.school_id', $user->school_id)
            ->with('author.employee')
            ->latest('announcements.created_at')
            ->paginate(15);

        return view('announcements.student-index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->hasRole('guru') && $user->school_id, 403);

        $validated = $request->validate([
            'audience' => ['required', Rule::in(['all', 'individual'])],
            'student_id' => [
                'nullable',
                'required_if:audience,individual',
                'integer',
                Rule::exists('students', 'id')->where(
                    fn ($query) => $query->where('school_id', $user->school_id)->where('status', 'aktif')
                ),
            ],
            'title' => ['required', 'string', 'max:180'],
            'body' => ['required', 'string', 'max:5000'],
        ], [
            'student_id.required_if' => 'Pilih siswa penerima pengumuman.',
            'student_id.exists' => 'Siswa yang dipilih tidak aktif atau bukan dari sekolah ini.',
        ]);

        $students = $validated['audience'] === 'all'
            ? Student::where('school_id', $user->school_id)->where('status', 'aktif')->pluck('id')
            : collect([(int) $validated['student_id']]);

        if ($students->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['audience' => 'Belum ada siswa aktif yang dapat menerima pengumuman.']);
        }

        DB::transaction(function () use ($validated, $user, $students) {
            $announcement = Announcement::create([
                'school_id' => $user->school_id,
                'user_id' => $user->id,
                'recipient_student_id' => $validated['audience'] === 'individual'
                    ? $students->first()
                    : null,
                'audience' => $validated['audience'],
                'title' => $validated['title'],
                'body' => $validated['body'],
            ]);

            $announcement->students()->sync($students);
        });

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dikirim.');
    }
}
