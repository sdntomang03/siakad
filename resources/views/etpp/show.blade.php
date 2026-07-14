<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kelengkapan Dokumen e-TPP Pegawai</title>

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
                    Portal Kelengkapan <span class="text-indigo-600 dark:text-indigo-400">Syarat e-TPP</span>
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Cek status validasi dan kelengkapan dokumen
                    pencairan e-TPP Anda.</p>
            </div>

            {{-- Card Form Pencarian --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sm:p-8">
                <form action="{{ route('etpp.search') }}" method="POST"
                    class="flex flex-col md:flex-row gap-4 items-end">
                    @csrf
                    <div class="flex-1 w-full">
                        <label for="nip"
                            class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Nomor
                            Induk Pegawai (NIP)</label>
                        <input type="number" id="nip" name="nip" value="{{ old('nip', $nip) }}" required
                            placeholder="Masukkan NIP Anda..."
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-base sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition">
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row gap-3 w-full md:w-auto">
                        @if($nip)
                        <a href="{{ route('etpp.show') }}"
                            class="w-full sm:w-auto px-6 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg text-sm font-bold text-center hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            Reset
                        </a>
                        @endif
                        <button type="submit"
                            class="w-full sm:w-auto px-8 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-md hover:bg-indigo-700 hover:shadow-lg transition">
                            Cari Dokumen
                        </button>
                    </div>
                </form>
            </div>

            {{-- HASIL PENCARIAN --}}
            @if($nip && !$employee)
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

                {{-- KIRI: Profil Pegawai --}}
                <div
                    class="md:col-span-1 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden self-start">
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
                            {{ $employee->kategori_pegawai }}
                        </span>
                    </div>
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
                </div>

                {{-- KANAN: Rekap Kelengkapan Dokumen --}}
                <div class="md:col-span-2 space-y-6">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sm:p-8">

                        <div
                            class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6 border-b border-gray-100 dark:border-gray-700 pb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Daftar Dokumen e-TPP</h3>
                                <p class="text-xs text-gray-500 mt-1">Periode Bulan Berjalan</p>
                            </div>
                            <div
                                class="px-4 py-2 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg text-center">
                                <span
                                    class="block text-[10px] font-bold text-yellow-600 dark:text-yellow-400 uppercase tracking-wider">Status
                                    Keseluruhan</span>
                                <span class="block text-sm font-black text-yellow-700 dark:text-yellow-500">BELUM
                                    LENGKAP</span>
                            </div>
                        </div>

                        {{-- Progress Bar Kelengkapan --}}
                        <div class="mb-8">
                            <div class="flex justify-between text-xs font-bold mb-1">
                                <span class="text-gray-600 dark:text-gray-400">Progres Kelengkapan</span>
                                <span class="text-indigo-600 dark:text-indigo-400">75%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                <div class="bg-indigo-600 h-2.5 rounded-full" style="width: 75%"></div>
                            </div>
                        </div>

                        {{-- List Dokumen Syarat (Mockup) --}}
                        <div class="space-y-4">

                            {{-- Dokumen 1: Valid --}}
                            <div
                                class="flex items-start gap-4 p-4 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                                <div class="mt-0.5">
                                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">Rekap Presensi
                                        (Absensi Elektronik)</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kehadiran 100%. Data
                                        ditarik otomatis dari sistem absensi.</p>
                                </div>
                                <div class="hidden sm:block">
                                    <span
                                        class="px-2.5 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-[10px] font-bold uppercase rounded border border-green-200 dark:border-green-800">Selesai</span>
                                </div>
                            </div>

                            {{-- Dokumen 2: Valid --}}
                            <div
                                class="flex items-start gap-4 p-4 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                                <div class="mt-0.5">
                                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">Surat Keputusan (SK)
                                        Pembagian Tugas</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sesuai dengan jam mengajar
                                        (Min. 24 Jam).</p>
                                </div>
                                <div class="hidden sm:block">
                                    <span
                                        class="px-2.5 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-[10px] font-bold uppercase rounded border border-green-200 dark:border-green-800">Selesai</span>
                                </div>
                            </div>

                            {{-- Dokumen 3: Menunggu Validasi --}}
                            <div
                                class="flex items-start gap-4 p-4 rounded-xl border border-yellow-200 dark:border-yellow-700 bg-yellow-50 dark:bg-yellow-900/20">
                                <div class="mt-0.5">
                                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">Laporan Kinerja
                                        Bulanan (Jurnal)</h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Dokumen telah diunggah.
                                        Sedang menunggu persetujuan dari Kepala Sekolah.</p>
                                </div>
                                <div class="hidden sm:block">
                                    <span
                                        class="px-2.5 py-1 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 text-[10px] font-bold uppercase rounded border border-yellow-200 dark:border-yellow-800">Menunggu</span>
                                </div>
                            </div>

                            {{-- Dokumen 4: Belum Upload --}}
                            @if($employee->tugas_tambahan)
                            <div
                                class="flex items-start gap-4 p-4 rounded-xl border border-red-200 dark:border-red-700 bg-red-50 dark:bg-red-900/20">
                                <div class="mt-0.5">
                                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">Laporan Tugas
                                        Tambahan</h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Dokumen bukti pelaksanaan
                                        tugas sebagai <strong>{{ $employee->tugas_tambahan }}</strong> belum diserahkan.
                                    </p>
                                </div>
                                <div class="hidden sm:block">
                                    <span
                                        class="px-2.5 py-1 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 text-[10px] font-bold uppercase rounded border border-red-200 dark:border-red-800">Kurang</span>
                                </div>
                            </div>
                            @endif

                        </div>

                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

</body>

</html>