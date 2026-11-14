<?php

use App\Exports\ExtracurricularsExport;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Extracurricular;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

function createExtracurricularRoleUser(string $roleName, int $schoolId): User
{
    $user = User::factory()->create(['school_id' => $schoolId]);
    $user->assignRole(Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']));

    return $user;
}

function createExtracurricularStudent(int $schoolId, ?User $user = null): Student
{
    return Student::create([
        'school_id' => $schoolId,
        'user_id' => $user?->id,
        'nama_lengkap' => 'Siswa Uji',
        'jenis_kelamin' => 'L',
    ]);
}

test('operators manage extracurriculars only for their own school', function () {
    $school = School::create(['nama_sekolah' => 'Sekolah Aktif']);
    $otherSchool = School::create(['nama_sekolah' => 'Sekolah Lain']);
    $operator = createExtracurricularRoleUser('operator', $school->id);
    $ownExtracurricular = Extracurricular::create([
        'school_id' => $school->id,
        'nama_ekskul' => 'Pramuka',
    ]);
    $otherExtracurricular = Extracurricular::create([
        'school_id' => $otherSchool->id,
        'nama_ekskul' => 'Futsal',
    ]);

    $this->actingAs($operator)
        ->get(route('extracurriculars.index'))
        ->assertOk()
        ->assertSee('Pramuka')
        ->assertDontSee('Futsal');

    $this->actingAs($operator)
        ->post(route('extracurriculars.store'), ['nama_ekskul' => 'Seni Tari'])
        ->assertRedirect(route('extracurriculars.index'));

    $this->assertDatabaseHas('extracurriculars', [
        'school_id' => $school->id,
        'nama_ekskul' => 'Seni Tari',
    ]);

    $this->actingAs($operator)
        ->delete(route('extracurriculars.destroy', $otherExtracurricular))
        ->assertNotFound();

    $this->assertDatabaseHas('extracurriculars', ['id' => $ownExtracurricular->id]);
    $this->assertDatabaseHas('extracurriculars', ['id' => $otherExtracurricular->id]);

    $this->actingAs($operator)
        ->delete(route('extracurriculars.destroy', $ownExtracurricular))
        ->assertRedirect(route('extracurriculars.index'));

    $this->assertDatabaseMissing('extracurriculars', ['id' => $ownExtracurricular->id]);
});

test('a superadmin manages extracurriculars for the selected school', function () {
    $school = School::create(['nama_sekolah' => 'Sekolah Pilihan']);
    $otherSchool = School::create(['nama_sekolah' => 'Sekolah Lain']);
    $superadmin = createExtracurricularRoleUser('superadmin', $school->id);
    Extracurricular::create(['school_id' => $school->id, 'nama_ekskul' => 'Pramuka']);
    Extracurricular::create(['school_id' => $otherSchool->id, 'nama_ekskul' => 'Futsal']);

    $this->actingAs($superadmin)
        ->get(route('extracurriculars.index', ['school_id' => $school->id]))
        ->assertOk()
        ->assertSee('Pramuka')
        ->assertDontSee('Futsal');

    $this->actingAs($superadmin)
        ->post(route('extracurriculars.store'), [
            'school_id' => $otherSchool->id,
            'nama_ekskul' => 'Basket',
        ])
        ->assertRedirect(route('extracurriculars.index', ['school_id' => $otherSchool->id]));

    $this->assertDatabaseHas('extracurriculars', [
        'school_id' => $otherSchool->id,
        'nama_ekskul' => 'Basket',
    ]);
});

test('a student can select multiple extracurriculars from their school on the profile page', function () {
    $school = School::create(['nama_sekolah' => 'Sekolah Siswa']);
    $otherSchool = School::create(['nama_sekolah' => 'Sekolah Lain']);
    $studentUser = createExtracurricularRoleUser('siswa', $school->id);
    $student = createExtracurricularStudent($school->id, $studentUser);
    $pramuka = Extracurricular::create(['school_id' => $school->id, 'nama_ekskul' => 'Pramuka']);
    $basket = Extracurricular::create(['school_id' => $school->id, 'nama_ekskul' => 'Basket']);
    Extracurricular::create(['school_id' => $otherSchool->id, 'nama_ekskul' => 'Futsal']);

    $this->actingAs($studentUser)
        ->get(route('students.show', $student))
        ->assertOk()
        ->assertSee('name="extracurricular_ids[]"', false)
        ->assertSee('Pramuka')
        ->assertSee('Basket')
        ->assertDontSee('Futsal');

    $this->actingAs($studentUser)
        ->get(route('students.edit', $studentUser))
        ->assertOk()
        ->assertSee('tab === \'ekstrakurikuler\'', false)
        ->assertSee('edit-extracurricular-'.$pramuka->id, false)
        ->assertSee('edit-extracurricular-'.$basket->id, false);

    $this->actingAs($studentUser)
        ->put(route('students.update', $studentUser), [
            'nama_lengkap' => 'Siswa Uji',
            'jenis_kelamin' => 'L',
            'extracurricular_ids' => [$pramuka->id, $basket->id],
        ])
        ->assertRedirect();

    $this->assertEqualsCanonicalizing(
        [$pramuka->id, $basket->id],
        $student->fresh()->extracurriculars()->pluck('extracurriculars.id')->all()
    );

    $this->actingAs($studentUser)
        ->putJson(route('students.ajax-update', $studentUser), [
            'active_tab' => 'ekstrakurikuler',
            'extracurricular_ids_present' => '1',
            'extracurricular_ids' => [$pramuka->id],
        ])
        ->assertOk()
        ->assertJsonPath('status', 'success');

    $this->assertEquals(
        [$pramuka->id],
        $student->fresh()->extracurriculars()->pluck('extracurriculars.id')->all()
    );

    $this->actingAs($studentUser)
        ->put(route('students.extracurriculars.update', $student), [
            'extracurricular_ids' => [$pramuka->id, $basket->id],
        ])
        ->assertRedirect(route('students.show', $student));

    $this->assertEqualsCanonicalizing(
        [$pramuka->id, $basket->id],
        $student->fresh()->extracurriculars()->pluck('extracurriculars.id')->all()
    );

    $this->actingAs($studentUser)
        ->put(route('students.extracurriculars.update', $student), [])
        ->assertRedirect(route('students.show', $student));

    $this->assertCount(0, $student->fresh()->extracurriculars);
});

test('a teacher can update a student extracurricular selection only within their school', function () {
    $school = School::create(['nama_sekolah' => 'Sekolah Guru']);
    $otherSchool = School::create(['nama_sekolah' => 'Sekolah Lain']);
    $teacher = createExtracurricularRoleUser('guru', $school->id);
    $studentUser = createExtracurricularRoleUser('siswa', $school->id);
    $student = createExtracurricularStudent($school->id, $studentUser);
    $otherStudent = createExtracurricularStudent($otherSchool->id);
    $extracurricular = Extracurricular::create([
        'school_id' => $school->id,
        'nama_ekskul' => 'Paduan Suara',
    ]);

    $this->actingAs($teacher)
        ->get(route('students.show', $student))
        ->assertOk()
        ->assertSee('Paduan Suara');

    $this->actingAs($teacher)
        ->get(route('students.edit', $studentUser))
        ->assertOk()
        ->assertSee('edit-extracurricular-'.$extracurricular->id, false);

    $this->actingAs($teacher)
        ->put(route('students.extracurriculars.update', $student), [
            'extracurricular_ids' => [$extracurricular->id],
        ])
        ->assertRedirect(route('students.show', $student));

    $this->actingAs($teacher)
        ->put(route('students.extracurriculars.update', $otherStudent), [
            'extracurricular_ids' => [$extracurricular->id],
        ])
        ->assertNotFound();

    $this->assertDatabaseHas('extracurricular_student', [
        'student_id' => $student->id,
        'extracurricular_id' => $extracurricular->id,
    ]);
});

test('a student cannot select an extracurricular belonging to another school', function () {
    $school = School::create(['nama_sekolah' => 'Sekolah A']);
    $otherSchool = School::create(['nama_sekolah' => 'Sekolah B']);
    $studentUser = createExtracurricularRoleUser('siswa', $school->id);
    $student = createExtracurricularStudent($school->id, $studentUser);
    $otherExtracurricular = Extracurricular::create([
        'school_id' => $otherSchool->id,
        'nama_ekskul' => 'Futsal',
    ]);

    $this->actingAs($studentUser)
        ->from(route('students.show', $student))
        ->put(route('students.extracurriculars.update', $student), [
            'extracurricular_ids' => [$otherExtracurricular->id],
        ])
        ->assertSessionHasErrors('extracurricular_ids.0');

    $this->assertDatabaseMissing('extracurricular_student', [
        'student_id' => $student->id,
        'extracurricular_id' => $otherExtracurricular->id,
    ]);
});

test('teachers and operators can download the whole school extracurricular report from the dashboard', function () {
    Excel::fake();

    $school = School::create(['nama_sekolah' => 'Sekolah Rekap']);
    $teacher = createExtracurricularRoleUser('guru', $school->id);
    $operator = createExtracurricularRoleUser('operator', $school->id);

    foreach ([$teacher, $operator] as $user) {
        $user->markEmailAsVerified();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('extracurriculars.export'));

        $this->actingAs($user)
            ->get(route('extracurriculars.export'))
            ->assertOk();
    }

    Excel::assertDownloaded('Rekap_Ekskul_sekolah-rekap_'.now()->format('Ymd').'.xlsx', function (ExtracurricularsExport $export) {
        return $export->collection()->isEmpty()
            && $export->headings() === ['NISN', 'NIPD', 'Nama Siswa', 'Kelas', 'Ekstrakurikuler'];
    });
});

test('superadmin dashboard offers an extracurricular export for a selected school', function () {
    Excel::fake();

    $school = School::create(['nama_sekolah' => 'Sekolah Pilihan']);
    $superadmin = createExtracurricularRoleUser('superadmin', $school->id);

    $this->actingAs($superadmin)
        ->get(route('superadmin.dashboard'))
        ->assertOk()
        ->assertSee('Rekap ekstrakurikuler siswa')
        ->assertSee('extracurricular_school_id')
        ->assertSee('Sekolah Pilihan');

    $this->actingAs($superadmin)
        ->get(route('extracurriculars.export', ['school_id' => $school->id]))
        ->assertOk();

    Excel::assertDownloaded('Rekap_Ekskul_sekolah-pilihan_'.now()->format('Ymd').'.xlsx');
});

test('the extracurricular report downloads as an xlsx file', function () {
    $school = School::create(['nama_sekolah' => 'Sekolah Rekap']);
    $operator = createExtracurricularRoleUser('operator', $school->id);
    Student::create([
        'school_id' => $school->id,
        'nama_lengkap' => 'Siswa Rekap',
        'jenis_kelamin' => 'L',
    ]);

    $this->actingAs($operator)
        ->get(route('extracurriculars.export'))
        ->assertOk()
        ->assertHeader('content-disposition', 'attachment; filename=Rekap_Ekskul_sekolah-rekap_'.now()->format('Ymd').'.xlsx');
});

test('the extracurricular report includes every student and only active-year classes and school extracurriculars', function () {
    $school = School::create(['nama_sekolah' => 'Sekolah Rekap']);
    $otherSchool = School::create(['nama_sekolah' => 'Sekolah Lain']);
    $activeYear = AcademicYear::create([
        'school_id' => $school->id,
        'tahun_ajaran' => '2026/2027',
        'semester' => 'Ganjil',
        'is_active' => true,
    ]);
    $previousYear = AcademicYear::create([
        'school_id' => $school->id,
        'tahun_ajaran' => '2025/2026',
        'semester' => 'Genap',
        'is_active' => false,
    ]);
    $activeClass = Classroom::create([
        'school_id' => $school->id,
        'academic_year_id' => $activeYear->id,
        'tingkat' => '5',
        'nama_kelas' => 'A',
        'kapasitas' => 30,
    ]);
    $oldClass = Classroom::create([
        'school_id' => $school->id,
        'academic_year_id' => $previousYear->id,
        'tingkat' => '4',
        'nama_kelas' => 'B',
        'kapasitas' => 30,
    ]);
    $student = createExtracurricularStudent($school->id);
    $student->update(['nisn' => '1234567890', 'nipd' => 'S-01']);
    $student->classrooms()->attach([$activeClass->id, $oldClass->id]);
    $secondStudent = Student::create([
        'school_id' => $school->id,
        'nama_lengkap' => 'Siswa Tanpa Ekskul',
        'jenis_kelamin' => 'P',
    ]);
    $otherSchoolStudent = createExtracurricularStudent($otherSchool->id);
    $pramuka = Extracurricular::create(['school_id' => $school->id, 'nama_ekskul' => 'Pramuka']);
    $basket = Extracurricular::create(['school_id' => $school->id, 'nama_ekskul' => 'Basket']);
    $foreignExtracurricular = Extracurricular::create(['school_id' => $otherSchool->id, 'nama_ekskul' => 'Futsal']);
    $student->extracurriculars()->attach([$pramuka->id, $basket->id, $foreignExtracurricular->id]);
    $otherSchoolStudent->extracurriculars()->attach($foreignExtracurricular->id);

    $export = new ExtracurricularsExport($school->id, $activeYear->id);
    $students = $export->collection();

    expect($students)->toHaveCount(2);
    expect($export->map($students->firstWhere('id', $student->id)))->toBe([
        '1234567890',
        'S-01',
        'Siswa Uji',
        '5 A',
        'Basket, Pramuka',
    ]);
    expect($export->map($students->firstWhere('id', $secondStudent->id)))->toBe([
        null,
        null,
        'Siswa Tanpa Ekskul',
        '',
        '',
    ]);
});

test('students cannot download the school extracurricular report', function () {
    $school = School::create(['nama_sekolah' => 'Sekolah Siswa']);
    $student = createExtracurricularRoleUser('siswa', $school->id);

    $this->actingAs($student)
        ->get(route('extracurriculars.export'))
        ->assertForbidden();
});
