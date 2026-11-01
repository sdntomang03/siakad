<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Laporan Pelaksanaan Piket</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto space-y-5 px-4 sm:px-6 sm:py-8 lg:px-8">

        <!-- FILTER LAPORAN -->
        <div class="bg-white p-6 rounded-md shadow-sm mb-6 border border-slate-200 print:hidden">
            <form action="{{ route('piket.laporan') }}" method="GET"
                class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)_minmax(0,0.7fr)_auto]">
                <div>
                    <label for="classroom_id" class="mb-2 block text-sm font-medium text-slate-700">Pilih Kelas</label>
                    <select id="classroom_id" name="classroom_id" class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600"
                        onchange="this.form.submit()">
                        @foreach($classrooms as $kelas)
                        <option value="{{ $kelas->id }}" {{ $classroomId==$kelas->id ? 'selected' : '' }}>
                            {{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="bulan" class="mb-2 block text-sm font-medium text-slate-700">Bulan</label>
                    <select id="bulan" name="bulan" class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600"
                        onchange="this.form.submit()">
                        @foreach($bulanList as $num => $namaBulan)
                        <option value="{{ $num }}" {{ $bulan==$num ? 'selected' : '' }}>{{ $namaBulan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tahun" class="mb-2 block text-sm font-medium text-slate-700">Tahun</label>
                    <input id="tahun" type="number" name="tahun" value="{{ $tahun }}"
                        class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600"
                        onchange="this.form.submit()">
                </div>
                <div class="sm:col-span-2 lg:col-span-1">
                    <button type="button" onclick="window.print()"
                        class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 sm:w-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                        Cetak Laporan
                    </button>
                </div>
            </form>
        </div>

        <!-- TABEL REKAPITULASI -->
        <div
            class="bg-white rounded-md shadow-sm border border-slate-200 overflow-hidden print:shadow-none print:border-none">
            <div class="p-5 border-b border-slate-100 bg-slate-50 print:bg-transparent">
                <h3 class="font-bold text-slate-800 text-lg uppercase text-center">Rekapitulasi Piket Siswa</h3>
                <p class="text-sm text-slate-500 text-center font-medium">Bulan {{ $bulanList[$bulan] }} {{ $tahun }}
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse print:border">
                    <thead
                        class="text-xs text-slate-700 uppercase bg-slate-100 print:bg-slate-200 print:text-black print:border">
                        <tr>
                            <th class="px-4 py-4 w-12 text-center print:border print:border-slate-800">No</th>
                            <th class="px-4 py-4 print:border print:border-slate-800">Nama Siswa</th>
                            <th class="px-4 py-4 text-center w-24 print:border print:border-slate-800">Total Piket</th>
                            <th
                                class="px-4 py-4 text-center w-32 bg-emerald-50 text-emerald-800 print:border print:border-slate-800">
                                Terlaksana</th>
                            <th
                                class="px-4 py-4 text-center w-36 bg-rose-50 text-rose-800 print:border print:border-slate-800">
                                Tidak Terlaksana</th>
                            <th class="px-4 py-4 w-1/3 print:border print:border-slate-800">Catatan Pelanggaran / Alasan
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $index => $siswa)
                        @php
                        $rekap = $rekapPiket[$siswa->id];
                        @endphp
                        <tr class="border-b border-slate-100 hover:bg-slate-50 print:border-slate-800">
                            <td class="px-4 py-3 text-center font-bold print:border print:border-slate-800">{{ $index +
                                1 }}</td>
                            <td class="px-4 py-3 font-medium print:border print:border-slate-800">{{
                                $siswa->nama_lengkap }}</td>
                            <td
                                class="px-4 py-3 text-center font-bold text-slate-600 print:border print:border-slate-800">
                                {{ $rekap['total'] }}</td>

                            <td
                                class="px-4 py-3 text-center font-black text-emerald-600 print:border print:border-slate-800">
                                {{ $rekap['terlaksana'] }}
                            </td>

                            <td
                                class="px-4 py-3 text-center font-black {{ $rekap['tidak_terlaksana'] > 0 ? 'text-rose-600' : 'text-slate-300' }} print:border print:border-slate-800">
                                {{ $rekap['tidak_terlaksana'] }}
                            </td>

                            <td class="px-4 py-3 text-xs text-slate-600 print:border print:border-slate-800">
                                @if(count($rekap['catatan_pelanggaran']) > 0)
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($rekap['catatan_pelanggaran'] as $tgl => $alasan)
                                    <li>
                                        <span class="font-bold">{{ \Carbon\Carbon::parse($tgl)->format('d/m') }}:</span>
                                        {{ $alasan }}
                                    </li>
                                    @endforeach
                                </ul>
                                @else
                                <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .print\:hidden {
                display: none !important;
            }

            .max-w-7xl {
                max-width: 100% !important;
                margin: 0;
                padding: 0;
            }

            .bg-white {
                background: transparent !important;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            table,
            th,
            td {
                border: 1px solid black !important;
            }

            /* Membuat tabel print bisa tampil dan tidak tertutup yang lain */
            .bg-white.rounded-md,
            .bg-white.rounded-md * {
                visibility: visible;
            }

            .bg-white.rounded-md {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</x-app-layout>