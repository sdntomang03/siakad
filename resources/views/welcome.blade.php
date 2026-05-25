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
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>

<body class="bg-pattern min-h-screen flex items-center justify-center p-4 sm:p-6" x-data="countdownTimer()">

    <div
        class="max-w-3xl w-full bg-white/95 backdrop-blur-sm rounded-[2rem] sm:rounded-[3rem] shadow-2xl shadow-indigo-200/40 border border-white/50 p-6 sm:p-12 relative overflow-hidden text-center">

        <div
            class="absolute -top-20 -left-20 w-72 h-72 bg-indigo-100 rounded-full blur-3xl opacity-50 pointer-events-none">
        </div>
        <div
            class="absolute -bottom-20 -right-20 w-72 h-72 bg-blue-50 rounded-full blur-3xl opacity-60 pointer-events-none">
        </div>

        <div class="relative z-10">
            <div
                class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-slate-800 to-slate-900 text-white rounded-3xl flex items-center justify-center text-4xl sm:text-5xl mx-auto mb-6 shadow-xl shadow-slate-200 transform hover:scale-105 transition-transform duration-300">
                <i class="fas fa-graduation-cap"></i>
            </div>

            <div
                class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-600 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest mb-4 border border-indigo-100">
                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                Tahun Ajaran 2025/2026
            </div>

            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-800 tracking-tight mb-2">
                Pengumuman Kelulusan
            </h1>
            <p class="text-slate-500 font-bold text-base sm:text-lg mb-8 uppercase tracking-widest">
                SDN Tomang 03 Pagi
            </p>
        </div>

        <div
            class="max-w-2xl mx-auto mb-8 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/60 rounded-2xl p-4 sm:p-5 flex items-start sm:items-center gap-4 text-left shadow-sm relative overflow-hidden z-10 transition-all hover:shadow-md">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-amber-100/50 rounded-full blur-xl pointer-events-none">
            </div>

            <div
                class="w-12 h-12 rounded-xl bg-white text-amber-500 flex items-center justify-center shrink-0 shadow-sm border border-amber-100">
                <i class="fas fa-calendar-alt text-xl"></i>
            </div>

            <div class="flex-1">
                <h3 class="text-amber-800 font-black text-xs sm:text-sm uppercase tracking-wider mb-1">Informasi Jadwal
                    Rilis</h3>
                <p class="text-amber-700/90 text-xs sm:text-sm font-semibold leading-relaxed">
                    Akses sistem pengumuman kelulusan akan resmi dibuka pada <br class="hidden sm:block">
                    <strong class="font-black text-amber-900 bg-amber-200/30 px-1 rounded">Selasa, 2 Juni 2026 pukul
                        10.00 WIB</strong>.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-4 gap-3 sm:gap-5 max-w-2xl mx-auto mb-10 relative z-10" x-show="!isFinished"
            x-transition>

            <div
                class="bg-white rounded-2xl border-2 border-slate-100 p-3 sm:p-5 shadow-sm flex flex-col items-center justify-center">
                <div class="text-3xl sm:text-5xl font-black text-slate-800 mb-1 font-mono tracking-tighter"
                    x-text="days">00</div>
                <div class="text-[9px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest">Hari</div>
            </div>

            <div
                class="bg-white rounded-2xl border-2 border-slate-100 p-3 sm:p-5 shadow-sm flex flex-col items-center justify-center">
                <div class="text-3xl sm:text-5xl font-black text-slate-800 mb-1 font-mono tracking-tighter"
                    x-text="hours">00</div>
                <div class="text-[9px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest">Jam</div>
            </div>

            <div
                class="bg-white rounded-2xl border-2 border-slate-100 p-3 sm:p-5 shadow-sm flex flex-col items-center justify-center">
                <div class="text-3xl sm:text-5xl font-black text-slate-800 mb-1 font-mono tracking-tighter"
                    x-text="minutes">00</div>
                <div class="text-[9px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest">Menit</div>
            </div>

            <div
                class="bg-gradient-to-b from-indigo-50 to-white rounded-2xl border-2 border-indigo-100 p-3 sm:p-5 shadow-sm relative overflow-hidden flex flex-col items-center justify-center">
                <div class="absolute top-0 inset-x-0 h-1 bg-indigo-500"></div>
                <div class="text-3xl sm:text-5xl font-black text-indigo-600 mb-1 font-mono tracking-tighter"
                    x-text="seconds">00</div>
                <div class="text-[9px] sm:text-xs font-bold text-indigo-400 uppercase tracking-widest relative">Detik
                </div>
            </div>

        </div>

        <div class="mb-10 relative z-10" x-show="isFinished" x-cloak x-transition.opacity.duration.1000ms>
            <div
                class="inline-flex items-center gap-3 bg-emerald-50 border-2 border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-black text-lg shadow-sm">
                <i class="fas fa-door-open text-2xl animate-bounce"></i>
                <span>Gerbang Pengumuman Telah Dibuka!</span>
            </div>
        </div>

        <div class="relative z-10 max-w-md mx-auto">
            <a :href="isFinished ? '{{ route('pengumuman.index') }}' : '#'"
                class="w-full flex items-center justify-center gap-3 px-8 sm:px-10 py-4 sm:py-5 rounded-2xl font-black text-base sm:text-lg transition-all duration-300 transform group"
                :class="isFinished
                            ? 'bg-slate-900 hover:bg-black text-white shadow-xl shadow-slate-300 hover:-translate-y-1 active:scale-95 cursor-pointer'
                            : 'bg-slate-50 text-slate-400 border-2 border-slate-100 cursor-not-allowed'">

                <i class="fas transition-transform duration-300"
                    :class="isFinished ? 'fa-arrow-right group-hover:translate-x-1' : 'fa-lock'"></i>
                <span x-text="isFinished ? 'Cek Hasil Kelulusan Sekarang' : 'Menunggu Waktu Rilis...'"></span>
            </a>

            <p class="mt-5 text-xs sm:text-sm font-bold text-slate-400" x-show="!isFinished">
                <i class="fas fa-info-circle mr-1"></i> Silakan kembali lagi saat hitung mundur selesai.
            </p>
            <p class="mt-5 text-xs sm:text-sm font-bold text-slate-500 bg-slate-50 py-2 rounded-lg border border-slate-100"
                x-show="isFinished" x-cloak>
                <i class="fas fa-id-card text-slate-400 mr-1"></i> Siapkan <span class="text-slate-700">NISN</span> dan
                <span class="text-slate-700">Tanggal Lahir</span> Anda.
            </p>
        </div>

    </div>

    <script>
        function countdownTimer() {
                return {
                    // Target disetel ke 2 Juni 2026 Pukul 10:00:00
                    targetDate: new Date("2026-06-02T10:00:00").getTime(),

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