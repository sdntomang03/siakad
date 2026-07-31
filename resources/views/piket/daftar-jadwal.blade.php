<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Tabel Jadwal Piket Kebersihan
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- FILTER KELAS -->
        <div
            class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-black text-slate-800">Tampilan Tabel Piket</h3>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Daftar siswa yang bertugas disusun per hari aktif.
                </p>
            </div>
            <form method="GET" action="{{ route('piket.daftarJadwal') }}" class="w-full sm:w-auto">
                <select id="classroom_id" name="classroom_id" onchange="this.form.submit()"
                    class="w-full sm:w-64 text-sm font-semibold rounded-xl border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                    @foreach($classrooms as $classroom)
                    <option value="{{ $classroom->id }}" {{ $classroomId==$classroom->id ? 'selected' : '' }}>
                        Kelas {{ $classroom->tingkat }} - {{ $classroom->nama_kelas }}
                    </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if($classroomId)
        <!-- MENCARI HARI YANG ADA SISWANYA SAJA -->
        @php
        $hariAktif = [];
        if(isset($jadwalTersimpan)) {
        foreach($hariList as $hari) {
        // Jika hari tersebut ada di array dan jumlah siswanya lebih dari 0
        if(isset($jadwalTersimpan[$hari]) && count($jadwalTersimpan[$hari]) > 0) {
        $hariAktif[] = $hari;
        }
        }
        }
        // Tentukan jumlah kolom untuk keperluan colspan
        $jumlahKolom = count($hariAktif) > 0 ? count($hariAktif) : 6;
        @endphp

        <!-- TABEL JADWAL -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

            <!-- Opsi Tombol Cetak / Aksi lain -->
            <div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">
                    Jadwal Piket Kelas {{ $classrooms->firstWhere('id', $classroomId)->nama_kelas ?? '' }}
                </h3>
                <button onclick="window.print()"
                    class="px-4 py-2 bg-slate-200 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-300 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    Cetak / Print
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-indigo-600 text-white">
                        <tr>
                            @if(count($hariAktif) > 0)
                            @foreach($hariAktif as $hari)
                            <th class="px-4 py-3 text-center border-r border-indigo-500 font-black tracking-widest"
                                style="width: {{ 100 / count($hariAktif) }}%">
                                {{ $hari }}
                            </th>
                            @endforeach
                            @else
                            <th class="px-4 py-3 text-center border-r border-indigo-500 font-black tracking-widest">
                                JADWAL PIKET
                            </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if($maxSiswaPerHari > 0 && count($hariAktif) > 0)
                        @for($i = 0; $i < $maxSiswaPerHari; $i++) <tr
                            class="border-b border-slate-100 hover:bg-slate-50 transition">
                            @foreach($hariAktif as $hari)
                            <td
                                class="px-4 py-3 text-center border-r border-slate-100 {{ isset($jadwalTersimpan[$hari][$i]) ? 'font-bold text-slate-800' : 'text-slate-300' }}">
                                {{ $jadwalTersimpan[$hari][$i] ?? '-' }}
                            </td>
                            @endforeach
                            </tr>
                            @endfor
                            @else
                            <tr>
                                <td colspan="{{ $jumlahKolom }}"
                                    class="px-4 py-8 text-center text-slate-500 font-medium bg-slate-50/50">
                                    Belum ada jadwal piket yang dibuat untuk kelas ini.
                                </td>
                            </tr>
                            @endif
                    </tbody>
                </table>
            </div>

        </div>
        @endif
    </div>

    <!-- CSS Tambahan Khusus untuk Tampilan Print/Cetak -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .bg-white.rounded-2xl.shadow-sm,
            .bg-white.rounded-2xl.shadow-sm * {
                visibility: visible;
            }

            .bg-white.rounded-2xl.shadow-sm {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none !important;
                border: none !important;
            }

            /* Sembunyikan tombol cetak saat dicetak */
            button[onclick="window.print()"] {
                display: none;
            }
        }
    </style>
</x-app-layout>