<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengumuman Kelulusan - SDN Tomang 03 Pagi</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        .bg-pattern {
            background-color: #f5f6f2;
            background-image: none;
        }
    </style>
</head>

<body class="bg-pattern min-h-screen flex items-center justify-center p-4 sm:p-6 overflow-x-hidden"
    x-data="countdownTimer()">

    <div
        class="relative z-10 w-full max-w-3xl rounded-lg border border-slate-200 bg-white p-5 text-center sm:p-8">

        <div class="relative z-10">
            <div
                class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-lg bg-brand-800 text-white sm:h-24 sm:w-24">
                <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14v7"></path>
                </svg>
            </div>

            <div
                class="mb-4 inline-flex items-center rounded-md border border-brand-200 bg-brand-50 px-3 py-1.5 text-sm font-medium text-brand-900">
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
            class="relative z-10 mx-auto mb-10 max-w-2xl rounded-lg border border-amber-200 bg-white p-1.5">
            <div
            class="relative flex items-start gap-4 rounded-md bg-amber-50 p-4 sm:items-center sm:p-5">

            <div
                class="relative flex h-12 w-12 shrink-0 items-center justify-center rounded-md border border-amber-200 bg-white text-amber-700">
                    <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>

                <div class="flex-1 text-left relative z-10">
                    <h3
                        class="mb-1 flex items-center gap-2 text-sm font-semibold text-amber-900">
                        Informasi Jadwal Rilis
                    </h3>
                    <p class="text-amber-700/90 text-xs sm:text-sm font-semibold leading-relaxed">
                        Akses sistem pengumuman kelulusan akan resmi dibuka pada <br class="hidden sm:block">
                        <span
                            class="mt-1 inline-block text-base font-semibold text-amber-900 sm:text-lg">
                            Selasa, 2 Juni 2026 pukul 10.00 WIB
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-4 gap-3 sm:gap-5 max-w-2xl mx-auto mb-10 relative z-10" x-show="!isFinished"
            x-transition>

            <div
                class="bg-white rounded-lg border-b-4 border-slate-200 p-3 sm:p-5 shadow-sm flex flex-col items-center justify-center relative overflow-hidden group hover:border-brand-400 transition-colors">
                <div class="text-3xl sm:text-5xl font-black text-slate-800 mb-1 font-mono tracking-tighter"
                    x-text="days">00</div>
                <div class="text-[9px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest">Hari</div>
            </div>

            <div
                class="bg-white rounded-lg border-b-4 border-slate-200 p-3 sm:p-5 shadow-sm flex flex-col items-center justify-center relative overflow-hidden group hover:border-brand-400 transition-colors">
                <div class="text-3xl sm:text-5xl font-black text-slate-800 mb-1 font-mono tracking-tighter"
                    x-text="hours">00</div>
                <div class="text-[9px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest">Jam</div>
            </div>

            <div
                class="bg-white rounded-lg border-b-4 border-slate-200 p-3 sm:p-5 shadow-sm flex flex-col items-center justify-center relative overflow-hidden group hover:border-brand-400 transition-colors">
                <div class="text-3xl sm:text-5xl font-black text-slate-800 mb-1 font-mono tracking-tighter"
                    x-text="minutes">00</div>
                <div class="text-[9px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest">Menit</div>
            </div>

            <div
                class="bg-brand-50 rounded-lg border-b-4 border-brand-500 p-3 sm:p-5 shadow-sm flex flex-col items-center justify-center relative overflow-hidden">
                <div class="text-3xl sm:text-5xl font-black text-brand-600 mb-1 font-mono tracking-tighter relative"
                    x-text="seconds">00</div>
                <div class="text-[9px] sm:text-xs font-bold text-brand-500 uppercase tracking-widest relative">Detik
                </div>
            </div>

        </div>

        <div class="mb-10 relative z-10" x-show="isFinished" x-cloak x-transition.opacity.duration.1000ms>
            <div
                class="inline-flex items-center gap-3 bg-emerald-50 border-2 border-emerald-200 text-emerald-700 px-8 py-4 rounded-lg font-black text-lg shadow-lg shadow-emerald-100/50">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-md bg-emerald-700 text-white">
                    <i class="fas fa-door-open text-xl"></i>
                </div>
                <span>Gerbang Pengumuman Telah Dibuka!</span>
            </div>
        </div>

        <div class="relative z-10 max-w-md mx-auto">
            <a :href="isFinished ? '{{ route('pengumuman.index') }}' : '#'"
                class="w-full flex items-center justify-center gap-3 px-8 sm:px-10 py-4 sm:py-5 rounded-lg font-black text-base sm:text-lg transition-all duration-300 transform group"
                :class="isFinished
                            ? 'bg-slate-900 hover:bg-black text-white shadow-2xl shadow-slate-400 hover:-translate-y-1 hover:scale-105 active:scale-95 cursor-pointer'
                            : 'bg-slate-100 text-slate-400 border-2 border-slate-200 cursor-not-allowed'">

                <i class="fas transition-transform duration-300"
                    :class="isFinished ? 'fa-arrow-right group-hover:translate-x-1' : 'fa-lock'"></i>
                <span x-text="isFinished ? 'Cek Hasil Kelulusan Sekarang' : 'Menunggu Waktu Rilis...'"></span>
            </a>

            <p class="mt-6 text-xs sm:text-sm font-bold text-slate-400 flex justify-center items-center gap-2"
                x-show="!isFinished">
                <i class="fas fa-circle-notch fa-spin"></i> Menghitung mundur secara otomatis
            </p>
            <div class="mt-6 text-xs sm:text-sm font-bold text-slate-500 bg-slate-50 py-3 px-4 rounded-md border border-slate-200 shadow-inner flex justify-center items-center gap-2"
                x-show="isFinished" x-cloak>
                <i class="fas fa-id-card text-brand-400 text-lg"></i>
                <span>Siapkan <strong class="text-slate-800">NISN</strong> dan <strong class="text-slate-800">Tanggal
                        Lahir</strong> Anda.</span>
            </div>
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