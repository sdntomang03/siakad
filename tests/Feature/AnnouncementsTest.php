<?php

use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Classroom;
use App\Models\Employee;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
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

function announcementExcelUpload(array $rows): UploadedFile
{
    $export = new class($rows) implements FromArray, WithHeadings
    {
        public function __construct(private array $rows) {}

        public function array(): array
        {
            return $this->rows;
        }

        public function headings(): array
        {
            return ['NISN', 'Nama Siswa', 'Pesan'];
        }
    };

    return UploadedFile::fake()->createWithContent(
        'pesan.xlsx',
        Excel::raw($export, ExcelFormat::XLSX)
    );
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

test('a teacher sends separate private message bodies to every student in an assigned class', function () {
    $school = School::create(['nama_sekolah' => 'Sekolah Pesan Kelas']);
    $teacher = createAnnouncementUser('guru', $school, 'Guru Kelas');
    $employee = Employee::create([
        'school_id' => $school->id,
        'user_id' => $teacher->id,
        'kategori_pegawai' => 'guru',
        'nama_lengkap' => 'Guru Kelas',
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
    $firstUser = createAnnouncementUser('siswa', $school, 'Ayu');
    $secondUser = createAnnouncementUser('siswa', $school, 'Bima');
    $otherUser = createAnnouncementUser('siswa', $school, 'Citra');
    $firstStudent = createAnnouncementStudent($school, 'Ayu', $firstUser);
    $secondStudent = createAnnouncementStudent($school, 'Bima', $secondUser);
    $otherStudent = createAnnouncementStudent($school, 'Citra', $otherUser);
    $classroom->students()->attach([$firstStudent->id, $secondStudent->id]);
    $unassignedClassroom = Classroom::create([
        'school_id' => $school->id,
        'academic_year_id' => $year->id,
        'tingkat' => '5',
        'nama_kelas' => 'B',
        'kapasitas' => 30,
    ]);
    $unassignedClassroom->students()->attach($otherStudent->id);

    $this->actingAs($teacher)
        ->get(route('announcements.index'))
        ->assertOk()
        ->assertSee('Isi berbeda untuk setiap siswa di kelas saya')
        ->assertSee('Ayu')
        ->assertSee('Bima');

    $this->actingAs($teacher)
        ->post(route('announcements.store'), [
            'audience' => 'class_personalized',
            'class_input_method' => 'manual',
            'classroom_id' => $unassignedClassroom->id,
            'title' => 'Tidak boleh dikirim',
            'student_bodies' => [$otherStudent->id => 'Pesan di luar kelas.'],
        ])
        ->assertForbidden();

    expect(Announcement::count())->toBe(0);

    $this->actingAs($teacher)
        ->from(route('announcements.index'))
        ->post(route('announcements.store'), [
            'audience' => 'class_personalized',
            'class_input_method' => 'manual',
            'classroom_id' => $classroom->id,
            'title' => 'Catatan untuk hari ini',
            'student_bodies' => [
                $firstStudent->id => 'Ayu, bawa buku bacaan besok.',
            ],
        ])
        ->assertRedirect(route('announcements.index'))
        ->assertSessionHasErrors('student_bodies');

    expect(Announcement::count())->toBe(0);

    $this->actingAs($teacher)
        ->post(route('announcements.store'), [
            'audience' => 'class_personalized',
            'class_input_method' => 'manual',
            'classroom_id' => $classroom->id,
            'title' => 'Catatan untuk hari ini',
            'student_bodies' => [
                $firstStudent->id => 'Ayu, bawa buku bacaan besok.',
                $secondStudent->id => 'Bima, kumpulkan tugas setelah istirahat.',
            ],
        ])
        ->assertRedirect(route('announcements.index'))
        ->assertSessionHas('success');

    $announcement = Announcement::firstOrFail();
    expect(Announcement::count())->toBe(1)
        ->and($announcement->title)->toBe('Catatan untuk hari ini')
        ->and($announcement->audience)->toBe('class_personalized')
        ->and($announcement->body)->toBeNull()
        ->and($announcement->classroom_id)->toBe($classroom->id)
        ->and($announcement->students()->count())->toBe(2)
        ->and($announcement->students()->whereKey($firstStudent->id)->first()->pivot->body)->toBe('Ayu, bawa buku bacaan besok.')
        ->and($announcement->students()->whereKey($secondStudent->id)->first()->pivot->body)->toBe('Bima, kumpulkan tugas setelah istirahat.');

    $this->actingAs($firstUser)
        ->get(route('announcements.index'))
        ->assertOk()
        ->assertSee('Catatan untuk hari ini')
        ->assertSee('Ayu, bawa buku bacaan besok.')
        ->assertDontSee('Bima, kumpulkan tugas setelah istirahat.');

    $this->actingAs($secondUser)
        ->get(route('announcements.index'))
        ->assertOk()
        ->assertSee('Bima, kumpulkan tugas setelah istirahat.')
        ->assertDontSee('Ayu, bawa buku bacaan besok.');

    $this->actingAs($otherUser)
        ->get(route('announcements.index'))
        ->assertOk()
        ->assertDontSee('Catatan untuk hari ini');
});

test('a teacher downloads an Excel template with the active class roster', function () {
    Excel::fake();

    $school = School::create(['nama_sekolah' => 'Sekolah Format']);
    $teacher = createAnnouncementUser('guru', $school, 'Guru Kelas');
    $employee = Employee::create([
        'school_id' => $school->id,
        'user_id' => $teacher->id,
        'kategori_pegawai' => 'guru',
        'nama_lengkap' => 'Guru Kelas',
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
    $student = createAnnouncementStudent($school, 'Ayu');
    $student->update(['nisn' => '0012345678']);
    $classroom->students()->attach($student->id);

    $this->actingAs($teacher)
        ->get(route('announcements.template', $classroom))
        ->assertOk();

    Excel::assertDownloaded('format-pengumuman-5-a.xlsx', function ($export) use ($student) {
        return $export->headings() === ['NISN', 'Nama Siswa', 'Pesan']
            && $export->collection()->modelKeys() === [$student->id]
            && $export->map($student) === ['0012345678', 'Ayu', ''];
    });

    $otherTeacher = createAnnouncementUser('guru', $school, 'Guru Lain');
    Employee::create([
        'school_id' => $school->id,
        'user_id' => $otherTeacher->id,
        'kategori_pegawai' => 'guru',
        'nama_lengkap' => 'Guru Lain',
        'jenis_kelamin' => 'P',
    ]);

    $this->actingAs($otherTeacher)
        ->get(route('announcements.template', $classroom))
        ->assertForbidden();
});

test('the compatibility migration adds classroom_id when the announcement table predates class announcements', function () {
    Schema::table('announcements', function (Blueprint $table) {
        $table->dropConstrainedForeignId('classroom_id');
    });

    $migration = require database_path('migrations/2026_11_19_065326_add_classroom_id_to_announcements_table.php');
    $migration->up();

    expect(Schema::hasColumn('announcements', 'classroom_id'))->toBeTrue();
});

test('the compatibility migration makes announcement body nullable for class-specific messages', function () {
    Schema::table('announcements', function (Blueprint $table) {
        $table->text('body')->nullable(false)->change();
    });

    $migration = require database_path('migrations/2026_11_19_065949_make_announcement_body_nullable.php');
    $migration->up();
    $bodyColumn = collect(Schema::getColumns('announcements'))->firstWhere('name', 'body');

    expect($bodyColumn['nullable'])->toBeTrue();
});

test('the compatibility migration adds per-student body to the announcement pivot', function () {
    Schema::table('announcement_student', function (Blueprint $table) {
        $table->dropColumn('body');
    });

    $migration = require database_path('migrations/2026_11_19_070239_add_body_to_announcement_student_table.php');
    $migration->up();
    $bodyColumn = collect(Schema::getColumns('announcement_student'))->firstWhere('name', 'body');

    expect($bodyColumn)->not->toBeNull()
        ->and($bodyColumn['nullable'])->toBeTrue();
});

test('a class with a missing NISN cannot download a template or receive an Excel announcement', function () {
    $school = School::create(['nama_sekolah' => 'Sekolah Tanpa NISN']);
    $teacher = createAnnouncementUser('guru', $school, 'Guru Kelas');
    $employee = Employee::create([
        'school_id' => $school->id,
        'user_id' => $teacher->id,
        'kategori_pegawai' => 'guru',
        'nama_lengkap' => 'Guru Kelas',
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
    $student = createAnnouncementStudent($school, 'Ayu');
    $classroom->students()->attach($student->id);

    $this->actingAs($teacher)
        ->from(route('announcements.index'))
        ->get(route('announcements.template', $classroom))
        ->assertRedirect(route('announcements.index'))
        ->assertSessionHasErrors('excel_file');

    $this->actingAs($teacher)
        ->from(route('announcements.index'))
        ->post(route('announcements.store'), [
            'audience' => 'class_personalized',
            'class_input_method' => 'excel',
            'classroom_id' => $classroom->id,
            'title' => 'Catatan untuk hari ini',
            'excel_file' => announcementExcelUpload([
                ['0012345678', 'Ayu', 'Pesan khusus.'],
            ]),
        ])
        ->assertRedirect(route('announcements.index'))
        ->assertSessionHasErrors('excel_file');

    expect(Announcement::count())->toBe(0);
});

test('a teacher imports personalized messages by NISN and keeps leading zeroes', function () {
    $school = School::create(['nama_sekolah' => 'Sekolah Impor']);
    $teacher = createAnnouncementUser('guru', $school, 'Guru Kelas');
    $employee = Employee::create([
        'school_id' => $school->id,
        'user_id' => $teacher->id,
        'kategori_pegawai' => 'guru',
        'nama_lengkap' => 'Guru Kelas',
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
    $firstStudent = createAnnouncementStudent($school, 'Ayu');
    $firstStudent->update(['nisn' => '0012345678']);
    $secondStudent = createAnnouncementStudent($school, 'Bima');
    $secondStudent->update(['nisn' => '0000000123']);
    $classroom->students()->attach([$firstStudent->id, $secondStudent->id]);
    $file = announcementExcelUpload([
        ['0012345678', 'Ayu', 'Ayu, bawa buku bacaan besok.'],
        ['0000000123', 'Bima', 'Bima, kumpulkan tugas setelah istirahat.'],
    ]);

    $this->actingAs($teacher)
        ->post(route('announcements.store'), [
            'audience' => 'class_personalized',
            'class_input_method' => 'excel',
            'classroom_id' => $classroom->id,
            'title' => 'Catatan untuk hari ini',
            'excel_file' => $file,
        ])
        ->assertRedirect(route('announcements.index'))
        ->assertSessionHas('success');

    $announcement = Announcement::firstOrFail();
    expect($announcement->students()->count())->toBe(2)
        ->and($announcement->students()->whereKey($firstStudent->id)->first()->pivot->body)->toBe('Ayu, bawa buku bacaan besok.')
        ->and($announcement->students()->whereKey($secondStudent->id)->first()->pivot->body)->toBe('Bima, kumpulkan tugas setelah istirahat.');
});

test('an Excel import rejects unknown, duplicate, or missing student messages without creating an announcement', function () {
    $school = School::create(['nama_sekolah' => 'Sekolah Validasi Excel']);
    $teacher = createAnnouncementUser('guru', $school, 'Guru Kelas');
    $employee = Employee::create([
        'school_id' => $school->id,
        'user_id' => $teacher->id,
        'kategori_pegawai' => 'guru',
        'nama_lengkap' => 'Guru Kelas',
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
    $firstStudent = createAnnouncementStudent($school, 'Ayu');
    $firstStudent->update(['nisn' => '0012345678']);
    $secondStudent = createAnnouncementStudent($school, 'Bima');
    $secondStudent->update(['nisn' => '0000000123']);
    $classroom->students()->attach([$firstStudent->id, $secondStudent->id]);
    $file = announcementExcelUpload([
        ['0012345678', 'Ayu', 'Pesan pertama.'],
        ['0012345678', 'Ayu', 'Pesan duplikat.'],
        ['9999999999', 'Siswa Lain', 'Bukan siswa di kelas.'],
    ]);

    $this->actingAs($teacher)
        ->from(route('announcements.index'))
        ->post(route('announcements.store'), [
            'audience' => 'class_personalized',
            'class_input_method' => 'excel',
            'classroom_id' => $classroom->id,
            'title' => 'Tidak boleh terkirim',
            'excel_file' => $file,
        ])
        ->assertRedirect(route('announcements.index'))
        ->assertSessionHasErrors('excel_file');

    expect(Announcement::count())->toBe(0);
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
