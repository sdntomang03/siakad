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
                        <thead class="bg-slate-100 dark:bg-slate-900/50">
                            <tr>
                                <th rowspan="2"
                                    class="px-4 py-3 sticky left-0 z-20 bg-slate-100 dark:bg-slate-900 border-r border-slate-200 dark:border-slate-700 text-xs text-center w-10">
                                    No</th>
                                <th rowspan="2"
                                    class="px-4 py-3 sticky left-10 z-20 bg-slate-100 dark:bg-slate-900 border-r border-slate-200 dark:border-slate-700 text-xs text-left w-48">
                                    Nama Siswa</th>

                                @foreach($dates as $day => $info)
                                @php
                                // Cek apakah hari ini libur akhir pekan ATAU libur nasional
                                $isDayOff = $info['is_weekend'] || $info['is_holiday'];
                                $dayOffTitle = $info['is_holiday'] ? $info['holiday_name'] : ($info['is_weekend'] ?
                                'Libur Akhir Pekan' : '');
                                @endphp
                                <th title="{{ $dayOffTitle }}"
                                    class="px-1 py-2 text-[10px] text-center border-b border-r border-slate-200 dark:border-slate-700 {{ $isDayOff ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-400 cursor-help' : 'text-slate-500' }}">
                                    {{ $info['day_name'] }}
                                </th>
                                @endforeach
                            </tr>

                            <tr>
                                @foreach($dates as $day => $info)
                                @php
                                $isDayOff = $info['is_weekend'] || $info['is_holiday'];
                                $dayOffTitle = $info['is_holiday'] ? $info['holiday_name'] : ($info['is_weekend'] ?
                                'Libur Akhir Pekan' : '');
                                @endphp
                                <th title="{{ $dayOffTitle }}"
                                    class="px-1 py-1 text-xs text-center font-black border-r border-slate-200 dark:border-slate-700 {{ $isDayOff ? 'bg-rose-500 text-white dark:bg-rose-600 cursor-help' : 'text-slate-700 dark:text-slate-300' }}">
                                    {{ $day }}
                                </th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($students as $index => $student)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition">
                                <td
                                    class="px-4 py-2 sticky left-0 z-10 bg-white dark:bg-slate-800 border-r border-slate-100 dark:border-slate-700 text-center font-bold text-slate-400 text-xs">
                                    {{ $index + 1 }}
                                </td>
                                <td
                                    class="px-4 py-2 sticky left-10 z-10 bg-white dark:bg-slate-800 border-r border-slate-100 dark:border-slate-700 font-bold text-slate-700 dark:text-slate-300 text-xs">
                                    {{ $student->nama_lengkap }}
                                </td>

                                @foreach($dates as $day => $info)
                                @php
                                $status = $attendanceData[$student->id][$day] ?? null;
                                $isDayOff = $info['is_weekend'] || $info['is_holiday'];
                                $dayOffTitle = $info['is_holiday'] ? $info['holiday_name'] : ($info['is_weekend'] ?
                                'Libur Akhir Pekan' : '');

                                $textClass = 'text-slate-300 dark:text-slate-600';
                                $label = '-';

                                if($status == 'hadir') { $textClass = 'text-emerald-600 font-bold'; $label = 'H'; }
                                elseif($status == 'sakit') { $textClass = 'text-amber-500 font-bold'; $label = 'S'; }
                                elseif($status == 'izin') { $textClass = 'text-blue-500 font-bold'; $label = 'I'; }
                                elseif($status == 'alfa') { $textClass = 'text-rose-600 font-bold'; $label = 'A'; }

                                // Background merah muda untuk semua hari libur (nasional/weekend)
                                $bgClass = $isDayOff ? 'bg-rose-50 dark:bg-rose-900/10' : '';
                                @endphp

                                <td title="{{ $dayOffTitle }}"
                                    class="px-1 py-2 text-center border-r border-slate-100 dark:border-slate-700 {{ $bgClass }}">
                                    @if($isDayOff && !$status)
                                    <span class="text-rose-300 dark:text-rose-800/50 text-[10px] cursor-help">L</span>
                                    @else
                                    <span class="{{ $textClass }}">{{ $label }}</span>
                                    @endif
                                </td>
                                @endforeach
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