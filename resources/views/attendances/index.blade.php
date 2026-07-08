<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
            Rekapitulasi Absensi Semester
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @role('superadmin')
            <div
                class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <form action="{{ route('attendances.index') }}" method="GET">
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
                <p class="text-slate-500 mt-2">Pilih sekolah di atas untuk melihat laporan rekapitulasi absensinya.</p>
            </div>
            @elseif(!$activeYear)
            <div
                class="text-center py-16 bg-amber-50 dark:bg-amber-900/20 rounded-2xl border-2 border-dashed border-amber-200 dark:border-amber-800">
                <h3 class="text-xl font-bold text-amber-700 dark:text-amber-400">Tahun Ajaran Belum Diatur</h3>
                <p class="text-amber-600 dark:text-amber-500 mt-2">Tidak ada Tahun Ajaran yang berstatus aktif
                    (is_active = true) di sekolah ini.</p>
            </div>
            @else
            <div
                class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex flex-col md:flex-row md:items-center gap-4 md:gap-6 w-full md:w-auto">
                    <div>
                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Tahun Ajaran
                            Aktif</span>
                        <h3 class="text-lg font-black text-slate-800 dark:text-white">{{ $activeYear->tahun_ajaran }} -
                            {{ $activeYear->semester }}</h3>
                    </div>

                    @if(auth()->user()->hasRole('guru'))
                    @php
                    // Cari kelas yang diampu guru ini pada tahun ajaran aktif
                    $myClass = \App\Models\Classroom::where('homeroom_teacher_id', auth()->user()->employee->id ?? 0)
                    ->where('academic_year_id', $activeYear->id ?? 0)
                    ->first();
                    @endphp

                    @if($myClass)
                    <a href="{{ route('attendances.show', $myClass->id) }}"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-bold shadow-md hover:bg-indigo-700 transition transform hover:-translate-y-0.5 w-full md:w-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        Input Absen Hari Ini
                    </a>
                    @endif
                    @endif
                </div>

                @if($allClassrooms->count() > 1 || !auth()->user()->hasRole('guru'))
                <form action="{{ route('attendances.index') }}" method="GET" class="flex gap-2 w-full md:w-auto">
                    @if(auth()->user()->hasRole('superadmin'))
                    <input type="hidden" name="school_id" value="{{ $selectedSchoolId }}">
                    @endif
                    <select name="classroom_id" onchange="this.form.submit()"
                        class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500 flex-1">
                        <option value="">Semua Kelas</option>
                        @foreach($allClassrooms as $cls)
                        <option value="{{ $cls->id }}" {{ request('classroom_id')==$cls->id ? 'selected' : '' }}>
                            Kelas {{ $cls->tingkat }} - {{ $cls->nama_kelas }}
                        </option>
                        @endforeach
                    </select>
                </form>
                @endif

            </div>

            @forelse($reportData as $namaKelas => $siswas)
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div
                    class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h4 class="font-black text-slate-800 dark:text-white uppercase tracking-tight">Kelas {{ $namaKelas
                        }}</h4>
                    <span class="text-xs font-bold text-slate-500 italic">Total {{ count($siswas) }} Siswa</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead
                            class="text-[10px] text-slate-500 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-900/30">
                            <tr>
                                <th class="px-6 py-3">Nama Siswa</th>

                                <th class="px-6 py-3 text-center bg-amber-50/30 text-amber-700">S</th>
                                <th class="px-6 py-3 text-center bg-blue-50/30 text-blue-700">I</th>
                                <th class="px-6 py-3 text-center bg-rose-50/30 text-rose-700">A</th>
                                <th class="px-6 py-3 text-right">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($siswas as $data)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition">
                                <td class="px-6 py-4">
                                    <span class="block font-bold text-slate-800 dark:text-slate-200">{{ $data['nama']
                                        }}</span>
                                    <span class="text-[10px] text-slate-400">NISN: {{ $data['nisn'] ?? '-' }}</span>
                                </td>

                                <td class="px-6 py-4 text-center font-bold text-amber-500">{{ $data['sakit'] }}</td>
                                <td class="px-6 py-4 text-center font-bold text-blue-500">{{ $data['izin'] }}</td>
                                <td class="px-6 py-4 text-center font-bold text-rose-500">{{ $data['alfa'] }}</td>

                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('attendances.student-report', $data['id']) }}"
                                        class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg inline-block transition"
                                        title="Lihat Riwayat">
                                        Lihat &rarr;
                                    </a>
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
                <p class="text-slate-500">Belum ada kelas atau data siswa pada Tahun Ajaran ini.</p>
            </div>
            @endforelse
            @endif

        </div>
    </div>
</x-app-layout>