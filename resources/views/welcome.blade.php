<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIAKAD - SDN Tomang 03 Pagi</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=fraunces:500,600,700,800,600i|plus-jakarta-sans:400,500,600,700,800"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --ink: #142622;
            --pine: #1F3A33;
            --pine-light: #2C5348;
            --paper: #EEF2EA;
            --paper-2: #E4EADD;
            --card: #FFFFFF;
            --line: #D7DECB;
            --moss: #5B6A61;
            --gold: #D9A441;
            --gold-dark: #B9822A;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--paper);
            color: var(--ink);
        }

        .font-display {
            font-family: 'Fraunces', serif;
        }

        .bg-paper {
            background-color: var(--paper);
            background-image: linear-gradient(var(--paper-2) 1px, transparent 1px);
            background-size: 100% 2.75rem;
            background-position: 0 6.5rem;
        }

        .seal {
            background: radial-gradient(circle at 35% 30%, var(--gold), var(--gold-dark));
            box-shadow: 0 8px 24px -8px rgba(185, 130, 42, 0.55), inset 0 0 0 3px rgba(255, 255, 255, 0.35);
        }

        .stat-divider:not(:last-child) {
            border-right: 1px solid var(--line);
        }

        .deck-card {
            position: absolute;
            width: 15rem;
            border-radius: 1.5rem;
            background: var(--card);
            border: 1px solid var(--line);
            box-shadow: 0 20px 45px -20px rgba(20, 38, 34, 0.35);
            padding: 1.5rem;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col antialiased">

    <nav class="fixed w-full z-50 bg-[var(--paper)]/90 backdrop-blur-md border-b border-[var(--line)]">
        <div class="max-w-6xl mx-auto px-5 sm:px-8">
            <div class="flex justify-between h-[4.5rem] items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full seal flex items-center justify-center text-white text-sm">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="leading-tight">
                        <div class="font-display font-semibold text-lg tracking-tight text-[var(--ink)]">SIAKAD</div>
                        <div class="text-[0.65rem] font-bold uppercase tracking-[0.18em] text-[var(--moss)] -mt-0.5">
                            Tomang 03 Pagi</div>
                    </div>
                </div>

                <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-[var(--pine)]">
                    <a href="#modul" class="hover:text-[var(--gold-dark)] transition">Modul</a>
                    <a href="#keunggulan" class="hover:text-[var(--gold-dark)] transition">Keunggulan</a>
                    <a href="#kontak" class="hover:text-[var(--gold-dark)] transition">Kontak</a>
                </div>

                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                    @auth
                    <a href="{{ url('/dashboard') }}"
                        class="px-5 py-2.5 bg-[var(--pine)] text-white rounded-full text-sm font-bold hover:bg-[var(--ink)] transition shadow-sm">
                        Dashboard
                    </a>
                    @else
                    <a href="{{ route('login') }}"
                        class="px-5 py-2.5 bg-[var(--pine)] text-white rounded-full text-sm font-bold hover:bg-[var(--ink)] transition shadow-sm">
                        Masuk
                    </a>
                    @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow pt-[4.5rem] bg-paper">
        <!-- Hero -->
        <section class="max-w-6xl mx-auto px-5 sm:px-8 pt-20 pb-28">
            <div class="grid lg:grid-cols-[1.05fr_0.95fr] gap-14 items-center">
                <div>
                    <div
                        class="inline-flex items-center gap-2 border border-[var(--pine)]/25 text-[var(--pine)] px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-[0.14em] mb-7">
                        <span class="w-1.5 h-1.5 rounded-full bg-[var(--gold)]"></span>
                        Sistem Informasi Akademik &middot; Tahun Ajaran 2025/2026
                    </div>
                    <h1
                        class="font-display text-5xl md:text-6xl font-semibold text-[var(--ink)] leading-[1.08] tracking-tight mb-6">
                        Satu sistem untuk<br>
                        seluruh urusan akademik<br>
                        <span class="italic text-[var(--pine-light)]">SDN Tomang 03 Pagi.</span>
                    </h1>
                    <p class="text-[var(--moss)] text-lg leading-relaxed max-w-lg mb-9">
                        Presensi, nilai, keuangan, hingga jadwal pelajaran dikelola dalam satu
                        portal digital yang rapi, transparan, dan mudah diakses oleh guru maupun staf.
                    </p>
                    <div class="flex flex-wrap items-center gap-4 mb-12">
                        @if (Route::has('login'))
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center gap-2 px-6 py-3.5 bg-[var(--gold)] text-[var(--ink)] rounded-full font-bold hover:bg-[var(--gold-dark)] hover:text-white transition shadow-md shadow-[var(--gold)]/20">
                            Masuk ke Sistem <i class="fas fa-arrow-right text-sm"></i>
                        </a>
                        @endif
                        <a href="#modul"
                            class="inline-flex items-center gap-2 px-6 py-3.5 border border-[var(--ink)]/15 text-[var(--ink)] rounded-full font-bold hover:border-[var(--ink)]/40 transition">
                            Lihat Modul
                        </a>
                    </div>
                    <div class="flex flex-wrap gap-x-8 gap-y-4">
                        <div class="stat-divider pr-8">
                            <div class="font-display text-2xl font-semibold text-[var(--ink)]">Real-time</div>
                            <div class="text-xs font-bold uppercase tracking-wider text-[var(--moss)]">Pembaruan Data
                            </div>
                        </div>
                        <div class="stat-divider pr-8">
                            <div class="font-display text-2xl font-semibold text-[var(--ink)]">Terenkripsi</div>
                            <div class="text-xs font-bold uppercase tracking-wider text-[var(--moss)]">Basis Data Aman
                            </div>
                        </div>
                        <div>
                            <div class="font-display text-2xl font-semibold text-[var(--ink)]">24/7</div>
                            <div class="text-xs font-bold uppercase tracking-wider text-[var(--moss)]">Akses Online
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Signature element: stacked module cards -->
                <div class="relative h-[22rem] hidden lg:block">
                    <div class="deck-card -rotate-6 top-4 left-2">
                        <div
                            class="w-10 h-10 rounded-xl bg-[var(--pine)]/10 text-[var(--pine)] flex items-center justify-center mb-4">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="font-display font-semibold text-[var(--ink)] mb-1">Presensi</div>
                        <div class="text-xs text-[var(--moss)]">Kehadiran harian real-time</div>
                    </div>
                    <div class="deck-card rotate-2 top-16 left-32">
                        <div
                            class="w-10 h-10 rounded-xl bg-[var(--gold)]/15 text-[var(--gold-dark)] flex items-center justify-center mb-4">
                            <i class="fas fa-square-poll-vertical"></i>
                        </div>
                        <div class="font-display font-semibold text-[var(--ink)] mb-1">Nilai &amp; Rapor</div>
                        <div class="text-xs text-[var(--moss)]">Rekap otomatis per semester</div>
                    </div>
                    <div class="deck-card -rotate-2 top-32 left-8">
                        <div
                            class="w-10 h-10 rounded-xl bg-[var(--pine)]/10 text-[var(--pine)] flex items-center justify-center mb-4">
                            <i class="fas fa-sack-dollar"></i>
                        </div>
                        <div class="font-display font-semibold text-[var(--ink)] mb-1">Keuangan Sekolah</div>
                        <div class="text-xs text-[var(--moss)]">Anggaran &amp; belanja tercatat</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modul -->
        <section id="modul" class="max-w-6xl mx-auto px-5 sm:px-8 pb-24">
            <div class="max-w-xl mb-12">
                <div class="text-xs font-bold uppercase tracking-[0.18em] text-[var(--gold-dark)] mb-3">Modul Sistem
                </div>
                <h2 class="font-display text-3xl md:text-4xl font-semibold text-[var(--ink)] tracking-tight">
                    Enam modul inti, satu portal terpadu
                </h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    class="bg-[var(--card)] rounded-[1.5rem] p-7 border border-[var(--line)] shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all">
                    <div
                        class="w-12 h-12 rounded-xl bg-[var(--pine)]/10 text-[var(--pine)] flex items-center justify-center text-xl mb-5">
                        <i class="fas fa-id-card-clip"></i>
                    </div>
                    <h3 class="font-display text-lg font-semibold text-[var(--ink)] mb-2">Data Induk Siswa &amp; Guru
                    </h3>
                    <p class="text-sm text-[var(--moss)] leading-relaxed">Satu basis data terpusat untuk profil,
                        riwayat, dan status seluruh siswa dan tenaga pendidik.</p>
                </div>

                <div
                    class="bg-[var(--card)] rounded-[1.5rem] p-7 border border-[var(--line)] shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all">
                    <div
                        class="w-12 h-12 rounded-xl bg-[var(--gold)]/15 text-[var(--gold-dark)] flex items-center justify-center text-xl mb-5">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="font-display text-lg font-semibold text-[var(--ink)] mb-2">Presensi Digital</h3>
                    <p class="text-sm text-[var(--moss)] leading-relaxed">Pencatatan kehadiran harian dengan rekap
                        otomatis sakit, izin, dan alfa per bulan.</p>
                </div>

                <div
                    class="bg-[var(--card)] rounded-[1.5rem] p-7 border border-[var(--line)] shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all">
                    <div
                        class="w-12 h-12 rounded-xl bg-[var(--pine)]/10 text-[var(--pine)] flex items-center justify-center text-xl mb-5">
                        <i class="fas fa-file-pen"></i>
                    </div>
                    <h3 class="font-display text-lg font-semibold text-[var(--ink)] mb-2">Ujian &amp; Bank Soal</h3>
                    <p class="text-sm text-[var(--moss)] leading-relaxed">Ujian berbasis komputer dengan penilaian
                        otomatis dan bank soal terorganisir per kelas.</p>
                </div>

                <div
                    class="bg-[var(--card)] rounded-[1.5rem] p-7 border border-[var(--line)] shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all">
                    <div
                        class="w-12 h-12 rounded-xl bg-[var(--gold)]/15 text-[var(--gold-dark)] flex items-center justify-center text-xl mb-5">
                        <i class="fas fa-square-poll-vertical"></i>
                    </div>
                    <h3 class="font-display text-lg font-semibold text-[var(--ink)] mb-2">Nilai &amp; Rapor</h3>
                    <p class="text-sm text-[var(--moss)] leading-relaxed">Pengolahan nilai per mata pelajaran hingga
                        cetak rapor siap tanda tangan.</p>
                </div>

                <div
                    class="bg-[var(--card)] rounded-[1.5rem] p-7 border border-[var(--line)] shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all">
                    <div
                        class="w-12 h-12 rounded-xl bg-[var(--pine)]/10 text-[var(--pine)] flex items-center justify-center text-xl mb-5">
                        <i class="fas fa-sack-dollar"></i>
                    </div>
                    <h3 class="font-display text-lg font-semibold text-[var(--ink)] mb-2">Keuangan &amp; Anggaran</h3>
                    <p class="text-sm text-[var(--moss)] leading-relaxed">Pencatatan transaksi belanja dan anggaran
                        sekolah yang rapi dan bisa diaudit.</p>
                </div>

                <div
                    class="bg-[var(--card)] rounded-[1.5rem] p-7 border border-[var(--line)] shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all">
                    <div
                        class="w-12 h-12 rounded-xl bg-[var(--gold)]/15 text-[var(--gold-dark)] flex items-center justify-center text-xl mb-5">
                        <i class="fas fa-calendar-days"></i>
                    </div>
                    <h3 class="font-display text-lg font-semibold text-[var(--ink)] mb-2">Jadwal &amp; Kalender Akademik
                    </h3>
                    <p class="text-sm text-[var(--moss)] leading-relaxed">Jadwal pelajaran, agenda sekolah, dan kalender
                        akademik dalam satu tampilan.</p>
                </div>
            </div>
        </section>

        <!-- Keunggulan -->
        <section id="keunggulan" class="bg-[var(--ink)] text-white">
            <div class="max-w-6xl mx-auto px-5 sm:px-8 py-20">
                <div class="max-w-xl mb-12">
                    <div class="text-xs font-bold uppercase tracking-[0.18em] text-[var(--gold)] mb-3">Keunggulan</div>
                    <h2 class="font-display text-3xl md:text-4xl font-semibold tracking-tight">
                        Dibangun untuk kebutuhan sekolah negeri
                    </h2>
                </div>
                <div class="grid md:grid-cols-3 gap-10">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-xl mb-5">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <h3 class="font-display text-lg font-semibold mb-2">Aman &amp; Transparan</h3>
                        <p class="text-sm text-white/60 leading-relaxed">Setiap perubahan data tercatat, sehingga proses
                            administrasi dapat dipertanggungjawabkan.</p>
                    </div>
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-xl mb-5">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3 class="font-display text-lg font-semibold mb-2">Cepat &amp; Ringan</h3>
                        <p class="text-sm text-white/60 leading-relaxed">Diakses dari perangkat apa pun tanpa instalasi
                            tambahan, cocok untuk kondisi jaringan sekolah.</p>
                    </div>
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-xl mb-5">
                            <i class="fas fa-people-group"></i>
                        </div>
                        <h3 class="font-display text-lg font-semibold mb-2">Mudah Digunakan</h3>
                        <p class="text-sm text-white/60 leading-relaxed">Antarmuka sederhana yang dirancang agar guru
                            dan staf dapat langsung menggunakannya.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        @if (Route::has('login'))
        <section class="max-w-6xl mx-auto px-5 sm:px-8 py-20">
            <div class="bg-[var(--card)] border border-[var(--line)] rounded-[2rem] px-8 py-14 text-center shadow-sm">
                <h2 class="font-display text-3xl md:text-4xl font-semibold text-[var(--ink)] tracking-tight mb-4">
                    Siap mengelola akademik lebih rapi?
                </h2>
                <p class="text-[var(--moss)] max-w-md mx-auto mb-8">
                    Masuk dengan akun staf atau guru untuk mulai menggunakan seluruh modul SIAKAD.
                </p>
                <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-2 px-7 py-3.5 bg-[var(--pine)] text-white rounded-full font-bold hover:bg-[var(--ink)] transition shadow-md">
                    Masuk ke Sistem <i class="fas fa-arrow-right text-sm"></i>
                </a>
            </div>
        </section>
        @endif
    </main>

    <footer id="kontak" class="bg-[var(--card)] border-t border-[var(--line)] py-12">
        <div class="max-w-6xl mx-auto px-5 sm:px-8 flex flex-col items-center gap-6">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full seal flex items-center justify-center text-white text-xs">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <span class="font-display font-semibold text-[var(--ink)]">SIAKAD Tomang 03 Pagi</span>
            </div>
            <div class="flex justify-center items-center gap-3">
                <a href="#"
                    class="w-9 h-9 rounded-full bg-[var(--paper-2)] flex items-center justify-center text-[var(--moss)] hover:text-[var(--pine)] transition">
                    <i class="fab fa-facebook-f text-sm"></i>
                </a>
                <a href="#"
                    class="w-9 h-9 rounded-full bg-[var(--paper-2)] flex items-center justify-center text-[var(--moss)] hover:text-[var(--pine)] transition">
                    <i class="fab fa-instagram text-sm"></i>
                </a>
                <a href="#"
                    class="w-9 h-9 rounded-full bg-[var(--paper-2)] flex items-center justify-center text-[var(--moss)] hover:text-[var(--pine)] transition">
                    <i class="fas fa-globe text-sm"></i>
                </a>
            </div>
            <p class="text-[var(--moss)] text-sm font-medium text-center">
                &copy; {{ date('Y') }} SDN Tomang 03 Pagi. All rights reserved.
            </p>
            <div class="flex justify-center gap-6 text-xs font-bold text-[var(--moss)] uppercase tracking-widest">
                <a href="#" class="hover:text-[var(--pine)]">Kebijakan Privasi</a>
                <a href="#" class="hover:text-[var(--pine)]">Bantuan</a>
                <a href="#" class="hover:text-[var(--pine)]">Kontak</a>
            </div>
        </div>
    </footer>

</body>

</html>