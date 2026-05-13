<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
                Laporan Absensi Siswa
            </h2>
            <a href="{{ url()->previous() }}" class="text-sm font-bold text-slate-500 hover:text-slate-700">&larr;
                Kembali</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div
                class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col md:flex-row gap-6 items-center">
                <div
                    class="w-20 h-20 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center text-3xl font-black shrink-0">
                    {{ substr($student->nama_lengkap, 0, 1) }}
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white">{{ $student->nama_lengkap }}</h3>
                    <p class="text-slate-500">NISN: {{ $student->nisn ?? '-' }} • Kelas: <span
                            class="font-bold text-indigo-600">{{ $classroom->nama_kelas }}</span></p>
                </div>

            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div
                    class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 text-center">
                    <p class="text-xs font-bold text-slate-500 uppercase">Hadir</p>
                    <p class="text-3xl font-black text-emerald-500 mt-2">{{ $rekap['hadir'] }}</p>
                    <p class="text-[10px] text-slate-400 mt-1">Hari Kerja</p>
                </div>
                <div
                    class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 text-center border-b-4 border-b-amber-400">
                    <p class="text-xs font-bold text-slate-500 uppercase">Sakit</p>
                    <p class="text-3xl font-black text-amber-500 mt-2">{{ $rekap['sakit'] }}</p>
                    <p class="text-[10px] text-slate-400 mt-1">Hari</p>
                </div>
                <div
                    class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 text-center border-b-4 border-b-blue-400">
                    <p class="text-xs font-bold text-slate-500 uppercase">Izin</p>
                    <p class="text-3xl font-black text-blue-500 mt-2">{{ $rekap['izin'] }}</p>
                    <p class="text-[10px] text-slate-400 mt-1">Hari</p>
                </div>
                <div
                    class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 text-center border-b-4 border-b-rose-400">
                    <p class="text-xs font-bold text-slate-500 uppercase">Alfa</p>
                    <p class="text-3xl font-black text-rose-500 mt-2">{{ $rekap['alfa'] }}</p>
                    <p class="text-[10px] text-slate-400 mt-1">Tanpa Alasan</p>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                    <h4 class="font-bold text-slate-800 dark:text-white">Detail Riwayat Ketidakhadiran</h4>
                    <p class="text-xs text-slate-500">Daftar tanggal di mana siswa tercatat tidak hadir.</p>
                </div>
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absences as $absen)
                        <tr class="border-b border-slate-100 dark:border-slate-700">
                            <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">
                                {{ date('d F Y', strtotime($absen->tanggal)) }}
                            </td>
                            <td class="px-6 py-4 capitalize">
                                @if($absen->status == 'sakit')
                                <span
                                    class="px-2 py-1 bg-amber-100 text-amber-700 rounded text-[10px] font-bold">SAKIT</span>
                                @elseif($absen->status == 'izin')
                                <span
                                    class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] font-bold">IZIN</span>
                                @else
                                <span
                                    class="px-2 py-1 bg-rose-100 text-rose-700 rounded text-[10px] font-bold">ALFA</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $absen->keterangan ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-slate-400">
                                Siswa ini memiliki catatan kehadiran 100% sempurna.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>