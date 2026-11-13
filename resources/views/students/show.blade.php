<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div
                class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-md bg-brand-700">
                <i class="fas fa-id-card text-white text-lg"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="font-bold text-lg sm:text-2xl text-slate-800 dark:text-white tracking-tight truncate">
                    Detail Biodata: <span class="text-brand-600 dark:text-brand-400">{{ $student->nama_lengkap ??
                        'Siswa' }}</span>
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Informasi pokok peserta didik berdasarkan
                    dokumen resmi.</p>
            </div>
            @if($student->user)
            <a href="{{ route('students.edit', $student->user->id) }}"
                class="inline-flex flex-shrink-0 items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-sm font-semibold shadow-md transition">
                <i class="fas fa-edit"></i> Edit Data
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- 1. IDENTITAS POKOK -->
            <div
                class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Identitas Pokok</h3>
                </div>
                <div class="p-6 flex flex-col md:flex-row gap-8">
                    <!-- Foto -->
                    <div class="flex-shrink-0 flex flex-col items-center">
                        <div
                            class="w-32 aspect-[2/3] rounded-lg overflow-hidden border-4 border-white dark:border-slate-700 shadow-lg bg-slate-100 dark:bg-slate-800 ring-1 ring-slate-200 dark:ring-slate-600">
                            <img src="{{ !empty($student->foto) ? asset('storage/' . $student->foto) : 'https://ui-avatars.com/api/?name='.urlencode($student->nama_lengkap).'&background=random' }}"
                                class="object-cover w-full h-full" alt="Foto Siswa">
                        </div>
                    </div>
                    <!-- Data Identitas -->
                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-4 gap-x-6">
                        <x-data-item label="Nama Lengkap" value="{{ $student->nama_lengkap ?? '-' }}" />
                        <x-data-item label="Nama Panggilan" value="{{ $student->nama_panggilan ?? '-' }}" />
                        <x-data-item label="NISN / NIPD"
                            value="{{ $student->nisn ?? '-' }} / {{ $student->nipd ?? '-' }}" />
                        <x-data-item label="NIK" value="{{ $student->nik ?? '-' }}" />
                        <x-data-item label="No. KK" value="{{ $student->no_kk ?? '-' }}" />
                        <x-data-item label="No. Akta Lahir" value="{{ $student->no_registrasi_akta_lahir ?? '-' }}" />
                        <x-data-item label="Tempat, Tanggal Lahir"
                            value="{{ $student->tempat_lahir ?? '-' }}, {{ optional($student->tanggal_lahir)->format('d F Y') ?? '-' }}" />
                        <x-data-item label="Jenis Kelamin"
                            value="{{ ($student->jenis_kelamin ?? '') == 'L' ? 'Laki-laki' : (($student->jenis_kelamin ?? '') == 'P' ? 'Perempuan' : '-') }}" />
                        <x-data-item label="Agama" value="{{ $student->agama ?? '-' }}" />
                        <x-data-item label="Anak Ke / Jml. Saudara"
                            value="{{ $student->anak_ke ?? '-' }} dari {{ $student->jml_saudara_kandung ?? '-' }} bersaudara" />
                        <x-data-item label="Asal Sekolah" value="{{ $student->sekolah_asal ?? '-' }}" />
                        <x-data-item label="Kontak (HP/Email)"
                            value="{{ $student->hp ?? '-' }} / {{ $student->email ?? '-' }}" />
                    </div>
                </div>
            </div>

            <section class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/70 sm:px-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Ekstrakurikuler</h3>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Pilih semua kegiatan yang diikuti siswa.</p>
                </div>

                <div class="p-5 sm:p-6">
                    @if(session('success'))
                    <p role="status" class="mb-4 rounded-md border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">
                        {{ session('success') }}
                    </p>
                    @endif

                    @if($errors->any())
                    <div role="alert" class="mb-4 rounded-md border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-900 dark:border-rose-800 dark:bg-rose-900/30 dark:text-rose-200">
                        <ul class="list-inside list-disc">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if($canManageExtracurriculars && $extracurricularOptions->isNotEmpty())
                    <form action="{{ route('students.extracurriculars.update', $student) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @php
                        $selectedExtracurriculars = old('extracurricular_ids', $student->extracurriculars->modelKeys());
                        @endphp

                        <fieldset>
                            <legend class="sr-only">Ekskul yang diikuti</legend>
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($extracurricularOptions as $extracurricular)
                                <label for="extracurricular-{{ $extracurricular->id }}"
                                    class="flex min-h-11 cursor-pointer items-center gap-3 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 transition hover:bg-brand-50 active:bg-brand-100 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-700 dark:active:bg-slate-600">
                                    <input id="extracurricular-{{ $extracurricular->id }}" type="checkbox"
                                        name="extracurricular_ids[]" value="{{ $extracurricular->id }}"
                                        @checked(in_array($extracurricular->id, $selectedExtracurriculars))
                                        class="h-4 w-4 cursor-pointer rounded border-slate-400 text-brand-700 focus:ring-brand-600 dark:border-slate-500">
                                    <span>{{ $extracurricular->nama_ekskul }}</span>
                                </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <button type="submit"
                            class="mt-5 inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-800 active:bg-brand-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-700">
                            Simpan ekskul
                        </button>
                    </form>
                    @elseif($canManageExtracurriculars)
                    <p class="rounded-md bg-slate-50 p-4 text-sm text-slate-700 dark:bg-slate-900/50 dark:text-slate-300">
                        Belum ada pilihan ekskul untuk sekolah ini. Hubungi admin sekolah untuk menambahkan daftar ekskul.
                    </p>
                    @elseif($student->extracurriculars->isNotEmpty())
                    <ul class="flex flex-wrap gap-2">
                        @foreach($student->extracurriculars as $extracurricular)
                        <li class="rounded-md bg-brand-50 px-3 py-2 text-sm font-medium text-brand-900 dark:bg-brand-900/30 dark:text-brand-100">
                            {{ $extracurricular->nama_ekskul }}
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-sm text-slate-600 dark:text-slate-300">Belum ada ekskul yang dipilih.</p>
                    @endif
                </div>
            </section>

            <!-- 2. ALAMAT & DOMISILI -->
            <div
                class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Alamat & Domisili</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-y-4 gap-x-6">
                    <div class="sm:col-span-2 lg:col-span-4">
                        <x-data-item label="Alamat Lengkap"
                            value="{{ $student->address->alamat ?? '-' }}, RT {{ $student->address->rt ?? '-' }} / RW {{ $student->address->rw ?? '-' }}" />
                    </div>
                    <x-data-item label="Kelurahan" value="{{ $student->address->kelurahan ?? '-' }}" />
                    <x-data-item label="Kecamatan" value="{{ $student->address->kecamatan ?? '-' }}" />
                    <x-data-item label="Kota/Kabupaten" value="{{ $student->address->kota ?? '-' }}" />
                    <x-data-item label="Provinsi (Kode Pos)"
                        value="{{ $student->address->provinsi ?? '-' }} ({{ $student->address->kode_pos ?? '-' }})" />

                    <x-data-item label="Jenis Tinggal" value="{{ $student->address->jenis_tinggal ?? '-' }}" />
                    <x-data-item label="Alat Transportasi" value="{{ $student->address->alat_transportasi ?? '-' }}" />
                    <x-data-item label="Jarak ke Sekolah" value="{{ $student->address->jarak_ke_sekolah_km ?? '-' }}" />
                </div>
            </div>

            <!-- 3. DATA KELUARGA -->
            <div
                class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Data Keluarga (Orang Tua / Wali)</h3>
                </div>
                <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Ayah -->
                    <div
                        class="border border-blue-100 dark:border-blue-900/50 rounded-md p-4 bg-blue-50/30 dark:bg-blue-900/10">
                        <h4
                            class="font-bold text-blue-700 dark:text-blue-400 mb-3 border-b border-blue-100 dark:border-blue-900/50 pb-2">
                            Data Ayah</h4>
                        <div class="space-y-3">
                            <x-data-item label="Nama Ayah"
                                value="{{ $student->family->nama_ayah ?? '-' }} ({{ ($student->family->is_ayah_hidup ?? 1) == 1 ? 'Masih Hidup' : 'Meninggal' }})" />
                            <x-data-item label="Pendidikan / Pekerjaan"
                                value="{{ $student->family->pendidikan_ayah ?? '-' }} / {{ $student->family->pekerjaan_ayah ?? '-' }}" />
                            <x-data-item label="No. HP" value="{{ $student->family->hp_ayah ?? '-' }}" />
                        </div>
                    </div>
                    <!-- Ibu -->
                    <div
                        class="border border-pink-100 dark:border-pink-900/50 rounded-md p-4 bg-pink-50/30 dark:bg-pink-900/10">
                        <h4
                            class="font-bold text-pink-700 dark:text-pink-400 mb-3 border-b border-pink-100 dark:border-pink-900/50 pb-2">
                            Data Ibu</h4>
                        <div class="space-y-3">
                            <x-data-item label="Nama Ibu"
                                value="{{ $student->family->nama_ibu ?? '-' }} ({{ ($student->family->is_ibu_hidup ?? 1) == 1 ? 'Masih Hidup' : 'Meninggal' }})" />
                            <x-data-item label="Pendidikan / Pekerjaan"
                                value="{{ $student->family->pendidikan_ibu ?? '-' }} / {{ $student->family->pekerjaan_ibu ?? '-' }}" />
                            <x-data-item label="No. HP" value="{{ $student->family->hp_ibu ?? '-' }}" />
                        </div>
                    </div>
                    <!-- Wali (Jika Ada) -->
                    @if(!empty($student->family->nama_wali))
                    <div
                        class="lg:col-span-2 border border-amber-100 dark:border-amber-900/50 rounded-md p-4 bg-amber-50/30 dark:bg-amber-900/10">
                        <h4
                            class="font-bold text-amber-700 dark:text-amber-400 mb-3 border-b border-amber-100 dark:border-amber-900/50 pb-2">
                            Data Wali</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-data-item label="Nama Wali" value="{{ $student->family->nama_wali ?? '-' }}" />
                            <x-data-item label="Pekerjaan / No. HP"
                                value="{{ $student->family->pekerjaan_wali ?? '-' }} / {{ $student->family->hp_wali ?? '-' }}" />
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- 4. KESEHATAN & BANTUAN (Grid 2 Kolom) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Kesehatan -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div
                        class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Kesehatan</h3>
                    </div>
                    <div class="p-6 grid grid-cols-2 gap-4">
                        <x-data-item label="Tinggi / Berat Badan"
                            value="{{ $student->health->tinggi_badan ?? '-' }} cm / {{ $student->health->berat_badan ?? '-' }} kg" />
                        <x-data-item label="Kebutuhan Khusus" value="{{ $student->health->kebutuhan_khusus ?? '-' }}" />
                        <div class="col-span-2">
                            <x-data-item label="Riwayat Penyakit"
                                value="{{ $student->health->penyakit ?? 'Tidak ada' }}" />
                        </div>
                    </div>
                </div>

                <!-- Bantuan -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div
                        class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Bantuan Finansial</h3>
                    </div>
                    <div class="p-6 space-y-4">

                        @if(!empty($student->financial->penerima_kjp))
                        <div class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                            <span class="font-medium text-slate-700 dark:text-slate-300">Penerima KJP (Kartu Jakarta
                                Pintar)</span>
                        </div>
                        @endif

                        @if(!empty($student->financial->penerima_pip))
                        <div class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                            <span class="font-medium text-slate-700 dark:text-slate-300">Penerima PIP (Program Indonesia
                                Pintar)</span>
                        </div>
                        @endif

                        @if(!empty($student->financial->penerima_bantuan_lain))
                        <div class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                            <span class="font-medium text-slate-700 dark:text-slate-300">Penerima Bantuan Lainnya</span>
                        </div>
                        @endif

                        {{-- Pesan default jika siswa tidak menerima bantuan apa pun --}}
                        @if(empty($student->financial->penerima_kjp) && empty($student->financial->penerima_pip) &&
                        empty($student->financial->penerima_bantuan_lain))
                        <div class="text-sm text-slate-500 dark:text-slate-400 italic">
                            Tidak ada catatan.
                        </div>
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>