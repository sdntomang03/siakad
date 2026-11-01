<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{
        // Cek localStorage dulu. Jika kosong (kunjungan pertama), gunakan deteksi layar (Desktop=True, HP=False)
        sidebarOpen: localStorage.getItem('sidebarOpen') !== null
                     ? localStorage.getItem('sidebarOpen') === 'true'
                     : window.innerWidth >= 768
    }" x-init="$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value))">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIAKAD') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-[#f5f6f2] dark:bg-slate-900 text-slate-900 dark:text-slate-100">
    <a href="#main-content"
        class="sr-only z-[60] rounded-md bg-white px-4 py-2 text-sm font-semibold text-brand-900 shadow focus:not-sr-only focus:fixed focus:left-4 focus:top-4">
        Lewati ke konten utama
    </a>
    <div class="flex h-screen h-[100dvh] min-w-0 overflow-hidden">

        @include('layouts.sidebar')

        <div class="flex min-w-0 flex-1 flex-col overflow-y-auto overflow-x-hidden">

            @include('layouts.navigation')

            @isset($header)
            <header id="page-header" class="border-b border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                <div class="mx-auto max-w-screen-2xl px-4 py-4 sm:px-6 lg:px-8">
                    @php
                        $breadcrumbRoute = request()->route()?->getName();
                        $breadcrumbSegments = array_values(array_filter(explode('.', $breadcrumbRoute ?? ''), fn ($segment) => $segment !== 'index'));
                        $breadcrumbRoots = [
                            'academic-years' => 'Tahun Ajaran',
                            'admin' => 'Administrasi',
                            'assignments' => 'Tugas',
                            'assessments' => 'Penilaian',
                            'attendances' => 'Absensi',
                            'books' => 'Buku',
                            'book-loans' => 'Peminjaman Buku',
                            'classrooms' => 'Kelas',
                            'cp' => 'Capaian Pembelajaran',
                            'dashboard' => 'Dashboard',
                            'etpp' => 'e-TPP',
                            'holidays' => 'Hari Libur',
                            'jadwal' => 'Jadwal Pelajaran',
                            'kelulusan' => 'Kelulusan',
                            'modul' => 'Modul Ajar AI',
                            'observations' => 'Observasi',
                            'operator' => 'Operasional',
                            'piket' => 'Piket',
                            'profile' => 'Profil',
                            'report-submissions' => 'Pengembalian Rapor',
                            'students' => 'Siswa',
                            'subjects' => 'Mata Pelajaran',
                            'superadmin' => 'Manajemen Pusat',
                            'teacher-notes' => 'Catatan Siswa',
                        ];
                        $breadcrumbActions = [
                            'create' => 'Tambah',
                            'edit' => 'Ubah',
                            'export' => 'Ekspor',
                            'generator' => 'Generator',
                            'hasil' => 'Hasil',
                            'import' => 'Impor',
                            'input' => 'Input',
                            'monitor' => 'Pemantauan',
                            'report' => 'Laporan',
                            'show' => 'Detail',
                            'store' => 'Simpan',
                            'update' => 'Perbarui',
                            'validasi' => 'Validasi',
                        ];
                    @endphp
                    <nav aria-label="Breadcrumb" class="mb-2 flex min-w-0 items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        @if ($breadcrumbRoute === 'dashboard' || empty($breadcrumbSegments))
                            <span aria-current="page" class="truncate font-medium text-brand-800 dark:text-brand-200">Dashboard</span>
                        @else
                            <a href="{{ route('dashboard') }}"
                                class="shrink-0 rounded-sm hover:text-brand-700 focus-visible:text-brand-800 dark:hover:text-brand-200 dark:focus-visible:text-white">
                                Dashboard
                            </a>
                            @foreach ($breadcrumbSegments as $segment)
                                @php
                                    $label = $breadcrumbRoots[$segment] ?? $breadcrumbActions[$segment] ?? \Illuminate\Support\Str::headline($segment);
                                @endphp
                                <span aria-hidden="true" class="text-slate-400">/</span>
                                <span @if ($loop->last) aria-current="page" @endif
                                    class="truncate {{ $loop->last ? 'font-medium text-brand-800 dark:text-brand-200' : 'text-slate-500 dark:text-slate-400' }}">
                                    {{ $label }}
                                </span>
                            @endforeach
                        @endif
                    </nav>
                    <div class="min-w-0 text-slate-900 dark:text-slate-100">
                        {{ $header }}
                    </div>
                </div>
            </header>
            @endisset

            <main id="main-content" class="min-w-0 flex-1" tabindex="-1">
                {{ $slot }}
            </main>
        </div>
    </div>
    <x-sweet-alert />
</body>

</html>