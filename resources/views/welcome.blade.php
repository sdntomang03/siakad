<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Portal layanan akademik dan administrasi SDN Tomang 03 Pagi.">
    <title>SIAKAD - SDN Tomang 03 Pagi</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <style>
        :root {
            --ink: #173C32;
            --pine: #1D4B3E;
            --paper: #F5F6F2;
            --line: #D7DED8;
            --moss: #46564E;
            --accent: #8A5E13;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: var(--paper);
            color: var(--ink);
        }

        section[id] {
            scroll-margin-top: 1.5rem;
        }

        summary {
            list-style: none;
        }

        summary::-webkit-details-marker {
            display: none;
        }

        .mobile-nav-close {
            display: none;
        }

        .mobile-nav[open] .mobile-nav-open {
            display: none;
        }

        .mobile-nav[open] .mobile-nav-close {
            display: block;
        }

        @layer base {
            :where(a[href], summary) {
                cursor: pointer;
            }

            :where(a[href], summary):active {
                transform: translateY(1px);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }
        }
    </style>
</head>

<body class="min-h-screen antialiased">
    <a href="#main-content"
        class="sr-only z-50 rounded-md bg-white px-4 py-2 text-sm font-semibold text-[var(--ink)] shadow focus:not-sr-only focus:fixed focus:left-4 focus:top-4">
        Lewati ke konten utama
    </a>

    <header class="border-b border-[var(--line)] bg-[var(--paper)]">
        <div class="mx-auto max-w-7xl px-4 sm:px-8">
            <div class="flex min-h-[4.5rem] items-center justify-between gap-4">
                <a href="{{ url('/') }}" class="inline-flex min-h-11 min-w-0 items-center gap-3 rounded-md"
                    aria-label="SIAKAD SDN Tomang 03 Pagi, beranda">
                    <span class="min-w-0 leading-tight">
                        <span class="block text-base font-semibold tracking-tight text-[var(--ink)]">SIAKAD</span>
                        <span class="block truncate text-xs font-medium text-[var(--moss)]">SDN Tomang 03 Pagi</span>
                    </span>
                </a>

                <nav aria-label="Navigasi halaman"
                    class="hidden items-center gap-6 text-sm font-semibold text-[var(--ink)] lg:flex">
                    <a href="#layanan" class="inline-flex min-h-11 items-center rounded-sm hover:text-[var(--pine)]">
                        Layanan
                    </a>
                    <a href="#cara-akses" class="inline-flex min-h-11 items-center rounded-sm hover:text-[var(--pine)]">
                        Cara akses
                    </a>
                    <a href="{{ route('pengumuman.index') }}"
                        class="inline-flex min-h-11 items-center rounded-sm hover:text-[var(--pine)]">
                        Pengumuman kelulusan
                    </a>
                </nav>

                <div class="hidden items-center gap-2 lg:flex">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="inline-flex min-h-11 items-center justify-center rounded-md bg-[var(--pine)] px-5 text-sm font-semibold text-white hover:bg-[var(--ink)]">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="inline-flex min-h-11 items-center justify-center rounded-md px-4 text-sm font-semibold text-[var(--ink)] hover:bg-white">
                                Masuk
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="inline-flex min-h-11 items-center justify-center rounded-md bg-[var(--pine)] px-5 text-sm font-semibold text-white hover:bg-[var(--ink)]">
                                    Daftar akun
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>

                <details class="mobile-nav relative lg:hidden">
                    <summary
                        aria-label="Buka atau tutup navigasi"
                        class="flex h-11 w-11 items-center justify-center rounded-md text-sm font-semibold text-[var(--ink)] hover:bg-white active:bg-[#DCEBE3]">
                        <svg aria-hidden="true" class="mobile-nav-open h-6 w-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg aria-hidden="true" class="mobile-nav-close h-6 w-6" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </summary>
                    <div class="absolute right-0 top-full z-50 mt-2 w-[min(20rem,calc(100vw-2rem))] rounded-md border border-[var(--line)] bg-white p-2">
                        <nav aria-label="Menu utama" class="grid">
                            <a href="#layanan"
                                class="flex min-h-11 items-center rounded-sm px-3 text-sm font-medium hover:bg-[var(--paper)]">
                                Layanan
                            </a>
                            <a href="#cara-akses"
                                class="flex min-h-11 items-center rounded-sm px-3 text-sm font-medium hover:bg-[var(--paper)]">
                                Cara akses
                            </a>
                            <a href="{{ route('pengumuman.index') }}"
                                class="flex min-h-11 items-center rounded-sm px-3 text-sm font-medium hover:bg-[var(--paper)]">
                                Pengumuman kelulusan
                            </a>
                            @if (Route::has('login'))
                                <div class="mt-2 grid gap-2 border-t border-[var(--line)] pt-2">
                                    @auth
                                        <a href="{{ url('/dashboard') }}"
                                            class="flex min-h-11 items-center rounded-md bg-[var(--pine)] px-3 text-sm font-semibold text-white hover:bg-[var(--ink)]">
                                            Buka dashboard
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}"
                                            class="flex min-h-11 items-center rounded-md bg-[var(--pine)] px-3 text-sm font-semibold text-white hover:bg-[var(--ink)]">
                                            Masuk
                                        </a>
                                        @if (Route::has('register'))
                                            <a href="{{ route('register') }}"
                                                class="flex min-h-11 items-center rounded-md px-3 text-sm font-semibold text-[var(--ink)] hover:bg-[var(--paper)]">
                                                Daftar akun
                                            </a>
                                        @endif
                                    @endauth
                                </div>
                            @endif
                        </nav>
                    </div>
                </details>
            </div>
        </div>
    </header>

    <main id="main-content" tabindex="-1">
        <section aria-labelledby="welcome-title" class="mx-auto max-w-7xl px-4 py-12 sm:px-8 sm:py-16 lg:py-20">
            <div class="grid items-start gap-10 lg:grid-cols-[minmax(0,1.08fr)_minmax(20rem,0.92fr)] lg:items-center lg:gap-16">
                <div class="min-w-0">
                    <p class="mb-5 text-sm font-semibold text-[var(--pine)]">
                        Sistem Informasi Akademik · SDN Tomang 03 Pagi
                    </p>
                    <h1 id="welcome-title"
                        class="max-w-[15ch] text-[clamp(2.5rem,5.5vw,4.5rem)] font-semibold leading-[1.04] tracking-tight text-[var(--ink)]">
                        Kegiatan belajar dan layanan sekolah, dalam satu portal.
                    </h1>
                    <p class="mt-6 max-w-2xl text-base leading-relaxed text-[var(--moss)] sm:text-lg">
                        SIAKAD membantu siswa, guru, dan tenaga sekolah mengakses layanan akademik serta administrasi
                        SDN Tomang 03 Pagi. Menu yang terlihat menyesuaikan hak akses akun.
                    </p>
                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                    class="inline-flex min-h-12 items-center justify-center rounded-md bg-[var(--pine)] px-6 text-base font-semibold text-white hover:bg-[var(--ink)]">
                                    Buka dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="inline-flex min-h-12 items-center justify-center rounded-md bg-[var(--pine)] px-6 text-base font-semibold text-white hover:bg-[var(--ink)]">
                                    Masuk ke SIAKAD
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}"
                                        class="inline-flex min-h-12 items-center justify-center rounded-md border border-[var(--line)] bg-white px-5 text-base font-semibold text-[var(--ink)] hover:border-[var(--pine)]">
                                        Daftar akun
                                    </a>
                                @endif
                            @endauth
                        @endif
                        <a href="#layanan"
                            class="inline-flex min-h-11 items-center rounded-md px-3 text-sm font-semibold text-[var(--pine)] hover:bg-white">
                            Lihat layanan
                        </a>
                    </div>
                </div>

                <aside id="layanan" aria-labelledby="menu-preview-title"
                    class="min-w-0 rounded-lg bg-[var(--ink)] px-6 py-7 text-white sm:px-8 sm:py-9">
                    <div class="border-b border-white/20 pb-5">
                        <p class="text-sm font-medium text-[#DCEBE3]">Cakupan layanan</p>
                        <h2 id="menu-preview-title" class="mt-2 text-2xl font-semibold tracking-tight">
                            Layanan sekolah
                        </h2>
                    </div>

                    <ul class="divide-y divide-white/15">
                        <li class="py-4">
                            <h3 class="font-semibold">Data akademik</h3>
                            <p class="mt-1 text-sm leading-relaxed text-white/85">
                                Tahun ajaran, kelas, mata pelajaran, dan data siswa.
                            </p>
                        </li>
                        <li class="py-4">
                            <h3 class="font-semibold">Kegiatan belajar</h3>
                            <p class="mt-1 text-sm leading-relaxed text-white/85">
                                Absensi, penilaian, tugas, catatan siswa, dan jadwal pelajaran.
                            </p>
                        </li>
                        <li class="py-4">
                            <h3 class="font-semibold">Operasional</h3>
                            <p class="mt-1 text-sm leading-relaxed text-white/85">
                                Buku, peminjaman, aset sekolah, dan pengembalian rapor.
                            </p>
                        </li>
                        <li class="pt-4">
                            <h3 class="font-semibold">Layanan guru</h3>
                            <p class="mt-1 text-sm leading-relaxed text-white/85">
                                e-TPP dan generator modul ajar.
                            </p>
                        </li>
                    </ul>
                    <p class="mt-5 border-t border-white/20 pt-4 text-sm leading-relaxed text-[#DCEBE3]">
                        Setiap akun hanya menampilkan menu sesuai perannya.
                    </p>
                </aside>
            </div>
        </section>

        <section id="cara-akses" aria-labelledby="access-title" class="scroll-mt-6">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-14 sm:px-8 sm:py-16 md:grid-cols-[0.8fr_1.2fr] md:gap-16">
                <div>
                    <p class="text-sm font-semibold text-[var(--accent)]">Mulai menggunakan portal</p>
                    <h2 id="access-title"
                        class="mt-3 text-3xl font-semibold leading-tight tracking-tight text-[var(--ink)] sm:text-4xl">
                        Masuk, lalu lanjutkan sesuai peran akun.
                    </h2>
                </div>

                <ol class="divide-y divide-[var(--line)] border-y border-[var(--line)]">
                    <li class="grid gap-3 py-5 sm:grid-cols-[2.5rem_1fr] sm:gap-5">
                        <span aria-hidden="true" class="text-lg font-semibold text-[var(--accent)]">01</span>
                        <div>
                            <h3 class="font-semibold text-[var(--ink)]">Gunakan akun Anda</h3>
                            <p class="mt-1 text-sm leading-relaxed text-[var(--moss)]">
                                Masuk dengan akun yang terdaftar, atau buat akun melalui halaman pendaftaran.
                            </p>
                            @if (Route::has('register'))
                                @guest
                                    <a href="{{ route('register') }}"
                                        class="mt-3 inline-flex min-h-11 items-center rounded-md text-sm font-semibold text-[var(--pine)] underline underline-offset-4 hover:text-[var(--ink)]">
                                        Buka pendaftaran
                                    </a>
                                @endguest
                            @endif
                        </div>
                    </li>
                    <li class="grid gap-3 py-5 sm:grid-cols-[2.5rem_1fr] sm:gap-5">
                        <span aria-hidden="true" class="text-lg font-semibold text-[var(--accent)]">02</span>
                        <div>
                            <h3 class="font-semibold text-[var(--ink)]">Pilih layanan dari dashboard</h3>
                            <p class="mt-1 text-sm leading-relaxed text-[var(--moss)]">
                                Dashboard menampilkan menu yang dapat digunakan oleh akun Anda.
                            </p>
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}"
                                        class="mt-3 inline-flex min-h-11 items-center rounded-md text-sm font-semibold text-[var(--pine)] underline underline-offset-4 hover:text-[var(--ink)]">
                                        Buka dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                        class="mt-3 inline-flex min-h-11 items-center rounded-md text-sm font-semibold text-[var(--pine)] underline underline-offset-4 hover:text-[var(--ink)]">
                                        Masuk ke SIAKAD
                                    </a>
                                @endauth
                            @endif
                        </div>
                    </li>
                </ol>
            </div>
        </section>

        <section aria-labelledby="announcement-title" class="border-t border-[var(--line)] bg-[#EAF1EC]">
            <div class="mx-auto flex max-w-7xl flex-col gap-5 px-4 py-9 sm:px-8 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 id="announcement-title" class="text-xl font-semibold text-[var(--ink)]">
                        Informasi kelulusan
                    </h2>
                    <p class="mt-1 text-sm leading-relaxed text-[var(--moss)]">
                        Buka portal pengumuman kelulusan SDN Tomang 03 Pagi.
                    </p>
                </div>
                <a href="{{ route('pengumuman.index') }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-md border border-[var(--pine)] px-5 text-sm font-semibold text-[var(--ink)] hover:bg-white">
                    Buka portal kelulusan
                </a>
            </div>
        </section>
    </main>

    <footer class="border-t border-[var(--line)] bg-[var(--paper)]">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-6 text-sm text-[var(--moss)] sm:px-8 md:flex-row md:items-center md:justify-between">
            <a href="{{ url('/') }}" class="inline-flex min-h-11 items-center gap-3 rounded-md"
                aria-label="SIAKAD SDN Tomang 03 Pagi, beranda">
                <span class="font-semibold text-[var(--ink)]">SIAKAD · SDN Tomang 03 Pagi</span>
            </a>
            <nav aria-label="Navigasi footer" class="flex flex-wrap gap-x-5 gap-y-2">
                <a href="#layanan" class="inline-flex min-h-11 items-center hover:text-[var(--ink)]">Layanan</a>
                <a href="#cara-akses" class="inline-flex min-h-11 items-center hover:text-[var(--ink)]">Cara akses</a>
                <a href="{{ route('pengumuman.index') }}"
                    class="inline-flex min-h-11 items-center hover:text-[var(--ink)]">Pengumuman kelulusan</a>
            </nav>
            <span>&copy; {{ date('Y') }} SDN Tomang 03 Pagi</span>
        </div>
    </footer>
</body>

</html>
