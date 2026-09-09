<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function edit($id)
    {
        $currentUser = auth()->user();

        // 1. CARI DATA TARGET
        $query = User::with(['student.family', 'student.address', 'student.financial', 'student.health']);

        if (! $currentUser->hasRole('superadmin')) {
            $query->where('school_id', $currentUser->school_id);
        }

        $student = $query->findOrFail($id);

        // ---------------------------------------------------------
        // KUNCI UTAMA: PASTIKAN TARGET YANG DIBUKA ADALAH SISWA!
        // ---------------------------------------------------------
        if (! $student->hasRole('siswa')) {
            abort(404, 'Halaman ini khusus untuk profil siswa. Pengguna ini bukan siswa.');
        }

        // 2. LOGIKA KEAMANAN: Siapa yang boleh edit siswa ini?
        $isSelf = $currentUser->id == $student->id; // Siswa edit dirinya sendiri
        $isAuthorizedEditor = $currentUser->hasRole('guru') || $currentUser->hasPermissionTo('edit-users'); // Guru / Operator

        if (! $isSelf && ! $isAuthorizedEditor) {
            abort(403, 'Akses Ditolak: Anda tidak berhak mengedit data siswa ini.');
        }

        // 3. Inisialisasi data student jika belum ada
        if (! $student->student) {
            $student->student()->create([
                'school_id' => $student->school_id,
                'nama_lengkap' => $student->name,
                'jenis_kelamin' => 'L',
                'status' => 'aktif',
            ]);
            $student->load('student');
        }

        return view('students.edit', compact('student'));
    }

    public function update(Request $request, $id)
    {
        $currentUser = auth()->user();

        // 1. CARI DATA TARGET
        $query = User::query();
        if (! $currentUser->hasRole('superadmin')) {
            $query->where('school_id', $currentUser->school_id);
        }

        $user = $query->findOrFail($id);

        // ---------------------------------------------------------
        // KUNCI UTAMA: PASTIKAN TARGET YANG DIUBAH ADALAH SISWA!
        // ---------------------------------------------------------
        if (! $user->hasRole('siswa')) {
            abort(404, 'Halaman ini khusus untuk profil siswa. Pengguna ini bukan siswa.');
        }

        // 2. LOGIKA KEAMANAN (SAMA SEPERTI EDIT)
        $isSelf = $currentUser->id == $user->id;
        $isAuthorizedEditor = $currentUser->hasRole('guru') || $currentUser->hasPermissionTo('edit-users');

        if (! $isSelf && ! $isAuthorizedEditor) {
            abort(403, 'Akses Ditolak: Anda tidak berhak mengubah data siswa ini.');
        }

        // Validasi dasar disesuaikan dengan input baru
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        DB::transaction(function () use ($request, $user) {
            // 1. Update User (Akun Login)
            $user->update(['name' => $request->nama_lengkap]);

            // 2. Update Student (Tabel Utama Dapodik)
            $user->student()->update([
                'nama_lengkap' => $request->nama_lengkap,
                'nama_panggilan' => $request->nama_panggilan,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nisn' => $request->nisn,
                'nipd' => $request->nipd,
                'class_code' => $request->class_code,
                'nik' => $request->nik,
                'no_kk' => $request->no_kk,
                'no_registrasi_akta_lahir' => $request->no_registrasi_akta_lahir,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'agama' => $request->agama,
                'hobi' => $request->hobi,
                'cita_cita' => $request->cita_cita,
                'prestasi' => $request->prestasi,
                'hp' => $request->hp,
                'telepon' => $request->telepon,
                'email' => $request->email,
                'skhun' => $request->skhun,
                'no_peserta_ujian_nasional' => $request->no_peserta_ujian_nasional,
                'no_seri_ijazah' => $request->no_seri_ijazah,
                'sekolah_asal' => $request->sekolah_asal,
                'anak_ke' => $request->anak_ke,
                'jml_saudara_kandung' => $request->jml_saudara_kandung,
            ]);

            // 3. Update Alamat (Menghapus dusun/lintang/bujur, menambahkan kota/provinsi)
            $user->student->address()->updateOrCreate(
                ['student_id' => $user->student->id],
                $request->only([
                    'alamat', 'rt', 'rw', 'kelurahan', 'kecamatan', 'kota', 'provinsi', 'kode_pos',
                    'jenis_tinggal', 'alat_transportasi', 'jarak_ke_sekolah_km',
                ])
            );

            // 4. Update Keluarga (Menghapus NIK, menambahkan status hidup, hp, email, dan alamat)
            $user->student->family()->updateOrCreate(
                ['student_id' => $user->student->id],
                $request->only([
                    // Data Ayah
                    'nama_ayah', 'is_ayah_hidup', 'tempat_lahir_ayah', 'tahun_lahir_ayah', 'pendidikan_ayah', 'pekerjaan_ayah', 'penghasilan_ayah', 'hp_ayah', 'email_ayah', 'alamat_ayah',
                    // Data Ibu
                    'nama_ibu', 'is_ibu_hidup', 'tempat_lahir_ibu', 'tahun_lahir_ibu', 'pendidikan_ibu', 'pekerjaan_ibu', 'penghasilan_ibu', 'hp_ibu', 'email_ibu', 'alamat_ibu',
                    // Data Wali
                    'nama_wali', 'tempat_lahir_wali', 'tahun_lahir_wali', 'pendidikan_wali', 'pekerjaan_wali', 'penghasilan_wali', 'hp_wali', 'email_wali', 'alamat_wali',
                ])
            );

            // 5. Update Finansial
            $user->student->financial()->updateOrCreate(
                ['student_id' => $user->student->id],
                [
                    'penerima_kps' => $request->has('penerima_kps'),
                    'no_kps' => $request->no_kps,
                    'penerima_kip' => $request->has('penerima_kip'),
                    'nomor_kip' => $request->nomor_kip,
                    'nama_di_kip' => $request->nama_di_kip,
                    'nomor_kks' => $request->nomor_kks,
                    'layak_pip' => $request->has('layak_pip'),
                    'alasan_layak_pip' => $request->alasan_layak_pip,
                    'bank' => $request->bank,
                    'nomor_rekening_bank' => $request->nomor_rekening_bank,
                    'rekening_atas_nama' => $request->rekening_atas_nama,
                ]
            );

            // 6. Update Kesehatan (Menambahkan kebutuhan khusus dan penyakit)
            $user->student->health()->updateOrCreate(
                ['student_id' => $user->student->id],
                $request->only(['berat_badan', 'tinggi_badan', 'kebutuhan_khusus', 'penyakit'])
            );
        });

        return back()->with('success', 'Data Profil Siswa berhasil diperbarui.');
    }
}
