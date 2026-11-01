<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">
                Laporan Absensi Siswa
            </h2>
            <a href="{{ url()->previous() }}" class="text-sm font-bold text-slate-500 hover:text-slate-700">&larr;
                Kembali</a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">

            <div
                class="flex flex-col items-center gap-5 rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:flex-row sm:p-6">
                <div
                    aria-hidden="true"
                    class="flex h-14 w-14 shrink-0 items-center justify-center rounded-md bg-brand-100 text-xl font-semibold text-brand-800 dark:bg-brand-900/50 dark:text-brand-200">
                    {{ substr($student->nama_lengkap, 0, 1) }}
                </div>
                <div class="min-w-0 flex-1 text-center sm:text-left">
                    <h3 class="text-xl font-semibold text-slate-950 dark:text-white">{{ $student->nama_lengkap }}</h3>
                    <p class="text-slate-500">NISN: {{ $student->nisn ?? '-' }} • Kelas: <span
                            class="font-bold text-brand-600">{{ $classroom->nama_kelas }}</span></p>
                </div>

            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

                <div
                    class="rounded-lg border border-slate-200 border-b-4 border-b-amber-500 bg-white p-5 text-center dark:border-slate-700 dark:border-b-amber-500 dark:bg-slate-800">
                    <p class="text-xs font-bold text-slate-500 uppercase">Sakit</p>
                    <p class="text-3xl font-black text-amber-500 mt-2">{{ $rekap['sakit'] }}</p>
                    <p class="text-[10px] text-slate-400 mt-1">Hari</p>
                </div>
                <div
                    class="rounded-lg border border-slate-200 border-b-4 border-b-blue-600 bg-white p-5 text-center dark:border-slate-700 dark:border-b-blue-500 dark:bg-slate-800">
                    <p class="text-xs font-bold text-slate-500 uppercase">Izin</p>
                    <p class="text-3xl font-black text-blue-500 mt-2">{{ $rekap['izin'] }}</p>
                    <p class="text-[10px] text-slate-400 mt-1">Hari</p>
                </div>
                <div
                    class="rounded-lg border border-slate-200 border-b-4 border-b-rose-600 bg-white p-5 text-center dark:border-slate-700 dark:border-b-rose-500 dark:bg-slate-800">
                    <p class="text-xs font-bold text-slate-500 uppercase">Alfa</p>
                    <p class="text-3xl font-black text-rose-500 mt-2">{{ $rekap['alfa'] }}</p>
                    <p class="text-[10px] text-slate-400 mt-1">Tanpa Alasan</p>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                    <h4 class="font-bold text-slate-800 dark:text-white">Detail Riwayat Ketidakhadiran</h4>
                    <p class="text-xs text-slate-500">Daftar tanggal di mana siswa tercatat tidak hadir.</p>
                </div>
                <div class="overflow-x-auto">
                <table class="min-w-[36rem] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-600 dark:bg-slate-900/50 dark:text-slate-300">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absences as $absen)
                        <tr class="border-b border-slate-100 text-slate-700 dark:border-slate-700 dark:text-slate-200">
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
    </div>
</x-app-layout>