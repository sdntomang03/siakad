<?php

use App\Models\Announcement;
use App\Models\Employee;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Spatie\Permission\Models\Role;

function createAnnouncementUser(string $roleName, School $school, string $name): User
{
    $user = User::factory()->create(['school_id' => $school->id, 'name' => $name]);
    $user->assignRole(Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']));

    return $user;
}

function createAnnouncementStudent(School $school, string $name, ?User $user = null, string $status = 'aktif'): Student
{
    return Student::create([
        'school_id' => $school->id,
        'user_id' => $user?->id,
        'nama_lengkap' => $name,
        'jenis_kelamin' => 'P',
        'status' => $status,
    ]);
}

test('a teacher sends one school announcement to all active students and students can read it', function () {
    $school = School::create(['nama_sekolah' => 'Sekolah Pengumuman']);
    $teacher = createAnnouncementUser('guru', $school, 'Guru Uji');
    Employee::create([
        'school_id' => $school->id,
        'user_id' => $teacher->id,
        'kategori_pegawai' => 'guru',
        'nama_lengkap' => 'Guru Uji',
        'jenis_kelamin' => 'P',
    ]);

    $firstStudentUser = createAnnouncementUser('siswa', $school, 'Siswa Satu');
    $secondStudentUser = createAnnouncementUser('siswa', $school, 'Siswa Dua');
    $firstStudent = createAnnouncementStudent($school, 'Siswa Satu', $firstStudentUser);
    $secondStudent = createAnnouncementStudent($school, 'Siswa Dua', $secondStudentUser);
    createAnnouncementStudent($school, 'Siswa Nonaktif', null, 'lulus');

    $otherSchool = School::create(['nama_sekolah' => 'Sekolah Lain']);
    $otherSchoolStudent = createAnnouncementStudent($otherSchool, 'Siswa Lain');

    $this->actingAs($teacher)
        ->post(route('announcements.store'), [
            'audience' => 'all',
            'title' => 'Perubahan jadwal',
            'body' => 'Kegiatan dimulai setelah apel.',
        ])
        ->assertRedirect(route('announcements.index'))
        ->assertSessionHas('success');

    $announcement = Announcement::firstOrFail();
    expect(Announcement::count())->toBe(1)
        ->and($announcement->audience)->toBe('all')
        ->and($announcement->students()->count())->toBe(2)
        ->and($announcement->students()->whereKey($firstStudent->id)->exists())->toBeTrue()
        ->and($announcement->students()->whereKey($secondStudent->id)->exists())->toBeTrue()
        ->and($announcement->students()->whereKey($otherSchoolStudent->id)->exists())->toBeFalse();

    $this->actingAs($firstStudentUser)
        ->get(route('announcements.index'))
        ->assertOk()
        ->assertSee('Perubahan jadwal')
        ->assertSee('Kegiatan dimulai setelah apel.');
});

test('an individual announcement is only visible to its selected student', function () {
    $school = School::create(['nama_sekolah' => 'Sekolah Pengumuman Individu']);
    $teacher = createAnnouncementUser('guru', $school, 'Guru Uji');
    $targetUser = createAnnouncementUser('siswa', $school, 'Siswa Tujuan');
    $otherUser = createAnnouncementUser('siswa', $school, 'Siswa Lain');
    $target = createAnnouncementStudent($school, 'Siswa Tujuan', $targetUser);
    createAnnouncementStudent($school, 'Siswa Lain', $otherUser);

    $this->actingAs($teacher)
        ->post(route('announcements.store'), [
            'audience' => 'individual',
            'student_id' => $target->id,
            'title' => 'Informasi pribadi',
            'body' => 'Silakan temui guru setelah pelajaran.',
        ])
        ->assertRedirect(route('announcements.index'));

    expect(Announcement::count())->toBe(1)
        ->and(Announcement::firstOrFail()->students()->count())->toBe(1);

    $this->actingAs($targetUser)
        ->get(route('announcements.index'))
        ->assertOk()
        ->assertSee('Informasi pribadi');

    $this->actingAs($otherUser)
        ->get(route('announcements.index'))
        ->assertOk()
        ->assertDontSee('Informasi pribadi');
});

test('teachers cannot target students outside their school and students cannot send announcements', function () {
    $school = School::create(['nama_sekolah' => 'Sekolah Pengirim']);
    $teacher = createAnnouncementUser('guru', $school, 'Guru Uji');
    $studentUser = createAnnouncementUser('siswa', $school, 'Siswa Uji');
    $otherSchool = School::create(['nama_sekolah' => 'Sekolah Tujuan']);
    $otherSchoolStudent = createAnnouncementStudent($otherSchool, 'Siswa Sekolah Lain');

    $this->actingAs($teacher)
        ->from(route('announcements.index'))
        ->post(route('announcements.store'), [
            'audience' => 'individual',
            'student_id' => $otherSchoolStudent->id,
            'title' => 'Tidak boleh dikirim',
            'body' => 'Isi pesan.',
        ])
        ->assertRedirect(route('announcements.index'))
        ->assertSessionHasErrors('student_id');

    $this->actingAs($studentUser)
        ->post(route('announcements.store'), [
            'audience' => 'all',
            'title' => 'Tidak boleh dikirim',
            'body' => 'Isi pesan.',
        ])
        ->assertForbidden();

    expect(Announcement::count())->toBe(0);
});
