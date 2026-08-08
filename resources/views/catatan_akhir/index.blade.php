<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
            Pengisian Catatan Akhir Siswa (Raport)
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filter Multi-Tenant untuk Superadmin --}}
            @role('superadmin')
            <div
                class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <form action="{{ route('catatan_akhir.index') }}" method="GET">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Pilih Sekolah</label>
                    <select name="school_id" onchange="this.form.submit()"
                        class="w-full md:w-1/2 rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500">
                        <option value="">-- Pilih Sekolah --</option>
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
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">Pilih Sekolah Terlebih Dahulu</h3>
                <p class="text-slate-500 mt-2">Pilih sekolah di atas untuk mulai mengisi catatan akhir siswa.</p>
            </div>

            @elseif(!$activeYear)
            <div
                class="text-center py-16 bg-amber-50 dark:bg-amber-900/20 rounded-2xl border-2 border-dashed border-amber-200 dark:border-amber-800">
                <h3 class="text-xl font-bold text-amber-700 dark:text-amber-400">Tahun Ajaran Belum Diatur</h3>
                <p class="text-amber-600 dark:text-amber-500 mt-2">Tidak ada Tahun Ajaran yang berstatus aktif di
                    sekolah ini.</p>
            </div>

            @else
            {{-- Panel Filter Kelas --}}
            <div
                class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Tahun Ajaran Aktif</span>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white">{{ $activeYear->tahun_ajaran }} - {{
                        $activeYear->semester }}</h3>
                </div>

                @if($allClassrooms->count() > 0)
                <form action="{{ route('catatan_akhir.index') }}" method="GET" class="w-full md:w-auto">
                    @if(auth()->user()->hasRole('superadmin'))
                    <input type="hidden" name="school_id" value="{{ $selectedSchoolId }}">
                    @endif
                    <select name="classroom_id" onchange="this.form.submit()"
                        class="w-full min-w-[250px] rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($allClassrooms as $cls)
                        <option value="{{ $cls->id }}" {{ request('classroom_id')==$cls->id ? 'selected' : '' }}>
                            Kelas {{ $cls->tingkat }} - {{ $cls->nama_kelas }}
                        </option>
                        @endforeach
                    </select>
                </form>
                @else
                <p class="text-sm font-bold text-red-500">Anda belum ditugaskan sebagai Wali Kelas pada tahun ajaran
                    ini.</p>
                @endif
            </div>

            {{-- Tabel Daftar Siswa --}}
            @if($selectedClassroom)
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div
                    class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h4 class="font-black text-slate-800 dark:text-white uppercase tracking-tight">Daftar Siswa - Kelas
                        {{ $selectedClassroom->nama_kelas }}</h4>
                    <span class="text-xs font-bold text-slate-500 italic">Total {{ $students->count() }} Siswa</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead
                            class="text-xs text-slate-500 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-900/30">
                            <tr>
                                <th class="px-6 py-4 text-center w-16">No</th>
                                <th class="px-6 py-4">NIS / NISN</th>
                                <th class="px-6 py-4">Nama Lengkap</th>
                                <th class="px-6 py-4 text-center">L/P</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($students as $index => $student)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition duration-150">
                                <td class="px-6 py-4 text-center text-slate-500 font-medium">{{ $index + 1 }}</td>

                                <td class="px-6 py-4 font-mono text-slate-600 dark:text-slate-400">
                                    {{ $student->nis ?? '-' }} / {{ $student->nisn ?? '-' }}
                                </td>

                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">
                                    {{ $student->nama_lengkap ?? $student->nama }}
                                </td>

                                <td class="px-6 py-4 text-center text-slate-600 dark:text-slate-400 font-medium">
                                    {{ $student->jenis_kelamin ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('catatan_akhir.edit', ['student_id' => $student->id, 'classroom_id' => $selectedClassroom->id]) }}"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-100 text-indigo-700 hover:bg-indigo-600 hover:text-white rounded-lg font-bold transition-colors text-xs shadow-sm">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                        Buat Catatan
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                            </path>
                                        </svg>
                                        <span class="text-sm font-medium">Belum ada siswa yang terdaftar di kelas
                                            ini.</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @elseif(request('classroom_id'))
            {{-- Jika ID kelas dimanipulasi dari URL dan tidak sesuai hak akses --}}
            <div
                class="text-center py-10 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <p class="text-red-500 font-bold">Kelas tidak ditemukan atau Anda tidak memiliki akses ke kelas
                    tersebut.</p>
            </div>
            @else
            {{-- Tampilan saat kelas belum dipilih --}}
            <div
                class="text-center py-20 bg-white dark:bg-slate-800 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700">
                <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                    </path>
                </svg>
                <h3 class="text-xl font-bold text-slate-700 dark:text-slate-300">Pilih Kelas</h3>
                <p class="text-slate-500 mt-2">Silakan pilih kelas melalui menu dropdown di atas untuk menampilkan
                    daftar siswa.</p>
            </div>
            @endif
            @endif

        </div>
    </div>
</x-app-layout>