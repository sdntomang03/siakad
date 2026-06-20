<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookLoan;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookLoanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $query = BookLoan::with('student');
        if (! $user->hasRole('superadmin')) {
            $query->where('school_id', $user->school_id);
        }
        $loans = $query->orderBy('borrowed_at', 'desc')->get();
        // daftar siswa untuk form peminjaman
        // Jika user adalah guru, batasi ke siswa di kelas yang diampu (homeroom)
        if ($user->hasRole('guru')) {
            $employee = $user->employee;
            $students = collect();
            if ($employee) {
                $students = Student::where('school_id', $user->school_id)
                    ->whereHas('classrooms', function ($q) use ($employee) {
                        $q->where('homeroom_teacher_id', $employee->id)
                            ->whereHas('academicYear', function ($q2) {
                                $q2->where('is_active', true);
                            });
                    })->orderBy('nama_lengkap')->get();
            }
        } else {
            $students = Student::where('school_id', $user->school_id)->orderBy('nama_lengkap')->get();
        }

        return view('book_loans.index', compact('loans', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required_without:student_id|array',
            'student_ids.*' => 'exists:students,id',
            'student_id' => 'required_without:student_ids|exists:students,id',
            'book_id' => 'nullable|exists:books,id',
            'book_title' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'borrowed_at' => 'nullable|date',
            'due_at' => 'nullable|date',
            'returned_at' => 'nullable|date',
        ]);

        $schoolId = auth()->user()->school_id;
        $borrowedAt = $validated['borrowed_at'] ?? now();
        $borrowedCarbon = Carbon::parse($borrowedAt);

        // If book_id provided, get title
        if (! empty($validated['book_id'])) {
            $book = Book::find($validated['book_id']);
            if ($book) {
                $validated['book_title'] = $book->title;
            }
        }

        if (empty($validated['book_title'])) {
            return back()->withErrors(['book_title' => 'Judul buku harus diisi jika tidak memilih dari daftar buku.'])->withInput();
        }

        // Normalize student ids to an array
        $studentIds = [];
        if (! empty($validated['student_ids'])) {
            $studentIds = $validated['student_ids'];
        } elseif (! empty($validated['student_id'])) {
            $studentIds = [$validated['student_id']];
        }

        $created = [];
        $skipped = [];

        // determine default due date: 14 days after borrowed_at if not provided
        $providedDue = $validated['due_at'] ?? null;
        $defaultDue = $providedDue ? Carbon::parse($providedDue) : $borrowedCarbon->copy()->addDays(14);

        foreach ($studentIds as $sid) {
            // Cek apakah siswa sudah meminjam buku yang sama dan belum mengembalikan
            $existsQuery = BookLoan::where('student_id', $sid)
                ->where('school_id', $schoolId)
                ->whereNull('returned_at');

            if (! empty($validated['book_id'])) {
                $existsQuery->where('book_id', $validated['book_id']);
            } else {
                $existsQuery->where('book_title', $validated['book_title']);
            }

            if ($existsQuery->exists()) {
                $student = Student::find($sid);
                $skipped[] = $student ? $student->nama_lengkap : $sid;

                continue;
            }

            $loan = BookLoan::create([
                'student_id' => $sid,
                'book_id' => $validated['book_id'] ?? null,
                'book_title' => $validated['book_title'],
                'notes' => $validated['notes'] ?? null,
                'borrowed_at' => $borrowedAt,
                'due_at' => ($validated['due_at'] ?? null) ? Carbon::parse($validated['due_at']) : $defaultDue,
                'returned_at' => null,
                'school_id' => $schoolId,
            ]);

            $created[] = $loan->id;
        }

        $redirect = back();
        if (count($created) > 0) {
            $redirect = $redirect->with('success', count($created).' peminjaman tercatat.');
        }
        if (count($skipped) > 0) {
            $redirect = $redirect->with('warning', 'Beberapa siswa sudah meminjam buku yang sama: '.implode(', ', $skipped));
        }

        return $redirect;
    }

    public function markReturned(BookLoan $bookLoan)
    {
        $this->ensureSchoolAccess($bookLoan);
        $bookLoan->update(['returned_at' => now()]);

        return back()->with('success', 'Buku ditandai sebagai sudah dikembalikan.');
    }

    public function destroy(BookLoan $bookLoan)
    {
        $this->ensureSchoolAccess($bookLoan);
        $bookLoan->delete();

        return back()->with('success', 'Catatan peminjaman dihapus.');
    }

    private function ensureSchoolAccess(BookLoan $bookLoan)
    {
        $user = auth()->user();
        if (! $user->hasRole('superadmin') && $bookLoan->school_id !== $user->school_id) {
            abort(403);
        }
    }

    public function returnMultiple(Request $request)
    {
        $data = $request->validate([
            'loan_ids' => 'required|array',
            'loan_ids.*' => 'exists:book_loans,id',
        ]);

        $user = auth()->user();
        $processed = 0;
        $skipped = [];

        foreach ($data['loan_ids'] as $id) {
            $loan = BookLoan::find($id);
            if (! $loan) {
                continue;
            }
            if (! $user->hasRole('superadmin') && $loan->school_id !== $user->school_id) {
                $skipped[] = $loan->id;

                continue;
            }
            if (is_null($loan->returned_at)) {
                $loan->returned_at = now();
                $loan->save();
                $processed++;
            }
        }

        $msg = '';
        if ($processed) {
            $msg .= "$processed peminjaman ditandai kembali. ";
        }
        if ($skipped) {
            $msg .= 'Beberapa peminjaman tidak diproses karena akses terbatas.';
        }

        return back()->with('success', trim($msg));
    }

    public function destroyMultiple(Request $request)
    {
        $data = $request->validate([
            'loan_ids' => 'required|array',
            'loan_ids.*' => 'exists:book_loans,id',
        ]);

        $user = auth()->user();
        $deleted = 0;
        $skipped = [];

        foreach ($data['loan_ids'] as $id) {
            $loan = BookLoan::find($id);
            if (! $loan) {
                continue;
            }
            if (! $user->hasRole('superadmin') && $loan->school_id !== $user->school_id) {
                $skipped[] = $loan->id;

                continue;
            }
            $loan->delete();
            $deleted++;
        }

        $msg = '';
        if ($deleted) {
            $msg .= "$deleted peminjaman dihapus. ";
        }
        if ($skipped) {
            $msg .= 'Beberapa peminjaman tidak dihapus karena akses terbatas.';
        }

        return back()->with('success', trim($msg));
    }
}
