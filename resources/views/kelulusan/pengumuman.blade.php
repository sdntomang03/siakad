<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Portal Pengumuman Kelulusan - SDN Tomang 03 Pagi</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        :root {
            --color-primary: 79 70 229;
            --color-success: 16 185 129;
            --color-fail: 225 29 72;
            --color-amber: 245 158 11;
        }

        /* Latar Belakang Dasar */
        body {
            background-color: #f8fafc;
            background-image:
                radial-gradient(at 0% 0%, rgba(var(--color-primary), 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(56, 189, 248, 0.08) 0px, transparent 50%);
            background-attachment: fixed;
        }

        /* Kaca Premium Form Depan */
        .premium-glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 20px 40px -15px rgba(var(--color-primary), 0.15);
        }

        /* Animasi Kilaun Permukaan */
        .shine-surface {
            position: relative;
            overflow: hidden;
        }

        .shine-surface::after {
            content: '';
            position: absolute;
            top: 0;
            left: -150%;
            width: 80%;
            height: 100%;
            background: linear-gradient(to right, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.4) 50%, rgba(255, 255, 255, 0) 100%);
            transform: skewX(-25deg);
            animation: shineAction 4s infinite;
        }

        @keyframes shineAction {
            0% {
                left: -150%;
            }

            30% {
                left: 150%;
            }

            100% {
                left: 150%;
            }
        }

        /* Animasi Goyang (TIDAK LULUS & ERROR) */
        .shake-gentle {
            animation: shakeGentle 0.5s cubic-bezier(.36, .07, .19, .97) both;
        }

        @keyframes shakeGentle {

            10%,
            90% {
                transform: translate3d(-1px, 0, 0);
            }

            20%,
            80% {
                transform: translate3d(2px, 0, 0);
            }

            30%,
            50%,
            70% {
                transform: translate3d(-4px, 0, 0);
            }

            40%,
            60% {
                transform: translate3d(4px, 0, 0);
            }
        }

        /* Animasi Mengambang */
        .float-gentle {
            animation: floatAnim 3s ease-in-out infinite;
        }

        @keyframes floatAnim {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        /* Animasi Radar / Pemindaian (Proses Loading) */
        .radar-scan {
            position: absolute;
            inset: 0;
            background: conic-gradient(from 0deg, transparent 70%, rgba(var(--color-primary), 0.2) 100%);
            border-radius: 50%;
            animation: spin 1.5s linear infinite;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }

        /* Sembunyikan Scrollbar namun tetap bisa scroll */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="min-h-[100dvh] flex flex-col font-sans selection:bg-indigo-100 overflow-x-hidden relative"
    x-data="kelulusanChecker()">

    <main class="flex-grow flex flex-col items-center justify-center p-4 sm:p-6 transition-all duration-700"
        :class="showModal ? 'blur-md scale-95 opacity-50' : 'blur-0 scale-100 opacity-100'">

        <div class="w-full max-w-md relative z-10 flex flex-col items-center justify-center">

            <div class="text-center mb-8 w-full flex flex-col items-center">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white shadow-xl mb-5 border-4 border-indigo-50 shine-surface">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z">
                        </path>
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">SDN Tomang 03 Pagi</h1>
                <p class="text-indigo-600 mt-2 font-bold uppercase tracking-widest text-xs">Portal Kelulusan Digital</p>
            </div>

            <div class="premium-glass rounded-[2rem] w-full p-8 sm:p-10 border border-white">
                <p class="text-slate-500 text-sm text-center mb-8 leading-relaxed">
                    Masukkan kredensial siswa dengan teliti untuk mengakses surat keputusan resmi kelulusan.
                </p>

                <form @submit.prevent="cekData" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-6 0a2 2 0 002 2h2a2 2 0 002-2m-6 0V5a2 2 0 012-2h2a2 2 0 012 2v1">
                                </path>
                            </svg>
                            NISN Siswa (10 Digit)
                        </label>
                        <input type="number" x-model="formData.nisn" required placeholder="0123456789"
                            inputmode="numeric"
                            class="w-full px-5 py-4 rounded-xl bg-white border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all font-mono text-lg text-slate-900 placeholder-slate-300 shadow-inner">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            Tanggal Lahir Siswa
                        </label>
                        <input type="date" x-model="formData.tanggal_lahir" required
                            class="w-full px-5 py-4 rounded-xl bg-white border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all text-lg text-slate-800 shadow-inner">
                    </div>

                    <button type="submit"
                        class="w-full mt-2 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 text-white font-extrabold py-4 px-6 rounded-2xl shadow-lg transition-all duration-300 transform active:scale-95 shine-surface">
                        Cek Hasil Kelulusan
                    </button>
                </form>
            </div>

            <div class="mt-8 text-center text-xs text-slate-400 font-medium tracking-wide">
                &copy; 2026 SDN Tomang 03 Pagi.<br>Terhubung dengan Database SIAKAD.
            </div>
        </div>
    </main>

    <div class="fixed inset-0 z-50 overflow-y-auto no-scrollbar" x-show="showModal" x-cloak>

        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300" x-show="showModal"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        <div class="flex min-h-full items-center justify-center p-4 py-8 sm:p-6">

            <div class="w-full max-w-lg relative transition-all transform duration-300" x-show="showModal"
                x-transition:enter="ease-out duration-500" x-transition:enter-start="opacity-0 scale-90 translate-y-8"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-300"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-90 translate-y-8">

                <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100"
                    :class="modalState === 'error' || studentData.keterangan === 'TIDAK LULUS' ? 'shake-gentle' : ''">

                    <div class="h-3 w-full shine-surface transition-colors duration-500" :class="{
                            'bg-slate-300': modalState === 'loading',
                            'bg-rose-500': modalState === 'error' || studentData.keterangan === 'TIDAK LULUS',
                            'bg-emerald-500': modalState === 'result' && studentData.keterangan === 'LULUS',
                            'bg-amber-500': modalState === 'result' && studentData.keterangan === 'DITUNDA'
                         }">
                    </div>

                    <div class="p-8 sm:p-10 flex flex-col items-center text-center">

                        <div x-show="modalState === 'loading'" class="w-full flex flex-col items-center py-6">
                            <div class="relative w-24 h-24 sm:w-32 sm:h-32 mb-8 flex items-center justify-center">
                                <div class="absolute inset-0 rounded-full border-4 border-indigo-50"></div>
                                <div class="radar-scan"></div>
                                <div
                                    class="absolute w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-md z-10">
                                    <svg class="w-6 h-6 text-indigo-600 animate-pulse" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <h2 class="text-2xl font-black text-slate-800 mb-2">Memverifikasi Data</h2>
                            <p class="text-slate-500 text-sm font-medium animate-pulse">Menghubungkan ke Server
                                SIAKAD...</p>
                        </div>

                        <div x-show="modalState === 'result'" style="display: none;"
                            class="w-full flex flex-col items-center">
                            <p class="text-[10px] sm:text-xs font-bold text-slate-400 tracking-widest uppercase mb-8">
                                Surat Keputusan Digital</p>

                            <div class="mb-8 text-center float-gentle">
                                <template x-if="studentData.keterangan === 'LULUS'">
                                    <svg class="h-28 w-28 sm:h-32 sm:w-32 text-emerald-500 drop-shadow-xl"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                        <path
                                            d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z">
                                        </path>
                                        <path d="M12 14v6.5" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round"></path>
                                    </svg>
                                </template>
                                <template x-if="studentData.keterangan === 'TIDAK LULUS'">
                                    <svg class="h-28 w-28 sm:h-32 sm:w-32 text-rose-500 drop-shadow-xl" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                </template>
                                <template x-if="studentData.keterangan === 'DITUNDA'">
                                    <svg class="h-28 w-28 sm:h-32 sm:w-32 text-amber-500 drop-shadow-xl" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </template>
                            </div>

                            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tighter leading-tight"
                                x-text="studentData.nama"></h2>
                            <div
                                class="inline-flex items-center gap-2 bg-slate-100 px-4 py-1.5 rounded-full text-slate-600 font-mono text-xs sm:text-sm font-bold mt-3 mb-8 border border-slate-200 shadow-inner">
                                NISN: <span x-text="studentData.nisn"></span>
                            </div>

                            <p
                                class="text-slate-700 mb-8 font-medium text-sm sm:text-base leading-relaxed border-l-4 border-indigo-200 pl-5 text-left bg-indigo-50/40 py-4 rounded-r-xl w-full">
                                Berdasarkan kriteria kelulusan dan hasil Keputusan Rapat Pleno Dewan Guru <b>SDN Tomang
                                    03 Pagi</b> Tahun Ajaran 2025/2026, siswa/i tersebut ditetapkan:
                            </p>

                            <div class="w-full rounded-2xl p-4 sm:p-5 mb-8 border-2 flex flex-col sm:flex-row items-center sm:justify-start gap-4 text-center sm:text-left shadow-lg"
                                :class="studentData.keterangan === 'LULUS' ? 'bg-emerald-50 border-emerald-300 text-emerald-700' : (studentData.keterangan === 'TIDAK LULUS' ? 'bg-rose-50 border-rose-300 text-rose-700' : 'bg-amber-50 border-amber-300 text-amber-700')">
                                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight uppercase w-full text-center"
                                    x-text="studentData.keterangan"></h1>
                            </div>

                            <button @click="resetModal"
                                class="w-full py-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 hover:text-indigo-600 font-bold text-sm sm:text-base flex items-center justify-center gap-2 transition-all shadow-sm active:scale-95 group">
                                <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali & Cek Lainnya
                            </button>
                        </div>

                        <div x-show="modalState === 'error'" style="display: none;"
                            class="w-full flex flex-col items-center py-4">
                            <div
                                class="w-24 h-24 mb-6 rounded-full bg-rose-50 flex items-center justify-center border-4 border-rose-100">
                                <svg class="w-12 h-12 text-rose-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 9l-6 6m0-6l6 6"></path>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-black text-slate-800 mb-4">Data Tidak Ditemukan</h2>
                            <p class="text-slate-600 text-sm sm:text-base mb-10 leading-relaxed px-4"
                                x-text="errorMessage"></p>

                            <button @click="resetModal"
                                class="w-full py-4 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-lg transition-all active:scale-95">
                                Perbaiki Input Data
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function kelulusanChecker() {
            return {
                showModal: false,
                modalState: 'hidden', // 'loading', 'result', 'error'
                errorMessage: '',
                formData: { nisn: '', tanggal_lahir: '' },
                studentData: { nama: '', nisn: '', keterangan: '' },

                async cekData() {
                    // 1. Munculkan Modal & Set Status ke Loading
                    this.showModal = true;
                    this.modalState = 'loading';

                    try {
                        // 2. Tembak API
                        const response = await fetch('/api/kelulusan/cek', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.formData)
                        });

                        const result = await response.json();

                        // 3. Delay 2 detik agar animasi Verifikasi Data terlihat profesional
                        setTimeout(() => {
                            if (response.ok && result.status === 'success') {
                                this.studentData = result.data;
                                this.modalState = 'result'; // Ganti fase jadi hasil

                                if (this.studentData.keterangan === 'LULUS') {
                                    this.tembakPetasanMeriah();
                                }
                            } else {
                                this.errorMessage = result.message || 'Pastikan penulisan NISN dan Tanggal Lahir sesuai dengan ijazah / akta kelahiran.';
                                this.modalState = 'error'; // Ganti fase jadi pesan gagal
                            }
                        }, 2000);

                    } catch (error) {
                        setTimeout(() => {
                            this.errorMessage = 'Terjadi kesalahan koneksi server. Pastikan internet Anda stabil.';
                            this.modalState = 'error';
                        }, 1000);
                    }
                },

                tembakPetasanMeriah() {
                    var duration = 4 * 1000;
                    var animationEnd = Date.now() + duration;
                    var defaults = { startVelocity: 35, spread: 360, ticks: 80, zIndex: 1000, scalar: window.innerWidth < 640 ? 0.9 : 1.3 };
                    function randomInRange(min, max) { return Math.random() * (max - min) + min; }

                    var interval = setInterval(function() {
                        var timeLeft = animationEnd - Date.now();
                        if (timeLeft <= 0) return clearInterval(interval);
                        var particleCount = 60 * (timeLeft / duration);
                        confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.05, 0.25), y: Math.random() - 0.2 } }));
                        confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.75, 0.95), y: Math.random() - 0.2 } }));
                    }, 250);
                },

                resetModal() {
                    // Tutup modal
                    this.showModal = false;

                    // Bersihkan form setelah animasi modal selesai menutup (0.5 detik)
                    setTimeout(() => {
                        this.modalState = 'hidden';
                        if (this.studentData.keterangan !== 'TIDAK LULUS' && !this.errorMessage) {
                             this.formData.nisn = '';
                             this.formData.tanggal_lahir = '';
                        }
                    }, 500);
                }
            }
        }
    </script>
</body>

</html>