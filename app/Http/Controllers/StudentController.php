<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ImageUploadService;
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

    public function update(Request $request, $id, ImageUploadService $imageUploadService)
    {
        $currentUser = auth()->user(); // [cite: 2]

        // 1. CARI DATA TARGET[cite: 2]
        $query = User::query(); // [cite: 2]
        if (! $currentUser->hasRole('superadmin')) { // [cite: 2]
            $query->where('school_id', $currentUser->school_id); // [cite: 2]
        }

        $user = $query->findOrFail($id); // [cite: 2]

        // ---------------------------------------------------------
        // KUNCI UTAMA: PASTIKAN TARGET YANG DIUBAH ADALAH SISWA!
        // ---------------------------------------------------------
        if (! $user->hasRole('siswa')) { // [cite: 2]
            abort(404, 'Halaman ini khusus untuk profil siswa. Pengguna ini bukan siswa.'); // [cite: 2]
        }

        // 2. LOGIKA KEAMANAN (SAMA SEPERTI EDIT)[cite: 2]
        $isSelf = $currentUser->id == $user->id; // [cite: 2]
        $isAuthorizedEditor = $currentUser->hasRole('guru') || $currentUser->hasPermissionTo('edit-users'); // [cite: 2]

        if (! $isSelf && ! $isAuthorizedEditor) { // [cite: 2]
            abort(403, 'Akses Ditolak: Anda tidak berhak mengubah data siswa ini.'); // [cite: 2]
        }

        // Validasi dasar & validasi upload foto
        $request->validate([
            'nama_lengkap' => 'required|string|max:255', // [cite: 2]
            'jenis_kelamin' => 'required|in:L,P', // [cite: 2]
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // <-- Tambahan validasi foto
        ]);

        DB::transaction(function () use ($request, $user, $imageUploadService) { // [cite: 2]

            // Proses Upload Foto (Hanya jika ada file yang diunggah)
            $fotoPath = $user->student->foto ?? null; // Ambil foto lama jika ada

            if ($request->hasFile('foto')) {
                // Gunakan service untuk upload dan konversi ke webp, sekaligus menghapus foto lama
                $fotoPath = $imageUploadService->uploadAndConvertToWebp(
                    $request->file('foto'),
                    'students/photos',
                    $fotoPath
                );
            }

            // 1. Update User (Akun Login)[cite: 2]
            $user->update(['name' => $request->nama_lengkap]); // [cite: 2]

            // 2. Update Student (Tabel Utama Dapodik)[cite: 2]
            $user->student()->update([
                'nama_lengkap' => $request->nama_lengkap, // [cite: 2]
                'nama_panggilan' => $request->nama_panggilan, // [cite: 2]
                'jenis_kelamin' => $request->jenis_kelamin, // [cite: 2]
                'nisn' => $request->nisn, // [cite: 2]
                'nipd' => $request->nipd, // [cite: 2]
                'class_code' => $request->class_code, // [cite: 2]
                'nik' => $request->nik, // [cite: 2]
                'no_kk' => $request->no_kk, // [cite: 2]
                'no_registrasi_akta_lahir' => $request->no_registrasi_akta_lahir, // [cite: 2]
                'tempat_lahir' => $request->tempat_lahir, // [cite: 2]
                'tanggal_lahir' => $request->tanggal_lahir, // [cite: 2]
                'agama' => $request->agama, // [cite: 2]
                'hobi' => $request->hobi, // [cite: 2]
                'cita_cita' => $request->cita_cita, // [cite: 2]
                'prestasi' => $request->prestasi, // [cite: 2]
                'foto' => $fotoPath, // <-- Tambahan: simpan lokasi path foto terbaru
                'hp' => $request->hp, // [cite: 2]
                'telepon' => $request->telepon, // [cite: 2]
                'email' => $request->email, // [cite: 2]
                'skhun' => $request->skhun, // [cite: 2]
                'no_peserta_ujian_nasional' => $request->no_peserta_ujian_nasional, // [cite: 2]
                'no_seri_ijazah' => $request->no_seri_ijazah, // [cite: 2]
                'sekolah_asal' => $request->sekolah_asal, // [cite: 2]
                'anak_ke' => $request->anak_ke, // [cite: 2]
                'jml_saudara_kandung' => $request->jml_saudara_kandung, // [cite: 2]
            ]);

            // 3. Update Alamat[cite: 2]
            $user->student->address()->updateOrCreate( // [cite: 2]
                ['student_id' => $user->student->id], // [cite: 2]
                $request->only([ // [cite: 2]
                    'alamat', 'rt', 'rw', 'kelurahan', 'kecamatan', 'kota', 'provinsi', 'kode_pos', // [cite: 2]
                    'jenis_tinggal', 'alat_transportasi', 'jarak_ke_sekolah_km', // [cite: 2]
                ]) // [cite: 2]
            );

            // 4. Update Keluarga (Tahun lahir menjadi tanggal lahir)[cite: 2]
            $user->student->family()->updateOrCreate( // [cite: 2]
                ['student_id' => $user->student->id], // [cite: 2]
                $request->only([ // [cite: 2]
                    // Data Ayah
                    'nama_ayah', 'is_ayah_hidup', 'tempat_lahir_ayah', 'tanggal_lahir_ayah', 'agama_ayah', 'pendidikan_ayah', 'pekerjaan_ayah', 'penghasilan_ayah', 'hp_ayah', 'email_ayah', 'alamat_ayah', // [cite: 2]
                    // Data Ibu
                    'nama_ibu', 'is_ibu_hidup', 'tempat_lahir_ibu', 'tanggal_lahir_ibu', 'agama_ibu', 'pendidikan_ibu', 'pekerjaan_ibu', 'penghasilan_ibu', 'hp_ibu', 'email_ibu', 'alamat_ibu', // [cite: 2]
                    // Data Wali
                    'nama_wali', 'tempat_lahir_wali', 'tanggal_lahir_wali', 'agama_wali', 'pendidikan_wali', 'pekerjaan_wali', 'penghasilan_wali', 'hp_wali', 'email_wali', 'alamat_wali', // [cite: 2]
                ]) // [cite: 2]
            );

            // 5. Update Finansial (Hanya menyimpan boolean KJP, PIP, dan Lainnya)[cite: 2]
            $user->student->financial()->updateOrCreate( // [cite: 2]
                ['student_id' => $user->student->id], // [cite: 2]
                [ // [cite: 2]
                    'penerima_kjp' => $request->has('penerima_kjp'), // [cite: 2]
                    'penerima_pip' => $request->has('penerima_pip'), // [cite: 2]
                    'penerima_bantuan_lain' => $request->has('penerima_bantuan_lain'), // [cite: 2]
                ] // [cite: 2]
            );

            // 6. Update Kesehatan[cite: 2]
            $user->student->health()->updateOrCreate( // [cite: 2]
                ['student_id' => $user->student->id], // [cite: 2]
                $request->only(['berat_badan', 'tinggi_badan', 'kebutuhan_khusus', 'penyakit']) // [cite: 2]
            );
        });

        return back()->with('success', 'Data Profil Siswa berhasil diperbarui.'); // [cite: 2]
    }
}
