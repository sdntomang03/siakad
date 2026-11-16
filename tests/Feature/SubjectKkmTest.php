<?php

use App\Models\School;
use App\Models\Subject;
use App\Models\User;
use Spatie\Permission\Models\Role;

function createSubjectKkmContext(): array
{
    $school = School::create(['nama_sekolah' => 'Sekolah KKM']);
    $otherSchool = School::create(['nama_sekolah' => 'Sekolah Lain']);
    $operator = User::factory()->create(['school_id' => $school->id]);
    $operator->assignRole(Role::firstOrCreate(['name' => 'operator', 'guard_name' => 'web']));

    $subject = Subject::create([
        'school_id' => $school->id,
        'tingkat' => 5,
        'nama_mapel' => 'Matematika',
        'pengampu' => 'guru_kelas',
        'kkm' => 75,
    ]);
    $otherSubject = Subject::create([
        'school_id' => $otherSchool->id,
        'tingkat' => 5,
        'nama_mapel' => 'Bahasa Indonesia',
        'pengampu' => 'guru_kelas',
        'kkm' => 70,
    ]);

    return compact('operator', 'subject', 'otherSubject');
}

test('operators can edit a subject KKM and cannot edit another school or save an invalid value', function () {
    $context = createSubjectKkmContext();

    $this->actingAs($context['operator'])
        ->get(route('subjects.index'))
        ->assertOk()
        ->assertSee('Ubah KKM Matematika kelas 5');

    $this->actingAs($context['operator'])
        ->patch(route('subjects.update-kkm', $context['subject']), ['kkm' => 82])
        ->assertSessionHas('success');

    $this->assertDatabaseHas('subjects', [
        'id' => $context['subject']->id,
        'kkm' => 82,
    ]);

    $this->actingAs($context['operator'])
        ->patch(route('subjects.update-kkm', $context['subject']), ['kkm' => 101])
        ->assertSessionHasErrors('kkm');

    $this->assertDatabaseHas('subjects', [
        'id' => $context['subject']->id,
        'kkm' => 82,
    ]);

    $this->actingAs($context['operator'])
        ->patch(route('subjects.update-kkm', $context['otherSubject']), ['kkm' => 90])
        ->assertForbidden();

    $this->assertDatabaseHas('subjects', [
        'id' => $context['otherSubject']->id,
        'kkm' => 70,
    ]);
});
