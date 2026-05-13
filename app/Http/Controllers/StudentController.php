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

        // Validasi dasar
        $request->validate([
            'name' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        DB::transaction(function () use ($request, $user) {
            // 1. Update User (Akun Login)
            $user->update(['name' => $request->name]);

            // 2. Update Student (Tabel Utama Dapodik)
            $user->student()->update([
                'nama_lengkap' => $request->name,
                'nisn' => $request->nisn,
                'nipd' => $request->nipd,
                'nik' => $request->nik,
                'no_kk' => $request->no_kk,
                'no_registrasi_akta_lahir' => $request->no_registrasi_akta_lahir,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'agama' => $request->agama,
                'anak_ke' => $request->anak_ke,
                'jml_saudara_kandung' => $request->jml_saudara_kandung,
                'hp' => $request->hp,
            ]);

            // 3. Update Alamat
            $user->student->address()->updateOrCreate(
                ['student_id' => $user->student->id],
                $request->only([
                    'alamat', 'rt', 'rw', 'dusun', 'kelurahan', 'kecamatan', 'kode_pos',
                    'lintang', 'bujur', 'jenis_tinggal', 'alat_transportasi', 'jarak_ke_sekolah_km',
                ])
            );

            // 4. Update Keluarga
            $user->student->family()->updateOrCreate(
                ['student_id' => $user->student->id],
                $request->only([
                    'nama_ayah', 'nik_ayah', 'tahun_lahir_ayah', 'pendidikan_ayah', 'pekerjaan_ayah', 'penghasilan_ayah',
                    'nama_ibu', 'nik_ibu', 'tahun_lahir_ibu', 'pendidikan_ibu', 'pekerjaan_ibu', 'penghasilan_ibu',
                    'nama_wali', 'nik_wali', 'tahun_lahir_wali', 'pendidikan_wali', 'pekerjaan_wali', 'penghasilan_wali',
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

            // 6. Update Kesehatan
            $user->student->health()->updateOrCreate(
                ['student_id' => $user->student->id],
                $request->only(['berat_badan', 'tinggi_badan', 'lingkar_kepala'])
            );
        });

        return back()->with('success', 'Data Dapodik siswa berhasil diperbarui.');
    }
}
