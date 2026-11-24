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
use App\Exports\AssessmentDetailExport;
use App\Exports\AssessmentTypeRecapExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelWriter;
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
        ->get(route('assessments.input', $context['assessment']))
        ->assertOk()
        ->assertSee('step="1"', false)
        ->assertSee('value="82.5"', false)
        ->assertSee('formnovalidate', false);

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

test('teachers can filter and export assessment recap by assessment type', function () {
    $context = createAssessmentMetadataContext();
    $assessment = Assessment::create([
        'school_id' => $context['school']->id,
        'academic_year_id' => $context['year']->id,
        'classroom_id' => $context['classroom']->id,
        'subject_id' => $context['subjects'][0]->id,
        'employee_id' => $context['employee']->id,
        'assessment_type_id' => $context['types'][1]->id,
        'keterangan' => 'Sumatif akhir bab 1',
        'tanggal' => '2026-10-11',
    ]);
    AssessmentScore::create([
        'assessment_id' => $assessment->id,
        'student_id' => $context['student']->id,
        'score' => 94,
    ]);

    $filters = [
        'classroom_id' => $context['classroom']->id,
        'subject_id' => 'all',
        'assessment_type_id' => $context['types'][1]->id,
    ];

    $this->actingAs($context['user'])
        ->get(route('assessments.recapByType'))
        ->assertOk()
        ->assertSee('Jenis Penilaian')
        ->assertSee('Semua Jenis Penilaian');

    $this->actingAs($context['user'])
        ->get(route('assessments.recapByType', $filters))
        ->assertOk()
        ->assertSee('SA')
        ->assertSee('94')
        ->assertSee('Semua Jenis Penilaian')
        ->assertSee('Unduh Rekap Excel')
        ->assertSee('Unduh Gradebook Excel')
        ->assertDontSee('SH');

    Excel::fake();

    $this->actingAs($context['user'])
        ->get(route('assessments.recapByType', $filters + ['export' => 'excel']))
        ->assertOk();

    Excel::assertDownloaded('rekap_jenis_kelas_'.$context['classroom']->id.'.xlsx', function ($export) {
        $rows = $export->array();

        return in_array('Matematika - Sumatif Akhir', $rows[0], true)
            && in_array(94, $rows[1])
            && ! in_array('Matematika - Sumatif Harian', $rows[0], true);
    });
});

test('teachers can export gradebook detail with the selected subject and assessment type filters', function () {
    $context = createAssessmentMetadataContext();
    $assessment = Assessment::create([
        'school_id' => $context['school']->id,
        'academic_year_id' => $context['year']->id,
        'classroom_id' => $context['classroom']->id,
        'subject_id' => $context['subjects'][0]->id,
        'employee_id' => $context['employee']->id,
        'assessment_type_id' => $context['types'][1]->id,
        'keterangan' => 'Sumatif akhir bab 1',
        'tanggal' => '2026-10-11',
    ]);
    AssessmentScore::create([
        'assessment_id' => $assessment->id,
        'student_id' => $context['student']->id,
        'score' => 94,
    ]);

    $this->actingAs($context['user'])
        ->get(route('assessments.recap', [
            'classroom_id' => $context['classroom']->id,
            'subject_id' => 'all',
            'assessment_type_id' => $context['types'][1]->id,
        ]))
        ->assertOk()
        ->assertSee('Unduh Excel');

    Excel::fake();

    $this->actingAs($context['user'])
        ->get(route('assessments.recap', [
            'classroom_id' => $context['classroom']->id,
            'subject_id' => 'all',
            'assessment_type_id' => $context['types'][1]->id,
            'export' => 'excel',
        ]))
        ->assertOk();

    Excel::assertDownloaded('gradebook_kelas_'.$context['classroom']->id.'.xlsx', function ($export) {
        $rows = $export->array();

        return in_array('Matematika - SA (11/10/2026)', $rows[0], true)
            && in_array(94, $rows[1])
            && ! in_array('SH (10/10/2026)', $rows[0], true);
    });
});

test('assessment recap exports produce valid xlsx workbooks', function () {
    $detailExport = new AssessmentDetailExport(collect(), collect(), [], true);
    $recapExport = new AssessmentTypeRecapExport(collect(), collect(), [], [], [], []);

    expect(substr(Excel::raw($detailExport, ExcelWriter::XLSX), 0, 2))->toBe('PK')
        ->and(substr(Excel::raw($recapExport, ExcelWriter::XLSX), 0, 2))->toBe('PK');
});

test('teachers can filter assessment history by subject and assessment type together', function () {
    $context = createAssessmentMetadataContext();
    $matchingAssessment = Assessment::create([
        'school_id' => $context['school']->id,
        'academic_year_id' => $context['year']->id,
        'classroom_id' => $context['classroom']->id,
        'subject_id' => $context['subjects'][0]->id,
        'employee_id' => $context['employee']->id,
        'assessment_type_id' => $context['types'][1]->id,
        'keterangan' => 'Nilai Matematika akhir',
        'tanggal' => '2026-10-11',
    ]);
    Assessment::create([
        'school_id' => $context['school']->id,
        'academic_year_id' => $context['year']->id,
        'classroom_id' => $context['classroom']->id,
        'subject_id' => $context['subjects'][1]->id,
        'employee_id' => $context['employee']->id,
        'assessment_type_id' => $context['types'][1]->id,
        'keterangan' => 'Nilai Bahasa akhir',
        'tanggal' => '2026-10-12',
    ]);

    $this->actingAs($context['user'])
        ->get(route('assessments.index', [
            'subject_id' => $context['subjects'][0]->id,
            'assessment_type_id' => $context['types'][1]->id,
        ]))
        ->assertOk()
        ->assertSee('Nilai Matematika akhir')
        ->assertDontSee('Bab 1')
        ->assertDontSee('Nilai Bahasa akhir')
        ->assertSee('name="subject_id"', false)
        ->assertSee('name="assessment_type_id"', false)
        ->assertSee('Hapus Filter');

    $this->actingAs($context['user'])
        ->get(route('assessments.index', [
            'subject_id' => $context['subjects'][1]->id,
            'assessment_type_id' => $context['types'][0]->id,
        ]))
        ->assertOk()
        ->assertSee('Tidak ada penilaian yang sesuai dengan filter.')
        ->assertSee(route('assessments.index'));

    $this->assertDatabaseHas('assessments', ['id' => $matchingAssessment->id]);
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
