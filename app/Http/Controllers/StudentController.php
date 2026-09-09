<?php

namespace App\Http\Controllers;

use App\Models\Student;
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
        $currentUser = auth()->user();

        // 1. CARI DATA TARGET[cite: 2]
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

        // 2. LOGIKA KEAMANAN (SAMA SEPERTI EDIT)[cite: 2]
        $isSelf = $currentUser->id == $user->id;
        $isAuthorizedEditor = $currentUser->hasRole('guru') || $currentUser->hasPermissionTo('edit-users');

        if (! $isSelf && ! $isAuthorizedEditor) {
            abort(403, 'Akses Ditolak: Anda tidak berhak mengubah data siswa ini.');
        }

        // Validasi dasar & validasi upload foto
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5048', // <-- Tambahan validasi foto
        ]);

        DB::transaction(function () use ($request, $user, $imageUploadService) {

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
            $user->update(['name' => $request->nama_lengkap]);

            // 2. Update Student (Tabel Utama Dapodik)[cite: 2]
            $user->student()->update([
                'nama_lengkap' => strtoupper($request->nama_lengkap),
                'nama_panggilan' => $request->nama_panggilan,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nisn' => $request->nisn,
                'nipd' => $request->nipd,
                'class_code' => $request->class_code,
                'nik' => $request->nik,
                'no_kk' => $request->no_kk,
                'no_registrasi_akta_lahir' => $request->no_registrasi_akta_lahir,
                'tempat_lahir' => ucwords($request->tempat_lahir),
                'tanggal_lahir' => $request->tanggal_lahir,
                'agama' => $request->agama,
                'hobi' => $request->hobi,
                'cita_cita' => $request->cita_cita,
                'prestasi' => $request->prestasi,
                'foto' => $fotoPath, // <-- Tambahan: simpan lokasi path foto terbaru
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

            // 3. Update Alamat[cite: 2]
            $user->student->address()->updateOrCreate(
                ['student_id' => $user->student->id],
                $request->only([
                    'alamat', 'rt', 'rw', 'kelurahan', 'kecamatan', 'kota', 'provinsi', 'kode_pos',
                    'jenis_tinggal', 'alat_transportasi', 'jarak_ke_sekolah_km',
                ])
            );

            // 4. Update Keluarga (Tahun lahir menjadi tanggal lahir)[cite: 2]
            $user->student->family()->updateOrCreate(
                ['student_id' => $user->student->id],
                $request->only([
                    // Data Ayah
                    'nama_ayah', 'is_ayah_hidup', 'tempat_lahir_ayah', 'tanggal_lahir_ayah', 'agama_ayah', 'pendidikan_ayah', 'pekerjaan_ayah', 'penghasilan_ayah', 'hp_ayah', 'email_ayah', 'alamat_ayah',
                    // Data Ibu
                    'nama_ibu', 'is_ibu_hidup', 'tempat_lahir_ibu', 'tanggal_lahir_ibu', 'agama_ibu', 'pendidikan_ibu', 'pekerjaan_ibu', 'penghasilan_ibu', 'hp_ibu', 'email_ibu', 'alamat_ibu',
                    // Data Wali
                    'nama_wali', 'tempat_lahir_wali', 'tanggal_lahir_wali', 'agama_wali', 'pendidikan_wali', 'pekerjaan_wali', 'penghasilan_wali', 'hp_wali', 'email_wali', 'alamat_wali',
                ])
            );

            // 5. Update Finansial (Hanya menyimpan boolean KJP, PIP, dan Lainnya)[cite: 2]
            $user->student->financial()->updateOrCreate(
                ['student_id' => $user->student->id],
                [
                    'penerima_kjp' => $request->has('penerima_kjp'),
                    'penerima_pip' => $request->has('penerima_pip'),
                    'penerima_bantuan_lain' => $request->has('penerima_bantuan_lain'),
                ]
            );

            // 6. Update Kesehatan[cite: 2]
            $user->student->health()->updateOrCreate(
                ['student_id' => $user->student->id],
                $request->only(['berat_badan', 'tinggi_badan', 'kebutuhan_khusus', 'penyakit'])
            );
        });

        return back()->with('success', 'Data Profil Siswa berhasil diperbarui.');
    }

    public function updateAjax(Request $request, $id, ImageUploadService $imageUploadService)
    {
        $currentUser = auth()->user();
        $user = User::findOrFail($id);

        // Validasi atau logika keamanan Anda di sini...

        DB::transaction(function () use ($request, $user, $imageUploadService) {
            $fotoPath = $user->student->foto ?? null;
            if ($request->hasFile('foto')) {
                $fotoPath = $imageUploadService->uploadAndConvertToWebp(
                    $request->file('foto'), 'students/photos', $fotoPath
                );
                $user->student()->update(['foto' => $fotoPath]);
            }

            // Update bagian data sesuai field yang dikirim dari tab aktif
            if ($request->has('nama_lengkap')) {
                $user->update(['name' => $request->nama_lengkap]);
                $user->student()->update($request->only([
                    'nama_lengkap', 'nama_panggilan', 'jenis_kelamin', 'nisn', 'nipd',
                    'class_code', 'nik', 'no_kk', 'no_registrasi_akta_lahir', 'tempat_lahir',
                    'tanggal_lahir', 'agama', 'hobi', 'cita_cita', 'prestasi', 'hp',
                    'telepon', 'email', 'sekolah_asal', 'anak_ke', 'jml_saudara_kandung',
                ]));
            }

            if ($request->has('alamat')) {
                $user->student->address()->updateOrCreate(
                    ['student_id' => $user->student->id],
                    $request->only(['alamat', 'rt', 'rw', 'kelurahan', 'kecamatan', 'kota', 'provinsi', 'kode_pos', 'jenis_tinggal', 'alat_transportasi', 'jarak_ke_sekolah_km'])
                );
            }

            if ($request->has('nama_ayah') || $request->has('nama_ibu')) {
                $user->student->family()->updateOrCreate(
                    ['student_id' => $user->student->id],
                    $request->only([
                        'nama_ayah', 'is_ayah_hidup', 'tempat_lahir_ayah', 'tanggal_lahir_ayah', 'agama_ayah', 'pendidikan_ayah', 'pekerjaan_ayah', 'penghasilan_ayah', 'hp_ayah', 'email_ayah', 'alamat_ayah',
                        'nama_ibu', 'is_ibu_hidup', 'tempat_lahir_ibu', 'tanggal_lahir_ibu', 'agama_ibu', 'pendidikan_ibu', 'pekerjaan_ibu', 'penghasilan_ibu', 'hp_ibu', 'email_ibu', 'alamat_ibu',
                        'nama_wali', 'tempat_lahir_wali', 'tanggal_lahir_wali', 'agama_wali', 'pendidikan_wali', 'pekerjaan_wali', 'penghasilan_wali', 'hp_wali', 'email_wali', 'alamat_wali',
                    ])
                );
            }

            if ($request->has('penerima_kjp') || $request->has('penerima_pip') || $request->has('penerima_bantuan_lain') || $request->isMethod('put')) {
                $user->student->financial()->updateOrCreate(
                    ['student_id' => $user->student->id],
                    [
                        'penerima_kjp' => $request->has('penerima_kjp'),
                        'penerima_pip' => $request->has('penerima_pip'),
                        'penerima_bantuan_lain' => $request->has('penerima_bantuan_lain'),
                    ]
                );
            }

            if ($request->has('tinggi_badan') || $request->has('berat_badan')) {
                $user->student->health()->updateOrCreate(
                    ['student_id' => $user->student->id],
                    $request->only(['berat_badan', 'tinggi_badan', 'kebutuhan_khusus', 'penyakit'])
                );
            }
        });

        return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan otomatis.']);
    }

    /**
     * Menampilkan detail biodata siswa.
     */
    public function show($id)
    {
        // 1. Ambil data siswa beserta relasinya
        $student = Student::with([
            'address',
            'family',
            'health',
            'financial',
            'user',
        ])->findOrFail($id);

        $currentUser = auth()->user();

        // 2. Cek apakah user saat ini memiliki role staff (Guru, Admin, Superadmin, Operator)
        // Sesuaikan nama role di bawah ini dengan yang ada di database Anda
        $isStaff = $currentUser->hasAnyRole(['guru', 'superadmin', 'operator']);

        // 3. Logika Pembatasan:
        // Jika user BUKAN staff, DAN user_id pada data siswa TIDAK SAMA dengan id user yang sedang login, maka tolak aksesnya!
        if (! $isStaff && $student->user_id !== $currentUser->id) {
            abort(403, 'Akses Ditolak! Anda hanya diizinkan untuk melihat profil Anda sendiri.');
        }

        // 4. Jika lolos pengecekan, tampilkan halaman view
        return view('students.show', compact('student'));
    }
}
