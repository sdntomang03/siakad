<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">
            Riwayat Penilaian
        </h2>
    </x-slot>

    <div class="space-y-6 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
      <div class="mx-auto max-w-screen-2xl space-y-6">

        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h3 class="text-lg font-black text-slate-800 dark:text-white">Daftar Nilai Masuk</h3>
                <p class="text-sm text-slate-500">Kelola riwayat ulangan, tugas, dan ujian yang pernah Anda input.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('assessments.recap') }}"
                    class="inline-flex min-h-11 items-center gap-2 rounded-md border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Lihat Rekap
                </a>

                <!-- Tambahan Tombol Buat Observasi -->
                <a href="{{ route('observations.create') }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-50 px-5 py-2.5 text-sm font-semibold text-brand-900 transition-colors hover:bg-brand-100 dark:bg-brand-900 dark:text-brand-100 dark:hover:bg-brand-900">
                    Observasi
                </a>

                <!-- Tombol Buat Tes Konvensional -->
                <a href="{{ route('assessments.create') }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-brand-800">
                    Penilaian
                </a>
            </div>
        </div>

        <div
            class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
            <form method="GET" action="{{ route('assessments.index') }}"
                class="grid grid-cols-1 gap-4 border-b border-slate-200 p-4 dark:border-slate-700 md:grid-cols-4 md:items-end">
                <div>
                    <label for="subject_id" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Mata Pelajaran
                    </label>
                    <select id="subject_id" name="subject_id"
                        class="min-h-11 w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                        <option value="">Semua mata pelajaran</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>
                            {{ $subject->nama_mapel }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="assessment_type_id" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Jenis Penilaian
                    </label>
                    <select id="assessment_type_id" name="assessment_type_id"
                        class="min-h-11 w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                        <option value="">Semua jenis penilaian</option>
                        @foreach($assessmentTypes as $type)
                        <option value="{{ $type->id }}" @selected(request('assessment_type_id') == $type->id)>
                            {{ $type->nama }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row md:col-span-2 md:justify-end">
                    <button type="submit"
                        class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-brand-800 active:bg-brand-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                        Terapkan Filter
                    </button>
                    @if(request()->filled('subject_id') || request()->filled('assessment_type_id'))
                    <a href="{{ route('assessments.index') }}"
                        class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50 active:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700 dark:active:bg-slate-600">
                        Hapus Filter
                    </a>
                    @endif
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                    <thead
                        class="bg-slate-50 text-xs font-medium text-slate-700 dark:bg-slate-900/50 dark:text-slate-300">
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
                        <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/50">
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-700 dark:text-slate-200">
                                {{ $item->tanggal->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="block font-semibold text-brand-800 dark:text-brand-200">{{
                                    $item->classroom->tingkat }} - {{ $item->classroom->nama_kelas }}</span>
                                <span class="block text-xs font-bold text-slate-500">{{ $item->subject->nama_mapel
                                    }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2 mb-1">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700 dark:bg-slate-700 dark:text-slate-200">
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
                                        class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-[10px] font-bold bg-brand-100 text-brand-700 dark:bg-brand-900/30 dark:text-brand-400 uppercase tracking-wider">
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
                                <a href="{{ route('assessments.edit', $item->id) }}"
                                    aria-label="Edit detail penilaian" title="Edit detail penilaian"
                                    class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 active:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 dark:active:bg-slate-600">
                                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        aria-hidden="true" focusable="false">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M8 4.75h6.5L19 9.25v4.5M14 4.75v4.5h4.5M8 4.75A2.25 2.25 0 0 0 5.75 7v10A2.25 2.25 0 0 0 8 19.25h4.5M9 13h5M9 9.75h1.5m-1.5 6.5h2.5m6.2-2.3 2.1 2.1-4.9 4.9-2.8.7.7-2.8 4.9-4.9Z">
                                        </path>
                                    </svg>
                                </a>

                                @if($item->format === 'non-tes')
                                <a href="{{ route('observations.input', $item->id) }}"
                                    class="inline-flex min-h-11 items-center gap-2 rounded-md bg-brand-50 px-4 py-2 text-sm font-medium text-brand-900 transition-colors hover:bg-brand-700 hover:text-white dark:bg-brand-900 dark:text-brand-100 dark:hover:bg-brand-700">
                                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        aria-hidden="true" focusable="false">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M5 5.75h14A1.25 1.25 0 0 1 20.25 7v10A1.25 1.25 0 0 1 19 18.25H5A1.25 1.25 0 0 1 3.75 17V7A1.25 1.25 0 0 1 5 5.75ZM7.5 9.5h3m3 0h3m-9 4h3m3 0h3m-9 4h3m5.5-2.4 2.5 2.5m-3.8 1.3.8-2.1 4.5-4.5 2.1 2.1-4.5 4.5-2.1.8Z">
                                        </path>
                                    </svg>
                                    Edit Observasi
                                </a>
                                @else
                                <a href="{{ route('assessments.input', $item->id) }}"
                                    aria-label="Edit nilai" title="Edit nilai"
                                    class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-50 px-3 py-2 text-sm font-medium text-brand-900 transition-colors hover:bg-brand-700 hover:text-white dark:bg-brand-900 dark:text-brand-100 dark:hover:bg-brand-700">
                                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        aria-hidden="true" focusable="false">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M4.75 5.75h14.5v12.5H4.75zM9.5 5.75v12.5m5-12.5v12.5m-9.75-8.25h14.5m-14.5 4h14.5">
                                        </path>
                                    </svg>
                                </a>
                                @endif

                                <form action="{{ route('assessments.destroy', $item->id) }}" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('PERINGATAN: Menghapus data ini akan menghapus seluruh nilai siswa di dalamnya. Lanjutkan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        aria-label="Hapus" title="Hapus"
                                        class="inline-flex min-h-11 items-center justify-center rounded-md bg-red-50 px-3 py-2 text-sm font-medium text-red-800 transition-colors hover:bg-red-700 hover:text-white dark:bg-red-950 dark:text-red-200">
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            aria-hidden="true" focusable="false">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                d="M4.75 7.25h14.5m-9.5 0V5.5h4.5v1.75m-7.5 0 .7 11.1a1.5 1.5 0 0 0 1.5 1.4h5.6a1.5 1.5 0 0 0 1.5-1.4l.7-11.1M9.5 10.5v6m5-6v6">
                                            </path>
                                        </svg>
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
                                    @if(request()->filled('subject_id') || request()->filled('assessment_type_id'))
                                    <p class="text-slate-500 font-bold">Tidak ada penilaian yang sesuai dengan filter.</p>
                                    <a href="{{ route('assessments.index') }}"
                                        class="mt-3 inline-flex min-h-11 items-center justify-center rounded-md bg-brand-50 px-4 py-2 text-sm font-semibold text-brand-900 transition-colors hover:bg-brand-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:bg-brand-900 dark:text-brand-100 dark:hover:bg-brand-800">
                                        Hapus Filter
                                    </a>
                                    @else
                                    <p class="text-slate-500 font-bold">Belum ada riwayat penilaian.</p>
                                    <p class="text-sm text-slate-400">Klik "Buat Tes" atau "Observasi" untuk mulai menginput nilai siswa.</p>
                                    @endif
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
    </div>
</x-app-layout>