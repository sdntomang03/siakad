<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Employee;
use App\Models\School;
use App\Models\Student;
use App\Models\Subject; // Tambahkan ini
use App\Models\User;   // Tambahkan ini
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Sekolah
        $sekolah = School::create([
            'npsn' => '20101234',
            'nama_sekolah' => 'SDN Tomang 03 Pagi',
            'tingkat' => 'SD',
            'alamat' => 'Jakarta',
        ]);

        // 2. Buat Akun Superadmin
        $superadmin = User::create([
            'name' => 'Super Admin Pusat',
            'username' => 'admin',
            'email' => 'admin@siakad.com',
            'password' => Hash::make('password'),
            'school_id' => null,
        ]);
        $superadmin->assignRole('superadmin');

        // 3. Buat Akun Operator Sekolah & Profilnya
        $operator = User::create([
            'name' => 'Operator Sekolah',
            'username' => 'operator',
            'email' => 'operator@sekolah.com',
            'password' => Hash::make('password'),
            'school_id' => $sekolah->id,
        ]);
        $operator->assignRole('operator');

        Employee::create([
            'school_id' => $sekolah->id,
            'user_id' => $operator->id,
            'kategori_pegawai' => 'tu',
            'nama_lengkap' => 'Operator Sekolah',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'Honorer',
        ]);

        // 4. Buat Akun Kepala Sekolah & Profilnya
        $kepsek = User::create([
            'name' => 'Pak Hanung',
            'username' => 'kepsek',
            'email' => 'hanung@sekolah.com',
            'password' => Hash::make('password'),
            'school_id' => $sekolah->id,
        ]);
        $kepsek->assignRole('kepsek');

        Employee::create([
            'school_id' => $sekolah->id,
            'user_id' => $kepsek->id,
            'kategori_pegawai' => 'guru',
            'nama_lengkap' => 'Hanung, S.Pd., M.Pd.',
            'jenis_kelamin' => 'L',
            'nip' => '198001012005011003',
            'status_kepegawaian' => 'PNS',
            'tugas_tambahan' => 'Kepala Sekolah',
        ]);

        // 5. Buat Akun Guru & Profilnya
        $guru = User::create([
            'name' => 'Bu Okti',
            'username' => 'guru',
            'email' => 'okti@sekolah.com',
            'password' => Hash::make('password'),
            'school_id' => $sekolah->id,
        ]);
        $guru->assignRole('guru');

        $guruEmployee = Employee::create([
            'school_id' => $sekolah->id,
            'user_id' => $guru->id,
            'kategori_pegawai' => 'guru',
            'nama_lengkap' => 'Okti, S.Pd.',
            'jenis_kelamin' => 'P',
            'nip' => '198502022010012004',
            'status_kepegawaian' => 'PNS',
        ]);

        // 6. Buat Akun Siswa Spesifik (Sandi Maulana)
        $siswaSandi = User::create([
            'name' => 'Sandi Maulana',
            'username' => '0123456789',
            'email' => 'sandi@siswa.com',
            'password' => Hash::make('password'),
            'school_id' => $sekolah->id,
        ]);
        $siswaSandi->assignRole('siswa');

        Student::create([
            'school_id' => $sekolah->id,
            'user_id' => $siswaSandi->id,
            'nisn' => '0123456789',
            'nama_lengkap' => 'Sandi Maulana',
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
        ]);

        // 7. Generate 10 Siswa menggunakan Faker
        $faker = Faker::create('id_ID');
        $roleSiswa = Role::firstOrCreate(['name' => 'siswa']);

        for ($i = 0; $i < 10; $i++) {
            $gender = $faker->randomElement(['L', 'P']);
            $fullName = ($gender === 'L' ? $faker->firstNameMale() : $faker->firstNameFemale()).' '.$faker->lastName();
            $nisn = $faker->unique()->numerify('00##########');

            $user = User::create([
                'school_id' => $sekolah->id,
                'name' => $fullName,
                'username' => $nisn,
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('12345678'),
            ]);
            $user->assignRole($roleSiswa);

            Student::create([
                'school_id' => $sekolah->id,
                'user_id' => $user->id,
                'nama_lengkap' => $fullName,
                'jenis_kelamin' => $gender,
                'nisn' => $nisn,
                'nipd' => $faker->unique()->numerify('####'),
                'status' => 'aktif',
            ]);
        }

        // 8. Buat Tahun Ajaran
        $activeYear = AcademicYear::create([
            'school_id' => $sekolah->id,
            'tahun_ajaran' => '2025/2026',
            'semester' => 'Ganjil',
            'is_active' => 1,
        ]);

        AcademicYear::create([
            'school_id' => $sekolah->id,
            'tahun_ajaran' => '2025/2026',
            'semester' => 'Genap',
            'is_active' => 0,
        ]);

        // 9. BUAT ROMBEL (KELAS 4B) & MASUKKAN SISWA
        $classroom = Classroom::create([
            'school_id' => $sekolah->id,
            'academic_year_id' => $activeYear->id,
            'homeroom_teacher_id' => $guruEmployee->id,
            'tingkat' => 4,
            'nama_kelas' => 'B',
            'kapasitas' => 32,
        ]);

        // Ambil semua ID siswa di sekolah ini dan masukkan ke kelas
        $allStudentIds = Student::where('school_id', $sekolah->id)->pluck('id');
        $classroom->students()->attach($allStudentIds);

        // 10. BUAT MATA PELAJARAN (SUBJECTS)
        // Mapel Guru Kelas
        $mapelGuruKelas = ['Bahasa Indonesia', 'Matematika', 'IPAS', 'Pendidikan Pancasila'];
        foreach ($mapelGuruKelas as $m) {
            Subject::create([
                'school_id' => $sekolah->id,
                'tingkat' => 4,
                'nama_mapel' => $m,
                'pengampu' => 'guru_kelas',
                'kkm' => 75,
            ]);
        }

        // Mapel Guru Khusus
        $mapelKhusus = ['Pendidikan Agama Islam', 'PJOK'];
        foreach ($mapelKhusus as $m) {
            Subject::create([
                'school_id' => $sekolah->id,
                'tingkat' => 4,
                'nama_mapel' => $m,
                'pengampu' => 'guru_mapel',
                'kkm' => 75,
            ]);
        }
    }
}
