<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
            Manajemen Mata Pelajaran
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @role('superadmin')
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <form action="{{ route('subjects.index') }}" method="GET">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Pilih Sekolah</label>
                <select name="school_id" onchange="this.form.submit()"
                    class="w-full md:w-1/2 rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500">
                    <option value="">-- Pilih Sekolah Terlebih Dahulu --</option>
                    @foreach($schools as $sekolah)
                    <option value="{{ $sekolah->id }}" {{ $selectedSchoolId==$sekolah->id ? 'selected' : '' }}>
                        {{ $sekolah->npsn }} - {{ $sekolah->nama_sekolah }}
                    </option>
                    @endforeach
                </select>
            </form>
        </div>
        @endrole

        @if(auth()->user()->hasRole('superadmin') && !$selectedSchoolId)
        <div
            class="text-center py-16 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <h3 class="text-xl font-bold text-slate-800 dark:text-white">Pilih Sekolah</h3>
            <p class="text-slate-500 mt-2">Pilih sekolah di atas untuk mengelola mata pelajarannya.</p>
        </div>
        @else

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-1">
                <div
                    class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 sticky top-6">
                    <h3 class="text-lg font-black text-slate-800 dark:text-white mb-1">Tambah Mapel Baru</h3>
                    <p class="text-xs text-slate-500 mb-6">Tambahkan struktur mata pelajaran untuk sekolah ini.</p>

                    <form action="{{ route('subjects.store') }}" method="POST" class="space-y-4">
                        @csrf
                        @if(auth()->user()->hasRole('superadmin'))
                        <input type="hidden" name="school_id" value="{{ $selectedSchoolId }}">
                        @endif

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Pilih Tingkat
                                Kelas</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @for($i = 1; $i <= 6; $i++) <label
                                    class="flex items-center gap-2 p-2 border border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                    <input type="checkbox" name="tingkat[]" value="{{ $i }}"
                                        class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4 cursor-pointer">
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Kelas {{ $i
                                        }}</span>
                                    </label>
                                    @endfor
                            </div>
                            @error('tingkat') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Mata
                                Pelajaran</label>
                            <input type="text" name="nama_mapel" placeholder="Cth: Ilmu Pengetahuan Alam dan Sosial"
                                required
                                class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500">
                            @error('nama_mapel') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kode
                                    (Opsional)</label>
                                <input type="text" name="kode_mapel" placeholder="Cth: IPAS"
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nilai KKM</label>
                                <input type="number" name="kkm" value="75" min="0" max="100" required
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm font-bold text-slate-700 focus:ring-indigo-500">
                            </div>

                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Siapa yang
                                Mengajar?</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label
                                    class="flex items-center gap-2 p-3 border border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                    <input type="radio" name="pengampu" value="guru_kelas" checked
                                        class="text-indigo-600 focus:ring-indigo-500">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Wali
                                            Kelas</span>
                                        <span class="text-[10px] text-slate-500">Otomatis diisi Wali Kelas</span>
                                    </div>
                                </label>
                                <label
                                    class="flex items-center gap-2 p-3 border border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                    <input type="radio" name="pengampu" value="guru_mapel"
                                        class="text-indigo-600 focus:ring-indigo-500">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Guru
                                            Mapel</span>
                                        <span class="text-[10px] text-slate-500">Cth: Agama, PJOK, Mulok</span>
                                    </div>
                                </label>
                            </div>
                            @error('pengampu') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="pt-4">
                            <button type="submit"
                                class="w-full px-4 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-black shadow-lg hover:bg-indigo-700 transition transform hover:-translate-y-0.5">
                                + Simpan Mata Pelajaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                @forelse($subjects as $tingkat => $mapels)
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div
                        class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-lg flex items-center justify-center font-black">
                                {{ $tingkat }}
                            </div>
                            <h4 class="font-black text-slate-800 dark:text-white uppercase tracking-tight">Tingkat Kelas
                                {{ $tingkat }}</h4>
                        </div>
                        <span
                            class="text-xs font-bold text-slate-500 bg-white dark:bg-slate-800 px-2 py-1 rounded-md border border-slate-200 dark:border-slate-700">{{
                            count($mapels) }} Mapel</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50/50 dark:bg-slate-900/30">
                                <tr>
                                    <th class="px-6 py-3 w-16">Kode</th>
                                    <th class="px-6 py-3">Nama Mata Pelajaran</th>
                                    <th class="px-6 py-3 text-center w-24">KKM</th>
                                    <th class="px-6 py-3 text-right w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach($mapels as $mapel)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition group">
                                    <td class="px-6 py-3 font-bold text-slate-400">{{ $mapel->kode_mapel ?? '-' }}</td>
                                    <td class="px-6 py-3 font-bold text-slate-700 dark:text-slate-200">{{
                                        $mapel->nama_mapel }}</td>
                                    <td class="px-6 py-3 text-center">
                                        <span
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 font-bold text-xs">
                                            {{ $mapel->kkm }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <form action="{{ route('subjects.destroy', $mapel->id) }}" method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus mapel {{ $mapel->nama_mapel }} untuk kelas {{ $tingkat }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-lg transition"
                                                title="Hapus Mapel">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @empty
                <div
                    class="py-20 bg-white dark:bg-slate-800 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 text-center">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                    <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300">Belum Ada Mata Pelajaran</h3>
                    <p class="text-slate-500 mt-1 text-sm">Gunakan form di samping untuk mulai mendaftarkan mata
                        pelajaran.</p>
                </div>
                @endforelse
            </div>

        </div>
        @endif
    </div>
</x-app-layout>