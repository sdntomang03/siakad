<?php

namespace App\Http\Controllers;

use App\Exports\AnnouncementTemplateExport;
use App\Imports\AnnouncementMessagesImport;
use App\Models\Announcement;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class AnnouncementController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('superadmin') || $user->hasRole('operator')) {
            abort_unless($user->hasRole('superadmin') || $user->school_id, 403);

            $announcements = Announcement::with(['author.employee', 'recipient', 'classroom', 'school'])
                ->withCount('students')
                ->when(! $user->hasRole('superadmin'), fn ($query) => $query->where('school_id', $user->school_id))
                ->latest()
                ->paginate(15);

            return view('announcements.admin-index', compact('announcements'));
        }

        abort_unless($user->school_id, 403);

        if ($user->hasRole('guru')) {
            $announcements = Announcement::with('recipient')
                ->with('classroom')
                ->withCount('students')
                ->where('school_id', $user->school_id)
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(15);
            $students = Student::where('school_id', $user->school_id)
                ->where('status', 'aktif')
                ->orderBy('nama_lengkap')
                ->get(['id', 'nama_lengkap']);
            $classrooms = collect();
            $employeeId = $user->employee?->id;

            if ($employeeId) {
                $classrooms = Classroom::with(['students' => function ($query) use ($user) {
                    $query->where('students.school_id', $user->school_id)
                        ->where('students.status', 'aktif')
                        ->orderBy('students.nama_lengkap')
                        ->select(['students.id', 'students.school_id', 'students.nama_lengkap', 'students.nisn']);
                }])
                    ->where('school_id', $user->school_id)
                    ->whereHas('academicYear', fn ($query) => $query->where('is_active', true))
                    ->where(function ($query) use ($employeeId) {
                        $query->where('homeroom_teacher_id', $employeeId)
                            ->orWhereHas('subjectTeachers', fn ($subjectTeachers) => $subjectTeachers->where('employee_id', $employeeId));
                    })
                    ->orderBy('tingkat')
                    ->orderBy('nama_kelas')
                    ->get(['id', 'tingkat', 'nama_kelas']);

                $classrooms->each(
                    fn ($classroom) => $classroom->setAttribute('template_url', route('announcements.template', $classroom))
                );
            }

            return view('announcements.teacher-index', compact('announcements', 'students', 'classrooms'));
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
            'audience' => ['required', Rule::in(['all', 'individual', 'class_personalized'])],
            'student_id' => [
                'nullable',
                'required_if:audience,individual',
                'integer',
                Rule::exists('students', 'id')->where(
                    fn ($query) => $query->where('school_id', $user->school_id)->where('status', 'aktif')
                ),
            ],
            'classroom_id' => [
                'nullable',
                'required_if:audience,class_personalized',
                'integer',
                Rule::exists('classrooms', 'id')->where(
                    fn ($query) => $query->where('school_id', $user->school_id)
                ),
            ],
            'class_input_method' => ['required_if:audience,class_personalized', Rule::in(['manual', 'excel'])],
            'student_bodies' => ['exclude_unless:class_input_method,manual', 'required_if:class_input_method,manual', 'array', 'min:1'],
            'student_bodies.*' => ['exclude_unless:class_input_method,manual', 'required', 'string', 'max:5000'],
            'excel_file' => ['exclude_unless:class_input_method,excel', 'required_if:class_input_method,excel', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
            'title' => ['required', 'string', 'max:180'],
            'body' => ['nullable', 'required_unless:audience,class_personalized', 'string', 'max:5000'],
        ], [
            'student_id.required_if' => 'Pilih siswa penerima pengumuman.',
            'student_id.exists' => 'Siswa yang dipilih tidak aktif atau bukan dari sekolah ini.',
            'classroom_id.required_if' => 'Pilih kelas yang Anda ampu.',
            'class_input_method.required_if' => 'Pilih cara mengisi pesan untuk siswa di kelas.',
            'student_bodies.required_if' => 'Isi pesan untuk setiap siswa di kelas.',
            'student_bodies.*.required' => 'Isi pesan untuk setiap siswa di kelas.',
            'excel_file.required_if' => 'Unggah file Excel berisi pesan setiap siswa.',
            'excel_file.mimes' => 'Gunakan file Excel .xlsx, .xls, atau .csv.',
            'excel_file.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        $classroom = null;
        $recipients = collect();

        if ($validated['audience'] === 'class_personalized') {
            $classroom = Classroom::with(['students' => function ($query) use ($user) {
                $query->where('students.school_id', $user->school_id)
                    ->where('students.status', 'aktif')
                    ->orderBy('students.nama_lengkap');
            }])
                ->where('school_id', $user->school_id)
                ->whereHas('academicYear', fn ($query) => $query->where('is_active', true))
                ->whereKey($validated['classroom_id'])
                ->firstOrFail();

            $this->authorizeTeacherClassroom($user, $classroom);

            if ($validated['class_input_method'] === 'excel') {
                $recipients = $this->personalizedRecipientsFromExcel($request, $classroom);
            } else {
                $classStudentIds = $classroom->students->modelKeys();
                $submittedStudentIds = collect(array_keys($validated['student_bodies']))
                    ->map(fn ($id) => (int) $id)
                    ->sort()
                    ->values();
                $expectedStudentIds = collect($classStudentIds)->sort()->values();

                if ($expectedStudentIds->isEmpty() || $submittedStudentIds->all() !== $expectedStudentIds->all()) {
                    return back()
                        ->withInput()
                        ->withErrors(['student_bodies' => 'Isi pesan untuk seluruh siswa aktif di kelas yang dipilih.']);
                }

                $recipients = $classroom->students->mapWithKeys(
                    fn ($student) => [$student->id => ['body' => $validated['student_bodies'][$student->id]]]
                );
            }
        } else {
            $students = $validated['audience'] === 'all'
                ? Student::where('school_id', $user->school_id)->where('status', 'aktif')->pluck('id')
                : collect([(int) $validated['student_id']]);

            if ($students->isEmpty()) {
                return back()
                    ->withInput()
                    ->withErrors(['audience' => 'Belum ada siswa aktif yang dapat menerima pengumuman.']);
            }

            $recipients = $students;
        }

        DB::transaction(function () use ($validated, $user, $recipients, $classroom) {
            $announcement = Announcement::create([
                'school_id' => $user->school_id,
                'user_id' => $user->id,
                'recipient_student_id' => $validated['audience'] === 'individual'
                    ? $validated['student_id']
                    : null,
                'classroom_id' => $classroom?->id,
                'audience' => $validated['audience'],
                'title' => $validated['title'],
                'body' => $validated['body'] ?? null,
            ]);

            $announcement->students()->sync($recipients->all());
        });

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dikirim.');
    }

    public function destroy(Announcement $announcement)
    {
        $user = auth()->user();
        abort_unless($user->hasAnyRole(['guru', 'operator', 'superadmin']), 403);

        if (! $user->hasRole('superadmin')) {
            abort_unless((int) $announcement->school_id === (int) $user->school_id, 403);
        }

        if ($user->hasRole('guru')) {
            abort_unless((int) $announcement->user_id === (int) $user->id, 403);
        }

        $announcement->delete();

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }

    public function downloadTemplate(Classroom $classroom)
    {
        $user = auth()->user();
        abort_unless($user->hasRole('guru') && $user->school_id, 403);
        $this->authorizeTeacherClassroom($user, $classroom);

        $classroom->load(['students' => function ($query) use ($user) {
            $query->where('students.school_id', $user->school_id)
                ->where('students.status', 'aktif')
                ->orderBy('students.nama_lengkap');
        }]);

        if ($classroom->students->isEmpty()) {
            throw ValidationException::withMessages([
                'excel_file' => 'Kelas ini belum memiliki siswa aktif untuk dimasukkan ke format Excel.',
            ]);
        }

        if ($classroom->students->contains(fn ($student) => blank($student->nisn))) {
            throw ValidationException::withMessages([
                'excel_file' => 'Lengkapi NISN seluruh siswa aktif di kelas ini sebelum mengunduh format Excel.',
            ]);
        }

        $filename = Str::slug("format-pengumuman-{$classroom->tingkat}-{$classroom->nama_kelas}").'.xlsx';

        return Excel::download(new AnnouncementTemplateExport($classroom->students), $filename);
    }

    private function authorizeTeacherClassroom(User $user, Classroom $classroom): void
    {
        abort_unless((int) $classroom->school_id === (int) $user->school_id, 403);
        abort_unless($classroom->academicYear()->where('is_active', true)->exists(), 403);

        $employeeId = $user->employee?->id;
        abort_unless($employeeId, 403);
        abort_unless(
            (int) $classroom->homeroom_teacher_id === (int) $employeeId
                || $classroom->subjectTeachers()->where('employee_id', $employeeId)->exists(),
            403
        );
    }

    private function personalizedRecipientsFromExcel(Request $request, Classroom $classroom)
    {
        $students = $classroom->students;

        if ($students->isEmpty()) {
            throw ValidationException::withMessages([
                'excel_file' => 'Kelas ini belum memiliki siswa aktif untuk menerima pengumuman.',
            ]);
        }

        if ($students->contains(fn ($student) => blank($student->nisn))) {
            throw ValidationException::withMessages([
                'excel_file' => 'Lengkapi NISN seluruh siswa aktif di kelas ini sebelum mengunggah pesan.',
            ]);
        }

        $import = new AnnouncementMessagesImport;
        Excel::import($import, $request->file('excel_file'));
        $rows = $import->rows();
        $headings = $rows->isNotEmpty() ? array_keys($rows->first()->all()) : [];

        if (! in_array('nisn', $headings, true) || ! in_array('pesan', $headings, true)) {
            throw ValidationException::withMessages([
                'excel_file' => 'Kolom NISN atau Pesan tidak ditemukan. Gunakan format Excel yang diunduh.',
            ]);
        }

        $studentsByNisn = $students->keyBy(fn ($student) => trim((string) $student->nisn));
        $seenNisn = [];
        $recipients = [];
        $errors = [];

        foreach ($rows as $index => $row) {
            $nisn = trim((string) ($row['nisn'] ?? ''));
            $name = trim((string) ($row['nama_siswa'] ?? ''));
            $body = trim((string) ($row['pesan'] ?? ''));

            if ($nisn === '' && $name === '' && $body === '') {
                continue;
            }

            $line = (int) $index + 2;

            if ($nisn === '') {
                $errors[] = "Baris {$line}: NISN harus diisi.";

                continue;
            }

            if (isset($seenNisn[$nisn])) {
                $errors[] = "Baris {$line}: NISN {$nisn} tercantum lebih dari sekali.";

                continue;
            }

            $seenNisn[$nisn] = true;
            $student = $studentsByNisn->get($nisn);

            if (! $student) {
                $errors[] = "Baris {$line}: NISN {$nisn} bukan siswa aktif di kelas ini.";

                continue;
            }

            if ($body === '') {
                $errors[] = "Baris {$line}: pesan untuk {$student->nama_lengkap} belum diisi.";

                continue;
            }

            if (Str::length($body) > 5000) {
                $errors[] = "Baris {$line}: pesan untuk {$student->nama_lengkap} melebihi 5000 karakter.";

                continue;
            }

            $recipients[$student->id] = ['body' => $body];
        }

        $missingStudents = $students->filter(fn ($student) => ! isset($seenNisn[trim((string) $student->nisn)]));

        if ($missingStudents->isNotEmpty()) {
            $names = $missingStudents->pluck('nama_lengkap')->implode(', ');
            $errors[] = "Pesan belum dicantumkan untuk seluruh siswa kelas: {$names}.";
        }

        if ($errors !== []) {
            $displayErrors = array_slice($errors, 0, 20);
            $remaining = count($errors) - count($displayErrors);

            if ($remaining > 0) {
                $displayErrors[] = "Ada {$remaining} kesalahan lain. Periksa kembali file Excel.";
            }

            throw ValidationException::withMessages(['excel_file' => implode(' ', $displayErrors)]);
        }

        return collect($recipients);
    }
}
