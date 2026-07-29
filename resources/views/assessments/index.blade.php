<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
            Riwayat Penilaian
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h3 class="text-lg font-black text-slate-800 dark:text-white">Daftar Nilai Masuk</h3>
                <p class="text-sm text-slate-500">Kelola riwayat ulangan, tugas, dan ujian yang pernah Anda input.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('assessments.recap') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-bold shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Lihat Rekap
                </a>

                <!-- Tambahan Tombol Buat Observasi -->
                <a href="{{ route('observations.create') }}"
                    class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-bold shadow-lg hover:bg-emerald-700 transition">
                    + Observasi
                </a>

                <!-- Tombol Buat Tes Konvensional -->
                <a href="{{ route('assessments.create') }}"
                    class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-bold shadow-lg hover:bg-indigo-700 transition">
                    + Buat Tes (Angka)
                </a>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                    <thead
                        class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-900/50 dark:text-slate-300">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Kelas & Mapel</th>
                            <th class="px-6 py-4">Keterangan</th>
                            <th class="px-6 py-4 text-center">Progres Input</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($assessments as $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-700 dark:text-slate-200">
                                {{ $item->tanggal->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="block font-black text-indigo-600 dark:text-indigo-400">{{
                                    $item->classroom->tingkat }} - {{ $item->classroom->nama_kelas }}</span>
                                <span class="block text-xs font-bold text-slate-500">{{ $item->subject->nama_mapel
                                    }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2 mb-1">
                                    <span
                                        class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                        {{ $item->assessmentType->nama ?? $item->jenis_penilaian ?? 'Penilaian' }}
                                    </span>

                                    <!-- Badge Penanda Format Tes atau Non-Tes -->
                                    @if($item->format === 'non-tes')
                                    <span
                                        class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 uppercase tracking-wider">
                                        NON-TES
                                    </span>
                                    @else
                                    <span
                                        class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 uppercase tracking-wider">
                                        TES
                                    </span>
                                    @endif
                                </div>
                                <span class="block text-slate-700 dark:text-slate-300">{{ $item->keterangan }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <!-- Penyesuaian tampilan Progres Input berdasarkan format -->
                                @if($item->format === 'non-tes')
                                <span class="text-xs font-bold text-slate-400 italic">Matriks Observasi</span>
                                @else
                                <div class="inline-flex flex-col items-center">
                                    <span
                                        class="text-lg font-black {{ $item->scores_count > 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                        {{ $item->scores_count }}
                                    </span>
                                    <span class="text-[10px] uppercase font-bold text-slate-400">Siswa dinilai</span>
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">

                                <!-- Logika Aksi Edit Dinamis berdasarkan Format -->
                                @if($item->format === 'non-tes')
                                <a href="{{ route('observations.input', $item->id) }}"
                                    class="inline-block px-4 py-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white font-bold rounded-lg transition text-xs">
                                    Edit Observasi &rarr;
                                </a>
                                @else
                                <a href="{{ route('assessments.input', $item->id) }}"
                                    class="inline-block px-4 py-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white font-bold rounded-lg transition text-xs">
                                    Edit Nilai &rarr;
                                </a>
                                @endif

                                <form action="{{ route('assessments.destroy', $item->id) }}" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('PERINGATAN: Menghapus data ini akan menghapus seluruh nilai siswa di dalamnya. Lanjutkan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-4 py-2 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white font-bold rounded-lg transition text-xs">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="inline-flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <p class="text-slate-500 font-bold">Belum ada riwayat penilaian.</p>
                                    <p class="text-sm text-slate-400">Klik "Buat Tes" atau "Observasi" untuk mulai
                                        menginput
                                        nilai siswa.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($assessments->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/30">
                {{ $assessments->links() }}
            </div>
            @endif
        </div>

    </div>
</x-app-layout>