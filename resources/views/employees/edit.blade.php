<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">
                Profil Kepegawaian: <span class="text-brand-600">{{ $user->name }}</span>
            </h2>

            @if(auth()->user()->hasRole('superadmin|operator'))
            <a href="{{ route('operator.users.index') }}" class="text-sm font-bold text-slate-500 hover:text-slate-700">
                &larr; Kembali ke Daftar Pengguna
            </a>
            @else
            <a href="{{ route('dashboard') }}" class="text-sm font-bold text-slate-500 hover:text-slate-700">
                &larr; Kembali ke Dashboard
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <form action="{{ route('employees.update', $user->id) }}" method="POST">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <div class="lg:col-span-2 space-y-6">
                        <div
                            class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                            <h3
                                class="text-lg font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-2 mb-4">
                                Identitas & Kepegawaian</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="col-span-full">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                        class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                                        required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Jenis
                                        Kelamin</label>
                                    <select name="jenis_kelamin"
                                        class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                                        required>
                                        <option value="L" {{ old('jenis_kelamin', $user->employee->jenis_kelamin ?? '')
                                            == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin', $user->employee->jenis_kelamin ?? '')
                                            == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Kategori
                                        Pegawai</label>
                                    <input type="text" name="kategori_pegawai"
                                        value="{{ old('kategori_pegawai', $user->employee->kategori_pegawai ?? '') }}"
                                        placeholder="Contoh: Guru Kelas, Tenaga Administrasi"
                                        class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                                        required>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">NIP</label>
                                    <input type="text" name="nip" value="{{ old('nip', $user->employee->nip ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">NUPTK</label>
                                    <input type="text" name="nuptk"
                                        value="{{ old('nuptk', $user->employee->nuptk ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Status
                                        Kepegawaian</label>
                                    <input type="text" name="status_kepegawaian"
                                        value="{{ old('status_kepegawaian', $user->employee->status_kepegawaian ?? '') }}"
                                        placeholder="PNS, PPPK, Honorer"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Tugas
                                        Tambahan</label>
                                    <input type="text" name="tugas_tambahan"
                                        value="{{ old('tugas_tambahan', $user->employee->tugas_tambahan ?? '') }}"
                                        placeholder="Kepala Sekolah, Bendahara BOS"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div
                            class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                            <h3
                                class="text-lg font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-2 mb-4">
                                Alamat Domisili</h3>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Jalan / Detail
                                        Alamat</label>
                                    <textarea name="alamat" rows="2"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">{{ old('alamat', $user->employee->alamat ?? '') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">RT</label>
                                    <input type="text" name="rt" value="{{ old('rt', $user->employee->rt ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase">RW</label>
                                    <input type="text" name="rw" value="{{ old('rw', $user->employee->rw ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Dusun</label>
                                    <input type="text" name="dusun"
                                        value="{{ old('dusun', $user->employee->dusun ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Kelurahan</label>
                                    <input type="text" name="kelurahan"
                                        value="{{ old('kelurahan', $user->employee->kelurahan ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Kecamatan</label>
                                    <input type="text" name="kecamatan"
                                        value="{{ old('kecamatan', $user->employee->kecamatan ?? '') }}"
                                        class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                </div>
                            </div>
                        </div>

                        <div
                            class="rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/50">
                            <button type="submit"
                                class="inline-flex min-h-11 w-full items-center justify-center rounded-md bg-brand-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>