<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIAKAD - SDN Tomang 03 Pagi</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-slate-800">SIAKAD <span
                            class="text-indigo-600">TOMANG 03</span></span>
                </div>
                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                    @auth
                    <a href="{{ url('/dashboard') }}"
                        class="text-sm font-semibold text-slate-600 hover:text-indigo-600 transition">Dashboard</a>
                    @else
                    <a href="{{ route('login') }}"
                        class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 transition shadow-md shadow-indigo-100">Login
                        Staff</a>
                    @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="flex-grow pt-32 pb-16 px-4">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-16">
                <div
                    class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-600 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6 border border-indigo-100">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    Sistem Informasi Akademik
                </div>
                <h1 class="text-4xl md:text-6xl font-black text-slate-900 mb-6 tracking-tight">
                    Selamat Datang di Portal <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-600">SDN Tomang
                        03 Pagi</span>
                </h1>
                <p class="text-lg text-slate-500 max-w-2xl mx-auto font-medium leading-relaxed">
                    Akses informasi akademik, pengumuman kelulusan, dan layanan pendidikan dalam satu platform digital
                    yang modern dan transparan.
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Kelulusan Card -->
                <div
                    class="group bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-xl shadow-slate-200/50 hover:border-indigo-300 transition-all duration-300 hover:-translate-y-1">
                    <div
                        class="w-14 h-14 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-scroll"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-3">Pengumuman Kelulusan</h3>
                    <p class="text-slate-500 mb-8 font-medium">Cek status kelulusan siswa kelas VI tahun ajaran
                        2025/2026 secara online menggunakan NISN.</p>
                    <a href="{{ route('kelulusan.index') }}"
                        class="inline-flex items-center gap-2 text-indigo-600 font-bold hover:gap-4 transition-all">
                        Cek Kelulusan <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <!-- Form Login Card (Quick Access) -->
                <div
                    class="bg-slate-900 p-8 rounded-[2.5rem] text-white shadow-xl shadow-slate-900/20 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-10 text-8xl">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 relative z-10">Portal Staff & Guru</h3>
                    <p class="text-slate-400 mb-8 font-medium relative z-10">Kelola data siswa, nilai, dan administrasi
                        sekolah melalui dashboard khusus tenaga pendidik.</p>

                    @auth
                    <a href="{{ url('/dashboard') }}"
                        class="inline-flex items-center justify-center w-full py-4 bg-white text-slate-900 rounded-2xl font-bold hover:bg-slate-100 transition relative z-10">
                        Buka Dashboard
                    </a>
                    @else
                    <form method="POST" action="{{ route('login') }}" class="space-y-4 relative z-10">
                        @csrf
                        @if (session('error'))
                        <div class="text-xs text-red-400 font-bold">{{ session('error') }}</div>
                        @endif
                        <div class="flex flex-col gap-3">
                            <input type="email" name="email" placeholder="Email Staff" required
                                class="bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500">
                            <input type="password" name="password" placeholder="Password" required
                                class="bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500">
                            <button type="submit"
                                class="w-full py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-500 transition">
                                Masuk ke Sistem
                            </button>
                        </div>
                    </form>
                    @endauth
                </div>
            </div>

            <!-- Stats -->
            <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="p-6">
                    <div class="text-3xl font-black text-slate-800">100%</div>
                    <div class="text-sm text-slate-500 font-bold uppercase tracking-wider">Transparansi</div>
                </div>
                <div class="p-6">
                    <div class="text-3xl font-black text-slate-800">Real-time</div>
                    <div class="text-sm text-slate-500 font-bold uppercase tracking-wider">Data Update</div>
                </div>
                <div class="p-6">
                    <div class="text-3xl font-black text-slate-800">Secure</div>
                    <div class="text-sm text-slate-500 font-bold uppercase tracking-wider">Database</div>
                </div>
                <div class="p-6">
                    <div class="text-3xl font-black text-slate-800">24/7</div>
                    <div class="text-sm text-slate-500 font-bold uppercase tracking-wider">Akses Online</div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex justify-center items-center gap-4 mb-6">
                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400">
                    <i class="fab fa-facebook-f"></i>
                </div>
                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400">
                    <i class="fab fa-instagram"></i>
                </div>
                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400">
                    <i class="fas fa-globe"></i>
                </div>
            </div>
            <p class="text-slate-400 text-sm font-medium">
                &copy; {{ date('Y') }} SDN Tomang 03 Pagi. All rights reserved.
            </p>
            <div class="mt-2 flex justify-center gap-6 text-xs font-bold text-slate-400 uppercase tracking-widest">
                <a href="#" class="hover:text-indigo-600">Kebijakan Privasi</a>
                <a href="#" class="hover:text-indigo-600">Bantuan</a>
                <a href="#" class="hover:text-indigo-600">Kontak</a>
            </div>
        </div>
    </footer>

</body>

</html>
</div>

<div class="flex items-center justify-end mt-6">
    <button type="submit"
        class="inline-flex items-center w-full justify-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25">
        Periksa Hasil Kelulusan
    </button>
</div>
</form>
</div>
</div>
</div>
</x-guest-layout>