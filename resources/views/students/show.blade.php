<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div
                class="flex-shrink-0 h-11 w-11 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center shadow-md shadow-indigo-500/20">
                <i class="fas fa-id-card text-white text-lg"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="font-bold text-lg sm:text-2xl text-slate-800 dark:text-white tracking-tight truncate">
                    Detail Biodata: <span class="text-indigo-600 dark:text-indigo-400">{{ $student->name ?? 'Siswa'
                        }}</span>
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Informasi pokok peserta didik berdasarkan
                    dokumen resmi.</p>
            </div>
            <a href="{{ route('students.edit', $student->id) }}"
                class="inline-flex flex-shrink-0 items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-md transition">
                <i class="fas fa-edit"></i> Edit Data
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- 1. IDENTITAS POKOK -->
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Identitas Pokok</h3>
                </div>
                <div class="p-6 flex flex-col md:flex-row gap-8">
                    <!-- Foto -->
                    <div class="flex-shrink-0 flex flex-col items-center">
                        <div
                            class="w-32 aspect-[2/3] rounded-lg overflow-hidden border-4 border-white dark:border-slate-700 shadow-lg bg-slate-100 dark:bg-slate-800 ring-1 ring-slate-200 dark:ring-slate-600">
                            <img src="{{ !empty($student->student->foto) ? asset('storage/' . $student->student->foto) : 'https://ui-avatars.com/api/?name='.urlencode($student->name).'&background=random' }}"
                                class="object-cover w-full h-full" alt="Foto Siswa">
                        </div>
                    </div>
                    <!-- Data Identitas -->
                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-4 gap-x-6">
                        <x-data-item label="Nama Lengkap" value="{{ $student->student->nama_lengkap ?? '-' }}" />
                        <x-data-item label="Nama Panggilan" value="{{ $student->student->nama_panggilan ?? '-' }}" />
                        <x-data-item label="NISN / NIPD"
                            value="{{ $student->student->nisn ?? '-' }} / {{ $student->student->nipd ?? '-' }}" />
                        <x-data-item label="NIK" value="{{ $student->student->nik ?? '-' }}" />
                        <x-data-item label="No. KK" value="{{ $student->student->no_kk ?? '-' }}" />
                        <x-data-item label="No. Akta Lahir"
                            value="{{ $student->student->no_registrasi_akta_lahir ?? '-' }}" />
                        <x-data-item label="Tempat, Tanggal Lahir"
                            value="{{ $student->student->tempat_lahir ?? '-' }}, {{ optional($student->student->tanggal_lahir)->format('d F Y') ?? '-' }}" />
                        <x-data-item label="Jenis Kelamin"
                            value="{{ ($student->student->jenis_kelamin ?? '') == 'L' ? 'Laki-laki' : (($student->student->jenis_kelamin ?? '') == 'P' ? 'Perempuan' : '-') }}" />
                        <x-data-item label="Agama" value="{{ $student->student->agama ?? '-' }}" />
                        <x-data-item label="Anak Ke / Jml. Saudara"
                            value="{{ $student->student->anak_ke ?? '-' }} dari {{ $student->student->jml_saudara_kandung ?? '-' }} bersaudara" />
                        <x-data-item label="Asal Sekolah" value="{{ $student->student->sekolah_asal ?? '-' }}" />
                        <x-data-item label="Kontak (HP/Email)"
                            value="{{ $student->student->hp ?? '-' }} / {{ $student->student->email ?? '-' }}" />
                    </div>
                </div>
            </div>

            <!-- 2. ALAMAT & DOMISILI -->
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Alamat & Domisili</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-y-4 gap-x-6">
                    <div class="sm:col-span-2 lg:col-span-4">
                        <x-data-item label="Alamat Lengkap"
                            value="{{ $student->student->address->alamat ?? '-' }}, RT {{ $student->student->address->rt ?? '-' }} / RW {{ $student->student->address->rw ?? '-' }}" />
                    </div>
                    <x-data-item label="Kelurahan" value="{{ $student->student->address->kelurahan ?? '-' }}" />
                    <x-data-item label="Kecamatan" value="{{ $student->student->address->kecamatan ?? '-' }}" />
                    <x-data-item label="Kota/Kabupaten" value="{{ $student->student->address->kota ?? '-' }}" />
                    <x-data-item label="Provinsi (Kode Pos)"
                        value="{{ $student->student->address->provinsi ?? '-' }} ({{ $student->student->address->kode_pos ?? '-' }})" />

                    <x-data-item label="Jenis Tinggal" value="{{ $student->student->address->jenis_tinggal ?? '-' }}" />
                    <x-data-item label="Alat Transportasi"
                        value="{{ $student->student->address->alat_transportasi ?? '-' }}" />
                    <x-data-item label="Jarak ke Sekolah"
                        value="{{ $student->student->address->jarak_ke_sekolah_km ?? '-' }}" />
                </div>
            </div>

            <!-- 3. DATA KELUARGA -->
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Data Keluarga (Orang Tua / Wali)</h3>
                </div>
                <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Ayah -->
                    <div
                        class="border border-blue-100 dark:border-blue-900/50 rounded-xl p-4 bg-blue-50/30 dark:bg-blue-900/10">
                        <h4
                            class="font-bold text-blue-700 dark:text-blue-400 mb-3 border-b border-blue-100 dark:border-blue-900/50 pb-2">
                            Data Ayah</h4>
                        <div class="space-y-3">
                            <x-data-item label="Nama Ayah"
                                value="{{ $student->student->family->nama_ayah ?? '-' }} ({{ ($student->student->family->is_ayah_hidup ?? 1) == 1 ? 'Masih Hidup' : 'Meninggal' }})" />
                            <x-data-item label="Pendidikan / Pekerjaan"
                                value="{{ $student->student->family->pendidikan_ayah ?? '-' }} / {{ $student->student->family->pekerjaan_ayah ?? '-' }}" />
                            <x-data-item label="No. HP" value="{{ $student->student->family->hp_ayah ?? '-' }}" />
                        </div>
                    </div>
                    <!-- Ibu -->
                    <div
                        class="border border-pink-100 dark:border-pink-900/50 rounded-xl p-4 bg-pink-50/30 dark:bg-pink-900/10">
                        <h4
                            class="font-bold text-pink-700 dark:text-pink-400 mb-3 border-b border-pink-100 dark:border-pink-900/50 pb-2">
                            Data Ibu</h4>
                        <div class="space-y-3">
                            <x-data-item label="Nama Ibu"
                                value="{{ $student->student->family->nama_ibu ?? '-' }} ({{ ($student->student->family->is_ibu_hidup ?? 1) == 1 ? 'Masih Hidup' : 'Meninggal' }})" />
                            <x-data-item label="Pendidikan / Pekerjaan"
                                value="{{ $student->student->family->pendidikan_ibu ?? '-' }} / {{ $student->student->family->pekerjaan_ibu ?? '-' }}" />
                            <x-data-item label="No. HP" value="{{ $student->student->family->hp_ibu ?? '-' }}" />
                        </div>
                    </div>
                    <!-- Wali (Jika Ada) -->
                    @if(!empty($student->student->family->nama_wali))
                    <div
                        class="lg:col-span-2 border border-amber-100 dark:border-amber-900/50 rounded-xl p-4 bg-amber-50/30 dark:bg-amber-900/10">
                        <h4
                            class="font-bold text-amber-700 dark:text-amber-400 mb-3 border-b border-amber-100 dark:border-amber-900/50 pb-2">
                            Data Wali</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-data-item label="Nama Wali" value="{{ $student->student->family->nama_wali ?? '-' }}" />
                            <x-data-item label="Pekerjaan / No. HP"
                                value="{{ $student->student->family->pekerjaan_wali ?? '-' }} / {{ $student->student->family->hp_wali ?? '-' }}" />
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- 4. KESEHATAN & BANTUAN (Grid 2 Kolom) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Kesehatan -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div
                        class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Kesehatan</h3>
                    </div>
                    <div class="p-6 grid grid-cols-2 gap-4">
                        <x-data-item label="Tinggi / Berat Badan"
                            value="{{ $student->student->health->tinggi_badan ?? '-' }} cm / {{ $student->student->health->berat_badan ?? '-' }} kg" />
                        <x-data-item label="Kebutuhan Khusus"
                            value="{{ $student->student->health->kebutuhan_khusus ?? '-' }}" />
                        <div class="col-span-2">
                            <x-data-item label="Riwayat Penyakit"
                                value="{{ $student->student->health->penyakit ?? 'Tidak ada' }}" />
                        </div>
                    </div>
                </div>

                <!-- Bantuan -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div
                        class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Bantuan Finansial</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center gap-3">
                            <i
                                class="fas {{ !empty($student->student->financial->penerima_kjp) ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-rose-500' }} text-xl"></i>
                            <span class="font-medium text-slate-700 dark:text-slate-300">Penerima KJP (Kartu Jakarta
                                Pintar)</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i
                                class="fas {{ !empty($student->student->financial->penerima_pip) ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-rose-500' }} text-xl"></i>
                            <span class="font-medium text-slate-700 dark:text-slate-300">Penerima PIP (Program Indonesia
                                Pintar)</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i
                                class="fas {{ !empty($student->student->financial->penerima_bantuan_lain) ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-rose-500' }} text-xl"></i>
                            <span class="font-medium text-slate-700 dark:text-slate-300">Penerima Bantuan Lainnya</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>