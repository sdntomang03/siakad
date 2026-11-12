<?php

use App\Models\Book;
use App\Models\BookLoan;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Spatie\Permission\Models\Role;

function createBookLoanApiContext(): array
{
    $school = School::create(['nama_sekolah' => 'Sekolah API']);
    $user = User::factory()->create(['school_id' => $school->id]);
    $student = Student::create([
        'school_id' => $school->id,
        'nama_lengkap' => 'Ayu Lestari',
        'jenis_kelamin' => 'P',
    ]);
    $book = Book::create([
        'school_id' => $school->id,
        'title' => 'Buku Sains',
    ]);

    return [$user, $school, $student, $book];
}

test('book loan options only include records from the signed-in user school', function () {
    [$user, $school, $student, $book] = createBookLoanApiContext();
    $otherSchool = School::create(['nama_sekolah' => 'Sekolah Lain']);
    $otherStudent = Student::create([
        'school_id' => $otherSchool->id,
        'nama_lengkap' => 'Budi Santoso',
        'jenis_kelamin' => 'L',
    ]);
    $otherBook = Book::create([
        'school_id' => $otherSchool->id,
        'title' => 'Buku Lain',
    ]);

    $this->actingAs($user)
        ->getJson(route('api.book-loans.options'))
        ->assertOk()
        ->assertJsonPath('data.students.0.id', $student->id)
        ->assertJsonPath('data.books.0.id', $book->id)
        ->assertJsonMissing(['id' => $otherStudent->id])
        ->assertJsonMissing(['id' => $otherBook->id]);
});

test('the book loan collection is paginated and scoped to the signed-in school', function () {
    [$user, $school, $student, $book] = createBookLoanApiContext();
    $loan = BookLoan::create([
        'school_id' => $school->id,
        'student_id' => $student->id,
        'book_id' => $book->id,
        'book_title' => $book->title,
        'borrowed_at' => now(),
        'due_at' => now()->addDays(14),
    ]);

    $this->actingAs($user)
        ->getJson(route('api.book-loans.index', ['per_page' => 1]))
        ->assertOk()
        ->assertJsonPath('data.total', 1)
        ->assertJsonPath('data.per_page', 1)
        ->assertJsonPath('data.data.0.id', $loan->id)
        ->assertJsonPath('data.data.0.student.nama_lengkap', $student->nama_lengkap);

    $token = $user->createToken('book-loans-test')->plainTextToken;
    $this->withToken($token)
        ->getJson(route('api.book-loans.index'))
        ->assertOk()
        ->assertJsonPath('data.total', 1);
});

test('the loan monitor renders borrow and return controls for a student', function () {
    [$user, $school, $student, $book] = createBookLoanApiContext();
    BookLoan::create([
        'school_id' => $school->id,
        'student_id' => $student->id,
        'book_id' => $book->id,
        'book_title' => $book->title,
        'borrowed_at' => now(),
        'due_at' => now()->addDays(14),
    ]);

    $this->actingAs($user)
        ->get(route('book-loans.monitor'))
        ->assertOk()
        ->assertSee('Catat peminjaman')
        ->assertSee('Terima Buku');
});

test('a book loan can be recorded through the API and duplicate active loans are rejected', function () {
    [$user, $school, $student, $book] = createBookLoanApiContext();

    $response = $this->actingAs($user)
        ->postJson(route('api.book-loans.store'), [
            'student_id' => $student->id,
            'book_id' => $book->id,
        ])
        ->assertCreated()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.student_id', $student->id)
        ->assertJsonPath('data.book_title', $book->title);

    $loanId = $response->json('data.id');
    expect(BookLoan::findOrFail($loanId)->due_at->toDateString())
        ->toBe(now()->addDays(14)->toDateString());

    $this->actingAs($user)
        ->postJson(route('api.book-loans.store'), [
            'student_id' => $student->id,
            'book_id' => $book->id,
        ])
        ->assertStatus(409)
        ->assertJsonPath('status', 'error');

    expect(BookLoan::where('student_id', $student->id)->count())->toBe(1);
});

test('a book loan can be recorded with a manual title and a custom due date', function () {
    [$user, , $student] = createBookLoanApiContext();
    $dueAt = now()->addDays(5)->toDateString();

    $this->actingAs($user)
        ->postJson(route('api.book-loans.store'), [
            'student_id' => $student->id,
            'book_title' => 'Buku Catatan',
            'due_at' => $dueAt,
        ])
        ->assertCreated()
        ->assertJsonPath('data.book_id', null)
        ->assertJsonPath('data.book_title', 'Buku Catatan')
        ->assertJsonPath('data.due_at', \Carbon\Carbon::parse($dueAt)->toISOString());
});

test('book loans can be returned through the API and cannot be returned by another school', function () {
    [$user, $school, $student, $book] = createBookLoanApiContext();
    $loan = BookLoan::create([
        'school_id' => $school->id,
        'student_id' => $student->id,
        'book_id' => $book->id,
        'book_title' => $book->title,
        'borrowed_at' => now(),
        'due_at' => now()->addDays(14),
    ]);

    $returnedResponse = $this->actingAs($user)
        ->patchJson(route('api.book-loans.return', $loan))
        ->assertOk()
        ->assertJsonPath('success', true);

    $returnedAt = $loan->fresh()->returned_at;
    expect($returnedAt)->not->toBeNull();

    $this->actingAs($user)
        ->patchJson(route('api.book-loans.return', $loan))
        ->assertOk()
        ->assertJsonPath('data.returned_at', $returnedResponse->json('data.returned_at'));
    expect($loan->fresh()->returned_at->toISOString())->toBe($returnedAt->toISOString());

    $otherSchool = School::create(['nama_sekolah' => 'Sekolah Lain']);
    $otherUser = User::factory()->create(['school_id' => $otherSchool->id]);

    $this->actingAs($otherUser)
        ->patchJson(route('api.book-loans.return', $loan))
        ->assertNotFound();
});

test('book loans cannot be created for another school or an inaccessible teacher roster', function () {
    [$user, $school] = createBookLoanApiContext();
    $otherSchool = School::create(['nama_sekolah' => 'Sekolah Lain']);
    $otherStudent = Student::create([
        'school_id' => $otherSchool->id,
        'nama_lengkap' => 'Budi Santoso',
        'jenis_kelamin' => 'L',
    ]);
    $otherBook = Book::create([
        'school_id' => $otherSchool->id,
        'title' => 'Buku Lain',
    ]);

    $this->actingAs($user)
        ->postJson(route('api.book-loans.store'), [
            'student_id' => $otherStudent->id,
            'book_id' => $otherBook->id,
        ])
        ->assertNotFound();

    $teacher = User::factory()->create(['school_id' => $school->id]);
    $teacher->assignRole(Role::create(['name' => 'guru', 'guard_name' => 'web']));

    $this->actingAs($teacher)
        ->getJson(route('api.book-loans.options'))
        ->assertOk()
        ->assertJsonPath('data.students', []);
});

test('the book loan API requires authentication', function () {
    $this->getJson(route('api.book-loans.index'))->assertUnauthorized();
});
