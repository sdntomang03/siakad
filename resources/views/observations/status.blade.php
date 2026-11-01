<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800">
            Status Penilaian: {{ $assessment->classroom->nama_kelas }} - {{ $assessment->subject->nama_mapel }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-5 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <!-- Tombol Navigasi -->
        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
            <!-- Tombol Kembali ke Daftar Riwayat Penilaian -->
            <a href="{{ route('assessments.index') }}"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700">
                <i class="fas fa-home"></i> Kembali ke Riwayat
            </a>

            <!-- Tombol Kembali ke Form Input -->
            <a href="{{ route('observations.input', $assessment->id) }}"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                <i class="fas fa-arrow-left"></i> Kembali ke Form Input
            </a>
        </div>

        <!-- Tabel 1: Belum Dinilai -->
        <div class="overflow-hidden rounded-lg border border-rose-200 bg-white dark:border-rose-900 dark:bg-slate-800">
            <div class="bg-rose-50 border-b border-rose-200 p-4">
                <h3 class="font-black text-rose-700">Belum Dinilai ({{ $belumDinilai->count() }} Siswa)</h3>
            </div>
            <div class="overflow-x-auto p-4">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-black">
                        <tr>
                            <th class="px-4 py-3 w-16 text-center">No</th>
                            <th class="px-4 py-3">Nama Siswa</th>

                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($belumDinilai as $index => $siswa)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-center font-bold">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $siswa->nama_lengkap }}</td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-slate-400">Semua siswa telah dinilai!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel 2: Sudah Dinilai -->
        <div class="overflow-hidden rounded-lg border border-emerald-200 bg-white dark:border-emerald-900 dark:bg-slate-800">
            <div class="bg-emerald-50 border-b border-emerald-200 p-4">
                <h3 class="font-black text-emerald-700">Sudah Dinilai ({{ $sudahDinilai->count() }} Siswa)</h3>
            </div>
            <div class="overflow-x-auto p-4">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-black">
                        <tr>
                            <th class="px-4 py-3 w-16 text-center">No</th>
                            <th class="px-4 py-3">Nama Siswa</th>

                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($sudahDinilai as $index => $siswa)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-center font-bold">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $siswa->nama_lengkap }}</td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-slate-400">Belum ada siswa yang dinilai.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>