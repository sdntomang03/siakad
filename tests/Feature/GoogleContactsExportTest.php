<?php

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Spatie\Permission\Models\Role;

function createGoogleContactsExportContext(): array
{
    $school = School::create(['nama_sekolah' => 'Sekolah Uji']);
    $academicYear = AcademicYear::create([
        'school_id' => $school->id,
        'tahun_ajaran' => '2026/2027',
        'semester' => 'Ganjil',
    ]);
    $classroom = Classroom::create([
        'school_id' => $school->id,
        'academic_year_id' => $academicYear->id,
        'tingkat' => '5',
        'nama_kelas' => '5A',
        'kapasitas' => 30,
    ]);
    $user = User::factory()->create(['school_id' => $school->id]);
    $user->assignRole(Role::create(['name' => 'operator', 'guard_name' => 'web']));

    return [$user, $classroom];
}

test('a class roster can be exported as a Google Contacts CSV using student WhatsApp numbers', function () {
    [$user, $classroom] = createGoogleContactsExportContext();

    $studentWithPhone = Student::create([
        'school_id' => $classroom->school_id,
        'nama_lengkap' => 'Ayu Lestari',
        'jenis_kelamin' => 'P',
        'hp' => '081234567890',
    ]);
    $studentWithoutPhone = Student::create([
        'school_id' => $classroom->school_id,
        'nama_lengkap' => 'Budi Santoso',
        'jenis_kelamin' => 'L',
        'hp' => '   ',
    ]);
    Student::create([
        'school_id' => $classroom->school_id,
        'nama_lengkap' => 'Citra Dewi',
        'jenis_kelamin' => 'P',
        'hp' => '081111111111',
    ]);
    $classroom->students()->attach([$studentWithPhone->id, $studentWithoutPhone->id]);

    $this->actingAs($user)
        ->get(route('classrooms.show', $classroom))
        ->assertOk()
        ->assertSee(route('classrooms.export_google_contacts', $classroom), false);

    $response = $this->actingAs($user)
        ->get(route('classrooms.export_google_contacts', $classroom));

    $response->assertOk();
    $this->assertStringContainsString('Kontak_Google_5-5a.csv', $response->headers->get('content-disposition'));

    $csv = preg_replace('/^\xEF\xBB\xBF/', '', $response->streamedContent());
    $rows = array_map(
        'str_getcsv',
        array_filter(preg_split('/\r\n|\n|\r/', trim($csv)))
    );

    expect($rows)->toBe([
        ['Name', 'Phone 1 - Type', 'Phone 1 - Value'],
        ['Ayu Lestari', 'Mobile', '081234567890'],
    ]);
});

test('the export reports when the class has no student WhatsApp numbers', function () {
    [$user, $classroom] = createGoogleContactsExportContext();

    Student::create([
        'school_id' => $classroom->school_id,
        'nama_lengkap' => 'Budi Santoso',
        'jenis_kelamin' => 'L',
        'hp' => null,
    ]);

    $response = $this->actingAs($user)
        ->from(route('classrooms.show', $classroom))
        ->get(route('classrooms.export_google_contacts', $classroom));

    $response->assertRedirect(route('classrooms.show', $classroom));
    $response->assertSessionHas('error', 'Tidak ada nomor HP/WhatsApp siswa di kelas ini untuk diekspor.');
});

test('a user cannot export contacts from a different school', function () {
    [$user, $classroom] = createGoogleContactsExportContext();
    $user->update(['school_id' => School::create(['nama_sekolah' => 'Sekolah Lain'])->id]);

    $this->actingAs($user)
        ->get(route('classrooms.export_google_contacts', $classroom))
        ->assertNotFound();
});
