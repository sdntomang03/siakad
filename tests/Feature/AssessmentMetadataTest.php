<?php

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\AssessmentType;
use App\Models\Classroom;
use App\Models\Employee;
use App\Models\School;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Spatie\Permission\Models\Role;

function createAssessmentMetadataContext(): array
{
    $school = School::create(['nama_sekolah' => 'Sekolah Penilaian']);
    $user = User::factory()->create(['school_id' => $school->id]);
    $user->assignRole(Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']));
    $employee = Employee::create([
        'school_id' => $school->id,
        'user_id' => $user->id,
        'kategori_pegawai' => 'guru',
        'nama_lengkap' => 'Guru Penilaian',
        'jenis_kelamin' => 'P',
    ]);
    $year = AcademicYear::create([
        'school_id' => $school->id,
        'tahun_ajaran' => '2026/2027',
        'semester' => 'Ganjil',
        'is_active' => true,
    ]);
    $classroom = Classroom::create([
        'school_id' => $school->id,
        'academic_year_id' => $year->id,
        'homeroom_teacher_id' => $employee->id,
        'tingkat' => '5',
        'nama_kelas' => 'A',
        'kapasitas' => 30,
    ]);
    $subjects = collect(['Matematika', 'Bahasa Indonesia'])->map(fn ($name) => Subject::create([
        'school_id' => $school->id,
        'tingkat' => 5,
        'nama_mapel' => $name,
        'kkm' => 75,
        'pengampu' => 'guru_kelas',
    ]));
    $types = collect([
        ['nama' => 'Sumatif Harian', 'singkatan' => 'SH'],
        ['nama' => 'Sumatif Akhir', 'singkatan' => 'SA'],
    ])->map(fn ($type) => AssessmentType::create([
        'school_id' => $school->id,
        ...$type,
    ]));
    $assessment = Assessment::create([
        'school_id' => $school->id,
        'academic_year_id' => $year->id,
        'classroom_id' => $classroom->id,
        'subject_id' => $subjects[0]->id,
        'employee_id' => $employee->id,
        'assessment_type_id' => $types[0]->id,
        'keterangan' => 'Bab 1',
        'tanggal' => '2026-10-10',
    ]);
    $student = Student::create([
        'school_id' => $school->id,
        'nama_lengkap' => 'Ayu',
        'jenis_kelamin' => 'P',
    ]);
    $classroom->students()->attach($student->id);
    AssessmentScore::create([
        'assessment_id' => $assessment->id,
        'student_id' => $student->id,
        'score' => 82.5,
    ]);

    return compact('school', 'user', 'employee', 'year', 'classroom', 'subjects', 'types', 'assessment', 'student');
}

test('teachers can update assessment details and existing scores follow the new subject and type in recap', function () {
    $context = createAssessmentMetadataContext();

    $this->actingAs($context['user'])
        ->get(route('assessments.index'))
        ->assertOk()
        ->assertSee(route('assessments.edit', $context['assessment']))
        ->assertSee('aria-label="Edit detail penilaian"', false)
        ->assertSee('aria-label="Edit nilai"', false)
        ->assertSee('aria-label="Hapus"', false)
        ->assertSee('aria-hidden="true"', false)
        ->assertDontSee('Edit Detail')
        ->assertDontSee('Edit Nilai')
        ->assertDontSee('Hapus</button>', false);

    $this->actingAs($context['user'])
        ->get(route('assessments.edit', $context['assessment']))
        ->assertOk()
        ->assertSee('Edit Detail Penilaian')
        ->assertSee('1 nilai siswa tersimpan')
        ->assertSee('Bahasa Indonesia')
        ->assertSee('Sumatif Akhir');

    $this->actingAs($context['user'])
        ->put(route('assessments.update', $context['assessment']), [
            'subject_id' => $context['subjects'][1]->id,
            'assessment_type_id' => $context['types'][1]->id,
            'keterangan' => 'Bab 2',
        ])
        ->assertRedirect(route('assessments.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('assessments', [
        'id' => $context['assessment']->id,
        'subject_id' => $context['subjects'][1]->id,
        'assessment_type_id' => $context['types'][1]->id,
        'keterangan' => 'Bab 2',
    ]);
    $this->assertDatabaseHas('assessment_scores', [
        'assessment_id' => $context['assessment']->id,
        'student_id' => $context['student']->id,
        'score' => 82.5,
    ]);

    $this->actingAs($context['user'])
        ->get(route('assessments.recapByType', [
            'classroom_id' => $context['classroom']->id,
            'subject_id' => $context['subjects'][1]->id,
        ]))
        ->assertOk()
        ->assertSee('Bahasa Indonesia')
        ->assertSee('SA')
        ->assertSee('82.5');
});

test('teachers cannot update another teacher assessment or use unassigned subjects and other school types', function () {
    $context = createAssessmentMetadataContext();
    $otherUser = User::factory()->create(['school_id' => $context['school']->id]);
    $otherUser->assignRole(Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']));
    Employee::create([
        'school_id' => $context['school']->id,
        'user_id' => $otherUser->id,
        'kategori_pegawai' => 'guru',
        'nama_lengkap' => 'Guru Lain',
        'jenis_kelamin' => 'P',
    ]);
    $otherSchool = School::create(['nama_sekolah' => 'Sekolah Lain']);
    $otherSchoolType = AssessmentType::create([
        'school_id' => $otherSchool->id,
        'nama' => 'Sumatif Sekolah Lain',
        'singkatan' => 'SL',
    ]);
    $unassignedSubject = Subject::create([
        'school_id' => $context['school']->id,
        'tingkat' => 4,
        'nama_mapel' => 'IPA Tingkat 4',
        'kkm' => 75,
        'pengampu' => 'guru_kelas',
    ]);

    $this->actingAs($otherUser)
        ->get(route('assessments.edit', $context['assessment']))
        ->assertForbidden();

    $this->actingAs($context['user'])
        ->put(route('assessments.update', $context['assessment']), [
            'subject_id' => $unassignedSubject->id,
            'assessment_type_id' => $otherSchoolType->id,
            'keterangan' => 'Perubahan tidak valid',
        ])
        ->assertSessionHasErrors(['subject_id', 'assessment_type_id']);

    $this->assertDatabaseHas('assessments', [
        'id' => $context['assessment']->id,
        'subject_id' => $context['subjects'][0]->id,
        'assessment_type_id' => $context['types'][0]->id,
        'keterangan' => 'Bab 1',
    ]);
});
