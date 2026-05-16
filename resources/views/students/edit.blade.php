<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 dark:text-white tracking-tight">
                    Edit Profil: <span class="text-indigo-600 dark:text-indigo-400">{{ $student->name }}</span>
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Lengkapi data pokok peserta didik sesuai
                    dengan dokumen resmi.</p>
            </div>
            <a href="{{ route('dashboard') }}"
                class="hidden md:inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    {{-- ALPINE.JS CONTAINER: Dilengkapi logika Navigasi Next/Prev --}}
    <div class="py-6 sm:py-8" x-data="{
        tab: 'identitas',
        tabsList: ['identitas', 'alamat', 'keluarga', 'finansial', 'kesehatan'],
        get currentIndex() { return this.tabsList.indexOf(this.tab); },
        next() {
            if(this.currentIndex < this.tabsList.length - 1) this.tab = this.tabsList[this.currentIndex + 1];
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        prev() {
            if(this.currentIndex > 0) this.tab = this.tabsList[this.currentIndex - 1];
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        get progress() {
            return ((this.currentIndex + 1) / this.tabsList.length * 100) + '%';
        },
        get progressText() {
            return 'Langkah ' + (this.currentIndex + 1) + ' dari ' + this.tabsList.length;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <form action="{{ route('students.update', $student->id) }}" method="POST">
                @csrf @method('PUT')

                {{-- PROGRESS BAR AREA --}}
                <div class="mb-8 px-4 sm:px-0">
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">
                            <span x-text="progressText"></span>:
                            <span x-show="tab === 'identitas'">Identitas Pokok</span>
                            <span x-show="tab === 'alamat'">Data Domisili</span>
                            <span x-show="tab === 'keluarga'">Data Keluarga</span>
                            <span x-show="tab === 'finansial'">Kesejahteraan & Finansial</span>
                            <span x-show="tab === 'kesehatan'">Data Kesehatan</span>
                        </span>
                        <span
                            class="text-xs font-extrabold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 px-2 py-1 rounded-md"
                            x-text="progress"></span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden shadow-inner">
                        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-2.5 rounded-full transition-all duration-500 ease-out"
                            :style="'width: ' + progress"></div>
                    </div>
                </div>

                <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">

                    {{-- SIDEBAR TABS --}}
                    <div class="w-full lg:w-72 flex-shrink-0 px-4 sm:px-0">
                        <div
                            class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-2 sm:p-3 sticky top-6">

                            <nav class="flex overflow-x-auto lg:flex-col gap-2 pb-2 lg:pb-0 scrollbar-hide">
                                <button type="button" @click="tab = 'identitas'"
                                    :class="tab === 'identitas' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400 font-bold ring-1 ring-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium'"
                                    class="flex-shrink-0 lg:w-full flex items-center gap-2 sm:gap-3 px-3 py-2 sm:px-4 sm:py-3 rounded-xl text-xs sm:text-sm transition-all duration-200 whitespace-nowrap lg:whitespace-normal">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2">
                                        </path>
                                    </svg>
                                    Identitas Pokok
                                </button>
                                <button type="button" @click="tab = 'alamat'"
                                    :class="tab === 'alamat' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400 font-bold ring-1 ring-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium'"
                                    class="flex-shrink-0 lg:w-full flex items-center gap-2 sm:gap-3 px-3 py-2 sm:px-4 sm:py-3 rounded-xl text-xs sm:text-sm transition-all duration-200 whitespace-nowrap lg:whitespace-normal">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                        </path>
                                    </svg>
                                    Alamat & Domisili
                                </button>
                                <button type="button" @click="tab = 'keluarga'"
                                    :class="tab === 'keluarga' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400 font-bold ring-1 ring-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium'"
                                    class="flex-shrink-0 lg:w-full flex items-center gap-2 sm:gap-3 px-3 py-2 sm:px-4 sm:py-3 rounded-xl text-xs sm:text-sm transition-all duration-200 whitespace-nowrap lg:whitespace-normal">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    Data Keluarga
                                </button>
                                <button type="button" @click="tab = 'finansial'"
                                    :class="tab === 'finansial' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400 font-bold ring-1 ring-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium'"
                                    class="flex-shrink-0 lg:w-full flex items-center gap-2 sm:gap-3 px-3 py-2 sm:px-4 sm:py-3 rounded-xl text-xs sm:text-sm transition-all duration-200 whitespace-nowrap lg:whitespace-normal">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                        </path>
                                    </svg>
                                    Finansial & Bantuan
                                </button>
                                <button type="button" @click="tab = 'kesehatan'"
                                    :class="tab === 'kesehatan' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400 font-bold ring-1 ring-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium'"
                                    class="flex-shrink-0 lg:w-full flex items-center gap-2 sm:gap-3 px-3 py-2 sm:px-4 sm:py-3 rounded-xl text-xs sm:text-sm transition-all duration-200 whitespace-nowrap lg:whitespace-normal">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                        </path>
                                    </svg>
                                    Data Kesehatan
                                </button>
                            </nav>

                            <div
                                class="pt-3 lg:pt-4 border-t border-slate-100 dark:border-slate-700 mt-2 lg:mt-4 hidden lg:block">
                                <button type="submit"
                                    class="w-full flex justify-center items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white py-3 px-4 rounded-xl text-sm font-bold shadow-lg shadow-emerald-500/30 transition-all focus:ring-4 focus:ring-emerald-500/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                        </path>
                                    </svg>
                                    Simpan Cepat
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- MAIN CONTENT AREA --}}
                    <div class="flex-1 min-w-0 px-4 sm:px-0">
                        <div
                            class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col">

                            {{-- TAB KONTEN (Area ini yang berubah-ubah) --}}
                            <div class="p-6 sm:p-8 flex-1">

                                {{-- 1. FORM IDENTITAS --}}
                                <div x-show="tab === 'identitas'" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                                    <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-700">
                                        <h3 class="text-xl font-bold text-slate-800 dark:text-white">Identitas Peserta
                                            Didik</h3>
                                        <p class="text-sm text-slate-500 mt-1">Data pokok siswa sesuai akta kelahiran.
                                        </p>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-6">
                                        <div class="sm:col-span-2">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama
                                                Lengkap</label>
                                            <input type="text" name="name" value="{{ old('name', $student->name) }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">NISN</label>
                                            <input type="text" name="nisn"
                                                value="{{ old('nisn', $student->student->nisn ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">NIS</label>
                                            <input type="text" name="nis"
                                                value="{{ old('nis', $student->student->nis ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Tempat
                                                Lahir</label>
                                            <input type="text" name="tempat_lahir"
                                                value="{{ old('tempat_lahir', $student->student->tempat_lahir ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal
                                                Lahir</label>
                                            <input type="date" name="tanggal_lahir"
                                                value="{{ old('tanggal_lahir', $student->student->tanggal_lahir ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis
                                                Kelamin</label>
                                            <select name="jenis_kelamin"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                                <option value="">-- Pilih Jenis Kelamin --</option>
                                                <option value="L" {{ old('jenis_kelamin', $student->
                                                    student->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki
                                                </option>
                                                <option value="P" {{ old('jenis_kelamin', $student->
                                                    student->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- 2. FORM ALAMAT --}}
                                <div x-show="tab === 'alamat'" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                                    <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-700">
                                        <h3 class="text-xl font-bold text-slate-800 dark:text-white">Alamat & Domisili
                                        </h3>
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
                                            <input type="text" name="kode_pos"
                                                value="{{ old('kode_pos', $student->student->address->kode_pos ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                        </div>
                                        <div class="sm:col-span-3">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Dusun</label>
                                            <input type="text" name="dusun"
                                                value="{{ old('dusun', $student->student->address->dusun ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                        </div>
                                        <div class="sm:col-span-3">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Kelurahan/Desa</label>
                                            <input type="text" name="kelurahan"
                                                value="{{ old('kelurahan', $student->student->address->kelurahan ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Kecamatan</label>
                                            <input type="text" name="kecamatan"
                                                value="{{ old('kecamatan', $student->student->address->kecamatan ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Lintang</label>
                                            <input type="text" name="lintang"
                                                value="{{ old('lintang', $student->student->address->lintang ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Bujur</label>
                                            <input type="text" name="bujur"
                                                value="{{ old('bujur', $student->student->address->bujur ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                        </div>
                                        <div
                                            class="sm:col-span-6 mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                            <div>
                                                <label
                                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis
                                                    Tinggal</label>
                                                <input type="text" name="jenis_tinggal"
                                                    value="{{ old('jenis_tinggal', $student->student->address->jenis_tinggal ?? '') }}"
                                                    placeholder="Bersama Orang Tua"
                                                    class="block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Alat
                                                    Transportasi</label>
                                                <input type="text" name="alat_transportasi"
                                                    value="{{ old('alat_transportasi', $student->student->address->alat_transportasi ?? '') }}"
                                                    placeholder="Jalan Kaki"
                                                    class="block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Jarak
                                                    ke Sekolah (KM)</label>
                                                <input type="number" step="0.01" name="jarak_ke_sekolah_km"
                                                    value="{{ old('jarak_ke_sekolah_km', $student->student->address->jarak_ke_sekolah_km ?? '') }}"
                                                    class="block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white transition-colors">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 3. FORM KELUARGA --}}
                                <div x-show="tab === 'keluarga'" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
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
                                            <h4 class="font-bold text-blue-800 dark:text-blue-400 mb-4">Data Ayah
                                                Kandung</h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Nama
                                                        Ayah</label>
                                                    <input type="text" name="nama_ayah"
                                                        value="{{ old('nama_ayah', $student->student->family->nama_ayah ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">NIK
                                                        Ayah</label>
                                                    <input type="text" name="nik_ayah"
                                                        value="{{ old('nik_ayah', $student->student->family->nik_ayah ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Tahun
                                                        Lahir</label>
                                                    <input type="text" name="tahun_lahir_ayah"
                                                        value="{{ old('tahun_lahir_ayah', $student->student->family->tahun_lahir_ayah ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Pendidikan</label>
                                                    <input type="text" name="pendidikan_ayah"
                                                        value="{{ old('pendidikan_ayah', $student->student->family->pendidikan_ayah ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
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
                                            </div>
                                        </div>

                                        {{-- Ibu --}}
                                        <div
                                            class="relative rounded-2xl border border-pink-100 bg-gradient-to-r from-pink-50/50 to-transparent p-5 sm:p-6 dark:border-pink-900/50 dark:from-pink-900/10">
                                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-pink-400 rounded-l-2xl">
                                            </div>
                                            <h4 class="font-bold text-pink-800 dark:text-pink-400 mb-4">Data Ibu Kandung
                                            </h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Nama
                                                        Ibu</label>
                                                    <input type="text" name="nama_ibu"
                                                        value="{{ old('nama_ibu', $student->student->family->nama_ibu ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">NIK
                                                        Ibu</label>
                                                    <input type="text" name="nik_ibu"
                                                        value="{{ old('nik_ibu', $student->student->family->nik_ibu ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Tahun
                                                        Lahir</label>
                                                    <input type="text" name="tahun_lahir_ibu"
                                                        value="{{ old('tahun_lahir_ibu', $student->student->family->tahun_lahir_ibu ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Pendidikan</label>
                                                    <input type="text" name="pendidikan_ibu"
                                                        value="{{ old('pendidikan_ibu', $student->student->family->pendidikan_ibu ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
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
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Nama
                                                        Wali</label>
                                                    <input type="text" name="nama_wali"
                                                        value="{{ old('nama_wali', $student->student->family->nama_wali ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">NIK
                                                        Wali</label>
                                                    <input type="text" name="nik_wali"
                                                        value="{{ old('nik_wali', $student->student->family->nik_wali ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Tahun
                                                        Lahir</label>
                                                    <input type="text" name="tahun_lahir_wali"
                                                        value="{{ old('tahun_lahir_wali', $student->student->family->tahun_lahir_wali ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Pendidikan
                                                        Wali</label>
                                                    <input type="text" name="pendidikan_wali"
                                                        value="{{ old('pendidikan_wali', $student->student->family->pendidikan_wali ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Pekerjaan
                                                        Wali</label>
                                                    <input type="text" name="pekerjaan_wali"
                                                        value="{{ old('pekerjaan_wali', $student->student->family->pekerjaan_wali ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Penghasilan
                                                        Wali</label>
                                                    <input type="text" name="penghasilan_wali"
                                                        value="{{ old('penghasilan_wali', $student->student->family->penghasilan_wali ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
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

                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        {{-- KPS / PKH --}}
                                        <div
                                            class="space-y-4 bg-slate-50 dark:bg-slate-800/80 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                                            <div
                                                class="flex items-center pb-3 border-b border-slate-200 dark:border-slate-700">
                                                <input type="checkbox" name="penerima_kps" id="kps" value="1" {{
                                                    old('penerima_kps', $student->student->financial->penerima_kps ??
                                                false) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300
                                                text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                                <label for="kps"
                                                    class="ml-3 font-bold text-slate-800 dark:text-slate-200 cursor-pointer">Siswa
                                                    Penerima KPS / PKH</label>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">No.
                                                    KPS / PKH</label>
                                                <input type="text" name="no_kps"
                                                    value="{{ old('no_kps', $student->student->financial->no_kps ?? '') }}"
                                                    class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">No.
                                                    KKS</label>
                                                <input type="text" name="nomor_kks"
                                                    value="{{ old('nomor_kks', $student->student->financial->nomor_kks ?? '') }}"
                                                    class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                            </div>
                                        </div>

                                        {{-- KIP / PIP --}}
                                        <div
                                            class="space-y-4 bg-slate-50 dark:bg-slate-800/80 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                                            <div
                                                class="flex items-center pb-3 border-b border-slate-200 dark:border-slate-700">
                                                <input type="checkbox" name="penerima_kip" id="kip" value="1" {{
                                                    old('penerima_kip', $student->student->financial->penerima_kip ??
                                                false) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300
                                                text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                                <label for="kip"
                                                    class="ml-3 font-bold text-slate-800 dark:text-slate-200 cursor-pointer">Siswa
                                                    Penerima KIP</label>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-slate-500 uppercase mb-1">Nomor
                                                    KIP</label>
                                                <input type="text" name="nomor_kip"
                                                    value="{{ old('nomor_kip', $student->student->financial->nomor_kip ?? '') }}"
                                                    class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama
                                                    Tertera di KIP</label>
                                                <input type="text" name="nama_di_kip"
                                                    value="{{ old('nama_di_kip', $student->student->financial->nama_di_kip ?? '') }}"
                                                    class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                            </div>

                                            <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                                                <div class="flex items-center mb-3">
                                                    <input type="checkbox" name="layak_pip" id="pip" value="1" {{
                                                        old('layak_pip', $student->student->financial->layak_pip ??
                                                    false) ? 'checked' : '' }} class="rounded border-slate-300
                                                    text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                                    <label for="pip"
                                                        class="ml-2 font-bold text-sm text-slate-700 dark:text-slate-300 cursor-pointer">Layak
                                                        Menerima PIP</label>
                                                </div>
                                                <input type="text" name="alasan_layak_pip"
                                                    placeholder="Sebutkan alasannya..."
                                                    value="{{ old('alasan_layak_pip', $student->student->financial->alasan_layak_pip ?? '') }}"
                                                    class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                            </div>
                                        </div>

                                        {{-- Info Rekening --}}
                                        <div
                                            class="lg:col-span-2 space-y-4 bg-emerald-50/50 dark:bg-emerald-900/10 p-5 rounded-2xl border border-emerald-100 dark:border-emerald-900/50">
                                            <h4 class="font-bold text-emerald-800 dark:text-emerald-400">Informasi
                                                Rekening Bank</h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-2">
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Nama
                                                        Bank</label>
                                                    <input type="text" name="bank"
                                                        value="{{ old('bank', $student->student->financial->bank ?? '') }}"
                                                        placeholder="Contoh: BRI / BNI"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Nomor
                                                        Rekening</label>
                                                    <input type="text" name="nomor_rekening_bank"
                                                        value="{{ old('nomor_rekening_bank', $student->student->financial->nomor_rekening_bank ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Atas
                                                        Nama</label>
                                                    <input type="text" name="rekening_atas_nama"
                                                        value="{{ old('rekening_atas_nama', $student->student->financial->rekening_atas_nama ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
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
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Tinggi
                                                Badan <span
                                                    class="text-xs text-slate-400 font-normal">(cm)</span></label>
                                            <input type="number" step="0.1" name="tinggi_badan"
                                                value="{{ old('tinggi_badan', $student->student->health->tinggi_badan ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Berat
                                                Badan <span
                                                    class="text-xs text-slate-400 font-normal">(kg)</span></label>
                                            <input type="number" step="0.1" name="berat_badan"
                                                value="{{ old('berat_badan', $student->student->health->berat_badan ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Lingkar
                                                Kepala <span
                                                    class="text-xs text-slate-400 font-normal">(cm)</span></label>
                                            <input type="number" step="0.1" name="lingkar_kepala"
                                                value="{{ old('lingkar_kepala', $student->student->health->lingkar_kepala ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- NAVIGASI FOOTER (PREV & NEXT) --}}
                            <div
                                class="px-6 py-4 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-700 flex justify-between items-center rounded-b-2xl">

                                {{-- Tombol Sebelumnya (Hanya muncul jika bukan tab pertama) --}}
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

                                {{-- Tombol Selanjutnya (Muncul di tab 1 sampai 4) --}}
                                <div>
                                    <button type="button" x-show="currentIndex < tabsList.length - 1" @click="next()"
                                        class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold shadow-md shadow-indigo-500/20 transition-colors">
                                        Selanjutnya
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>

                                    {{-- Tombol Simpan Utama (Hanya muncul di tab terakhir / Kesehatan) --}}
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
            </form>
        </div>
    </div>

    {{-- CSS Tambahan untuk menyembunyikan scrollbar bawaan browser tapi tetap bisa di-scroll --}}
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</x-app-layout>