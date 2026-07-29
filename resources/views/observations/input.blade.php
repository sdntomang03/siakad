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
                            class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase tracking-wider">
                            Format Observasi (1 - {{ $assessment->scale }})
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 uppercase tracking-wider">
                            {{ $assessment->assessmentType->nama ?? 'Non-Tes' }}
                        </span>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white">{{ $assessment->keterangan }}</h3>
                    <p class="text-sm text-slate-500 font-medium mt-1">
                        {{ $assessment->classroom->tingkat }} - {{ $assessment->classroom->nama_kelas }} |
                        {{ $assessment->subject->nama_mapel }} |
                        {{ \Carbon\Carbon::parse($assessment->tanggal)->format('d M Y') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('assessments.index') }}"
                        class="text-sm font-bold text-slate-500 hover:text-slate-700 underline">
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
                <h4 class="text-sm font-black text-emerald-800 dark:text-emerald-400 mb-3 uppercase tracking-wider">
                    Daftar Kriteria Observasi</h4>
                <ul class="space-y-2">
                    @foreach($assessment->criteria as $index => $kriteria)
                    <li class="flex items-start gap-3 text-sm text-slate-700 dark:text-slate-300">
                        <span class="font-bold text-emerald-600 dark:text-emerald-500 shrink-0 w-8">K{{ $index + 1
                            }}</span>
                        <span>{{ $kriteria->descriptor }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- MATRIKS TABEL -->
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div
                    class="p-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-black text-slate-800 dark:text-white">Matriks Penilaian Siswa</h3>
                        <p class="text-xs font-bold text-slate-500 mt-1">Isi nilai dari 1 sampai {{ $assessment->scale
                            }}. Kosongkan jika tidak dinilai.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                        <thead
                            class="text-xs text-slate-700 uppercase bg-slate-100/80 dark:bg-slate-900/80 dark:text-slate-300">
                            <tr>
                                <th
                                    class="px-4 py-4 w-10 text-center sticky left-0 bg-slate-100 dark:bg-slate-900 z-10">
                                    No</th>
                                <th
                                    class="px-4 py-4 w-64 sticky left-10 bg-slate-100 dark:bg-slate-900 z-10 border-r border-slate-200 dark:border-slate-700">
                                    Nama Siswa</th>

                                @foreach($assessment->criteria as $index => $kriteria)
                                <th class="px-4 py-4 text-center min-w-[100px] border-r border-slate-200 dark:border-slate-700"
                                    title="{{ $kriteria->descriptor }}">
                                    <span class="block font-black text-emerald-600 dark:text-emerald-400">K{{ $index + 1
                                        }}</span>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $siswa)
                            <tr
                                class="border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                <td class="px-4 py-3 font-bold text-center sticky left-0 bg-white dark:bg-slate-800">{{
                                    $index + 1 }}</td>
                                <td
                                    class="px-4 py-3 sticky left-10 bg-white dark:bg-slate-800 border-r border-slate-100 dark:border-slate-700">
                                    <span class="block font-bold text-slate-800 dark:text-slate-200 truncate">{{
                                        $siswa->nama_lengkap }}</span>
                                </td>

                                @foreach($assessment->criteria as $kriteria)
                                @php
                                // Ambil nilai yang sudah tersimpan sebelumnya (jika ada)
                                $nilaiTersimpan = $existingScores[$siswa->id][$kriteria->id] ?? '';
                                @endphp
                                <td class="px-2 py-2 text-center border-r border-slate-100 dark:border-slate-700">
                                    <select name="scores[{{ $siswa->id }}][{{ $kriteria->id }}]"
                                        class="w-full text-center text-sm font-bold rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 focus:ring-emerald-500 py-1.5 {{ $nilaiTersimpan ? 'text-emerald-700 bg-emerald-50' : '' }}">
                                        <option value="">-</option>

                                        <!-- Looping skala berdasarkan input guru (misal 1 s/d 4) -->
                                        @for($i = 1; $i <= $assessment->scale; $i++)
                                            <option value="{{ $i }}" {{ $nilaiTersimpan==$i ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                            @endfor

                                    </select>
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TOMBOL SIMPAN -->
            <div class="flex justify-end pt-4">
                <button type="submit"
                    class="px-8 py-3 bg-emerald-600 text-white rounded-xl text-sm font-black shadow-lg hover:bg-emerald-700 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Nilai Observasi
                </button>
            </div>
        </form>

    </div>
</x-app-layout>