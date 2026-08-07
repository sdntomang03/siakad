<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Output e-Kinerja Pegawai</title>

    <!-- Fonts & Scripts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50 dark:bg-gray-900 dark:text-gray-100 min-h-screen">

    <div class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-6">

            {{-- Judul Halaman[cite: 2] --}}
            <div class="text-center mb-10">
                <h2 class="text-2xl sm:text-3xl font-black text-gray-800 dark:text-gray-200">
                    Portal Output <span class="text-indigo-600 dark:text-indigo-400">e-Kinerja</span>
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Cek rincian Rencana Aksi dan Output berdasarkan
                    Target Waktu (TW).</p>
            </div>

            {{-- Card Form Pencarian & Filter TW[cite: 2] --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sm:p-8">
                <form action="{{ route('ekinerja.search') }}" method="GET"
                    class="flex flex-col md:flex-row gap-4 items-end">

                    {{-- Input NIP[cite: 2] --}}
                    <div class="flex-1 w-full">
                        <label for="nip"
                            class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Nomor
                            Induk Pegawai (NIP)</label>
                        <input type="number" id="nip" name="nip" value="{{ request('nip', $nip) }}" required
                            placeholder="Masukkan NIP Anda..."
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-base sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition">
                    </div>

                    {{-- Select Filter TW --}}
                    <div class="flex-1 w-full md:w-1/3">
                        <label for="tw"
                            class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Filter
                            Target Waktu</label>
                        <select id="tw" name="tw"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-base sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition">
                            <option value="semua" {{ request('tw', $filter_tw)=='semua' ? 'selected' : '' }}>-- Semua TW
                                --</option>
                            <option value="TW 1" {{ request('tw', $filter_tw)=='TW 1' ? 'selected' : '' }}>Triwulan 1
                                (TW 1)</option>
                            <option value="TW 2" {{ request('tw', $filter_tw)=='TW 2' ? 'selected' : '' }}>Triwulan 2
                                (TW 2)</option>
                            <option value="TW 3" {{ request('tw', $filter_tw)=='TW 3' ? 'selected' : '' }}>Triwulan 3
                                (TW 3)</option>
                            <option value="TW 4" {{ request('tw', $filter_tw)=='TW 4' ? 'selected' : '' }}>Triwulan 4
                                (TW 4)</option>
                        </select>
                    </div>

                    {{-- Tombol Aksi[cite: 2] --}}
                    <div class="flex flex-col-reverse sm:flex-row gap-3 w-full md:w-auto">
                        @if($nip)
                        <a href="{{ route('ekinerja.index') }}"
                            class="w-full sm:w-auto px-6 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg text-sm font-bold text-center hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            Reset
                        </a>
                        @endif
                        <button type="submit"
                            class="w-full sm:w-auto px-8 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-md hover:bg-indigo-700 hover:shadow-lg transition">
                            Tampilkan
                        </button>
                    </div>
                </form>
            </div>

            {{-- HASIL PENCARIAN --}}
            @if($nip && !$employee)
            {{-- Alert Tidak Ditemukan[cite: 2] --}}
            <div
                class="p-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl text-center">
                <svg class="w-12 h-12 text-red-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-bold text-red-700 dark:text-red-400">Data Tidak Ditemukan</h3>
                <p class="text-sm text-red-600 mt-1">Tidak ada data pegawai yang cocok dengan NIP: <strong
                        class="text-base">{{ $nip }}</strong>.</p>
            </div>

            @elseif($employee)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- KIRI: Profil Pegawai[cite: 2] --}}
                <div
                    class="md:col-span-1 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden self-start sticky top-6">
                    <div
                        class="p-6 bg-indigo-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 text-center">
                        <div
                            class="w-24 h-24 bg-indigo-200 text-indigo-700 rounded-full mx-auto flex items-center justify-center text-3xl font-black mb-4 uppercase shadow-inner">
                            {{ substr($employee->nama_lengkap, 0, 1) }}
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">{{
                            $employee->nama_lengkap }}</h3>
                        <p class="text-sm text-gray-500 font-mono mt-1">{{ $employee->nip ?? '-' }}</p>
                        <span
                            class="inline-block mt-3 px-3 py-1 bg-indigo-600 text-white text-[10px] font-bold uppercase rounded-full tracking-widest">
                            {{ $employee->kategori_pegawai ?? 'Pegawai' }}
                        </span>
                    </div>
                    <div class="p-6 space-y-4 text-sm bg-white dark:bg-gray-800">
                        <div>
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Status
                                Kepegawaian</span>
                            <span class="block font-semibold text-gray-800 dark:text-gray-200">{{
                                $employee->status_kepegawaian ?? 'Belum Diatur' }}</span>
                        </div>
                    </div>
                </div>

                {{-- KANAN: Daftar Dokumen e-Kinerja (Pengganti area "Dalam Perbaikan") --}}
                <div class="md:col-span-2 space-y-6">

                    @forelse($data_kategori as $kategori)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        {{-- Header Kategori --}}
                        <div class="bg-gray-50 dark:bg-gray-900/50 p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-bold text-gray-800 dark:text-gray-200 text-lg flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z">
                                    </path>
                                </svg>
                                {{ $kategori->nama_kategori }}
                            </h3>
                        </div>

                        {{-- Body RHK --}}
                        <div class="p-5 space-y-6">
                            @forelse($kategori->rhk as $rhk)
                            <div class="space-y-3">
                                <h4 class="font-semibold text-sm text-indigo-700 dark:text-indigo-400 leading-snug">
                                    {{ $rhk->deskripsi_rhk }}
                                </h4>

                                <div class="pl-4 border-l-2 border-indigo-100 dark:border-indigo-900/50 space-y-4">
                                    @foreach($rhk->rencanaAksi as $ra)
                                    <div
                                        class="bg-gray-50/50 dark:bg-gray-800/50 rounded-lg p-4 border border-gray-100 dark:border-gray-700">
                                        <div class="mb-3">
                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                Rencana Aksi</p>
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{
                                                $ra->deskripsi_ra }}</p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                                Target & Output</p>
                                            <ul class="space-y-2">
                                                @foreach($ra->outputTarget as $output)
                                                <li
                                                    class="flex items-start gap-3 bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-700 shadow-sm">
                                                    <span
                                                        class="shrink-0 mt-0.5 px-2 py-1 bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300 text-xs font-bold rounded">
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
                    @empty
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                            </path>
                        </svg>
                        <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300">Dokumen Tidak Ditemukan</h3>
                        <p class="text-sm text-gray-500 mt-1">Tidak ada dokumen output yang sesuai dengan filter Target
                            Waktu yang dipilih.</p>
                    </div>
                    @endforelse

                </div>
            </div>
            @endif

        </div>
    </div>

</body>

</html>