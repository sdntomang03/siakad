<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookLoan;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BookLoanController extends Controller
{
    public function index(Request $request)
    {
        $loans = $this->visibleLoans($request->user())
            ->with('student:id,nama_lengkap')
            ->orderByDesc('borrowed_at')
            ->paginate(min(max($request->integer('per_page', 50), 1), 100));

        return response()->json([
            'status' => 'success',
            'data' => $loans,
        ]);
    }

    public function options(Request $request)
    {
        $user = $request->user();
        $students = $this->studentsFor($user)
            ->with(['classrooms' => fn ($query) => $query->whereHas('academicYear', fn ($year) => $year->where('is_active', true))])
            ->orderBy('nama_lengkap')
            ->get(['id', 'school_id', 'nama_lengkap'])
            ->map(function (Student $student) {
                $classroom = $student->classrooms->first();

                return [
                    'id' => $student->id,
                    'school_id' => $student->school_id,
                    'name' => $student->nama_lengkap,
                    'classroom' => $classroom ? trim($classroom->tingkat.' '.$classroom->nama_kelas) : null,
                ];
            });

        $books = Book::query()
            ->when(
                ! $user->hasRole('superadmin'),
                fn ($query) => $user->school_id
                    ? $query->where('school_id', $user->school_id)
                    : $query->whereRaw('1 = 0')
            )
            ->orderBy('title')
            ->get(['id', 'school_id', 'title', 'author', 'type', 'stock']);

        return response()->json([
            'status' => 'success',
            'data' => [
                'students' => $students,
                'books' => $books,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'student_id' => ['required', 'integer'],
        ]);

        $student = $this->studentsFor($user)->findOrFail($request->integer('student_id'));

        $validated = $request->validate([
            'book_id' => [
                'nullable',
                'integer',
                Rule::exists('books', 'id')->where('school_id', $student->school_id),
            ],
            'book_title' => 'required_without:book_id|nullable|string|max:255',
            'notes' => 'nullable|string|max:5000',
            'borrowed_at' => 'nullable|date',
            'due_at' => 'nullable|date',
        ]);

        $book = ! empty($validated['book_id'])
            ? Book::query()->where('school_id', $student->school_id)->findOrFail($validated['book_id'])
            : null;
        $bookTitle = $book?->title ?? trim($validated['book_title']);
        $borrowedAt = Carbon::parse($validated['borrowed_at'] ?? now());
        $dueAt = ! empty($validated['due_at'])
            ? Carbon::parse($validated['due_at'])
            : $borrowedAt->copy()->addDays(14);

        $loan = DB::transaction(function () use ($student, $book, $bookTitle, $validated, $borrowedAt, $dueAt) {
            Student::query()->whereKey($student->id)->lockForUpdate()->firstOrFail();

            $existingLoan = BookLoan::query()
                ->where('student_id', $student->id)
                ->where('school_id', $student->school_id)
                ->whereNull('returned_at')
                ->when(
                    $book,
                    fn ($query) => $query->where('book_id', $book->id),
                    fn ($query) => $query->where('book_title', $bookTitle)
                )
                ->exists();

            if ($existingLoan) {
                return null;
            }

            return BookLoan::create([
                'school_id' => $student->school_id,
                'student_id' => $student->id,
                'book_id' => $book?->id,
                'book_title' => $bookTitle,
                'notes' => $validated['notes'] ?? null,
                'borrowed_at' => $borrowedAt,
                'due_at' => $dueAt,
            ]);
        });

        if (! $loan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Siswa masih memiliki pinjaman aktif untuk buku tersebut.',
            ], 409);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Peminjaman berhasil dicatat.',
            'data' => $this->loanData($loan),
        ], 201);
    }

    public function markReturned(Request $request, BookLoan $bookLoan)
    {
        $user = $request->user();
        $loan = DB::transaction(function () use ($bookLoan, $user) {
            $loan = BookLoan::query()->whereKey($bookLoan->id)->lockForUpdate()->firstOrFail();
            abort_unless(
                $loan->school_id === $loan->student->school_id
                    && $this->studentsFor($user)->whereKey($loan->student_id)->exists(),
                404
            );

            if (! $loan->returned_at) {
                $loan->update(['returned_at' => now()]);
            }

            return $loan->refresh();
        });

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Pengembalian berhasil dicatat.',
            'data' => $this->loanData($loan),
        ]);
    }

    private function visibleLoans(User $user): Builder
    {
        return BookLoan::query()
            ->whereIn('student_id', $this->studentsFor($user)->select('id'))
            ->whereHas('student', fn ($query) => $query->whereColumn('students.school_id', 'book_loans.school_id'));
    }

    private function studentsFor(User $user): Builder
    {
        $query = Student::query();

        if (! $user->hasRole('superadmin')) {
            if (! $user->school_id) {
                return $query->whereRaw('1 = 0');
            }

            $query->where('school_id', $user->school_id);
        }

        if ($user->hasRole('guru')) {
            $employee = $user->employee;
            if (! $employee) {
                return $query->whereRaw('1 = 0');
            }

            $query->whereHas('classrooms', function ($classrooms) use ($employee) {
                $classrooms->where('homeroom_teacher_id', $employee->id)
                    ->whereHas('academicYear', fn ($year) => $year->where('is_active', true));
            });
        }

        return $query;
    }

    private function loanData(BookLoan $loan): array
    {
        return [
            'id' => $loan->id,
            'student_id' => $loan->student_id,
            'book_id' => $loan->book_id,
            'book_title' => $loan->book_title,
            'borrowed_at' => $loan->borrowed_at?->toISOString(),
            'due_at' => $loan->due_at?->toISOString(),
            'returned_at' => $loan->returned_at?->toISOString(),
        ];
    }
}
