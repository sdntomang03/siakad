<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
                Ledger Lengkap Nilai Siswa (Kelas 4 - Kelas 6)
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm font-bold text-slate-500 hover:text-slate-700">
                &larr; Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">

            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 max-w-3xl">
                @if($myClassrooms->isEmpty())
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-sm font-semibold">
                    Anda belum ditetapkan sebagai Wali Kelas untuk kelas mana pun. Silakan hubungi operator sekolah.
                </div>
                @else
                <form action="{{ route('grades.ledger') }}" method="GET"
                    class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:w-2/3">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Pilih Kelas</label>
                        <select name="classroom_id"
                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500"
                            required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($myClassrooms as $kelas)
                            <option value="{{ $kelas->id }}" {{ request('classroom_id')==$kelas->id ? 'selected' : ''
                                }}>
                                {{ $kelas->nama_kelas }} (Tingkat {{ $kelas->tingkat }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full md:w-1/3">
                        <button type="submit"
                            class="w-full bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-bold shadow-sm hover:bg-indigo-700 transition uppercase text-sm">
                            Tampilkan Ledger
                        </button>
                    </div>
                </form>
                @endif
            </div>

            @if($selectedClassroom)
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">

                <div
                    class="p-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Matriks Rekapitulasi Nilai Siswa (6
                            Semester)</h3>
                        <p class="text-sm text-slate-500">
                            Kelas: <span class="font-semibold text-slate-700 dark:text-slate-300">{{
                                $selectedClassroom->nama_kelas }}</span>
                        </p>
                    </div>
                    <button onclick="window.print()"
                        class="text-sm bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-lg font-bold hover:bg-slate-300 transition flex items-center">
                        Cetak Leger
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-xs whitespace-nowrap">
                        <thead class="bg-slate-100 dark:bg-slate-700/50">
                            <tr>
                                <th rowspan="2"
                                    class="px-3 py-2 text-center font-bold text-slate-700 dark:text-slate-300 border-r border-slate-200 dark:border-slate-700 w-10 sticky left-0 bg-slate-100 dark:bg-slate-700/90 z-20">
                                    No</th>
                                <th rowspan="2"
                                    class="px-3 py-2 text-left font-bold text-slate-700 dark:text-slate-300 border-r border-slate-200 dark:border-slate-700 sticky col-nisn bg-slate-100 dark:bg-slate-700/90 z-20">
                                    NISN</th>
                                <th rowspan="2"
                                    class="px-3 py-2 text-left font-bold text-slate-700 dark:text-slate-300 border-r border-slate-200 dark:border-slate-700 sticky col-nama bg-slate-100 dark:bg-slate-700/90 z-20">
                                    Nama Lengkap</th>

                                @foreach($subjects as $subject)
                                <th colspan="7"
                                    class="px-2 py-2 text-center font-bold text-slate-700 dark:text-slate-300 border-r border-b border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-700">
                                    {{ $subject->nama_mapel }}
                                </th>
                                @endforeach

                                <th rowspan="2"
                                    class="px-4 py-2 text-center font-bold text-emerald-700 dark:text-emerald-300 border-l border-slate-300 bg-emerald-50 dark:bg-emerald-900/20">
                                    Rata-rata Akhir
                                </th>
                            </tr>
                            <tr>
                                @foreach($subjects as $subject)
                                <th
                                    class="px-1.5 py-1 text-center font-semibold text-slate-500 border-r border-slate-200 bg-slate-50">
                                    4.1</th>
                                <th
                                    class="px-1.5 py-1 text-center font-semibold text-slate-500 border-r border-slate-200 bg-slate-50">
                                    4.2</th>
                                <th
                                    class="px-1.5 py-1 text-center font-semibold text-slate-500 border-r border-slate-200 bg-slate-50">
                                    5.1</th>
                                <th
                                    class="px-1.5 py-1 text-center font-semibold text-slate-500 border-r border-slate-200 bg-slate-50">
                                    5.2</th>
                                <th
                                    class="px-1.5 py-1 text-center font-semibold text-slate-500 border-r border-slate-200 bg-slate-50">
                                    6.1</th>
                                <th
                                    class="px-1.5 py-1 text-center font-semibold text-slate-500 border-r border-slate-200 bg-slate-50">
                                    6.2</th>
                                <th
                                    class="px-2 py-1 text-center font-bold text-indigo-700 border-r border-slate-200 bg-indigo-50/70">
                                    RT</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">

                            @php
                            $targetPeriods = [41, 42, 51, 52, 61, 62];
                            @endphp

                            @forelse($students as $index => $student)
                            @php
                            $grandTotalSidanira = 0;
                            $mapelCountSidanira = 0;
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                                <td
                                    class="px-3 py-2 text-center text-slate-600 dark:text-slate-400 border-r border-slate-200 sticky left-0 bg-white dark:bg-slate-800 z-10">
                                    {{ $index + 1 }}</td>
                                <td
                                    class="px-3 py-2 font-medium text-slate-600 dark:text-slate-400 border-r border-slate-200 sticky col-nisn bg-white dark:bg-slate-800 z-10">
                                    {{ $student->nisn }}</td>
                                <td
                                    class="px-3 py-2 font-bold text-slate-800 dark:text-slate-200 border-r border-slate-200 sticky col-nama bg-white dark:bg-slate-800 z-10 whitespace-normal min-w-[220px]">
                                    {{ $student->nama_lengkap }}</td>

                                @foreach($subjects as $subject)
                                @php
                                $totalMapel = 0;
                                $countMapel = 0;
                                @endphp

                                @foreach($targetPeriods as $period)
                                @php
                                $nilai = $ledgerData[$student->id][$subject->nama_mapel][$period] ?? null;
                                if($nilai !== null) {
                                $totalMapel += $nilai;
                                $countMapel++;
                                }
                                @endphp
                                <td class="px-1.5 py-2 text-center text-slate-600 border-r border-slate-200">
                                    {{ $nilai ?? '-' }}
                                </td>
                                @endforeach

                                @php
                                $rataMapel = $countMapel > 0 ? round($totalMapel / $countMapel, 2) : 0;
                                if($rataMapel > 0) {
                                $grandTotalSidanira += $rataMapel;
                                $mapelCountSidanira++;
                                }
                                @endphp
                                <td
                                    class="px-2 py-2 text-center font-bold text-indigo-700 border-r border-slate-200 bg-indigo-50/50">
                                    {{ $rataMapel > 0 ? $rataMapel : '-' }}
                                </td>
                                @endforeach

                                <td
                                    class="px-4 py-2 text-center font-bold text-emerald-700 bg-emerald-50/80 border-l border-slate-300">
                                    {{ $mapelCountSidanira > 0 ? round($grandTotalSidanira / $mapelCountSidanira, 2) :
                                    '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ (count($subjects) * 7) + 4 }}"
                                    class="px-4 py-8 text-center text-slate-500 italic">
                                    Tidak ada siswa yang terdaftar di kelas ini.
                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>

    <style>
        .sticky {
            position: sticky;
        }

        .col-nisn {
            left: 40px;
            min-width: 100px;
        }

        .col-nama {
            left: 140px;
            min-width: 220px;
        }

        @media print {
            @page {
                size: landscape;
                margin: 8mm;
            }

            .sticky {
                position: static !important;
            }

            .overflow-x-auto {
                overflow: visible !important;
            }

            form,
            header,
            nav,
            a,
            button {
                display: none !important;
            }

            .shadow-sm {
                box-shadow: none !important;
            }

            .bg-white,
            .bg-slate-50 {
                background-color: transparent !important;
            }

            table {
                width: 100% !important;
                font-size: 9pt !important;
            }

            .py-8 {
                padding-top: 0 !important;
            }

            th,
            td {
                padding: 4px 2px !important;
                border: 1px solid #cbd5e1 !important;
            }
        }
    </style>
</x-app-layout>