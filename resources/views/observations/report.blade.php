<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
            Laporan Hasil Observasi
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- HEADER LAPORAN -->
        <div
            class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white">{{ $assessment->keterangan }}</h3>
                <p class="text-sm text-slate-500 font-medium mt-1">
                    {{ $assessment->classroom->tingkat }} - {{ $assessment->classroom->nama_kelas }} |
                    {{ $assessment->subject->nama_mapel }} |
                    {{ \Carbon\Carbon::parse($assessment->tanggal)->format('d M Y') }}
                </p>
            </div>
            <div class="flex gap-2">
                <!-- Tombol Cetak PDF/Print -->
                <button onclick="window.print()"
                    class="px-4 py-2 bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold rounded-lg transition text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    Cetak
                </button>
                <a href="{{ route('assessments.index') }}"
                    class="px-4 py-2 bg-slate-800 text-white hover:bg-slate-700 font-bold rounded-lg transition text-sm">
                    Kembali
                </a>
            </div>
        </div>

        <!-- DAFTAR KRITERIA -->
        <div
            class="bg-emerald-50 dark:bg-emerald-900/20 p-5 rounded-2xl border border-emerald-100 dark:border-emerald-800/50">
            <h4 class="text-sm font-black text-emerald-800 dark:text-emerald-400 mb-3 uppercase tracking-wider">
                Keterangan Kriteria Observasi (Skala 1 - {{ $assessment->scale }})</h4>
            <ul class="space-y-2">
                @foreach($assessment->criteria as $index => $kriteria)
                <li class="flex items-start gap-3 text-sm text-slate-700 dark:text-slate-300">
                    <span class="font-black text-emerald-600 dark:text-emerald-500 shrink-0 w-8">K{{ $index + 1
                        }}</span>
                    <span>{{ $kriteria->descriptor }}</span>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- MATRIKS HASIL -->
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto print:overflow-visible">
                <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                    <thead class="text-xs text-slate-700 uppercase bg-slate-100 dark:bg-slate-900 dark:text-slate-300">
                        <tr>
                            <th class="px-4 py-4 text-center w-10">No</th>
                            <th class="px-4 py-4 w-64 border-r border-slate-200 dark:border-slate-700">Nama Siswa</th>

                            @foreach($assessment->criteria as $index => $kriteria)
                            <th class="px-2 py-4 text-center border-r border-slate-200 dark:border-slate-700"
                                title="{{ $kriteria->descriptor }}">
                                K{{ $index + 1 }}
                            </th>
                            @endforeach

                            <th
                                class="px-4 py-4 text-center bg-emerald-50/50 dark:bg-emerald-900/20 border-r border-slate-200 dark:border-slate-700">
                                Rata-rata</th>
                            <th
                                class="px-4 py-4 text-center bg-emerald-50/50 dark:bg-emerald-900/20 border-r border-slate-200 dark:border-slate-700">
                                Predikat</th>

                            <!-- TAMBAHAN KOLOM HEADER CATATAN -->
                            <th class="px-4 py-4 text-left w-64 bg-slate-50 dark:bg-slate-900/30">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $index => $siswa)
                        @php
                        $data = $reportData[$siswa->id];
                        @endphp
                        <tr
                            class="{{ $loop->last ? '' : 'border-b border-slate-100 dark:border-slate-700' }} hover:bg-slate-50 dark:hover:bg-slate-700/50">
                            <td class="px-4 py-3 font-bold text-center">{{ $index + 1 }}</td>
                            <td
                                class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200 border-r border-slate-100 dark:border-slate-700 truncate">
                                {{ $siswa->nama_lengkap }}
                            </td>

                            <!-- Nilai per Kriteria -->
                            @foreach($assessment->criteria as $kriteria)
                            <td
                                class="px-2 py-3 text-center border-r border-slate-100 dark:border-slate-700 font-semibold text-slate-700 dark:text-slate-300">
                                {{ $data['scores'][$kriteria->id] ?? '-' }}
                            </td>
                            @endforeach

                            <!-- Hasil Akhir -->
                            <td
                                class="px-4 py-3 text-center bg-emerald-50/30 dark:bg-emerald-900/10 border-r border-slate-100 dark:border-slate-700">
                                @if($data['is_assessed'])
                                <span class="font-black text-emerald-600 dark:text-emerald-400 text-base">{{
                                    number_format($data['average'], 2) }}</span>
                                @else
                                <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td
                                class="px-4 py-3 text-center bg-emerald-50/30 dark:bg-emerald-900/10 border-r border-slate-100 dark:border-slate-700">
                                @if($data['is_assessed'])
                                @php
                                $badgeColor = match($data['predikat']) {
                                'Sangat Baik' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'Baik' => 'bg-blue-100 text-blue-700 border-blue-200',
                                'Cukup' => 'bg-amber-100 text-amber-700 border-amber-200',
                                default => 'bg-rose-100 text-rose-700 border-rose-200',
                                };
                                @endphp
                                <span
                                    class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wide border {{ $badgeColor }}">
                                    {{ $data['predikat'] }}
                                </span>
                                @else
                                <span class="text-xs text-slate-400 italic">Belum dinilai</span>
                                @endif
                            </td>

                            <!-- TAMBAHAN TAMPILAN DATA CATATAN -->
                            <td
                                class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-900/10">
                                {{ $data['catatan'] }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>