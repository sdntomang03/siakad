<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
            Input Nilai Observasi / Non-Tes
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- KARTU INFORMASI PENILAIAN -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span
                            class="inline-flex items-center gap-1.5 py-0.5 px-3 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 uppercase tracking-wider">
                            Format Observasi (1 - {{ $assessment->scale }})
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 py-0.5 px-3 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            {{ $assessment->assessmentType->nama ?? 'Non-Tes' }}
                        </span>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white">{{ $assessment->keterangan }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        {{ $assessment->classroom->tingkat }} - {{ $assessment->classroom->nama_kelas }} &bull;
                        {{ $assessment->subject->nama_mapel }} &bull;
                        {{ \Carbon\Carbon::parse($assessment->tanggal)->format('d M Y') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('assessments.index') }}"
                        class="inline-flex items-center gap-1 text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition">
                        &larr; Kembali ke Riwayat
                    </a>
                </div>
            </div>
        </div>

        <!-- FORM INPUT NILAI -->
        <form action="{{ route('observations.updateScores', $assessment->id) }}" method="POST" class="space-y-6">
            @csrf

            <!-- LEGEND / DAFTAR KRITERIA -->
            <div
                class="bg-emerald-50 dark:bg-emerald-900/20 p-5 rounded-2xl border border-emerald-100 dark:border-emerald-800/50">
                <h4
                    class="text-sm font-black text-emerald-800 dark:text-emerald-400 mb-3 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                    Daftar Kriteria Observasi
                </h4>
                <ul class="space-y-2.5">
                    @foreach($assessment->criteria as $index => $kriteria)
                    <li
                        class="flex items-start gap-3 text-sm text-slate-700 dark:text-slate-300 bg-white/60 dark:bg-slate-900/40 px-3 py-2 rounded-lg">
                        <span class="font-black text-emerald-600 dark:text-emerald-500 shrink-0 w-8">K{{ $index + 1
                            }}</span>
                        <span class="font-medium">{{ $kriteria->descriptor }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- MATRIKS TABEL -->
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                    <h3 class="text-base font-black text-slate-800 dark:text-white">Matriks Penilaian Siswa</h3>
                    <p class="text-xs font-bold text-slate-500 mt-1">Pilih nilai dari 1 sampai {{ $assessment->scale }}.
                        Klik strip (-) untuk mereset/mengosongkan nilai.</p>
                </div>

                <div class="overflow-x-auto relative">
                    <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                        <thead
                            class="text-xs text-slate-700 uppercase bg-slate-100 dark:bg-slate-900 dark:text-slate-300 whitespace-nowrap">
                            <tr>
                                <th
                                    class="px-4 py-4 w-12 text-center sticky left-0 bg-slate-100 dark:bg-slate-900 z-20">
                                    No</th>
                                <th
                                    class="px-4 py-4 w-64 sticky left-12 bg-slate-100 dark:bg-slate-900 z-20 border-r-2 border-slate-200 dark:border-slate-700">
                                    Nama Siswa</th>

                                @foreach($assessment->criteria as $index => $kriteria)
                                <th class="px-4 py-4 text-center min-w-[150px] border-r border-slate-200 dark:border-slate-700"
                                    title="{{ $kriteria->descriptor }}">
                                    <span class="block font-black text-emerald-600 dark:text-emerald-400 text-sm">K{{
                                        $index + 1 }}</span>
                                </th>
                                @endforeach

                                <!-- TAMBAHAN HEADER CATATAN -->
                                <th
                                    class="px-4 py-4 min-w-[200px] border-r border-slate-200 dark:border-slate-700 text-center bg-slate-100 dark:bg-slate-900">
                                    Catatan Tambahan
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $siswa)
                            <tr
                                class="{{ $loop->last ? '' : 'border-b border-slate-100 dark:border-slate-700' }} hover:bg-emerald-50/50 dark:hover:bg-slate-700/50 transition duration-150">
                                <td
                                    class="px-4 py-3 font-bold text-center sticky left-0 bg-white dark:bg-slate-800 z-10 group-hover:bg-emerald-50/50 dark:group-hover:bg-slate-700/50">
                                    {{ $index + 1 }}
                                </td>
                                <td
                                    class="px-4 py-3 sticky left-12 bg-white dark:bg-slate-800 z-10 border-r-2 border-slate-200 dark:border-slate-700 whitespace-nowrap group-hover:bg-emerald-50/50 dark:group-hover:bg-slate-700/50">
                                    <span class="block font-bold text-slate-800 dark:text-slate-200">{{
                                        $siswa->nama_lengkap }}</span>
                                </td>

                                @foreach($assessment->criteria as $kriteria)
                                @php
                                $nilaiTersimpan = $existingScores[$siswa->id][$kriteria->id] ?? '';
                                @endphp
                                <td class="px-2 py-3 text-center border-r border-slate-100 dark:border-slate-700">

                                    <!-- CONTAINER RADIO BUTTONS -->
                                    <div class="flex items-center justify-center gap-1.5">

                                        <!-- Opsi Kosong (-) -->
                                        <label class="cursor-pointer group" title="Kosongkan">
                                            <input type="radio" name="scores[{{ $siswa->id }}][{{ $kriteria->id }}]"
                                                value="" {{ $nilaiTersimpan=='' ? 'checked' : '' }}
                                                class="peer sr-only">
                                            <div
                                                class="w-7 h-8 flex items-center justify-center rounded-md border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-slate-400 font-black text-sm peer-checked:bg-slate-200 peer-checked:text-slate-700 peer-checked:border-slate-300 dark:peer-checked:bg-slate-700 dark:peer-checked:text-slate-300 hover:bg-slate-100 transition shadow-sm">
                                                -
                                            </div>
                                        </label>

                                        <!-- Looping skala dari 1 s/d max scale -->
                                        @for($i = 1; $i <= $assessment->scale; $i++)
                                            <label class="cursor-pointer group">
                                                <input type="radio" name="scores[{{ $siswa->id }}][{{ $kriteria->id }}]"
                                                    value="{{ $i }}" {{ $nilaiTersimpan==$i ? 'checked' : '' }}
                                                    class="peer sr-only">
                                                <div
                                                    class="w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-black text-sm peer-checked:bg-emerald-500 peer-checked:border-emerald-600 peer-checked:text-white peer-checked:shadow-inner hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 transition shadow-sm">
                                                    {{ $i }}
                                                </div>
                                            </label>
                                            @endfor

                                    </div>

                                </td>
                                @endforeach

                                <!-- TAMBAHAN INPUT CATATAN -->
                                @php
                                $catatanSiswa = $existingNotes[$siswa->id] ?? '';
                                @endphp
                                <td class="px-3 py-3 border-r border-slate-100 dark:border-slate-700">
                                    <input type="text" name="notes[{{ $siswa->id }}]" value="{{ $catatanSiswa }}"
                                        placeholder="Tulis catatan (opsional)..."
                                        class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 focus:ring-emerald-500 focus:border-emerald-500 py-1.5 dark:text-white transition shadow-sm">
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TOMBOL SIMPAN -->
            <div class="flex justify-end pt-2 pb-6">
                <button type="submit"
                    class="px-8 py-3.5 bg-emerald-600 text-white rounded-xl text-sm font-black shadow-lg shadow-emerald-500/30 hover:bg-emerald-700 transform hover:-translate-y-0.5 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7">
                        </path>
                    </svg>
                    Simpan Penilaian Observasi
                </button>
            </div>
        </form>

    </div>
</x-app-layout>