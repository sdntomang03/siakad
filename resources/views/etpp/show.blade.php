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

                {{-- KIRI: Profil Pegawai (Tetap Tampil) --}}
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

                {{-- KANAN: Fitur Sedang Dalam Perbaikan --}}
                <div class="md:col-span-2 flex items-stretch">
                    <div
                        class="w-full bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 sm:p-12 flex flex-col items-center justify-center text-center">

                        {{-- Ikon Ilustrasi (Animasi Wrench/Gear) --}}
                        <div class="relative w-24 h-24 mb-6">
                            <div
                                class="absolute inset-0 bg-yellow-100 dark:bg-yellow-900/30 rounded-full animate-ping opacity-75">
                            </div>
                            <div
                                class="relative w-24 h-24 bg-yellow-100 dark:bg-yellow-900/50 text-yellow-600 dark:text-yellow-400 rounded-full flex items-center justify-center">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <h3 class="text-xl sm:text-2xl font-black text-gray-800 dark:text-gray-200 mb-2">
                            Fitur Sedang Dalam Pengembangan
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto leading-relaxed">
                            Mohon maaf, modul <strong class="text-gray-700 dark:text-gray-300">Kelengkapan Dokumen
                                e-TPP</strong> saat ini sedang dalam proses sinkronisasi dan perbaikan sistem. Silakan
                            cek kembali secara berkala.
                        </p>

                        <div class="mt-8">
                            <span
                                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold rounded-full uppercase tracking-widest border border-gray-200 dark:border-gray-600">
                                Estimasi Selesai: Segera
                            </span>
                        </div>

                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

</body>

</html>