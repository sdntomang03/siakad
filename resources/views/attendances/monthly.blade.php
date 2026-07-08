<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
            Rekap Absensi <span class="text-indigo-600">Bulanan</span>
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-[98%] mx-auto sm:px-6 lg:px-8 space-y-6">

            <div
                class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <form action="{{ route('attendances.monthly') }}" method="GET"
                    class="flex flex-wrap md:flex-nowrap gap-4 items-end">

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Pilih Kelas</label>
                        <select name="classroom_id"
                            class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 text-sm focus:ring-indigo-500 w-48"
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
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Bulan</label>
                        <select name="month"
                            class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 text-sm focus:ring-indigo-500 w-32"
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
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Tahun</label>
                        <select name="year"
                            class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 text-sm focus:ring-indigo-500 w-28"
                            required>
                            @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                            <option value="{{ $y }}" {{ $year==$y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-2.5 h-[42px] rounded-lg font-bold shadow-sm hover:bg-indigo-700 transition uppercase text-xs">
                        Tampilkan Data
                    </button>
                </form>
            </div>

            @if($selectedClassroom)
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">

                <div
                    class="p-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-black text-slate-800 dark:text-white">Matriks Kehadiran Siswa</h3>
                        <p class="text-xs text-slate-500 font-bold mt-1">Kelas: {{ $selectedClassroom->nama_kelas }} |
                            Periode: {{ date('F Y', mktime(0,0,0, $month, 1, $year)) }}</p>
                    </div>
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
                                <th
                                    class="px-1 py-2 text-[10px] text-center border-b border-r border-slate-200 dark:border-slate-700 {{ $info['is_weekend'] ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-400' : 'text-slate-500' }}">
                                    {{ $info['day_name'] }}
                                </th>
                                @endforeach
                            </tr>

                            <tr>
                                @foreach($dates as $day => $info)
                                <th
                                    class="px-1 py-1 text-xs text-center font-black border-r border-slate-200 dark:border-slate-700 {{ $info['is_weekend'] ? 'bg-rose-500 text-white dark:bg-rose-600' : 'text-slate-700 dark:text-slate-300' }}">
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
                                // Ambil status absensi di hari ini, jika kosong tampilkan strip atau titik
                                $status = $attendanceData[$student->id][$day] ?? null;

                                // Styling text berdasarkan status
                                $textClass = 'text-slate-300 dark:text-slate-600'; // Default jika kosong
                                $label = '-';

                                if($status == 'hadir') { $textClass = 'text-emerald-600 font-bold'; $label = 'H'; }
                                elseif($status == 'sakit') { $textClass = 'text-amber-500 font-bold'; $label = 'S'; }
                                elseif($status == 'izin') { $textClass = 'text-blue-500 font-bold'; $label = 'I'; }
                                elseif($status == 'alfa') { $textClass = 'text-rose-600 font-bold'; $label = 'A'; }

                                // Jika hari ini adalah weekend, berikan background merah pucat
                                $bgClass = $info['is_weekend'] ? 'bg-rose-50 dark:bg-rose-900/10' : '';
                                @endphp

                                <td
                                    class="px-1 py-2 text-center border-r border-slate-100 dark:border-slate-700 {{ $bgClass }}">
                                    @if($info['is_weekend'] && !$status)
                                    <span class="text-rose-200 dark:text-rose-800/50 text-[10px]">L</span>
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

                <div
                    class="p-4 bg-slate-50 dark:bg-slate-800/80 text-xs text-slate-500 border-t border-slate-200 dark:border-slate-700 flex gap-4">
                    <span><b>Keterangan:</b></span>
                    <span class="text-emerald-600 font-bold">H: Hadir</span>
                    <span class="text-amber-500 font-bold">S: Sakit</span>
                    <span class="text-blue-500 font-bold">I: Izin</span>
                    <span class="text-rose-600 font-bold">A: Alfa</span>
                    <span class="text-rose-400 font-bold">L: Libur (Weekend)</span>
                </div>

            </div>
            @endif

        </div>
    </div>
</x-app-layout>