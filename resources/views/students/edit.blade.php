<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 dark:text-white tracking-tight">
                    Edit Profil: <span class="text-indigo-600 dark:text-indigo-400">{{ $student->name ?? 'Siswa'
                        }}</span>
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

    {{-- ALPINE.JS CONTAINER: Navigasi Tab & Fetch API Wilayah --}}
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
        get progress() { return ((this.currentIndex + 1) / this.tabsList.length * 100) + '%'; },
        get progressText() { return 'Langkah ' + (this.currentIndex + 1) + ' dari ' + this.tabsList.length; },

        /* --- LOGIKA API WILAYAH INDONESIA --- */
        apiBase: 'https://www.emsifa.com/api-wilayah-indonesia/v2',

        // Data lama dari database
        savedProvinsi: '{{ old('provinsi', $student->student->address->provinsi ?? '') }}',
        savedKota: '{{ old('kota', $student->student->address->kota ?? '') }}',
        savedKecamatan: '{{ old('kecamatan', $student->student->address->kecamatan ?? '') }}',
        savedKelurahan: '{{ old('kelurahan', $student->student->address->kelurahan ?? '') }}',

        // Wadah data API
        provinces: [], cities: [], districts: [], villages: [],

        // State Pilihan
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

                if(this.savedProvinsi) {
                    let p = this.provinces.find(x => x.name.toUpperCase() === this.savedProvinsi.toUpperCase());
                    if(p) {
                        this.selectedProvId = p.id;
                        await this.fetchCities(p.id, true);
                    }
                }
            } catch(e) { console.error('Gagal memuat provinsi', e); }
        },

        async fetchCities(provId, isInit = false) {
            if(!isInit) {
                this.selectedCityId = ''; this.selectedDistId = ''; this.selectedVillId = '';
                this.cityName = ''; this.distName = ''; this.villName = '';
                this.cities = []; this.districts = []; this.villages = [];
                let p = this.provinces.find(x => x.id === provId);
                this.provName = p ? p.name : '';
            }
            if(!provId) return;

            let res = await fetch(this.apiBase + '/regencies/' + provId + '.json');
            let json = await res.json();
            this.cities = json.data;

            if(isInit && this.savedKota) {
                let c = this.cities.find(x => x.name.toUpperCase() === this.savedKota.toUpperCase());
                if(c) {
                    this.selectedCityId = c.id;
                    await this.fetchDistricts(c.id, true);
                }
            }
        },

        async fetchDistricts(cityId, isInit = false) {
            if(!isInit) {
                this.selectedDistId = ''; this.selectedVillId = '';
                this.distName = ''; this.villName = '';
                this.districts = []; this.villages = [];
                let c = this.cities.find(x => x.id === cityId);
                this.cityName = c ? c.name : '';
            }
            if(!cityId) return;

            let res = await fetch(this.apiBase + '/districts/' + cityId + '.json');
            let json = await res.json();
            this.districts = json.data;

            if(isInit && this.savedKecamatan) {
                let d = this.districts.find(x => x.name.toUpperCase() === this.savedKecamatan.toUpperCase());
                if(d) {
                    this.selectedDistId = d.id;
                    await this.fetchVillages(d.id, true);
                }
            }
        },

        async fetchVillages(distId, isInit = false) {
            if(!isInit) {
                this.selectedVillId = ''; this.villName = '';
                this.villages = [];
                let d = this.districts.find(x => x.id === distId);
                this.distName = d ? d.name : '';
            }
            if(!distId) return;

            let res = await fetch(this.apiBase + '/villages/' + distId + '.json');
            let json = await res.json();
            this.villages = json.data;

            if(isInit && this.savedKelurahan) {
                let v = this.villages.find(x => x.name.toUpperCase() === this.savedKelurahan.toUpperCase());
                if(v) {
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
    }" x-init="initWilayah()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <form action="{{ route('students.update', $student->id ?? 0) }}" method="POST">
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
                                    class="flex-shrink-0 lg:w-full flex items-center gap-2 sm:gap-3 px-3 py-2 sm:px-4 sm:py-3 rounded-xl text-xs sm:text-sm transition-all duration-200">
                                    <i class="fas fa-user-circle w-4 sm:w-5"></i> Identitas Pokok
                                </button>
                                <button type="button" @click="tab = 'alamat'"
                                    :class="tab === 'alamat' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400 font-bold ring-1 ring-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium'"
                                    class="flex-shrink-0 lg:w-full flex items-center gap-2 sm:gap-3 px-3 py-2 sm:px-4 sm:py-3 rounded-xl text-xs sm:text-sm transition-all duration-200">
                                    <i class="fas fa-map-marker-alt w-4 sm:w-5"></i> Alamat & Domisili
                                </button>
                                <button type="button" @click="tab = 'keluarga'"
                                    :class="tab === 'keluarga' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400 font-bold ring-1 ring-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium'"
                                    class="flex-shrink-0 lg:w-full flex items-center gap-2 sm:gap-3 px-3 py-2 sm:px-4 sm:py-3 rounded-xl text-xs sm:text-sm transition-all duration-200">
                                    <i class="fas fa-users w-4 sm:w-5"></i> Data Keluarga
                                </button>
                                <button type="button" @click="tab = 'finansial'"
                                    :class="tab === 'finansial' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400 font-bold ring-1 ring-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium'"
                                    class="flex-shrink-0 lg:w-full flex items-center gap-2 sm:gap-3 px-3 py-2 sm:px-4 sm:py-3 rounded-xl text-xs sm:text-sm transition-all duration-200">
                                    <i class="fas fa-wallet w-4 sm:w-5"></i> Finansial & Bantuan
                                </button>
                                <button type="button" @click="tab = 'kesehatan'"
                                    :class="tab === 'kesehatan' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400 font-bold ring-1 ring-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium'"
                                    class="flex-shrink-0 lg:w-full flex items-center gap-2 sm:gap-3 px-3 py-2 sm:px-4 sm:py-3 rounded-xl text-xs sm:text-sm transition-all duration-200">
                                    <i class="fas fa-heartbeat w-4 sm:w-5"></i> Data Kesehatan
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
                                                class="block w-full rounded-lg shadow-sm sm:text-sm dark:bg-slate-900 dark:text-white transition-colors {{ $errors->has('nisn') ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-600' }}">
                                            @error('nisn') <p class="mt-1 text-xs font-semibold text-rose-500">{{
                                                $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">NIPD
                                                / NIS</label>
                                            <input type="text" name="nipd"
                                                value="{{ old('nipd', $student->student->nipd ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode
                                                Kelas</label>
                                            <input type="text" name="class_code"
                                                value="{{ old('class_code', $student->student->class_code ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
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
                                            <input type="text" name="agama"
                                                value="{{ old('agama', $student->student->agama ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
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
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">SKHUN</label>
                                            <input type="text" name="skhun"
                                                value="{{ old('skhun', $student->student->skhun ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">No.
                                                Ujian Nasional</label>
                                            <input type="text" name="no_peserta_ujian_nasional"
                                                value="{{ old('no_peserta_ujian_nasional', $student->student->no_peserta_ujian_nasional ?? '') }}"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">No.
                                                Seri Ijazah</label>
                                            <input type="text" name="no_seri_ijazah"
                                                value="{{ old('no_seri_ijazah', $student->student->no_seri_ijazah ?? '') }}"
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
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Tahun
                                                        Lahir</label>
                                                    <input type="text" name="tahun_lahir_wali"
                                                        value="{{ old('tahun_lahir_wali', $student->student->family->tahun_lahir_wali ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Pendidikan</label>
                                                    <input type="text" name="pendidikan_wali"
                                                        value="{{ old('pendidikan_wali', $student->student->family->pendidikan_wali ?? '') }}"
                                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
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
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
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
                                        <div class="sm:col-span-2">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Kebutuhan
                                                Khusus</label>
                                            <input type="text" name="kebutuhan_khusus"
                                                value="{{ old('kebutuhan_khusus', $student->student->health->kebutuhan_khusus ?? '') }}"
                                                placeholder="Contoh: Disleksia, Autisme, dll (Kosongkan jika tidak ada)"
                                                class="block w-full rounded-lg border-slate-300 shadow-sm sm:text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
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

                            {{-- NAVIGASI FOOTER --}}
                            <div
                                class="px-6 py-4 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-700 flex justify-between items-center rounded-b-2xl">
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
</x-app-layout>