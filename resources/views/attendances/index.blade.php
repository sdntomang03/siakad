<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">
            Rekapitulasi Absensi Semester
        </h2>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto max-w-screen-2xl space-y-6">

            @role('superadmin')
            <div
                class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                <form action="{{ route('attendances.index') }}" method="GET">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Pilih Sekolah</label>
                    <select name="school_id" onchange="this.form.submit()"
                        class="min-h-11 w-full rounded-md border-slate-300 text-sm focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-700 md:w-1/2">
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
                class="rounded-lg border border-slate-200 bg-white px-5 py-12 text-center dark:border-slate-700 dark:bg-slate-800">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">Pilih Sekolah Terlebih Dahulu</h3>
                <p class="text-slate-500 mt-2">Pilih sekolah di atas untuk melihat laporan rekapitulasi absensinya.</p>
            </div>
            @elseif(!$activeYear)
            <div
                class="rounded-lg border border-amber-200 bg-amber-50 px-5 py-12 text-center dark:border-amber-800 dark:bg-amber-900/20">
                <h3 class="text-xl font-bold text-amber-700 dark:text-amber-400">Tahun Ajaran Belum Diatur</h3>
                <p class="text-amber-600 dark:text-amber-500 mt-2">Tidak ada Tahun Ajaran yang berstatus aktif
                    (is_active = true) di sekolah ini.</p>
            </div>
            @else
            <div
                class="flex flex-col items-start justify-between gap-4 rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 md:flex-row md:items-center">
                <div class="flex flex-col md:flex-row md:items-center gap-4 md:gap-6 w-full md:w-auto">
                    <div>
                        <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Tahun Ajaran
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
                    <a href="{{ route('attendances.show', $myClass->hashid) }}"
                        class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-brand-800 md:w-auto">
                        <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        class="min-h-11 flex-1 rounded-md border-slate-300 text-sm focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-700">
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
                class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                <div
                    class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-700 dark:bg-slate-900/50 sm:px-6">
                    <h4 class="font-semibold text-slate-950 dark:text-white">Kelas {{ $namaKelas
                        }}</h4>
                    <span class="text-xs font-bold text-slate-500 italic">Total {{ count($siswas) }} Siswa</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead
                            class="text-[10px] text-slate-500 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-900/30">
                            <tr>
                                <th class="px-6 py-3">No</th>
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
                                <td class="px-6 py-4">{{ $loop->iteration }}</td>
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
                                        class="inline-flex min-h-11 items-center rounded-md px-3 text-sm font-medium text-brand-800 underline underline-offset-4 hover:bg-brand-50 dark:text-brand-200 dark:hover:bg-brand-900"
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
                class="rounded-lg border border-slate-200 bg-white px-5 py-12 text-center dark:border-slate-700 dark:bg-slate-800">
                <p class="text-slate-500">Belum ada kelas atau data siswa pada Tahun Ajaran ini.</p>
            </div>
            @endforelse
            @endif

        </div>
    </div>
</x-app-layout>