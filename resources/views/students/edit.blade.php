<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
                Profil Siswa: <span class="text-indigo-600">{{ $student->name }}</span>
            </h2>
            <a href="{{ route('operator.users.index') }}" class="text-sm font-bold text-slate-500 hover:text-slate-700">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ tab: 'identitas' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <form action="{{ route('students.update', $student->id) }}" method="POST">
                @csrf @method('PUT')

                <div class="flex flex-col md:flex-row gap-6">

                    <div class="w-full md:w-64 flex-shrink-0 space-y-2">
                        @php
                        $tabs = [
                        'identitas' => 'Identitas Pokok',
                        'alamat' => 'Alamat & Domisili',
                        'keluarga' => 'Data Keluarga',
                        'finansial' => 'Finansial & Bantuan',
                        'kesehatan' => 'Data Kesehatan'
                        ];
                        @endphp

                        @foreach($tabs as $key => $label)
                        <button type="button" @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'"
                            class="w-full flex items-center px-4 py-3 rounded-xl text-sm font-bold transition">
                            {{ $label }}
                        </button>
                        @endforeach

                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700 mt-4">
                            <button type="submit"
                                class="w-full bg-emerald-600 text-white py-3 rounded-xl text-sm font-bold shadow-lg shadow-emerald-500/20 hover:bg-emerald-700 transition">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>

                    <div
                        class="flex-1 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">

                        <div x-show="tab === 'identitas'" class="space-y-4">
                            <h3
                                class="text-lg font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-2">
                                Identitas Peserta Didik</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ old('name', $student->name) }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">NISN</label>
                                    <input type="text" name="nisn"
                                        value="{{ old('nisn', $student->student->nisn ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">NIS</label>
                                    <input type="text" name="nis" value="{{ old('nis', $student->student->nis ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir"
                                        value="{{ old('tempat_lahir', $student->student->tempat_lahir ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Tanggal
                                        Lahir</label>
                                    <input type="date" name="tanggal_lahir"
                                        value="{{ old('tanggal_lahir', $student->student->tanggal_lahir ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Jenis
                                        Kelamin</label>
                                    <select name="jenis_kelamin"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                        <option value="">-- Pilih --</option>
                                        <option value="L" {{ old('jenis_kelamin', $student->student->jenis_kelamin ??
                                            '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin', $student->student->jenis_kelamin ??
                                            '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div x-show="tab === 'alamat'" style="display: none;" class="space-y-4">
                            <h3
                                class="text-lg font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-2">
                                Alamat & Tempat Tinggal</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="col-span-3">
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Alamat Jalan</label>
                                    <textarea name="alamat" rows="2"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">{{ old('alamat', $student->student->address->alamat ?? '') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">RT</label>
                                    <input type="text" name="rt"
                                        value="{{ old('rt', $student->student->address->rt ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">RW</label>
                                    <input type="text" name="rw"
                                        value="{{ old('rw', $student->student->address->rw ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Kode Pos</label>
                                    <input type="text" name="kode_pos"
                                        value="{{ old('kode_pos', $student->student->address->kode_pos ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Dusun</label>
                                    <input type="text" name="dusun"
                                        value="{{ old('dusun', $student->student->address->dusun ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold text-slate-500 uppercase">Kelurahan/Desa</label>
                                    <input type="text" name="kelurahan"
                                        value="{{ old('kelurahan', $student->student->address->kelurahan ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Kecamatan</label>
                                    <input type="text" name="kecamatan"
                                        value="{{ old('kecamatan', $student->student->address->kecamatan ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Lintang</label>
                                    <input type="text" name="lintang"
                                        value="{{ old('lintang', $student->student->address->lintang ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Bujur</label>
                                    <input type="text" name="bujur"
                                        value="{{ old('bujur', $student->student->address->bujur ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                                <div
                                    class="col-span-3 border-t border-slate-100 dark:border-slate-700 mt-2 pt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase">Jenis
                                            Tinggal</label>
                                        <input type="text" name="jenis_tinggal"
                                            value="{{ old('jenis_tinggal', $student->student->address->jenis_tinggal ?? '') }}"
                                            placeholder="Bersama Orang Tua"
                                            class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 uppercase">Transportasi</label>
                                        <input type="text" name="alat_transportasi"
                                            value="{{ old('alat_transportasi', $student->student->address->alat_transportasi ?? '') }}"
                                            placeholder="Jalan Kaki"
                                            class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase">Jarak ke Sekolah
                                            (KM)</label>
                                        <input type="number" step="0.01" name="jarak_ke_sekolah_km"
                                            value="{{ old('jarak_ke_sekolah_km', $student->student->address->jarak_ke_sekolah_km ?? '') }}"
                                            class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div x-show="tab === 'keluarga'" style="display: none;" class="space-y-6">
                            <h3
                                class="text-lg font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-2">
                                Data Orang Tua / Wali</h3>

                            <div
                                class="bg-blue-50 dark:bg-blue-900/10 p-4 rounded-xl border border-blue-100 dark:border-blue-900/50">
                                <h4 class="font-bold text-blue-700 dark:text-blue-400 mb-3">Data Ayah Kandung</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="col-span-3 md:col-span-1">
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase">Nama
                                            Ayah</label>
                                        <input type="text" name="nama_ayah"
                                            value="{{ old('nama_ayah', $student->student->family->nama_ayah ?? '') }}"
                                            class="mt-1 w-full rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase">NIK
                                            Ayah</label>
                                        <input type="text" name="nik_ayah"
                                            value="{{ old('nik_ayah', $student->student->family->nik_ayah ?? '') }}"
                                            class="mt-1 w-full rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase">Tahun
                                            Lahir</label>
                                        <input type="text" name="tahun_lahir_ayah"
                                            value="{{ old('tahun_lahir_ayah', $student->student->family->tahun_lahir_ayah ?? '') }}"
                                            class="mt-1 w-full rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-slate-500 uppercase">Pendidikan</label>
                                        <input type="text" name="pendidikan_ayah"
                                            value="{{ old('pendidikan_ayah', $student->student->family->pendidikan_ayah ?? '') }}"
                                            class="mt-1 w-full rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-slate-500 uppercase">Pekerjaan</label>
                                        <input type="text" name="pekerjaan_ayah"
                                            value="{{ old('pekerjaan_ayah', $student->student->family->pekerjaan_ayah ?? '') }}"
                                            class="mt-1 w-full rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-slate-500 uppercase">Penghasilan</label>
                                        <input type="text" name="penghasilan_ayah"
                                            value="{{ old('penghasilan_ayah', $student->student->family->penghasilan_ayah ?? '') }}"
                                            class="mt-1 w-full rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                </div>
                            </div>

                            <div
                                class="bg-pink-50 dark:bg-pink-900/10 p-4 rounded-xl border border-pink-100 dark:border-pink-900/50">
                                <h4 class="font-bold text-pink-700 dark:text-pink-400 mb-3">Data Ibu Kandung</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="col-span-3 md:col-span-1">
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase">Nama
                                            Ibu</label>
                                        <input type="text" name="nama_ibu"
                                            value="{{ old('nama_ibu', $student->student->family->nama_ibu ?? '') }}"
                                            class="mt-1 w-full rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase">NIK
                                            Ibu</label>
                                        <input type="text" name="nik_ibu"
                                            value="{{ old('nik_ibu', $student->student->family->nik_ibu ?? '') }}"
                                            class="mt-1 w-full rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase">Tahun
                                            Lahir</label>
                                        <input type="text" name="tahun_lahir_ibu"
                                            value="{{ old('tahun_lahir_ibu', $student->student->family->tahun_lahir_ibu ?? '') }}"
                                            class="mt-1 w-full rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-slate-500 uppercase">Pendidikan</label>
                                        <input type="text" name="pendidikan_ibu"
                                            value="{{ old('pendidikan_ibu', $student->student->family->pendidikan_ibu ?? '') }}"
                                            class="mt-1 w-full rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-slate-500 uppercase">Pekerjaan</label>
                                        <input type="text" name="pekerjaan_ibu"
                                            value="{{ old('pekerjaan_ibu', $student->student->family->pekerjaan_ibu ?? '') }}"
                                            class="mt-1 w-full rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-slate-500 uppercase">Penghasilan</label>
                                        <input type="text" name="penghasilan_ibu"
                                            value="{{ old('penghasilan_ibu', $student->student->family->penghasilan_ibu ?? '') }}"
                                            class="mt-1 w-full rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                </div>
                            </div>

                            <div
                                class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                                <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-3">Data Wali (Opsional)</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="col-span-3 md:col-span-1">
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase">Nama
                                            Wali</label>
                                        <input type="text" name="nama_wali"
                                            value="{{ old('nama_wali', $student->student->family->nama_wali ?? '') }}"
                                            class="mt-1 w-full rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase">NIK
                                            Wali</label>
                                        <input type="text" name="nik_wali"
                                            value="{{ old('nik_wali', $student->student->family->nik_wali ?? '') }}"
                                            class="mt-1 w-full rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase">Tahun
                                            Lahir</label>
                                        <input type="text" name="tahun_lahir_wali"
                                            value="{{ old('tahun_lahir_wali', $student->student->family->tahun_lahir_wali ?? '') }}"
                                            class="mt-1 w-full rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div x-show="tab === 'finansial'" style="display: none;" class="space-y-6">
                            <h3
                                class="text-lg font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-2">
                                Kesejahteraan & Finansial</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div
                                    class="space-y-4 bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="penerima_kps" id="kps" value="1" {{
                                            old('penerima_kps', $student->student->financial->penerima_kps ?? false) ?
                                        'checked' : '' }} class="rounded border-slate-300 text-indigo-600">
                                        <label for="kps"
                                            class="ml-2 font-bold text-sm text-slate-700 dark:text-slate-300">Penerima
                                            KPS/PKH</label>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase">No. KPS /
                                            PKH</label>
                                        <input type="text" name="no_kps"
                                            value="{{ old('no_kps', $student->student->financial->no_kps ?? '') }}"
                                            class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase">No. KKS</label>
                                        <input type="text" name="nomor_kks"
                                            value="{{ old('nomor_kks', $student->student->financial->nomor_kks ?? '') }}"
                                            class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                </div>

                                <div
                                    class="space-y-4 bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="penerima_kip" id="kip" value="1" {{
                                            old('penerima_kip', $student->student->financial->penerima_kip ?? false) ?
                                        'checked' : '' }} class="rounded border-slate-300 text-indigo-600">
                                        <label for="kip"
                                            class="ml-2 font-bold text-sm text-slate-700 dark:text-slate-300">Penerima
                                            KIP</label>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase">Nomor
                                            KIP</label>
                                        <input type="text" name="nomor_kip"
                                            value="{{ old('nomor_kip', $student->student->financial->nomor_kip ?? '') }}"
                                            class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase">Nama tertera di
                                            KIP</label>
                                        <input type="text" name="nama_di_kip"
                                            value="{{ old('nama_di_kip', $student->student->financial->nama_di_kip ?? '') }}"
                                            class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                    <hr class="border-slate-200 dark:border-slate-600">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="layak_pip" id="pip" value="1" {{ old('layak_pip',
                                            $student->student->financial->layak_pip ?? false) ? 'checked' : '' }}
                                        class="rounded border-slate-300 text-indigo-600">
                                        <label for="pip"
                                            class="ml-2 font-bold text-sm text-slate-700 dark:text-slate-300">Layak
                                            PIP</label>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase">Alasan Layak
                                            PIP</label>
                                        <input type="text" name="alasan_layak_pip"
                                            value="{{ old('alasan_layak_pip', $student->student->financial->alasan_layak_pip ?? '') }}"
                                            class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                </div>

                                <div
                                    class="col-span-1 md:col-span-2 space-y-4 bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                                    <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-2">Informasi Rekening
                                        Bank</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase">Bank</label>
                                            <input type="text" name="bank"
                                                value="{{ old('bank', $student->student->financial->bank ?? '') }}"
                                                placeholder="BRI / BNI / dll"
                                                class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase">Nomor
                                                Rekening</label>
                                            <input type="text" name="nomor_rekening_bank"
                                                value="{{ old('nomor_rekening_bank', $student->student->financial->nomor_rekening_bank ?? '') }}"
                                                class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase">Atas
                                                Nama</label>
                                            <input type="text" name="rekening_atas_nama"
                                                value="{{ old('rekening_atas_nama', $student->student->financial->rekening_atas_nama ?? '') }}"
                                                class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div x-show="tab === 'kesehatan'" style="display: none;" class="space-y-4">
                            <h3
                                class="text-lg font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-2">
                                Data Periodik & Kesehatan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Tinggi Badan
                                        (cm)</label>
                                    <input type="number" step="0.1" name="tinggi_badan"
                                        value="{{ old('tinggi_badan', $student->student->health->tinggi_badan ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Berat Badan
                                        (kg)</label>
                                    <input type="number" step="0.1" name="berat_badan"
                                        value="{{ old('berat_badan', $student->student->health->berat_badan ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Lingkar Kepala
                                        (cm)</label>
                                    <input type="number" step="0.1" name="lingkar_kepala"
                                        value="{{ old('lingkar_kepala', $student->student->health->lingkar_kepala ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>