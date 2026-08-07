<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>E-Kinerja Saya</title>

    <!-- Fonts & Scripts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50 dark:bg-gray-900 dark:text-gray-100 min-h-screen">

    <div class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-6">

            {{-- Judul Halaman --}}
            <div class="text-center mb-10">
                <h2 class="text-2xl sm:text-3xl font-black text-gray-800 dark:text-gray-200">
                    Dokumen <span class="text-indigo-600 dark:text-indigo-400">e-Kinerja Saya</span>
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Daftar Rencana Aksi dan Output milik Anda
                    berdasarkan Target Waktu (TW).</p>
            </div>

            {{-- Card Filter TW (Tanpa Input NIP) --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sm:p-8">
                <form action="{{ route('etpp.ku') }}" method="GET"
                    class="flex flex-col sm:flex-row gap-4 items-end justify-center">

                    {{-- Select Filter TW --}}
                    <div class="w-full sm:w-1/2">
                        <label for="tw"
                            class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Pilih
                            Target Waktu (TW)</label>
                        <select id="tw" name="tw"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-base sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition">
                            <option value="semua" {{ $filter_tw=='semua' ? 'selected' : '' }}>-- Tampilkan Semua TW --
                            </option>
                            <option value="TW 1" {{ $filter_tw=='TW 1' ? 'selected' : '' }}>Triwulan 1 (TW 1)</option>
                            <option value="TW 2" {{ $filter_tw=='TW 2' ? 'selected' : '' }}>Triwulan 2 (TW 2)</option>
                            <option value="TW 3" {{ $filter_tw=='TW 3' ? 'selected' : '' }}>Triwulan 3 (TW 3)</option>
                            <option value="TW 4" {{ $filter_tw=='TW 4' ? 'selected' : '' }}>Triwulan 4 (TW 4)</option>
                        </select>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="w-full sm:w-auto">
                        <button type="submit"
                            class="w-full sm:w-auto px-8 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-md hover:bg-indigo-700 hover:shadow-lg transition">
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- KIRI: Profil Pegawai Anda --}}
                <div
                    class="md:col-span-1 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden self-start sticky top-6">
                    <div
                        class="p-6 bg-indigo-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 text-center">
                        <div
                            class="w-24 h-24 bg-indigo-200 text-indigo-700 rounded-full mx-auto flex items-center justify-center text-3xl font-black mb-4 uppercase shadow-inner">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">
                            {{ $employee->nama_lengkap ?? auth()->user()->name }}
                        </h3>
                        <p class="text-sm text-gray-500 font-mono mt-1">{{ $employee->nip ?? 'NIP Belum Terdaftar' }}
                        </p>

                        @if(isset($employee->kategori_pegawai))
                        <span
                            class="inline-block mt-3 px-3 py-1 bg-indigo-600 text-white text-[10px] font-bold uppercase rounded-full tracking-widest">
                            {{ $employee->kategori_pegawai }}
                        </span>
                        @endif
                    </div>

                    @if($employee)
                    <div class="p-6 space-y-4 text-sm bg-white dark:bg-gray-800">
                        <div>
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Status
                                Kepegawaian</span>
                            <span class="block font-semibold text-gray-800 dark:text-gray-200">{{
                                $employee->status_kepegawaian ?? 'Belum Diatur' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Tugas
                                Tambahan</span>
                            <span class="block font-semibold text-gray-800 dark:text-gray-200">{{
                                $employee->tugas_tambahan ?? 'Tidak Ada' }}</span>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- KANAN: Daftar Dokumen Output e-Kinerja Saya --}}
                <div class="md:col-span-2 space-y-6">

                    @if(isset($data_kategori) && count($data_kategori) > 0)
                    @foreach($data_kategori as $kategori)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

                        {{-- Header Kategori --}}
                        <div class="bg-gray-50 dark:bg-gray-900/50 p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-bold text-gray-800 dark:text-gray-200 text-lg flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                    </path>
                                </svg>
                                {{ $kategori->nama_kategori }}
                            </h3>
                        </div>

                        {{-- Body Daftar RHK & Aksi --}}
                        <div class="p-5 space-y-6">
                            @forelse($kategori->rhk as $rhk)
                            <div class="space-y-3">
                                {{-- Judul RHK --}}
                                <h4 class="font-semibold text-sm text-indigo-700 dark:text-indigo-400 leading-snug">
                                    {{ $rhk->deskripsi_rhk }}
                                </h4>

                                {{-- Indentasi untuk Rencana Aksi --}}
                                <div class="pl-4 border-l-2 border-indigo-100 dark:border-indigo-900/50 space-y-4">
                                    @foreach($rhk->rencanaAksi as $ra)

                                    {{-- Hanya tampilkan jika output memiliki data (sesuai filter TW) --}}
                                    @if(count($ra->outputTarget) > 0)
                                    <div
                                        class="bg-gray-50/50 dark:bg-gray-800/50 rounded-lg p-4 border border-gray-100 dark:border-gray-700">

                                        <div class="mb-4">
                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                Rencana Aksi</p>
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2">{{
                                                $ra->deskripsi_ra }}</p>

                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                Kriteria Keberhasilan</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{
                                                $ra->kriteria_keberhasilan }}</p>
                                        </div>

                                        {{-- Output & Target Waktu --}}
                                        <div>
                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                                Target & Output</p>
                                            <ul class="space-y-2">
                                                @foreach($ra->outputTarget as $output)
                                                <li
                                                    class="flex items-start gap-3 bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-700 shadow-sm">
                                                    <span
                                                        class="shrink-0 mt-0.5 px-2 py-1 bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300 text-[11px] font-black tracking-wider uppercase rounded">
                                                        {{ $output->target_waktu }}
                                                    </span>
                                                    <span class="text-sm text-gray-600 dark:text-gray-300">
                                                        {{ $output->deskripsi_output }}
                                                    </span>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                    </div>
                                    @endif

                                    @endforeach
                                </div>
                            </div>

                            @if(!$loop->last)
                            <hr class="border-gray-100 dark:border-gray-700"> @endif
                            @empty
                            <p class="text-sm text-gray-500 italic">Belum ada Rencana Hasil Kerja untuk kategori ini.
                            </p>
                            @endforelse
                        </div>
                    </div>
                    @endforeach
                    @else
                    {{-- Tampilan jika Data Kosong / Belum Import Data e-Kinerja --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center flex flex-col items-center justify-center h-full min-h-[300px]">
                        <div
                            class="relative w-20 h-20 mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center text-gray-400 dark:text-gray-500">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300">Dokumen Tidak Ditemukan</h3>
                        <p class="text-sm text-gray-500 mt-1 max-w-sm">Anda belum mengimpor data e-Kinerja, atau tidak
                            ada output yang sesuai dengan filter <strong class="text-gray-600 dark:text-gray-400">{{
                                $filter_tw }}</strong>.</p>

                        <a href="{{ route('etpp.import.form') }}"
                            class="mt-6 px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-md hover:bg-indigo-700 transition">
                            Import Data e-Kinerja Sekarang
                        </a>
                    </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

</body>

</html>