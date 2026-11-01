<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">
            Rekap Absensi <span class="text-brand-600">Bulanan</span>
        </h2>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-[98rem] space-y-5 px-4 sm:px-6 lg:px-8">

            <div
                class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                <form action="{{ route('attendances.monthly') }}" method="GET"
                    class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 lg:grid-cols-[minmax(0,1.3fr)_minmax(0,0.8fr)_minmax(0,0.7fr)_auto]">

                    <div>
                        <label for="classroom_id" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Pilih Kelas</label>
                        <select id="classroom_id" name="classroom_id"
                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                            required>
                            <option value="">-- Kelas --</option>
                            @foreach($classrooms as $kelas)
                            <option value="{{ $kelas->id }}" {{ request('classroom_id')==$kelas->id ? 'selected' : ''
                                }}>
                                {{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="month" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Bulan</label>
                        <select id="month" name="month"
                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                            required>
                            @foreach(range(1, 12) as $m)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $month==sprintf('%02d', $m) ? 'selected' : ''
                                }}>
                                {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="year" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Tahun</label>
                        <select id="year" name="year"
                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                            required>
                            @for($y = date('Y') + 1; $y >= date('Y') - 3; $y--)
                            <option value="{{ $y }}" {{ $year==$y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <button type="submit"
                        class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                        Tampilkan Data
                    </button>
                </form>
            </div>

            @if($selectedClassroom)
            <div
                class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">

                <div
                    class="p-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-black text-slate-800 dark:text-white">Matriks Kehadiran Siswa</h3>
                        <p class="text-xs text-slate-500 font-bold mt-1">Kelas: {{ $selectedClassroom->nama_kelas }} |
                            Periode: {{ \Carbon\Carbon::createFromDate($year, $month, 1)->locale('id')->isoFormat('MMMM
                            YYYY') }}</p>
                    </div>

                    <!-- Tombol Download PDF -->
                    <a href="{{ route('attendances.monthly.pdf', request()->all()) }}"
                        class="inline-flex min-h-11 items-center rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download PDF
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm whitespace-nowrap">
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

                                if($status == 'hadir') {
                                $textClass = 'text-emerald-600 font-bold';
                                $label = 'H';
                                } elseif($status == 'sakit') {
                                $textClass = 'text-amber-500 font-bold';
                                $label = 'S';
                                } elseif($status == 'izin') {
                                $textClass = 'text-blue-500 font-bold';
                                $label = 'I';
                                } elseif($status == 'alfa') {
                                $textClass = 'text-rose-600 font-bold';
                                $label = 'A';
                                } elseif(!$isDayOff && !$status) {
                                // LOGIKA BARU: Jika hari kerja & kosong, anggap Hadir dengan tanda titik
                                $textClass = 'text-emerald-600 font-black text-lg';
                                $label = 'H';
                                }

                                $bgClass = $isDayOff ? 'bg-rose-50 dark:bg-rose-900/10' : '';
                                @endphp

                                <td title="{{ $dayOffTitle }}"
                                    class="px-1 py-2 text-center border-r border-slate-100 dark:border-slate-700 {{ $bgClass }}">
                                    @if($isDayOff && !$status)
                                    <span
                                        class="text-rose-300 dark:text-rose-800/50 text-xs font-black cursor-help">L</span>
                                    @else
                                    <span class="{{ $textClass }}">{{ $label }}</span>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>

                        <tfoot
                            class="bg-slate-50 dark:bg-slate-800/90 font-bold text-xs border-t-2 border-slate-200 dark:border-slate-700">
                            <tr>
                                <td
                                    class="sticky left-0 z-10 bg-slate-50 dark:bg-slate-800/90 border-r border-slate-200 dark:border-slate-700">
                                </td>
                                <td
                                    class="px-4 py-2 sticky left-10 z-10 bg-slate-50 dark:bg-slate-800/90 border-r border-slate-200 dark:border-slate-700 text-right text-amber-500 uppercase tracking-wide">
                                    Total Sakit (S)
                                </td>
                                @foreach($dates as $day => $info)
                                @php
                                $countS = 0;
                                foreach($students as $student) {
                                if(($attendanceData[$student->id][$day] ?? null) == 'sakit') $countS++;
                                }
                                $isDayOff = $info['is_weekend'] || $info['is_holiday'];
                                $bgClass = $isDayOff ? 'bg-rose-50 dark:bg-rose-900/10' : '';
                                @endphp
                                <td
                                    class="px-1 py-2 text-center border-r border-slate-200 dark:border-slate-700 text-amber-500 {{ $bgClass }}">
                                    {{ $countS > 0 ? $countS : '-' }}
                                </td>
                                @endforeach
                            </tr>

                            <tr>
                                <td
                                    class="sticky left-0 z-10 bg-slate-50 dark:bg-slate-800/90 border-r border-slate-200 dark:border-slate-700">
                                </td>
                                <td
                                    class="px-4 py-2 sticky left-10 z-10 bg-slate-50 dark:bg-slate-800/90 border-r border-slate-200 dark:border-slate-700 text-right text-blue-500 uppercase tracking-wide">
                                    Total Izin (I)
                                </td>
                                @foreach($dates as $day => $info)
                                @php
                                $countI = 0;
                                foreach($students as $student) {
                                if(($attendanceData[$student->id][$day] ?? null) == 'izin') $countI++;
                                }
                                $isDayOff = $info['is_weekend'] || $info['is_holiday'];
                                $bgClass = $isDayOff ? 'bg-rose-50 dark:bg-rose-900/10' : '';
                                @endphp
                                <td
                                    class="px-1 py-2 text-center border-r border-slate-200 dark:border-slate-700 text-blue-500 {{ $bgClass }}">
                                    {{ $countI > 0 ? $countI : '-' }}
                                </td>
                                @endforeach
                            </tr>

                            <tr>
                                <td
                                    class="sticky left-0 z-10 bg-slate-50 dark:bg-slate-800/90 border-r border-slate-200 dark:border-slate-700 border-b">
                                </td>
                                <td
                                    class="px-4 py-2 sticky left-10 z-10 bg-slate-50 dark:bg-slate-800/90 border-r border-slate-200 dark:border-slate-700 border-b text-right text-rose-600 uppercase tracking-wide">
                                    Total Alfa (A)
                                </td>
                                @foreach($dates as $day => $info)
                                @php
                                $countA = 0;
                                foreach($students as $student) {
                                if(($attendanceData[$student->id][$day] ?? null) == 'alfa') $countA++;
                                }
                                $isDayOff = $info['is_weekend'] || $info['is_holiday'];
                                $bgClass = $isDayOff ? 'bg-rose-50 dark:bg-rose-900/10' : '';
                                @endphp
                                <td
                                    class="px-1 py-2 text-center border-r border-b border-slate-200 dark:border-slate-700 text-rose-600 {{ $bgClass }}">
                                    {{ $countA > 0 ? $countA : '-' }}
                                </td>
                                @endforeach
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
            @endif

        </div>
    </div>
</x-app-layout>