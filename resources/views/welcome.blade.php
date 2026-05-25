<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengumuman Kelulusan - SDN Tomang 03 Pagi</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        .bg-pattern {
            background-color: #f8fafc;
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</head>

<body class="bg-pattern min-h-screen flex items-center justify-center p-4 sm:p-6" x-data="countdownTimer()">

    <div
        class="max-w-3xl w-full bg-white rounded-[2rem] sm:rounded-[3rem] shadow-2xl shadow-indigo-100/50 border border-slate-100 p-8 sm:p-12 relative overflow-hidden text-center">

        <div
            class="absolute -top-20 -left-20 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-60 pointer-events-none">
        </div>
        <div
            class="absolute -bottom-20 -right-20 w-64 h-64 bg-blue-50 rounded-full blur-3xl opacity-60 pointer-events-none">
        </div>

        <div class="relative z-10">
            <div
                class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-indigo-600 to-blue-600 text-white rounded-3xl flex items-center justify-center text-4xl sm:text-5xl mx-auto mb-6 shadow-lg shadow-indigo-200 rotate-3 transform hover:rotate-0 transition-transform">
                <i class="fas fa-graduation-cap -rotate-3"></i>
            </div>

            <h2 class="text-sm font-bold text-indigo-600 uppercase tracking-widest mb-2">Sistem Informasi Akademik</h2>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-800 tracking-tight mb-2">
                Pengumuman Kelulusan
            </h1>
            <p class="text-slate-500 font-medium text-lg sm:text-xl mb-8">
                SDN Tomang 03 Pagi - Tahun Ajaran 2025/2026
            </p>
        </div>

        <div class="grid grid-cols-4 gap-3 sm:gap-6 max-w-2xl mx-auto mb-10 relative z-10" x-show="!isFinished"
            x-transition>

            <div class="bg-slate-50 rounded-2xl border border-slate-100 p-3 sm:p-4 shadow-sm">
                <div class="text-3xl sm:text-5xl font-black text-slate-800 mb-1" x-text="days">00</div>
                <div class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Hari</div>
            </div>

            <div class="bg-slate-50 rounded-2xl border border-slate-100 p-3 sm:p-4 shadow-sm">
                <div class="text-3xl sm:text-5xl font-black text-slate-800 mb-1" x-text="hours">00</div>
                <div class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Jam</div>
            </div>

            <div class="bg-slate-50 rounded-2xl border border-slate-100 p-3 sm:p-4 shadow-sm">
                <div class="text-3xl sm:text-5xl font-black text-slate-800 mb-1" x-text="minutes">00</div>
                <div class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Menit</div>
            </div>

            <div class="bg-slate-50 rounded-2xl border border-slate-100 p-3 sm:p-4 shadow-sm relative overflow-hidden">
                <div class="absolute inset-0 bg-indigo-50 opacity-0 animate-ping"></div>
                <div class="text-3xl sm:text-5xl font-black text-indigo-600 mb-1 relative" x-text="seconds">00</div>
                <div class="text-[10px] sm:text-xs font-bold text-indigo-400 uppercase tracking-wider relative">Detik
                </div>
            </div>

        </div>

        <div class="mb-10 relative z-10" x-show="isFinished" x-cloak x-transition.opacity.duration.1000ms>
            <div
                class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 px-6 py-3 rounded-full font-bold text-lg animate-bounce">
                <i class="fas fa-unlock-alt"></i> Pengumuman Telah Dibuka!
            </div>
        </div>

        <div class="relative z-10">
            <a :href="isFinished ? '{{ route('pengumuman') }}' : '#'"
                class="inline-flex items-center justify-center gap-3 w-full sm:w-auto px-8 sm:px-12 py-4 sm:py-5 rounded-2xl font-black text-lg transition-all duration-300 transform"
                :class="isFinished
                            ? 'bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white shadow-xl shadow-indigo-200 hover:-translate-y-1 active:scale-95 cursor-pointer'
                            : 'bg-slate-100 text-slate-400 cursor-not-allowed'">

                <i class="fas" :class="isFinished ? 'fa-search' : 'fa-lock'"></i>
                <span x-text="isFinished ? 'Cek Hasil Kelulusan Sekarang' : 'Menunggu Waktu Rilis...'"></span>
            </a>

            <p class="mt-6 text-sm font-medium text-slate-500" x-show="!isFinished">
                Silakan kembali lagi saat waktu hitung mundur selesai.
            </p>
            <p class="mt-6 text-sm font-medium text-slate-500" x-show="isFinished" x-cloak>
                Siapkan NISN dan Tanggal Lahir Anda untuk melihat hasil.
            </p>
        </div>

    </div>

    <script>
        function countdownTimer() {
                return {
                    // ========================================================
                    // UBAH TANGGAL DAN WAKTU PENGUMUMAN DI SINI
                    // Format: Tahun-Bulan-TanggalTJam:Menit:Detik
                    // Contoh: 2026-06-15T10:00:00
                    // ========================================================
                    targetDate: new Date("2026-06-15T10:00:00").getTime(),

                    days: '00',
                    hours: '00',
                    minutes: '00',
                    seconds: '00',
                    isFinished: false,
                    timer: null,

                    init() {
                        this.updateCountdown();
                        this.timer = setInterval(() => {
                            this.updateCountdown();
                        }, 1000);
                    },

                    updateCountdown() {
                        const now = new Date().getTime();
                        const distance = this.targetDate - now;

                        if (distance <= 0) {
                            clearInterval(this.timer);
                            this.isFinished = true;
                            this.days = '00';
                            this.hours = '00';
                            this.minutes = '00';
                            this.seconds = '00';
                            return;
                        }

                        this.days = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, '0');
                        this.hours = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                        this.minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                        this.seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
                    }
                }
            }
    </script>
</body>

</html>