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
            background-image:
                linear-gradient(var(--paper-2) 1px, transparent 1px);
            background-size: 100% 2.75rem;
            background-position: 0 6.5rem;
        }

        .seal {
            background: radial-gradient(circle at 35% 30%, var(--gold), var(--gold-dark));
            box-shadow: 0 8px 24px -8px rgba(185, 130, 42, 0.55), inset 0 0 0 3px rgba(255, 255, 255, 0.35);
        }

        .ticket {
            position: relative;
            background: var(--card);
        }

        .ticket::before,
        .ticket::after {
            content: "";
            position: absolute;
            width: 28px;
            height: 28px;
            background: var(--paper);
            border-radius: 999px;
            top: 50%;
            transform: translateY(-50%);
        }

        .ticket::before {
            left: -14px;
        }

        .ticket::after {
            right: -14px;
        }

        @media (min-width: 768px) {

            .ticket::before,
            .ticket::after {
                left: auto;
                right: auto;
                top: -14px;
                transform: translateX(-50%);
            }

            .ticket::before {
                left: 38%;
                top: -14px;
            }

            .ticket::after {
                left: 38%;
                top: auto;
                bottom: -14px;
            }
        }

        .certificate {
            background: var(--card);
            border: 1.5px dashed rgba(20, 38, 34, 0.25);
        }

        .stat-divider:not(:last-child) {
            border-right: 1px solid var(--line);
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
                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                    @auth
                    <a href="{{ url('/dashboard') }}"
                        class="text-sm font-semibold text-[var(--pine)] hover:text-[var(--gold-dark)] transition">Dashboard</a>
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
        <section class="max-w-6xl mx-auto px-5 sm:px-8 pt-20 pb-16">
            <div class="grid lg:grid-cols-[1.05fr_0.95fr] gap-14 items-center">
                <div>
                    <div
                        class="inline-flex items-center gap-2 border border-[var(--pine)]/25 text-[var(--pine)] px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-[0.14em] mb-7">
                        <span class="w-1.5 h-1.5 rounded-full bg-[var(--gold)]"></span>
                        Sistem Informasi Akademik &middot; Tahun Ajaran 2025/2026
                    </div>
                    <h1
                        class="font-display text-5xl md:text-6xl font-semibold text-[var(--ink)] leading-[1.08] tracking-tight mb-6">
                        Satu portal untuk<br>
                        setiap langkah akademik<br>
                        <span class="italic text-[var(--pine-light)]">di SDN Tomang 03 Pagi.</span>
                    </h1>
                    <p class="text-[var(--moss)] text-lg leading-relaxed max-w-lg mb-9">
                        Cek pengumuman kelulusan, pantau data siswa, dan kelola administrasi sekolah
                        secara digital, transparan, dan dapat diakses kapan saja.
                    </p>
                    <div class="flex flex-wrap items-center gap-4 mb-12">
                        <a href="#kelulusan"
                            class="inline-flex items-center gap-2 px-6 py-3.5 bg-[var(--gold)] text-[var(--ink)] rounded-full font-bold hover:bg-[var(--gold-dark)] hover:text-white transition shadow-md shadow-[var(--gold)]/20">
                            Cek Kelulusan <i class="fas fa-arrow-right text-sm"></i>
                        </a>
                        <a href="#staff"
                            class="inline-flex items-center gap-2 px-6 py-3.5 border border-[var(--ink)]/15 text-[var(--ink)] rounded-full font-bold hover:border-[var(--ink)]/40 transition">
                            Portal Staff &amp; Guru
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

                <!-- Signature element: digital certificate card -->
                <div class="relative mx-auto max-w-sm w-full">
                    <div class="certificate rounded-[1.75rem] p-8 rotate-1 shadow-2xl shadow-[var(--ink)]/10">
                        <div class="flex items-center justify-between mb-8">
                            <span class="text-[0.65rem] font-bold uppercase tracking-[0.2em] text-[var(--moss)]">Ijazah
                                Digital</span>
                            <span class="text-[0.65rem] font-bold uppercase tracking-[0.2em] text-[var(--moss)]">No.
                                03/2026</span>
                        </div>
                        <div class="flex justify-center mb-8">
                            <div
                                class="w-20 h-20 rounded-full seal flex items-center justify-center text-white text-2xl">
                                <i class="fas fa-award"></i>
                            </div>
                        </div>
                        <p class="text-center font-display italic text-lg text-[var(--ink)] leading-snug mb-6">
                            &ldquo;Dinyatakan lulus dengan predikat terbaik&rdquo;
                        </p>
                        <div
                            class="border-t border-dashed border-[var(--ink)]/15 pt-5 flex justify-between text-xs text-[var(--moss)] font-semibold">
                            <span>SDN Tomang 03 Pagi</span>
                            <span>Jakarta Barat</span>
                        </div>
                    </div>
                    <div class="absolute -bottom-4 -left-4 w-24 h-24 rounded-full bg-[var(--gold)]/15 -z-10"></div>
                    <div class="absolute -top-6 -right-6 w-16 h-16 rounded-full bg-[var(--pine)]/10 -z-10"></div>
                </div>
            </div>
        </section>

        <!-- Feature cards -->
        <section class="max-w-6xl mx-auto px-5 sm:px-8 pb-20">
            <div class="grid md:grid-cols-2 gap-10 md:gap-16">
                <div id="kelulusan"
                    class="ticket rounded-[1.75rem] p-8 border border-[var(--line)] shadow-lg shadow-[var(--ink)]/5">
                    <div
                        class="w-14 h-14 rounded-2xl bg-[var(--gold)]/15 text-[var(--gold-dark)] flex items-center justify-center text-2xl mb-6">
                        <i class="fas fa-scroll"></i>
                    </div>
                    <h3 class="font-display text-2xl font-semibold text-[var(--ink)] mb-3">Pengumuman Kelulusan</h3>
                    <p class="text-[var(--moss)] mb-8 leading-relaxed">
                        Cek status kelulusan siswa kelas VI tahun ajaran 2025/2026 secara online
                        menggunakan Nomor Induk Siswa Nasional (NISN).
                    </p>
                    <a href="#"
                        class="inline-flex items-center gap-2 text-[var(--pine)] font-bold hover:gap-3.5 transition-all">
                        Cek Kelulusan <i class="fas fa-arrow-right text-sm"></i>
                    </a>
                </div>

                <div id="staff" class="ticket rounded-[1.75rem] p-8 text-white relative overflow-hidden"
                    style="background: var(--ink);">
                    <div class="absolute top-0 right-0 p-8 opacity-[0.08] text-8xl">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h3 class="font-display text-2xl font-semibold mb-3 relative z-10">Portal Staff &amp; Guru</h3>
                    <p class="text-white/60 mb-8 leading-relaxed relative z-10">
                        Kelola data siswa, nilai, dan administrasi sekolah melalui dashboard
                        khusus tenaga pendidik.
                    </p>

                    @auth
                    <a href="{{ url('/dashboard') }}"
                        class="inline-flex items-center justify-center w-full py-4 bg-[var(--gold)] text-[var(--ink)] rounded-2xl font-bold hover:bg-[var(--gold-dark)] hover:text-white transition relative z-10">
                        Buka Dashboard
                    </a>
                    @else
                    <form method="POST" action="{{ route('login') }}" class="space-y-3.5 relative z-10">
                        @csrf
                        @if (session('error'))
                        <div class="text-xs text-red-400 font-bold">{{ session('error') }}</div>
                        @endif
                        <div class="flex flex-col gap-3">
                            <input type="email" name="email" placeholder="Email Staff" required
                                class="bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm placeholder:text-white/40 focus:ring-2 focus:ring-[var(--gold)] focus:outline-none">
                            <input type="password" name="password" placeholder="Password" required
                                class="bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm placeholder:text-white/40 focus:ring-2 focus:ring-[var(--gold)] focus:outline-none">
                            <button type="submit"
                                class="w-full py-3.5 bg-[var(--gold)] text-[var(--ink)] rounded-xl font-bold hover:bg-[var(--gold-dark)] hover:text-white transition">
                                Masuk ke Sistem
                            </button>
                        </div>
                    </form>
                    @endauth
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-[var(--card)] border-t border-[var(--line)] py-12">
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