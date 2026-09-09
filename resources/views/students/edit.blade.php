<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 sm:gap-4">
            <a href="{{ route('dashboard') }}"
                class="sm:hidden flex-shrink-0 inline-flex items-center justify-center h-10 w-10 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 active:scale-95 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>

            <div
                class="hidden sm:flex flex-shrink-0 h-11 w-11 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 items-center justify-center shadow-md shadow-indigo-500/20">
                <i class="fas fa-user-edit text-white text-lg"></i>
            </div>

            <div class="min-w-0 flex-1">
                <h2 class="font-bold text-lg sm:text-2xl text-slate-800 dark:text-white tracking-tight truncate">
                    Edit Profil: <span class="text-indigo-600 dark:text-indigo-400">{{ $student->name ?? 'Siswa'
                        }}</span>
                </h2>
                <p class="hidden sm:block text-sm text-slate-500 dark:text-slate-400 mt-1">Lengkapi data pokok peserta
                    didik sesuai dengan dokumen resmi.</p>
            </div>

            <a href="{{ route('dashboard') }}"
                class="hidden sm:inline-flex flex-shrink-0 items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    {{-- ALPINE.JS CONTAINER: Navigasi Tab & Fetch API Wilayah --}}
    <div class="py-4 sm:py-8 pb-28 lg:pb-8" x-data="studentForm()" x-init="initWilayah()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- PERBAIKAN: Menambahkan enctype="multipart/form-data" --}}
            <form id="studentForm" action="{{ route('students.update', $student->id ?? 0) }}" method="POST"
                enctype="multipart/form-data">
                @csrf @method('PUT')

                {{-- NOTIFIKASI ERROR GLOBAL --}}
                @if ($errors->any())
                <div
                    class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-xl shadow-sm dark:bg-rose-900/30 dark:border-rose-600">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-rose-500 dark:text-rose-400" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-rose-800 dark:text-rose-300">Gagal menyimpan! Terdapat {{
                                $errors->count() }} kesalahan:</h3>
                            <ul class="mt-1 text-xs text-rose-700 dark:text-rose-400 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                {{-- PROGRESS BAR --}}
                <div class="mb-6 sm:mb-8 px-4 sm:px-0">
                    <div class="flex justify-between items-end mb-2 gap-2">
                        <span class="text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-300 truncate">
                            <span x-text="progressText"></span>:
                            <span x-show="tab === 'identitas'">Identitas</span>
                            <span x-show="tab === 'alamat'">Data Domisili</span>
                            <span x-show="tab === 'keluarga'">Data Keluarga</span>
                            <span x-show="tab === 'finansial'">Data Finansial</span>
                            <span x-show="tab === 'kesehatan'">Data Kesehatan</span>
                        </span>
                        <span
                            class="flex-shrink-0 text-xs font-extrabold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 px-2 py-1 rounded-md"
                            x-text="progress"></span>
                    </div>
                    <div
                        class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 sm:h-2.5 overflow-hidden shadow-inner">
                        <div class="bg-gradient-to-r from-indigo-500 via-indigo-600 to-violet-600 h-full rounded-full transition-all duration-500 ease-out"
                            :style="'width: ' + progress"></div>
                    </div>
                    {{-- Step dots (desktop) --}}
                    <div class="hidden sm:flex justify-between mt-2">
                        <template x-for="(t, i) in tabsList" :key="t">
                            <div class="flex items-center gap-1.5"
                                :class="i <= currentIndex ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-300 dark:text-slate-600'">
                                <span class="h-1.5 w-1.5 rounded-full"
                                    :class="i <= currentIndex ? 'bg-indigo-600 dark:bg-indigo-400' : 'bg-slate-300 dark:bg-slate-600'"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">
                    {{-- SIDEBAR TABS --}}
                    <div class="w-full lg:w-72 flex-shrink-0 px-0 sm:px-0">
                        <div
                            class="sticky top-0 lg:top-6 z-20 bg-white/95 dark:bg-slate-800/95 backdrop-blur supports-[backdrop-filter]:bg-white/80 dark:supports-[backdrop-filter]:bg-slate-800/80 rounded-none sm:rounded-2xl shadow-sm sm:border border-y sm:border-y-0 border-slate-200 dark:border-slate-700 p-2 sm:p-3">
                            <nav
                                class="flex overflow-x-auto snap-x snap-mandatory lg:flex-col gap-2 pb-1 lg:pb-0 px-4 sm:px-0 scrollbar-hide">
                                <button type="button" @click="tab = 'identitas'"
                                    :class="tab === 'identitas' ? 'bg-gradient-to-br from-indigo-600 to-indigo-500 text-white shadow-md shadow-indigo-500/30 font-bold' : 'bg-slate-50 dark:bg-slate-900/40 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50 font-medium'"
                                    class="snap-start flex-shrink-0 lg:w-full flex flex-col lg:flex-row items-center justify-center lg:justify-start gap-1 lg:gap-3 min-w-[76px] lg:min-w-0 px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl text-[11px] sm:text-sm transition-all duration-200">
                                    <i class="fas fa-user-circle text-base lg:w-5"></i>
                                    <span class="leading-tight text-center lg:text-left">Identitas<span
                                            class="hidden lg:inline"> Pokok</span></span>
                                </button>
                                <button type="button" @click="tab = 'alamat'"
                                    :class="tab === 'alamat' ? 'bg-gradient-to-br from-indigo-600 to-indigo-500 text-white shadow-md shadow-indigo-500/30 font-bold' : 'bg-slate-50 dark:bg-slate-900/40 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50 font-medium'"
                                    class="snap-start flex-shrink-0 lg:w-full flex flex-col lg:flex-row items-center justify-center lg:justify-start gap-1 lg:gap-3 min-w-[76px] lg:min-w-0 px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl text-[11px] sm:text-sm transition-all duration-200">
                                    <i class="fas fa-map-marker-alt text-base lg:w-5"></i>
                                    <span class="leading-tight text-center lg:text-left">Alamat<span
                                            class="hidden lg:inline"> & Domisili</span></span>
                                </button>
                                <button type="button" @click="tab = 'keluarga'"
                                    :class="tab === 'keluarga' ? 'bg-gradient-to-br from-indigo-600 to-indigo-500 text-white shadow-md shadow-indigo-500/30 font-bold' : 'bg-slate-50 dark:bg-slate-900/40 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50 font-medium'"
                                    class="snap-start flex-shrink-0 lg:w-full flex flex-col lg:flex-row items-center justify-center lg:justify-start gap-1 lg:gap-3 min-w-[76px] lg:min-w-0 px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl text-[11px] sm:text-sm transition-all duration-200">
                                    <i class="fas fa-users text-base lg:w-5"></i>
                                    <span class="leading-tight text-center lg:text-left">Keluarga<span
                                            class="hidden lg:inline"> Data</span></span>
                                </button>
                                <button type="button" @click="tab = 'finansial'"
                                    :class="tab === 'finansial' ? 'bg-gradient-to-br from-indigo-600 to-indigo-500 text-white shadow-md shadow-indigo-500/30 font-bold' : 'bg-slate-50 dark:bg-slate-900/40 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50 font-medium'"
                                    class="snap-start flex-shrink-0 lg:w-full flex flex-col lg:flex-row items-center justify-center lg:justify-start gap-1 lg:gap-3 min-w-[76px] lg:min-w-0 px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl text-[11px] sm:text-sm transition-all duration-200">
                                    <i class="fas fa-wallet text-base lg:w-5"></i>
                                    <span class="leading-tight text-center lg:text-left">Finansial<span
                                            class="hidden lg:inline"> & Bantuan</span></span>
                                </button>
                                <button type="button" @click="tab = 'kesehatan'"
                                    :class="tab === 'kesehatan' ? 'bg-gradient-to-br from-indigo-600 to-indigo-500 text-white shadow-md shadow-indigo-500/30 font-bold' : 'bg-slate-50 dark:bg-slate-900/40 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50 font-medium'"
                                    class="snap-start flex-shrink-0 lg:w-full flex flex-col lg:flex-row items-center justify-center lg:justify-start gap-1 lg:gap-3 min-w-[76px] lg:min-w-0 px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl text-[11px] sm:text-sm transition-all duration-200">
                                    <i class="fas fa-heartbeat text-base lg:w-5"></i>
                                    <span class="leading-tight text-center lg:text-left">Kesehatan</span>
                                </button>
                            </nav>

                            <div
                                class="pt-3 lg:pt-4 border-t border-slate-100 dark:border-slate-700 mt-2 lg:mt-4 hidden lg:block">
                                <button type="submit"
                                    class="w-full flex justify-center items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white py-3 px-4 rounded-xl text-sm font-bold shadow-lg shadow-emerald-500/30 transition-all focus:ring-4 focus:ring-emerald-500/20">
                                    <i class="fas fa-save"></i> Simpan Cepat
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- MAIN CONTENT AREA --}}
                    <div class="flex-1 min-w-0 px-4 sm:px-0">
                        <div
                            class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col">

                            <div class="p-6 sm:p-8 flex-1">

                                {{-- 1. FORM IDENTITAS --}}
                                <div x-show="tab === 'identitas'" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                                    <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-700">
                                        <h3 class="text-xl font-bold text-slate-800 dark:text-white">Identitas Pokok
                                            Siswa</h3>
                                    </div>

                                    {{-- PERBAIKAN: Input Upload Foto 4x6 dengan Live Preview --}}
                                    <div x-data="{ photoName: null, photoPreview: null }"
                                        class="mb-8 flex flex-col sm:flex-row gap-4 sm:gap-6 items-center sm:items-start p-4 sm:p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700">
                                        <!-- Area Preview Foto 4x6 -->
                                        <button type="button" @click="$refs.foto.click()"
                                            class="relative w-28 sm:w-32 aspect-[2/3] rounded-lg overflow-hidden border-4 border-white dark:border-slate-700 shadow-lg bg-slate-100 dark:bg-slate-800 flex-shrink-0 ring-1 ring-slate-200 dark:ring-slate-600 group">
                                            <!-- Foto Lama -->
                                            <img x-show="!photoPreview"
                                                src="{{ !empty($student->student->foto) ? asset('storage/' . $student->student->foto) : 'https://ui-avatars.com/api/?name='.urlencode($student->name).'&background=random' }}"
                                                class="object-cover w-full h-full transition group-hover:brightness-90"
                                                alt="Current Photo">
                                            <!-- Foto Baru (Preview) -->
                                            <img x-show="photoPreview" :src="photoPreview"
                                                class="object-cover w-full h-full transition group-hover:brightness-90"
                                                style="display: none;" alt="Preview Photo">
                                            <!-- Label ukuran -->
                                            <span
                                                class="absolute top-1 left-1 px-1.5 py-0.5 rounded bg-black/50 text-white text-[9px] font-bold tracking-wide">4x6</span>
                                            <!-- Badge Kamera -->
                                            <span
                                                class="absolute bottom-1.5 right-1.5 h-7 w-7 sm:h-8 sm:w-8 flex items-center justify-center rounded-full bg-indigo-600 text-white border-2 border-white dark:border-slate-800 shadow-md group-active:scale-90 transition">
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </span>
                                        </button>

                                        <!-- Area Input File -->
                                        <div class="flex-1 text-center sm:text-left w-full">
                                            <label
                                                class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Foto
                                                Profil Siswa <span
                                                    class="font-normal text-slate-400">(4x6)</span></label>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Gunakan foto
                                                dengan rasio 4x6 (potret, latar bebas), format JPG/PNG/WebP maks. 5MB.
                                            </p>

                                            <!-- Input file tersembunyi -->
                                            <input type="file" name="foto" id="foto" class="hidden"
                                                accept="image/jpeg, image/png, image/webp" @change="
                                                    photoName = $refs.foto.files[0].name;
                                                    const reader = new FileReader();
                                                    reader.onload = (e) => { photoPreview = e.target.result; };
                                                    reader.readAsDataURL($refs.foto.files[0]);
                                                " x-ref="foto">

                                            <!-- Tombol Trigger -->
                                            <button type="button"
                                                class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 active:scale-[0.98] transition"
                                                @click="$refs.foto.click()">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <span x-text="photoName ? photoName : 'Pilih Foto'"
                                                    class="truncate max-w-[10rem]"></span>
                                            </button>

                                            @error('foto') <p class="mt-2 text-xs font-semibold text-rose-500">{{
                                                $message }}</p> @enderror
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                                        <div class="sm:col-span-2">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama
                                                Lengkap <span class="text-rose-500">*</span></label>
                                            <input type="text" name="nama_lengkap"
                                                value="{{ old('nama_lengkap', $student->student->nama_lengkap ?? '') }}"
                                                class="block w-full rounded-lg shadow-sm sm:text-sm dark:bg-slate-900 dark:text-white transition-colors {{ $errors->has('nama_lengkap') ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-600' }}">
                                            @error('nama_lengkap') <p class="mt-1 text-xs font-semibold text-rose-500">
                                                {{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama
                                                Panggilan</label>
                                            <input type="text" name="nama_panggilan"
                                                value="{{ old('nama_panggilan', $student->student->nama_panggilan ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">NISN</label>
                                            <input type="text" name="nisn"
                                                value="{{ old('nisn', $student->student->nisn ?? '') }}"
                                                class="block w-full rounded-lg shadow-sm sm:text-sm dark:bg-slate-900 dark:text-white transition-colors {{ $errors->has('nisn') ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-600' }}"
                                                disabled>
                                            @error('nisn') <p class="mt-1 text-xs font-semibold text-rose-500">{{
                                                $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">NIPD</label>
                                            <input type="text" name="nipd"
                                                value="{{ old('nipd', $student->student->nipd ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white"
                                                disabled>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode
                                                Kelas</label>
                                            <input type="text" name="class_code"
                                                value="{{ old('class_code', $student->student->class_code ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white"
                                                disabled>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">NIK
                                                Siswa</label>
                                            <input type="text" name="nik"
                                                value="{{ old('nik', $student->student->nik ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">No.
                                                KK</label>
                                            <input type="text" name="no_kk"
                                                value="{{ old('no_kk', $student->student->no_kk ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">No.
                                                Akta Lahir</label>
                                            <input type="text" name="no_registrasi_akta_lahir"
                                                value="{{ old('no_registrasi_akta_lahir', $student->student->no_registrasi_akta_lahir ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Tempat
                                                Lahir</label>
                                            <input type="text" name="tempat_lahir"
                                                value="{{ old('tempat_lahir', $student->student->tempat_lahir ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal
                                                Lahir</label>
                                            <input type="date" name="tanggal_lahir"
                                                value="{{ old('tanggal_lahir', optional($student->student->tanggal_lahir)->format('Y-m-d') ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis
                                                Kelamin <span class="text-rose-500">*</span></label>
                                            <select name="jenis_kelamin"
                                                class="block w-full rounded-lg shadow-sm sm:text-sm dark:bg-slate-900 dark:text-white transition-colors {{ $errors->has('jenis_kelamin') ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-600' }}">
                                                <option value="">-- Pilih --</option>
                                                <option value="L" {{ old('jenis_kelamin', $student->
                                                    student->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki
                                                </option>
                                                <option value="P" {{ old('jenis_kelamin', $student->
                                                    student->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan
                                                </option>
                                            </select>
                                            @error('jenis_kelamin') <p class="mt-1 text-xs font-semibold text-rose-500">
                                                {{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Agama</label>
                                            <select name="agama"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                <option value="">-- Pilih --</option>
                                                @php $agamaLama = old('agama', $student->student->agama ?? ''); @endphp
                                                <option value="Islam" {{ $agamaLama=='Islam' ? 'selected' : '' }}>
                                                    Islam</option>
                                                <option value="Kristen" {{ $agamaLama=='Kristen' ? 'selected' : '' }}>
                                                    Kristen</option>
                                                <option value="Katholik" {{ $agamaLama=='Katholik' ? 'selected' : '' }}>
                                                    Katholik</option>
                                                <option value="Hindu" {{ $agamaLama=='Hindu' ? 'selected' : '' }}>
                                                    Hindu</option>
                                                <option value="Buddha" {{ $agamaLama=='Buddha' ? 'selected' : '' }}>
                                                    Buddha</option>
                                                <option value="Khonghucu" {{ $agamaLama=='Khonghucu' ? 'selected' : ''
                                                    }}>
                                                    Khonghucu</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Hobi</label>
                                            <input type="text" name="hobi"
                                                value="{{ old('hobi', $student->student->hobi ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Cita-cita</label>
                                            <input type="text" name="cita_cita"
                                                value="{{ old('cita_cita', $student->student->cita_cita ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                        <div class="sm:col-span-3">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Prestasi</label>
                                            <textarea name="prestasi" rows="2"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">{{ old('prestasi', $student->student->prestasi ?? '') }}</textarea>
                                        </div>
                                    </div>

                                    <h4
                                        class="font-bold text-indigo-700 dark:text-indigo-400 mb-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                                        Kontak & Akademik Lainnya</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">No.
                                                HP</label>
                                            <input type="text" name="hp"
                                                value="{{ old('hp', $student->student->hp ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Telepon
                                                Rumah</label>
                                            <input type="text" name="telepon"
                                                value="{{ old('telepon', $student->student->telepon ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Email
                                                Pribadi</label>
                                            <input type="email" name="email"
                                                value="{{ old('email', $student->student->email ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Asal
                                                Sekolah</label>
                                            <input type="text" name="sekolah_asal"
                                                value="{{ old('sekolah_asal', $student->student->sekolah_asal ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>

                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Anak
                                                Ke-</label>
                                            <input type="number" name="anak_ke"
                                                value="{{ old('anak_ke', $student->student->anak_ke ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Jml.
                                                Saudara Kandung</label>
                                            <input type="number" name="jml_saudara_kandung"
                                                value="{{ old('jml_saudara_kandung', $student->student->jml_saudara_kandung ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                    </div>
                                </div>

                                {{-- 2. FORM ALAMAT (Terintegrasi API) --}}
                                <div x-show="tab === 'alamat'" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                                    <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-700">
                                        <h3 class="text-xl font-bold text-slate-800 dark:text-white">Alamat & Domisili
                                            Siswa</h3>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-6 gap-y-6 gap-x-4">
                                        <div class="sm:col-span-6">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat
                                                Jalan</label>
                                            <textarea name="alamat" rows="2"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">{{ old('alamat', $student->student->address->alamat ?? '') }}</textarea>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">RT</label>
                                            <input type="text" name="rt"
                                                value="{{ old('rt', $student->student->address->rt ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">RW</label>
                                            <input type="text" name="rw"
                                                value="{{ old('rw', $student->student->address->rw ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode
                                                Pos</label>
                                            <input type="text" name="kode_pos" id="kode_pos_input"
                                                value="{{ old('kode_pos', $student->student->address->kode_pos ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                        </div>

                                        {{-- DROPDOWN API --}}
                                        <div class="sm:col-span-2">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Provinsi</label>
                                            <select x-model="selectedProvId" @change="fetchCities(selectedProvId)"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                                <option value="">-- Pilih Provinsi --</option>
                                                <template x-for="prov in provinces" :key="prov.id">
                                                    <option :value="prov.id" x-text="prov.name"></option>
                                                </template>
                                            </select>
                                            <input type="hidden" name="provinsi" :value="provName">
                                        </div>

                                        <div class="sm:col-span-2">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Kota/Kabupaten</label>
                                            <select x-model="selectedCityId" @change="fetchDistricts(selectedCityId)"
                                                :disabled="cities.length === 0"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors disabled:opacity-50 disabled:bg-slate-100 dark:disabled:bg-slate-800">
                                                <option value="">-- Pilih Kota/Kabupaten --</option>
                                                <template x-for="city in cities" :key="city.id">
                                                    <option :value="city.id" x-text="city.name"></option>
                                                </template>
                                            </select>
                                            <input type="hidden" name="kota" :value="cityName">
                                        </div>

                                        <div class="sm:col-span-2">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Kecamatan</label>
                                            <select x-model="selectedDistId" @change="fetchVillages(selectedDistId)"
                                                :disabled="districts.length === 0"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors disabled:opacity-50 disabled:bg-slate-100 dark:disabled:bg-slate-800">
                                                <option value="">-- Pilih Kecamatan --</option>
                                                <template x-for="dist in districts" :key="dist.id">
                                                    <option :value="dist.id" x-text="dist.name"></option>
                                                </template>
                                            </select>
                                            <input type="hidden" name="kecamatan" :value="distName">
                                        </div>

                                        <div class="sm:col-span-6">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Kelurahan/Desa</label>
                                            <select x-model="selectedVillId" @change="setVillage()"
                                                :disabled="villages.length === 0"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors disabled:opacity-50 disabled:bg-slate-100 dark:disabled:bg-slate-800">
                                                <option value="">-- Pilih Kelurahan/Desa --</option>
                                                <template x-for="vill in villages" :key="vill.id">
                                                    <option :value="vill.id" x-text="vill.name"></option>
                                                </template>
                                            </select>
                                            <input type="hidden" name="kelurahan" :value="villName">
                                        </div>

                                        <div
                                            class="sm:col-span-6 mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                            <div>
                                                <label
                                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis
                                                    Tinggal</label>
                                                <select name="jenis_tinggal"
                                                    class="block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                                    <option value="">-- Pilih --</option>
                                                    @php $jenisTinggalLama = old('jenis_tinggal',
                                                    $student->student->address->jenis_tinggal ?? ''); @endphp
                                                    <option value="Bersama Orang Tua" {{
                                                        $jenisTinggalLama=='Bersama Orang Tua' ? 'selected' : '' }}>
                                                        Bersama Orang Tua</option>
                                                    <option value="Bersama Wali" {{ $jenisTinggalLama=='Bersama Wali'
                                                        ? 'selected' : '' }}>
                                                        Bersama Wali</option>
                                                    <option value="Kost" {{ $jenisTinggalLama=='Kost' ? 'selected' : ''
                                                        }}>
                                                        Kost</option>
                                                    <option value="Asrama" {{ $jenisTinggalLama=='Asrama' ? 'selected'
                                                        : '' }}>
                                                        Asrama</option>
                                                    <option value="Panti Asuhan" {{ $jenisTinggalLama=='Panti Asuhan'
                                                        ? 'selected' : '' }}>
                                                        Panti Asuhan</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Alat
                                                    Transportasi</label>
                                                <select name="alat_transportasi"
                                                    class="block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                                    <option value="">-- Pilih --</option>
                                                    @php $alatTransportasiLama = old('alat_transportasi',
                                                    $student->student->address->alat_transportasi ?? ''); @endphp
                                                    <option value="Jalan Kaki" {{ $alatTransportasiLama=='Jalan Kaki'
                                                        ? 'selected' : '' }}>Jalan Kaki</option>
                                                    <option value="Sepeda" {{ $alatTransportasiLama=='Sepeda'
                                                        ? 'selected' : '' }}>Sepeda</option>
                                                    <option value="Motor" {{ $alatTransportasiLama=='Motor' ? 'selected'
                                                        : '' }}>Motor</option>
                                                    <option value="Mobil" {{ $alatTransportasiLama=='Mobil' ? 'selected'
                                                        : '' }}>Mobil</option>
                                                    <option value="Angkutan Umum" {{
                                                        $alatTransportasiLama=='Angkutan Umum' ? 'selected' : '' }}>
                                                        Angkutan Umum</option>
                                                    <option value="Antar Jemput" {{
                                                        $alatTransportasiLama=='Antar Jemput' ? 'selected' : '' }}>Antar
                                                        Jemput</option>
                                                    <option value="Lainnya" {{ $alatTransportasiLama=='Lainnya'
                                                        ? 'selected' : '' }}>Lainnya</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Jarak
                                                    ke Sekolah (KM)</label>
                                                <select name="jarak_ke_sekolah_km"
                                                    class="block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                                    <option value="">-- Pilih --</option>
                                                    @php $jarakLama = old('jarak_ke_sekolah_km',
                                                    $student->student->address->jarak_ke_sekolah_km ?? ''); @endphp
                                                    <option value="0-1 KM" {{ $jarakLama=='0-1 KM' ? 'selected' : '' }}>
                                                        0-1 KM</option>
                                                    <option value="1-3 KM" {{ $jarakLama=='1-3 KM' ? 'selected' : '' }}>
                                                        1-3 KM</option>
                                                    <option value="3-5 KM" {{ $jarakLama=='3-5 KM' ? 'selected' : '' }}>
                                                        3-5 KM</option>
                                                    <option value="5-10 KM" {{ $jarakLama=='5-10 KM' ? 'selected' : ''
                                                        }}>5-10 KM</option>
                                                    <option value="Lebih dari 10 KM" {{ $jarakLama=='Lebih dari 10 KM'
                                                        ? 'selected' : '' }}>Lebih dari 10 KM</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 3. FORM KELUARGA --}}
                                <div x-show="tab === 'keluarga'" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                                    @php
                                    $agamaOptions = ['Islam', 'Kristen', 'Katholik', 'Hindu', 'Buddha', 'Khonghucu'];
                                    $agamaAyahLama = old('agama_ayah', $student->student->family->agama_ayah ?? '');
                                    $agamaIbuLama = old('agama_ibu', $student->student->family->agama_ibu ?? '');
                                    $agamaWaliLama = old('agama_wali', $student->student->family->agama_wali ?? '');
                                    $pendidikanOptions = ['Tidak tamat', 'SD/MI', 'SMP/MTs', 'SMA/SMK/MA', 'S1', 'S2',
                                    'S3'];
                                    $pendidikanAyahLama = old('pendidikan_ayah',
                                    $student->student->family->pendidikan_ayah ?? '');
                                    $pendidikanIbuLama = old('pendidikan_ibu', $student->student->family->pendidikan_ibu
                                    ?? '');
                                    $pendidikanWaliLama = old('pendidikan_wali',
                                    $student->student->family->pendidikan_wali ?? '');
                                    @endphp
                                    <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-700">
                                        <h3 class="text-xl font-bold text-slate-800 dark:text-white">Data Orang Tua /
                                            Wali</h3>
                                    </div>
                                    <div class="space-y-8">

                                        {{-- Ayah --}}
                                        <div
                                            class="relative rounded-2xl border border-blue-100 bg-gradient-to-r from-blue-50/50 to-transparent p-5 sm:p-6 dark:border-blue-900/50 dark:from-blue-900/10">
                                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-400 rounded-l-2xl">
                                            </div>
                                            <div
                                                class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-2">
                                                <h4 class="font-bold text-blue-800 dark:text-blue-400">Data Ayah Kandung
                                                </h4>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-slate-500">Status:</span>
                                                    <select name="is_ayah_hidup"
                                                        class="rounded-md border-slate-300 shadow-sm text-xs py-1.5 pl-3 pr-8 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-800 dark:border-slate-600 dark:text-white font-semibold">
                                                        <option value="1" {{ old('is_ayah_hidup', $student->
                                                            student->family->is_ayah_hidup ?? 1) == 1 ? 'selected' : ''
                                                            }}>Masih Hidup</option>
                                                        <option value="0" {{ old('is_ayah_hidup', $student->
                                                            student->family->is_ayah_hidup ?? 1) == 0 ? 'selected' : ''
                                                            }}>Meninggal</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                                <div class="sm:col-span-2">
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Nama
                                                        Ayah</label>
                                                    <input type="text" name="nama_ayah"
                                                        value="{{ old('nama_ayah', $student->student->family->nama_ayah ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Tempat
                                                        Lahir</label>
                                                    <input type="text" name="tempat_lahir_ayah"
                                                        value="{{ old('tempat_lahir_ayah', $student->student->family->tempat_lahir_ayah ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Tanggal
                                                        Lahir</label>
                                                    <input type="date" name="tanggal_lahir_ayah"
                                                        value="{{ old('tanggal_lahir_ayah', $student->student?->family?->tanggal_lahir_ayah?->format('Y-m-d') ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Agama</label>
                                                    <select name="agama_ayah"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                        <option value="">-- Pilih --</option>
                                                        @foreach ($agamaOptions as $opt)
                                                        <option value="{{ $opt }}" {{ $agamaAyahLama==$opt ? 'selected'
                                                            : '' }}>{{ $opt }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Pendidikan</label>
                                                    <select name="pendidikan_ayah"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                        <option value="">-- Pilih --</option>
                                                        @foreach ($pendidikanOptions as $opt)
                                                        <option value="{{ $opt }}" {{ $pendidikanAyahLama==$opt
                                                            ? 'selected' : '' }}>{{ $opt }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Pekerjaan</label>
                                                    <input type="text" name="pekerjaan_ayah"
                                                        value="{{ old('pekerjaan_ayah', $student->student->family->pekerjaan_ayah ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Penghasilan</label>
                                                    <input type="text" name="penghasilan_ayah"
                                                        value="{{ old('penghasilan_ayah', $student->student->family->penghasilan_ayah ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Nomor
                                                        HP</label>
                                                    <input type="text" name="hp_ayah"
                                                        value="{{ old('hp_ayah', $student->student->family->hp_ayah ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Email</label>
                                                    <input type="email" name="email_ayah"
                                                        value="{{ old('email_ayah', $student->student->family->email_ayah ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div class="sm:col-span-3">
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Alamat
                                                        Tinggal Ayah</label>
                                                    <textarea name="alamat_ayah" rows="2"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">{{ old('alamat_ayah', $student->student->family->alamat_ayah ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Ibu --}}
                                        <div
                                            class="relative rounded-2xl border border-pink-100 bg-gradient-to-r from-pink-50/50 to-transparent p-5 sm:p-6 dark:border-pink-900/50 dark:from-pink-900/10">
                                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-pink-400 rounded-l-2xl">
                                            </div>
                                            <div
                                                class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-2">
                                                <h4 class="font-bold text-pink-800 dark:text-pink-400">Data Ibu Kandung
                                                </h4>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-slate-500">Status:</span>
                                                    <select name="is_ibu_hidup"
                                                        class="rounded-md border-slate-300 shadow-sm text-xs py-1.5 pl-3 pr-8 focus:ring-pink-500 focus:border-pink-500 dark:bg-slate-800 dark:border-slate-600 dark:text-white font-semibold">
                                                        <option value="1" {{ old('is_ibu_hidup', $student->
                                                            student->family->is_ibu_hidup ?? 1) == 1 ? 'selected' : ''
                                                            }}>Masih Hidup</option>
                                                        <option value="0" {{ old('is_ibu_hidup', $student->
                                                            student->family->is_ibu_hidup ?? 1) == 0 ? 'selected' : ''
                                                            }}>Meninggal</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                                <div class="sm:col-span-2">
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Nama
                                                        Ibu</label>
                                                    <input type="text" name="nama_ibu"
                                                        value="{{ old('nama_ibu', $student->student->family->nama_ibu ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Tempat
                                                        Lahir</label>
                                                    <input type="text" name="tempat_lahir_ibu"
                                                        value="{{ old('tempat_lahir_ibu', $student->student->family->tempat_lahir_ibu ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Tanggal
                                                        Lahir</label>
                                                    <input type="date" name="tanggal_lahir_ibu"
                                                        value="{{ old('tanggal_lahir_ibu', $student->student?->family?->tanggal_lahir_ibu?->format('Y-m-d') ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Agama</label>
                                                    <select name="agama_ibu"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                        <option value="">-- Pilih --</option>
                                                        @foreach ($agamaOptions as $opt)
                                                        <option value="{{ $opt }}" {{ $agamaIbuLama==$opt ? 'selected'
                                                            : '' }}>{{ $opt }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Pendidikan</label>
                                                    <select name="pendidikan_ibu"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                        <option value="">-- Pilih --</option>
                                                        @foreach ($pendidikanOptions as $opt)
                                                        <option value="{{ $opt }}" {{ $pendidikanIbuLama==$opt
                                                            ? 'selected' : '' }}>{{ $opt }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Pekerjaan</label>
                                                    <input type="text" name="pekerjaan_ibu"
                                                        value="{{ old('pekerjaan_ibu', $student->student->family->pekerjaan_ibu ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Penghasilan</label>
                                                    <input type="text" name="penghasilan_ibu"
                                                        value="{{ old('penghasilan_ibu', $student->student->family->penghasilan_ibu ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Nomor
                                                        HP</label>
                                                    <input type="text" name="hp_ibu"
                                                        value="{{ old('hp_ibu', $student->student->family->hp_ibu ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Email</label>
                                                    <input type="email" name="email_ibu"
                                                        value="{{ old('email_ibu', $student->student->family->email_ibu ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div class="sm:col-span-3">
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Alamat
                                                        Tinggal Ibu</label>
                                                    <textarea name="alamat_ibu" rows="2"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">{{ old('alamat_ibu', $student->student->family->alamat_ibu ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Wali --}}
                                        <div
                                            class="relative rounded-2xl border border-slate-200 bg-slate-50/50 p-5 sm:p-6 dark:border-slate-700 dark:bg-slate-800/50">
                                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-slate-400 rounded-l-2xl">
                                            </div>
                                            <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-4">
                                                Data Wali <span
                                                    class="text-xs font-normal text-slate-400 bg-slate-200 dark:bg-slate-700 px-2 py-0.5 rounded ml-2">(Opsional)</span>
                                            </h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                                <div class="sm:col-span-2">
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Nama
                                                        Wali</label>
                                                    <input type="text" name="nama_wali"
                                                        value="{{ old('nama_wali', $student->student->family->nama_wali ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Tempat
                                                        Lahir</label>
                                                    <input type="text" name="tempat_lahir_wali"
                                                        value="{{ old('tempat_lahir_wali', $student->student->family->tempat_lahir_wali ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Tanggal
                                                        Lahir</label>
                                                    <input type="date" name="tanggal_lahir_wali"
                                                        value="{{ old('tanggal_lahir_wali', $student->student?->family?->tanggal_lahir_wali?->format('Y-m-d') ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Agama</label>
                                                    <select name="agama_wali"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                        <option value="">-- Pilih --</option>
                                                        @foreach ($agamaOptions as $opt)
                                                        <option value="{{ $opt }}" {{ $agamaWaliLama==$opt ? 'selected'
                                                            : '' }}>{{ $opt }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Pendidikan</label>
                                                    <select name="pendidikan_wali"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                        <option value="">-- Pilih --</option>
                                                        @foreach ($pendidikanOptions as $opt)
                                                        <option value="{{ $opt }}" {{ $pendidikanWaliLama==$opt
                                                            ? 'selected' : '' }}>{{ $opt }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Pekerjaan</label>
                                                    <input type="text" name="pekerjaan_wali"
                                                        value="{{ old('pekerjaan_wali', $student->student->family->pekerjaan_wali ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Penghasilan</label>
                                                    <input type="text" name="penghasilan_wali"
                                                        value="{{ old('penghasilan_wali', $student->student->family->penghasilan_wali ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Nomor
                                                        HP</label>
                                                    <input type="text" name="hp_wali"
                                                        value="{{ old('hp_wali', $student->student->family->hp_wali ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Email</label>
                                                    <input type="email" name="email_wali"
                                                        value="{{ old('email_wali', $student->student->family->email_wali ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div class="sm:col-span-3">
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Alamat
                                                        Tinggal Wali</label>
                                                    <textarea name="alamat_wali" rows="2"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">{{ old('alamat_wali', $student->student->family->alamat_wali ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 4. FORM FINANSIAL --}}
                                <div x-show="tab === 'finansial'" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                                    <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-700">
                                        <h3 class="text-xl font-bold text-slate-800 dark:text-white">Kesejahteraan &
                                            Finansial</h3>
                                    </div>
                                    <div class="max-w-3xl">
                                        <div
                                            class="space-y-4 bg-slate-50 dark:bg-slate-800/80 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                                            <div class="flex items-center">
                                                <input type="checkbox" name="penerima_kjp" id="kjp" value="1" {{
                                                    old('penerima_kjp', $student->student->financial->penerima_kjp ??
                                                false) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300
                                                text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                                <label for="kjp"
                                                    class="ml-3 font-bold text-slate-800 dark:text-slate-200 cursor-pointer">Siswa
                                                    Penerima KJP (Kartu Jakarta Pintar)</label>
                                            </div>
                                            <div
                                                class="flex items-center pt-4 border-t border-slate-200 dark:border-slate-700">
                                                <input type="checkbox" name="penerima_pip" id="pip" value="1" {{
                                                    old('penerima_pip', $student->student->financial->penerima_pip ??
                                                false) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300
                                                text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                                <label for="pip"
                                                    class="ml-3 font-bold text-slate-800 dark:text-slate-200 cursor-pointer">Siswa
                                                    Penerima PIP (Program Indonesia Pintar)</label>
                                            </div>
                                            <div
                                                class="flex items-center pt-4 border-t border-slate-200 dark:border-slate-700">
                                                <input type="checkbox" name="penerima_bantuan_lain" id="bantuan_lain"
                                                    value="1" {{ old('penerima_bantuan_lain',
                                                    $student->student->financial->penerima_bantuan_lain ?? false) ?
                                                'checked' : '' }} class="w-5 h-5 rounded border-slate-300
                                                text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                                <label for="bantuan_lain"
                                                    class="ml-3 font-bold text-slate-800 dark:text-slate-200 cursor-pointer">Siswa
                                                    Penerima Bantuan Lainnya</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 5. FORM KESEHATAN --}}
                                <div x-show="tab === 'kesehatan'" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                                    <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-700">
                                        <h3 class="text-xl font-bold text-slate-800 dark:text-white">Data Kesehatan</h3>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Tinggi
                                                Badan <span
                                                    class="text-xs text-slate-400 font-normal">(cm)</span></label>
                                            <input type="number" step="1" name="tinggi_badan"
                                                value="{{ old('tinggi_badan', $student->student->health->tinggi_badan ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Berat
                                                Badan <span
                                                    class="text-xs text-slate-400 font-normal">(kg)</span></label>
                                            <input type="number" step="1" name="berat_badan"
                                                value="{{ old('berat_badan', $student->student->health->berat_badan ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Kebutuhan
                                                Khusus</label>
                                            <select name="kebutuhan_khusus"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                @php $kebutuhanKhususLama = old('kebutuhan_khusus',
                                                $student->student->health->kebutuhan_khusus ?? 'Tidak'); @endphp
                                                <option value="Tidak" {{ $kebutuhanKhususLama=='Tidak' ? 'selected' : ''
                                                    }}>
                                                    Tidak</option>
                                                <option value="Rungu" {{ $kebutuhanKhususLama=='Rungu' ? 'selected' : ''
                                                    }}>
                                                    Rungu</option>
                                                <option value="Grahita Sedang" {{ $kebutuhanKhususLama=='Grahita Sedang'
                                                    ? 'selected' : '' }}>
                                                    Grahita Sedang</option>
                                                <option value="Grahita Ringan" {{ $kebutuhanKhususLama=='Grahita Ringan'
                                                    ? 'selected' : '' }}>
                                                    Grahita Ringan</option>
                                                <option value="Daksa Sedang" {{ $kebutuhanKhususLama=='Daksa Sedang'
                                                    ? 'selected' : '' }}>
                                                    Daksa Sedang</option>
                                                <option value="Daksa Ringan" {{ $kebutuhanKhususLama=='Daksa Ringan'
                                                    ? 'selected' : '' }}>
                                                    Daksa Ringan</option>
                                                <option value="Laras" {{ $kebutuhanKhususLama=='Laras' ? 'selected' : ''
                                                    }}>
                                                    Laras</option>
                                                <option value="Wicara" {{ $kebutuhanKhususLama=='Wicara' ? 'selected'
                                                    : '' }}>
                                                    Wicara</option>
                                                <option value="Tuna Ganda" {{ $kebutuhanKhususLama=='Tuna Ganda'
                                                    ? 'selected' : '' }}>
                                                    Tuna Ganda</option>
                                                <option value="Hiperaktif" {{ $kebutuhanKhususLama=='Hiperaktif'
                                                    ? 'selected' : '' }}>
                                                    Hiperaktif</option>
                                                <option value="Cerdas Istimewa" {{
                                                    $kebutuhanKhususLama=='Cerdas Istimewa' ? 'selected' : '' }}>
                                                    Cerdas Istimewa</option>
                                                <option value="Lainnya" {{ $kebutuhanKhususLama=='Lainnya' ? 'selected'
                                                    : '' }}>
                                                    Lainnya</option>
                                            </select>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Riwayat
                                                Penyakit</label>
                                            <input type="text" name="penyakit"
                                                value="{{ old('penyakit', $student->student->health->penyakit ?? '') }}"
                                                placeholder="Contoh: Asma, Alergi Debu, dll (Kosongkan jika tidak ada)"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- NAVIGASI FOOTER (desktop) --}}
                            <div
                                class="hidden lg:flex px-6 py-4 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-700 justify-between items-center rounded-b-2xl">
                                <div>
                                    <button type="button" x-show="currentIndex > 0" @click="prev()"
                                        class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                        Sebelumnya
                                    </button>
                                </div>
                                <div>
                                    <button type="button" x-show="currentIndex < tabsList.length - 1" @click="next()"
                                        class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold shadow-md shadow-indigo-500/20 transition-colors">
                                        Selanjutnya
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                    <button type="submit" x-show="currentIndex === tabsList.length - 1"
                                        class="flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-bold shadow-md shadow-emerald-500/20 transition-colors"
                                        style="display: none;">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Selesai & Simpan Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- NAVIGASI FOOTER (mobile, mengambang) --}}
                <div class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-white/95 dark:bg-slate-800/95 backdrop-blur supports-[backdrop-filter]:bg-white/90 dark:supports-[backdrop-filter]:bg-slate-800/90 border-t border-slate-200 dark:border-slate-700 px-4 pt-3 shadow-[0_-4px_16px_-4px_rgba(0,0,0,0.08)]"
                    style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom));">
                    <div class="flex items-center gap-3 max-w-7xl mx-auto">
                        <button type="button" x-show="currentIndex > 0" @click="prev()"
                            class="flex-shrink-0 flex items-center justify-center h-11 w-11 text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-xl active:scale-95 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <button type="button" x-show="currentIndex < tabsList.length - 1" @click="next()"
                            class="flex-1 flex items-center justify-center gap-2 h-11 bg-indigo-600 active:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-md shadow-indigo-500/20 transition-colors">
                            Selanjutnya
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                        <button type="submit" x-show="currentIndex === tabsList.length - 1"
                            class="flex-1 flex items-center justify-center gap-2 h-11 bg-emerald-600 active:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-md shadow-emerald-500/20 transition-colors"
                            style="display: none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- CSS Tambahan --}}
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    <script>
        document.addEventListener('alpine:init', () => {
        Alpine.data('studentForm', () => ({
            tab: 'identitas',
            tabsList: ['identitas', 'alamat', 'keluarga', 'finansial', 'kesehatan'],

            get currentIndex() {
                return this.tabsList.indexOf(this.tab);
            },

            get progress() {
                return ((this.currentIndex + 1) / this.tabsList.length * 100) + '%';
            },

            get progressText() {
                return 'Langkah ' + (this.currentIndex + 1) + ' dari ' + this.tabsList.length;
            },

            // Fungsi Next dengan Auto-Save AJAX
            async next() {
                const formElement = document.getElementById('studentForm');
                const formData = new FormData(formElement);

                try {
                    let response = await fetch("{{ route('students.ajax-update', $student->id ?? 0) }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-HTTP-Method-Override': 'PUT',
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    if (response.ok) {
                        if (this.currentIndex < this.tabsList.length - 1) {
                            this.tab = this.tabsList[this.currentIndex + 1];
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    } else {
                        alert('Gagal menyimpan otomatis. Periksa kembali inputan Anda.');
                    }
                } catch (error) {
                    console.error('Terjadi kesalahan:', error);
                }
            },

            // Fungsi Prev (Sebelumnya)
            prev() {
                if (this.currentIndex > 0) {
                    this.tab = this.tabsList[this.currentIndex - 1];
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            /* --- LOGIKA API WILAYAH INDONESIA --- */
            apiBase: 'https://www.emsifa.com/api-wilayah-indonesia/v2',
            savedProvinsi: '{{ old('provinsi', $student->student->address->provinsi ?? '') }}',
            savedKota: '{{ old('kota', $student->student->address->kota ?? '') }}',
            savedKecamatan: '{{ old('kecamatan', $student->student->address->kecamatan ?? '') }}',
            savedKelurahan: '{{ old('kelurahan', $student->student->address->kelurahan ?? '') }}',

            provinces: [], cities: [], districts: [], villages: [],
            selectedProvId: '', selectedCityId: '', selectedDistId: '', selectedVillId: '',
            provName: '{{ old('provinsi', $student->student->address->provinsi ?? '') }}',
            cityName: '{{ old('kota', $student->student->address->kota ?? '') }}',
            distName: '{{ old('kecamatan', $student->student->address->kecamatan ?? '') }}',
            villName: '{{ old('kelurahan', $student->student->address->kelurahan ?? '') }}',

            async initWilayah() {
                try {
                    let res = await fetch(this.apiBase + '/provinces.json');
                    let json = await res.json();
                    this.provinces = json.data;

                    if (this.savedProvinsi) {
                        let p = this.provinces.find(x => x.name.toUpperCase() === this.savedProvinsi.toUpperCase());
                        if (p) {
                            this.selectedProvId = p.id;
                            await this.fetchCities(p.id, true);
                        }
                    }
                } catch(e) { console.error('Gagal memuat provinsi', e); }
            },

            async fetchCities(provId, isInit = false) {
                if (!isInit) {
                    this.selectedCityId = ''; this.selectedDistId = ''; this.selectedVillId = '';
                    this.cityName = ''; this.distName = ''; this.villName = '';
                    this.cities = []; this.districts = []; this.villages = [];
                    let p = this.provinces.find(x => x.id === provId);
                    this.provName = p ? p.name : '';
                }
                if (!provId) return;

                let res = await fetch(this.apiBase + '/regencies/' + provId + '.json');
                let json = await res.json();
                this.cities = json.data;

                if (isInit && this.savedKota) {
                    let c = this.cities.find(x => x.name.toUpperCase() === this.savedKota.toUpperCase());
                    if (c) {
                        this.selectedCityId = c.id;
                        await this.fetchDistricts(c.id, true);
                    }
                }
            },

            async fetchDistricts(cityId, isInit = false) {
                if (!isInit) {
                    this.selectedDistId = ''; this.selectedVillId = '';
                    this.distName = ''; this.villName = '';
                    this.districts = []; this.villages = [];
                    let c = this.cities.find(x => x.id === cityId);
                    this.cityName = c ? c.name : '';
                }
                if (!cityId) return;

                let res = await fetch(this.apiBase + '/districts/' + cityId + '.json');
                let json = await res.json();
                this.districts = json.data;

                if (isInit && this.savedKecamatan) {
                    let d = this.districts.find(x => x.name.toUpperCase() === this.savedKecamatan.toUpperCase());
                    if (d) {
                        this.selectedDistId = d.id;
                        await this.fetchVillages(d.id, true);
                    }
                }
            },

            async fetchVillages(distId, isInit = false) {
                if (!isInit) {
                    this.selectedVillId = ''; this.villName = '';
                    this.villages = [];
                    let d = this.districts.find(x => x.id === distId);
                    this.distName = d ? d.name : '';
                }
                if (!distId) return;

                let res = await fetch(this.apiBase + '/villages/' + distId + '.json');
                let json = await res.json();
                this.villages = json.data;

                if (isInit && this.savedKelurahan) {
                    let v = this.villages.find(x => x.name.toUpperCase() === this.savedKelurahan.toUpperCase());
                    if (v) {
                        this.selectedVillId = v.id;
                    }
                }
            },

            setVillage() {
                let v = this.villages.find(x => x.id === this.selectedVillId);
                this.villName = v ? v.name : '';
                if (v && v.postal_code) {
                    document.getElementById('kode_pos_input').value = v.postal_code;
                }
            }
        }));
    });
    </script>
</x-app-layout>