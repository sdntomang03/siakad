<?php

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\AssessmentType;
use App\Models\Classroom;
use App\Models\Employee;
use App\Models\RemedialProgram;
use App\Models\RemedialRecord;
use App\Models\School;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Spatie\Permission\Models\Role;

function createRemedialTeacherContext(): array
{
    $school = School::create(['nama_sekolah' => 'Sekolah Remedial']);
    $user = User::factory()->create(['school_id' => $school->id]);
    $user->assignRole(Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']));
    $employee = Employee::create([
        'school_id' => $school->id,
        'user_id' => $user->id,
        'kategori_pegawai' => 'guru',
        'nama_lengkap' => 'Guru Uji',
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
    $subject = Subject::create([
        'school_id' => $school->id,
        'tingkat' => 5,
        'nama_mapel' => 'Matematika',
        'kkm' => 75,
        'pengampu' => 'guru_kelas',
    ]);
    $type = AssessmentType::create([
        'school_id' => $school->id,
        'nama' => 'Sumatif Harian',
        'singkatan' => 'SH',
    ]);

    return compact('school', 'user', 'employee', 'year', 'classroom', 'subject', 'type');
}

function createRemedialAssessment(array $context, string $title): Assessment
{
    return Assessment::create([
        'school_id' => $context['school']->id,
        'academic_year_id' => $context['year']->id,
        'classroom_id' => $context['classroom']->id,
        'subject_id' => $context['subject']->id,
        'employee_id' => $context['employee']->id,
        'assessment_type_id' => $context['type']->id,
        'keterangan' => $title,
        'tanggal' => '2026-10-10',
    ]);
}

test('teachers do not see the NISN JSON before selecting assessments', function () {
    $context = createRemedialTeacherContext();

    foreach ([
        ['name' => 'Budi', 'nisn' => '0000000002'],
        ['name' => 'Ayu', 'nisn' => '0000000001'],
        ['name' => 'Citra', 'nisn' => null],
        ['name' => 'Dini', 'nisn' => ''],
    ] as $data) {
        $student = Student::create([
            'school_id' => $context['school']->id,
            'nama_lengkap' => $data['name'],
            'jenis_kelamin' => 'P',
            'nisn' => $data['nisn'],
        ]);
        $context['classroom']->students()->attach($student->id);
    }

    $this->actingAs($context['user'])
        ->get(route('remedials.create', ['classroom_id' => $context['classroom']->id]))
        ->assertOk()
        ->assertDontSee('NISN siswa untuk CBT')
        ->assertDontSee('nisn-cbt-dialog');
});

test('teachers see only students below KKM in the NISN JSON', function () {
    $context = createRemedialTeacherContext();
    $firstAssessment = createRemedialAssessment($context, 'Bab 1');
    $secondAssessment = createRemedialAssessment($context, 'Bab 2');
    collect([
        ['name' => 'Ayu', 'nisn' => '0000000001', 'scores' => [60, 70]],
        ['name' => 'Budi', 'nisn' => '0000000002', 'scores' => [80, 90]],
        ['name' => 'Citra', 'nisn' => '0000000003', 'scores' => [60, null]],
        ['name' => 'Dini', 'nisn' => '', 'scores' => [60, 60]],
        ['name' => 'Eka', 'nisn' => '0000000005', 'scores' => [null, null]],
    ])->each(function (array $data) use ($context, $firstAssessment, $secondAssessment) {
        $student = Student::create([
            'school_id' => $context['school']->id,
            'nama_lengkap' => $data['name'],
            'jenis_kelamin' => 'P',
            'nisn' => $data['nisn'],
        ]);
        $context['classroom']->students()->attach($student->id);

        foreach ([$firstAssessment, $secondAssessment] as $index => $assessment) {
            if ($data['scores'][$index] === null) {
                continue;
            }

            AssessmentScore::create([
                'assessment_id' => $assessment->id,
                'student_id' => $student->id,
                'score' => $data['scores'][$index],
            ]);
        }
    });

    $this->actingAs($context['user'])
        ->get(route('remedials.create', [
            'classroom_id' => $context['classroom']->id,
            'subject_id' => $context['subject']->id,
            'assessment_ids' => [$firstAssessment->id, $secondAssessment->id],
        ]))
        ->assertOk()
        ->assertSee('NISN siswa untuk CBT')
        ->assertSee('&quot;0000000001&quot;', false)
        ->assertSee('&quot;0000000003&quot;', false)
        ->assertDontSee('&quot;0000000002&quot;', false)
        ->assertDontSee('&quot;0000000005&quot;', false);
});

test('teachers average selected assessments, flag students below KKM, and save remedial status and score', function () {
    $context = createRemedialTeacherContext();
    $firstAssessment = createRemedialAssessment($context, 'Bab 1');
    $secondAssessment = createRemedialAssessment($context, 'Bab 2');
    $students = collect([
        ['name' => 'Ayu', 'scores' => [60, 70]],
        ['name' => 'Budi', 'scores' => [80, 90]],
        ['name' => 'Citra', 'scores' => [60, null]],
        ['name' => 'Dini', 'scores' => [70, 70]],
        ['name' => 'Eka', 'scores' => [null, null]],
    ])->map(function (array $data) use ($context, $firstAssessment, $secondAssessment) {
        $student = Student::create([
            'school_id' => $context['school']->id,
            'nama_lengkap' => $data['name'],
            'jenis_kelamin' => 'P',
        ]);
        $context['classroom']->students()->attach($student->id);

        foreach ([$firstAssessment, $secondAssessment] as $index => $assessment) {
            if ($data['scores'][$index] !== null) {
                AssessmentScore::create([
                    'assessment_id' => $assessment->id,
                    'student_id' => $student->id,
                    'score' => $data['scores'][$index],
                ]);
            }
        }

        return $student;
    });

    $this->actingAs($context['user'])
        ->get(route('remedials.create', [
            'classroom_id' => $context['classroom']->id,
            'subject_id' => $context['subject']->id,
            'assessment_ids' => [$firstAssessment->id, $secondAssessment->id],
        ]))
        ->assertOk()
        ->assertSee('class="flex min-w-0 min-h-screen"', false)
        ->assertSee('md:min-h-max md:flex-none md:overflow-visible', false)
        ->assertDontSee('overflow-y-auto overflow-x-hidden', false)
        ->assertSee('KKM untuk program ini')
        ->assertSee('id="kkm" name="kkm" type="number"', false)
        ->assertSee('value="75"', false)
        ->assertSee('65.00')
        ->assertSee('60.00')
        ->assertSee('Di bawah KKM')
        ->assertSee('Tuntas')
        ->assertSee('Nilai belum lengkap')
        ->assertSee('Lihat penilaian yang belum lengkap')
        ->assertSee('id="missing-assessments-dialog"', false)
        ->assertSee('data-student="Citra"', false)
        ->assertSee('data-missing-assessments="[&quot;Bab 2&quot;]"', false)
        ->assertSee('data-student="Eka"', false)
        ->assertSee('data-missing-assessments="[&quot;Bab 1&quot;,&quot;Bab 2&quot;]"', false)
        ->assertSee('3 siswa di bawah KKM dari 4 siswa yang sudah memiliki nilai.');

    $response = $this->actingAs($context['user'])
        ->post(route('remedials.store'), [
            'classroom_id' => $context['classroom']->id,
            'subject_id' => $context['subject']->id,
            'assessment_ids' => [$firstAssessment->id, $secondAssessment->id],
            'records' => [
                $students[0]->id => ['completed' => '1'],
                $students[3]->id => ['score' => '78.5'],
            ],
        ]);

    $program = RemedialProgram::firstOrFail();
    $response->assertRedirect(route('remedials.show', $program));
    $this->assertDatabaseHas('remedial_program_assessments', [
        'remedial_program_id' => $program->id,
        'assessment_id' => $firstAssessment->id,
    ]);
    $record = RemedialRecord::where('remedial_program_id', $program->id)->firstOrFail();
    expect($record->student_id)->toBe($students[0]->id)
        ->and($record->average_score)->toBe(65.0)
        ->and($record->kkm)->toBe(75)
        ->and($record->is_completed)->toBeTrue()
        ->and($record->remedial_score)->toBeNull();
    $partialScoreRecord = RemedialRecord::where('remedial_program_id', $program->id)
        ->where('student_id', $students[2]->id)
        ->firstOrFail();
    expect($partialScoreRecord->average_score)->toBe(60.0)
        ->and($partialScoreRecord->is_completed)->toBeFalse();
    $scoreRecord = RemedialRecord::where('remedial_program_id', $program->id)
        ->where('student_id', $students[3]->id)
        ->firstOrFail();
    expect($scoreRecord->is_completed)->toBeTrue()
        ->and($scoreRecord->remedial_score)->toBe(78.5);

    $this->actingAs($context['user'])
        ->get(route('remedials.show', $program))
        ->assertOk()
        ->assertSee('Bab 1')
        ->assertSee('Ayu')
        ->assertSee('78.5');

    $this->actingAs($context['user'])
        ->post(route('remedials.update', $program), [
            'records' => [
                $record->id => ['completed' => '0', 'score' => ''],
            ],
        ])
        ->assertRedirect(route('remedials.show', $program));

    $this->assertDatabaseHas('remedial_records', [
        'id' => $record->id,
        'is_completed' => false,
        'remedial_score' => null,
    ]);
});

test('teachers cannot calculate a remedial program for an unassigned class', function () {
    $context = createRemedialTeacherContext();
    $otherClassroom = Classroom::create([
        'school_id' => $context['school']->id,
        'academic_year_id' => $context['year']->id,
        'tingkat' => '5',
        'nama_kelas' => 'B',
        'kapasitas' => 30,
    ]);

    $this->actingAs($context['user'])
        ->get(route('remedials.create', [
            'classroom_id' => $otherClassroom->id,
            'subject_id' => $context['subject']->id,
        ]))
        ->assertForbidden();
});

test('teachers can adjust the subject KKM for a remedial program', function () {
    $context = createRemedialTeacherContext();
    $assessment = createRemedialAssessment($context, 'Bab 1');
    $student = Student::create([
        'school_id' => $context['school']->id,
        'nama_lengkap' => 'Ayu',
        'jenis_kelamin' => 'P',
    ]);
    $context['classroom']->students()->attach($student->id);
    AssessmentScore::create([
        'assessment_id' => $assessment->id,
        'student_id' => $student->id,
        'score' => 60,
    ]);

    $this->actingAs($context['user'])
        ->get(route('remedials.create', [
            'classroom_id' => $context['classroom']->id,
            'subject_id' => $context['subject']->id,
            'kkm' => 65,
            'assessment_ids' => [$assessment->id],
        ]))
        ->assertOk()
        ->assertSee('KKM mata pelajaran:')
        ->assertSee('value="65"', false)
        ->assertSee('1 siswa di bawah KKM dari 1 siswa yang sudah memiliki nilai.');

    $this->actingAs($context['user'])
        ->post(route('remedials.store'), [
            'classroom_id' => $context['classroom']->id,
            'subject_id' => $context['subject']->id,
            'assessment_ids' => [$assessment->id],
            'kkm' => 101,
            'records' => [$student->id => ['completed' => '1']],
        ])
        ->assertSessionHasErrors('kkm');

    $response = $this->actingAs($context['user'])
        ->post(route('remedials.store'), [
            'classroom_id' => $context['classroom']->id,
            'subject_id' => $context['subject']->id,
            'assessment_ids' => [$assessment->id],
            'kkm' => 65,
            'records' => [$student->id => ['completed' => '1']],
        ]);
    $program = RemedialProgram::firstOrFail();

    $response->assertRedirect(route('remedials.show', $program));
    $this->assertDatabaseHas('remedial_records', [
        'remedial_program_id' => $program->id,
        'student_id' => $student->id,
        'kkm' => 65,
    ]);

    $this->actingAs($context['user'])
        ->get(route('remedials.show', $program))
        ->assertOk()
        ->assertSee('KKM saat program dibuat: 65');
});

test('teachers can delete their own remedial program but not another teachers program', function () {
    $context = createRemedialTeacherContext();
    $assessment = createRemedialAssessment($context, 'Bab 1');
    $student = Student::create([
        'school_id' => $context['school']->id,
        'nama_lengkap' => 'Ayu',
        'jenis_kelamin' => 'P',
    ]);
    $program = RemedialProgram::create([
        'school_id' => $context['school']->id,
        'academic_year_id' => $context['year']->id,
        'classroom_id' => $context['classroom']->id,
        'subject_id' => $context['subject']->id,
        'employee_id' => $context['employee']->id,
    ]);
    $program->assessments()->attach($assessment->id);
    $record = RemedialRecord::create([
        'remedial_program_id' => $program->id,
        'student_id' => $student->id,
        'average_score' => 60,
        'kkm' => 75,
    ]);
    $score = AssessmentScore::create([
        'assessment_id' => $assessment->id,
        'student_id' => $student->id,
        'score' => 60,
    ]);

    $otherUser = User::factory()->create(['school_id' => $context['school']->id]);
    $otherUser->assignRole(Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']));
    Employee::create([
        'school_id' => $context['school']->id,
        'user_id' => $otherUser->id,
        'kategori_pegawai' => 'guru',
        'nama_lengkap' => 'Guru Lain',
        'jenis_kelamin' => 'P',
    ]);

    $this->actingAs($context['user'])
        ->get(route('remedials.index'))
        ->assertOk()
        ->assertSee('Hapus program remedial?', false)
        ->assertSee('name="_method" value="DELETE"', false);

    $this->actingAs($otherUser)
        ->delete(route('remedials.destroy', $program))
        ->assertForbidden();

    $this->assertDatabaseHas('remedial_programs', ['id' => $program->id]);

    $this->actingAs($context['user'])
        ->delete(route('remedials.destroy', $program))
        ->assertRedirect(route('remedials.index'))
        ->assertSessionHas('success', 'Program remedial berhasil dihapus.');

    $this->assertDatabaseMissing('remedial_programs', ['id' => $program->id]);
    $this->assertDatabaseMissing('remedial_program_assessments', [
        'remedial_program_id' => $program->id,
    ]);
    $this->assertDatabaseMissing('remedial_records', ['id' => $record->id]);
    $this->assertDatabaseHas('assessments', ['id' => $assessment->id]);
    $this->assertDatabaseHas('assessment_scores', ['id' => $score->id]);
});
