<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">
                Pengolahan Nilai <span class="text-brand-700 dark:text-brand-400">Akhir Ijazah</span>
            </h2>
            <a href="{{ route('dashboard') }}" class="inline-flex min-h-10 items-center text-sm font-medium text-slate-600 transition hover:text-brand-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:text-slate-300 dark:hover:text-brand-300">
                &larr; Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-[95rem] space-y-5 px-4 sm:px-6 lg:px-8">

            <div
                class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <div
                    class="border-b border-slate-100 dark:border-slate-700 pb-4 mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-black text-slate-800 dark:text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                        </svg>
                        Pengaturan Kalkulasi Ijazah
                    </h3>
                    @if(count($ijazahSubjects) == 0)
                    <span class="bg-rose-100 text-rose-700 text-xs px-3 py-1 rounded-full font-bold">Belum Ada Mapel
                        Terdaftar</span>
                    @endif
                </div>

                <form action="{{ route('ijazah.index') }}" method="GET"
                    class="grid grid-cols-1 items-end gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="classroom_id" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Pilih Kelas 6</label>
                        <select id="classroom_id" name="classroom_id"
                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                            required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classrooms as $kelas)
                            <option value="{{ $kelas->id }}" {{ request('classroom_id')==$kelas->id ? 'selected' : ''
                                }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div
                        class="col-span-1 flex flex-col gap-4 rounded-md border border-brand-100 bg-brand-50/50 p-4 dark:border-brand-800 dark:bg-brand-900/20 sm:col-span-2 sm:flex-row lg:col-span-2">
                        <div class="w-full sm:w-1/2">
                            <label
                                for="bobot_rapor" class="mb-2 block text-sm font-medium text-brand-800 dark:text-brand-200">Bobot
                                Rapor (%)</label>
                            <input id="bobot_rapor" type="number" name="bobot_rapor" value="{{ $bobotRapor }}" min="0" max="100"
                                class="w-full rounded-md border-slate-300 bg-white text-center text-sm font-semibold text-slate-900 focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                                required>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelas 4, 5, dan 6 (Smt 7-12)</p>
                        </div>
                        <div class="w-full sm:w-1/2">
                            <label
                                for="bobot_ujian" class="mb-2 block text-sm font-medium text-brand-800 dark:text-brand-200">Bobot
                                Ujian (%)</label>
                            <input id="bobot_ujian" type="number" name="bobot_ujian" value="{{ $bobotUjian }}" min="0" max="100"
                                class="w-full rounded-md border-slate-300 bg-white text-center text-sm font-semibold text-slate-900 focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                                required>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Nilai Ujian Sekolah</p>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="inline-flex min-h-11 w-full items-center justify-center rounded-md bg-brand-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                            Proses Data Ijazah
                        </button>
                    </div>
                </form>
            </div>

            @if($selectedClassroom && count($ijazahSubjects) > 0)
            <div
                class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-black text-slate-800 dark:text-white">Matriks Nilai Akhir Ijazah</h3>
                        <p class="text-xs font-bold text-slate-500 mt-1">
                            Kelas: {{ $selectedClassroom->nama_kelas }} | Formula: (Rapor x {{ $bobotRapor }}%) + (Ujian
                            x {{ $bobotUjian }}%)
                        </p>
                    </div>
                    <button onclick="window.print()"
                        class="text-xs bg-slate-800 text-white px-4 py-2 rounded-lg font-bold shadow-sm hover:bg-slate-900 transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak Leger Ijazah
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm whitespace-nowrap">
                        <thead class="bg-slate-100 dark:bg-slate-900/50">
                            <tr>
                                <th rowspan="2"
                                    class="px-4 py-3 border-r border-slate-200 dark:border-slate-700 sticky left-0 z-20 bg-slate-100 dark:bg-slate-900 w-10 text-center text-xs">
                                    No</th>
                                <th rowspan="2"
                                    class="px-4 py-3 border-r border-slate-200 dark:border-slate-700 sticky left-10 z-20 bg-slate-100 dark:bg-slate-900 text-xs">
                                    Nama Siswa</th>

                                @foreach($ijazahSubjects as $mapel)
                                <th colspan="3"
                                    class="px-4 py-2 border-r border-b border-slate-200 dark:border-slate-700 text-center font-black text-emerald-700 dark:text-emerald-400 uppercase text-[11px] tracking-wider">
                                    {{ $mapel->nama_mapel }}
                                </th>
                                @endforeach
                            </tr>

                            <tr class="bg-slate-50 dark:bg-slate-800/80 text-[10px] uppercase font-bold text-slate-500">
                                @foreach($ijazahSubjects as $mapel)
                                <th class="px-2 py-2 text-center border-r border-slate-200 dark:border-slate-700 w-16">
                                    NR <span class="block font-normal lowercase">({{ $bobotRapor }}%)</span>
                                </th>
                                <th class="px-2 py-2 text-center border-r border-slate-200 dark:border-slate-700 w-16">
                                    NU <span class="block font-normal lowercase">({{ $bobotUjian }}%)</span>
                                </th>
                                <th
                                    class="px-2 py-2 text-center border-r border-slate-200 dark:border-slate-700 w-16 text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-slate-700">
                                    NA
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($students as $index => $student)
                            <tr class="hover:bg-emerald-50/30 dark:hover:bg-slate-700/30 transition">
                                <td
                                    class="px-4 py-2 text-center text-slate-500 border-r border-slate-100 dark:border-slate-700 sticky left-0 z-10 bg-white dark:bg-slate-800 font-bold">
                                    {{ $index + 1 }}
                                </td>
                                <td
                                    class="px-4 py-2 border-r border-slate-100 dark:border-slate-700 sticky left-10 z-10 bg-white dark:bg-slate-800 font-bold text-slate-700 dark:text-slate-300">
                                    {{ $student->nama_lengkap }}
                                </td>

                                @foreach($ijazahSubjects as $mapel)
                                @php
                                $data = $ijazahData[$student->id][$mapel->id];
                                $isBelowKKM = $data['final'] < $mapel->kkm;
                                    @endphp
                                    <td
                                        class="px-2 py-2 text-center border-r border-slate-100 dark:border-slate-700 text-slate-500">
                                        {{ number_format($data['avg_rapor'], 1) }}
                                    </td>
                                    <td
                                        class="px-2 py-2 text-center border-r border-slate-100 dark:border-slate-700 text-slate-500">
                                        {{ number_format($data['exam'], 1) }}
                                    </td>
                                    <td
                                        class="px-2 py-2 text-center border-r border-slate-200 dark:border-slate-700 font-black {{ $isBelowKKM ? 'text-rose-600 bg-rose-50' : 'text-slate-800 dark:text-white bg-slate-50 dark:bg-slate-900/30' }}">
                                        {{ number_format($data['final'], 2) }}
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
                    <span><b>NR:</b> Nilai Rata-rata Rapor (Kelas 4-6)</span>
                    <span><b>NU:</b> Nilai Ujian Sekolah</span>
                    <span><b>NA:</b> Nilai Akhir Ijazah</span>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>