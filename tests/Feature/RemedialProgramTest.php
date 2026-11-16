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

test('teachers average selected assessments, flag students below KKM, and save remedial status and score', function () {
    $context = createRemedialTeacherContext();
    $firstAssessment = createRemedialAssessment($context, 'Bab 1');
    $secondAssessment = createRemedialAssessment($context, 'Bab 2');
    $students = collect([
        ['name' => 'Ayu', 'scores' => [60, 70]],
        ['name' => 'Budi', 'scores' => [80, 90]],
        ['name' => 'Citra', 'scores' => [60, null]],
        ['name' => 'Dini', 'scores' => [70, 70]],
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
        ->assertSee('65.00')
        ->assertSee('Di bawah KKM')
        ->assertSee('Tuntas')
        ->assertSee('Nilai belum lengkap')
        ->assertSee('2 siswa di bawah KKM dari 3 siswa dengan nilai lengkap.');

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
