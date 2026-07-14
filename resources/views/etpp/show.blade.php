<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Cek e-TPP Pegawai</title>

    <!-- Fonts & Scripts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50 dark:bg-gray-900 dark:text-gray-100 min-h-screen">

    <div class="py-12 px-4 sm:px-6 lg:px-8">
        <!-- Wrapper Utama diperlebar menjadi max-w-5xl -->
        <div class="max-w-5xl mx-auto space-y-6">

            {{-- Judul Halaman --}}
            <div class="text-center mb-10">
                <h2 class="text-2xl sm:text-3xl font-black text-gray-800 dark:text-gray-200">
                    Portal <span class="text-indigo-600 dark:text-indigo-400">e-TPP Pegawai</span>
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Silakan masukkan NIP untuk melihat rincian
                    e-TPP Anda.</p>
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
                            placeholder="Ketik NIP di sini..."
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
                            Cari Data
                        </button>
                    </div>
                </form>
            </div>

            {{-- HASIL PENCARIAN --}}
            @if($nip && !$employee)
            {{-- Alert NIP Tidak Ditemukan --}}
            <div
                class="p-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl text-center">
                <svg class="w-12 h-12 text-red-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-bold text-red-700 dark:text-red-400">Pegawai Tidak Ditemukan</h3>
                <p class="text-sm text-red-600 mt-1">Tidak ada data pegawai yang cocok dengan NIP: <strong
                        class="text-base">{{ $nip }}</strong>.</p>
            </div>
            @elseif($employee)
            {{-- Jika Ditemukan: Grid 3 Kolom untuk Desktop, Bertumpuk untuk Mobile --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Kolom Kiri (1 bagian): Profil Pegawai --}}
                <div
                    class="md:col-span-1 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
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
                    <div class="p-6 space-y-4 text-sm">
                        <div>
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Status</span>
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

                {{-- Kolom Kanan (2 bagian): Rincian e-TPP --}}
                <div class="md:col-span-2 space-y-6">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sm:p-8">

                        <div
                            class="flex justify-between items-center mb-6 border-b border-gray-100 dark:border-gray-700 pb-4">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Estimasi Penerimaan e-TPP</h3>
                            <span
                                class="px-3 py-1 bg-green-100 text-green-800 border border-green-200 rounded-lg text-xs font-bold uppercase tracking-wider">Bulan
                                Aktif</span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-8">
                            <div
                                class="p-5 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-center sm:text-left">
                                <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Skor
                                    Kehadiran</span>
                                <span class="text-3xl font-black text-indigo-600 dark:text-indigo-400">100%</span>
                            </div>
                            <div
                                class="p-5 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-center sm:text-left">
                                <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Skor
                                    Kinerja</span>
                                <span class="text-3xl font-black text-green-600 dark:text-green-400">100%</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center">
                                <span class="text-sm font-bold text-gray-600 dark:text-gray-400">Besaran Dasar
                                    TPP</span>
                                <span class="text-base font-semibold text-gray-800 dark:text-gray-200">Rp
                                    2.000.000</span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center">
                                <span class="text-sm font-bold text-red-500">Potongan Pajak (PPh 21)</span>
                                <span class="text-base font-semibold text-red-500">- Rp 100.000</span>
                            </div>

                            <div
                                class="mt-6 pt-6 border-t-2 border-dashed border-gray-200 dark:border-gray-600 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                                <span
                                    class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-wider">Total
                                    Diterima (Netto)</span>
                                <span class="text-2xl sm:text-3xl font-black text-green-600 dark:text-green-400">Rp
                                    1.900.000</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

</body>

</html>