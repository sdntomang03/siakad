<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
            AI Modul <span class="text-indigo-600">Generator</span>
        </h2>
    </x-slot>

    <!-- CSS Khusus Print & Scrollbar -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #previewArea,
            #previewArea * {
                visibility: visible;
            }

            #previewArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th,
            td {
                border: 1px solid black;
                padding: 8px;
            }

            .bg-amber-400 {
                background-color: #fbbf24 !important;
                -webkit-print-color-adjust: exact;
            }

            .bg-gray-100 {
                background-color: #f3f4f6 !important;
                -webkit-print-color-adjust: exact;
            }

            .bg-slate-100 {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
            }

            .bg-slate-200 {
                background-color: #e2e8f0 !important;
                -webkit-print-color-adjust: exact;
            }

            .bg-blue-50 {
                background-color: #eff6ff !important;
                -webkit-print-color-adjust: exact;
            }
        }

        /* ============================================================
           RAPIKAN BULLET & PENOMORAN
           Sengaja di-scope ke #output pakai selector ID supaya
           spesifisitasnya SELALU menang dibanding utility class Tailwind
           (list-disc/list-decimal/list-inside) ataupun preflight reset
           (list-style:none). Jadi walau hasil dari AI kadang lupa
           menambahkan class yang benar, bullet & nomor tetap rapi baik
           di preview maupun saat dicetak ke PDF.
           ============================================================ */
        #output ul,
        #output ol {
            list-style-position: outside !important;
            padding-left: 1.5em !important;
            margin: 0.35em 0 !important;
        }

        #output ul {
            list-style-type: disc !important;
        }

        #output ol {
            list-style-type: decimal !important;
        }

        #output ul ul,
        #output ol ul {
            list-style-type: circle !important;
        }

        #output ol ol,
        #output ul ol {
            list-style-type: lower-alpha !important;
        }

        #output li {
            display: list-item !important;
            margin-bottom: 0.3em;
            padding-left: 0.15em;
        }

        #output li::marker {
            font-weight: 700;
        }

        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>

    <div class="py-8">
        <div class="max-w-[98%] mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Kolom API Key (AMAN UNTUK GITHUB) -->
            <div
                class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15.5 7.5 2.3 2.3a1 1 0 0 0 1.4 0l2.1-2.1a1 1 0 0 0 0-1.4L19 4l-4 4Z" />
                    <path d="m21 2-9.6 9.6" />
                    <path d="m5.4 16.6-2.1 2.1a1 1 0 0 0 0 1.4l2.2 2.2a1 1 0 0 0 1.4 0l2.1-2.1" />
                    <path d="M12 14v2" />
                    <path d="M14 12h2" />
                </svg>
                <!-- API Key diambil secara dinamis dari file .env -->
                <input type="password" id="apiKey" value="{{ env('GEMINI_API_KEY', '') }}"
                    placeholder="Masukkan Gemini API Key..."
                    class="outline-none bg-transparent text-sm w-full focus:ring-0 dark:text-white border-none p-0">
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- PANEL KIRI: Form Input -->
                <aside class="lg:col-span-4 space-y-6">
                    <div
                        class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                        <h2
                            class="text-lg font-bold mb-4 flex items-center gap-2 border-b dark:border-slate-700 pb-2 dark:text-white">
                            Pengaturan Modul
                        </h2>

                        <!-- Hidden Inputs untuk Database -->
                        <input type="hidden" id="academicYearId" value="{{ $activeYear->id ?? '' }}">

                        <div class="space-y-4">
                            <!-- Identitas (Otomatis dari Database) -->
                            <div
                                class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700 space-y-3">
                                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Identitas Sekolah
                                    & Guru</h3>
                                <div>
                                    <label
                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nama
                                        Sekolah</label>
                                    <input type="text" id="namaSekolah" value="{{ $sekolah->nama_sekolah ?? '' }}"
                                        readonly
                                        class="w-full p-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-500 cursor-not-allowed">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nama
                                            KS</label>
                                        <input type="text" id="namaKS" value="{{ $sekolah->kepala_sekolah ?? '' }}"
                                            readonly
                                            class="w-full p-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-500 cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">NIP
                                            KS</label>
                                        <input type="text" id="nipKS" value="{{ $sekolah->nip ?? '' }}" readonly
                                            class="w-full p-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-500 cursor-not-allowed">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nama
                                            Guru</label>
                                        <input type="text" id="namaGuru" value="{{ $namaGuru }}" readonly
                                            class="w-full p-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-500 cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">NIP
                                            Guru</label>
                                        <input type="text" id="nipGuru" value="{{ $nipGuru }}" readonly
                                            class="w-full p-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-500 cursor-not-allowed">
                                    </div>
                                </div>
                            </div>

                            <!-- Dropdown Dinamis -->
                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Jenjang
                                    Pendidikan</label>
                                <select id="jenjang"
                                    class="w-full p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                                    <option value="PAUD">PAUD</option>
                                    <option value="SD" selected>SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA">SMA</option>
                                    <option value="SMK">SMK</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Fase</label>
                                    <select id="fase"
                                        class="w-full p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none"></select>
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Kelas</label>
                                    <select id="kelas"
                                        class="w-full p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none"></select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Jml
                                        Pertemuan</label>
                                    <input type="number" id="jumlahPertemuan" min="1" max="10" value="1"
                                        class="w-full p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Alokasi
                                        Waktu</label>
                                    <input type="text" id="alokasiWaktu" value="2 JP x 35 Menit"
                                        class="w-full p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Mata
                                    Pelajaran</label>
                                <input type="text" id="mapel" placeholder="Contoh: Matematika"
                                    class="w-full p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Topik
                                    Utama</label>
                                <input type="text" id="topik" placeholder="Contoh: Bilangan Cacah"
                                    class="w-full p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Cakupan
                                    / Penjabaran Materi</label>
                                <textarea id="cakupan" rows="3"
                                    placeholder="Contoh: Pertemuan 1 mengidentifikasi organ..."
                                    class="w-full p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                            </div>

                            <!-- 8 Dimensi -->
                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Dimensi
                                    Profil Lulusan</label>
                                <div
                                    class="space-y-2 max-h-48 overflow-y-auto p-2 border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-sm custom-scroll">
                                    @php
                                    $profils = ['Keimanan dan Ketakwaan', 'Kewargaan', 'Penalaran Kritis',
                                    'Kreativitas', 'Kolaborasi', 'Kemandirian', 'Kesehatan (Fisik & Mental)',
                                    'Komunikasi'];
                                    @endphp
                                    @foreach($profils as $profil)
                                    <label class="flex items-start gap-2 cursor-pointer">
                                        <input type="checkbox" name="profilLulusan" value="{{ $profil }}"
                                            class="mt-1 w-4 h-4 text-indigo-600 rounded">
                                        <span class="text-slate-600 dark:text-slate-300 text-xs"><b>{{ $profil
                                                }}</b></span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Tanggal
                                    Dokumen</label>
                                <input type="date" id="tanggal"
                                    class="w-full p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                            </div>

                            <!-- Tombol Generate -->
                            <button onclick="generateModul()" id="btnGenerate"
                                class="w-full mt-6 py-3 px-6 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-200 dark:shadow-none transition-all flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z" />
                                    <path d="M20 2v4" />
                                    <path d="M22 4h-4" />
                                    <circle cx="4" cy="20" r="2" />
                                </svg>
                                <span id="btnText">Generate Modul Sekarang</span>
                            </button>
                        </div>
                    </div>
                </aside>

                <!-- PANEL KANAN: Hasil Preview -->
                <main class="lg:col-span-8">
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 h-full min-h-[600px] flex flex-col">

                        <div
                            class="p-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 rounded-t-2xl sticky top-0 z-10">
                            <h3 class="font-bold text-slate-700 dark:text-slate-300">Pratinjau Hasil</h3>
                            <div class="flex gap-2">
                                <button onclick="saveToDatabase()" id="btnSaveDb"
                                    class="hidden px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-bold flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                    </svg>
                                    Simpan ke Database
                                </button>
                                <button onclick="window.print()" id="btnPrint"
                                    class="hidden px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-sm font-bold flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    Cetak PDF
                                </button>
                            </div>
                        </div>

                        <!-- Area Hasil -->
                        <div id="previewArea" class="p-8 flex-1 overflow-auto bg-slate-50/20 dark:bg-slate-900/20">
                            <div id="loading"
                                class="hidden h-full flex flex-col items-center justify-center text-indigo-600">
                                <svg class="w-10 h-10 animate-spin mb-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <p class="animate-pulse font-medium">Menghubungi AI... menyusun modul...</p>
                            </div>

                            <div id="empty" class="h-full flex flex-col items-center justify-center text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mb-4 opacity-30"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z" />
                                    <path d="M20 2v4" />
                                    <path d="M22 4h-4" />
                                    <circle cx="4" cy="20" r="2" />
                                </svg>
                                <p class="text-center italic opacity-60">Isi data di samping, lalu klik
                                    generate<br>untuk menyusun modul ajar.</p>
                            </div>

                            <!-- Output HTML -->
                            <div id="output"
                                class="hidden max-w-[210mm] mx-auto bg-white text-black text-sm p-4 shadow"></div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Script Generasi Modul -->
    <script>
        // Logika Dropdown Dinamis
        const jenjangSelect = document.getElementById('jenjang');
        const faseSelect = document.getElementById('fase');
        const kelasSelect = document.getElementById('kelas');

        const optionsMap = {
            'PAUD': { fase: [{ value: 'Pondasi', text: 'Fase Pondasi' }], kelas: ['PAUD', 'TK A', 'TK B'] },
            'SD': { fase: [{ value: 'A', text: 'Fase A' }, { value: 'B', text: 'Fase B' }, { value: 'C', text: 'Fase C' }], kelas: ['1', '2', '3', '4', '5', '6'] },
            'SMP': { fase: [{ value: 'D', text: 'Fase D' }], kelas: ['7', '8', '9'] },
            'SMA': { fase: [{ value: 'E', text: 'Fase E' }, { value: 'F', text: 'Fase F' }], kelas: ['10', '11', '12'] },
            'SMK': { fase: [{ value: 'E', text: 'Fase E' }, { value: 'F', text: 'Fase F' }], kelas: ['10', '11', '12'] }
        };

        function updateFormOptions() {
            const selectedJenjang = jenjangSelect.value;
            const config = optionsMap[selectedJenjang];
            faseSelect.innerHTML = config.fase.map(f => `<option value="${f.value}">${f.text}</option>`).join('');
            kelasSelect.innerHTML = config.kelas.map(k => `<option value="${k}">${k}</option>`).join('');
        }

        jenjangSelect.addEventListener('change', updateFormOptions);
        updateFormOptions();

        async function generateModul() {
            const apiKey = document.getElementById('apiKey').value.trim();
            const namaSekolah = document.getElementById('namaSekolah').value;
            const namaKS = document.getElementById('namaKS').value;
            const nipKS = document.getElementById('nipKS').value || "-";
            const namaGuru = document.getElementById('namaGuru').value;
            const nipGuru = document.getElementById('nipGuru').value || "-";

            const jenjang = document.getElementById('jenjang').value;
            const fase = document.getElementById('fase').value;
            const kelas = document.getElementById('kelas').value;
            const tanggal = document.getElementById('tanggal') ? document.getElementById('tanggal').value : '-';
            const alokasi = document.getElementById('alokasiWaktu').value || '-';
            const mapel = document.getElementById('mapel').value;
            const topik = document.getElementById('topik').value;
            const cakupan = document.getElementById('cakupan').value;
            const jumlahPertemuan = parseInt(document.getElementById('jumlahPertemuan').value) || 1;

            const selectedProfiles = Array.from(document.querySelectorAll('input[name="profilLulusan"]:checked')).map(cb => cb.value).join(', ');

            if (!apiKey) return alert("Silakan masukkan Gemini API Key Anda!");
            if (!mapel || !topik) return alert("Mata Pelajaran dan Topik Materi harus diisi!");
            if (!selectedProfiles) return alert("Pilih setidaknya satu Dimensi Profil Lulusan!");

            toggleLoading(true);

            let strukturPertemuan = '';
            let strukturLampiran = '';

            for (let i = 1; i <= jumlahPertemuan; i++) {
                strukturPertemuan += `
                        <tr class="border border-black bg-emerald-100">
                            <td colspan="6" class="p-2 font-bold text-center uppercase text-emerald-800">PERTEMUAN ${i} DARI ${jumlahPertemuan}</td>
                        </tr>
                        <tr class="border border-black">
                            <td colspan="2" class="p-2 font-bold align-top bg-emerald-50">Kegiatan Awal</td>
                            <td colspan="4" class="p-2 bg-white">
                                <div class="mb-2">(AI: Tuliskan 3-4 poin kegiatan pendahuluan pertemuan ${i} (mis. salam & doa, presensi, apersepsi, motivasi/tujuan). WAJIB pakai <ol class="list-decimal list-inside space-y-1"> dengan satu <li> per poin, JANGAN teks biasa.)</div>
                            </td>
                        </tr>
                        <tr class="border border-black">
                            <td colspan="2" class="p-2 font-bold align-top bg-emerald-50">Kegiatan Inti</td>
                            <td colspan="4" class="p-2 space-y-4 bg-white">
                                <div>
                                    <strong class="text-blue-700 block">Langkah Pembelajaran:</strong>
                                    <div class="mt-1">(AI: Tuliskan langkah-langkah aktivitas inti pertemuan ${i} secara mendetail dan berurutan. WAJIB pakai <ol class="list-decimal list-inside space-y-1"> dengan satu <li> per langkah agar nomornya muncul otomatis.)</div>
                                </div>
                            </td>
                        </tr>
                        <tr class="border border-black">
                            <td colspan="2" class="p-2 font-bold align-top bg-emerald-50">Kegiatan Penutup</td>
                            <td colspan="4" class="p-2 bg-white">(AI: Tuliskan 2-3 poin refleksi, kesimpulan, dan doa penutup pertemuan ${i}. WAJIB pakai <ol class="list-decimal list-inside space-y-1"> dengan satu <li> per poin.)</td>
                        </tr>
                `;

                strukturLampiran += `
                        <tr class="border border-black bg-amber-200">
                            <td colspan="6" class="p-2 font-bold text-center uppercase text-amber-900">LAMPIRAN PERTEMUAN ${i}</td>
                        </tr>
                        <tr class="border border-black">
                            <td colspan="2" class="p-2 font-bold align-top bg-amber-50">LKPD Pertemuan ${i}</td>
                            <td colspan="4" class="p-2 bg-white">(AI: Buatkan instruksi LKPD spesifik Pertemuan ${i})</td>
                        </tr>
                        <tr class="border border-black">
                            <td colspan="2" class="p-2 font-bold align-top bg-amber-50">Soal Evaluasi Pertemuan ${i}</td>
                            <td colspan="4" class="p-2 bg-white">
                                (AI: Buatkan TEPAT 5 soal latihan/evaluasi Pertemuan ${i}, nomor 1 sampai 5. <strong>WAJIB</strong> gunakan <code>&lt;ol class="list-decimal list-inside space-y-2"&gt;</code> dengan satu <code>&lt;li&gt;</code> per soal, JANGAN gabung ke satu paragraf, agar nomornya muncul otomatis.)
                            </td>
                        </tr>
                        <tr class="border border-black">
                            <td colspan="2" class="p-2 font-bold align-top bg-amber-50">Kunci Jawaban Pertemuan ${i}</td>
                            <td colspan="4" class="p-2 bg-white">
                                (AI: Tuliskan kunci jawaban dari ke-5 soal Pertemuan ${i}, urut nomor 1 sampai 5 sesuai nomor soalnya. <strong>WAJIB</strong> gunakan <code>&lt;ol class="list-decimal list-inside space-y-1"&gt;</code> dengan satu <code>&lt;li&gt;</code> per jawaban agar nomornya muncul otomatis.)
                            </td>
                        </tr>
                `;
            }

            const prompt = `
                Bertindaklah sebagai Konsultan Kurikulum Merdeka Kemendikbud.
                Susunlah Modul Ajar dalam format HTML (hanya <div> dan <table>). JANGAN gunakan raw Markdown (seperti * atau -).

                DATA INPUT:
                - Mapel: ${mapel}, Topik: ${topik}
                - Cakupan Materi: ${cakupan} (Bagi materi ini ke dalam ${jumlahPertemuan} pertemuan).
                - Identitas: Kelas ${kelas} ${jenjang}, Sekolah: ${namaSekolah}.
                - Tanggal Dokumen: ${tanggal}, Alokasi Waktu: ${alokasi}
                - Dimensi Profil Lulusan: ${selectedProfiles}
                - Kepala Sekolah: ${namaKS} (NIP: ${nipKS})
                - Guru: ${namaGuru} (NIP: ${nipGuru})

                Instruksi Tambahan (SANGAT PENTING):
                1. SEMUA daftar/poin (Tujuan Pembelajaran, Langkah-langkah, Kegiatan Awal/Inti/Penutup, dll) WAJIB dibungkus tag <ul> untuk poin biasa atau <ol> untuk urutan bernomor. Selalu tambahkan class Tailwind yang sesuai (list-disc list-inside untuk <ul>, list-decimal list-inside untuk <ol>) sebagai praktik terbaik, TAPI JANGAN pernah menuliskan poin sebagai teks biasa dengan tanda "-" atau "*" manual — wajib pakai tag <li> agar rapi dan bernomor otomatis.
                2. Setiap <ol>/<ul> WAJIB berisi elemen <li> terpisah untuk tiap poin (bukan satu <li> panjang berisi banyak kalimat). Jangan gabungkan beberapa poin dalam satu baris.
                3. Untuk Soal Evaluasi dan Kunci Jawaban di bagian lampiran SETIAP pertemuan, WAJIB buat TEPAT 5 butir menggunakan <ol class="list-decimal list-inside space-y-2">, satu <li> per nomor soal, sehingga nomor 1-5 tampil otomatis. Nomor pada Kunci Jawaban HARUS sesuai urutan nomor soalnya (Jawaban 1 untuk Soal 1, dst).
                4. Untuk setiap PERTEMUAN, isi Kegiatan Awal, Kegiatan Inti, dan Kegiatan Penutup secara LENGKAP dan TERPISAH sesuai jumlah pertemuan yang diminta (${jumlahPertemuan} pertemuan) — jangan digabung menjadi satu pertemuan saja, dan jangan ada pertemuan yang kosong.
                5. PASTIKAN NIP Kepala Sekolah dan NIP Guru ditulis PERSIS seperti Data Input di atas pada bagian TANDA TANGAN.

                HTML STRUKTUR:
                <div class="max-w-[210mm] mx-auto p-4 bg-white text-black font-serif">
                    <h1 class="text-center text-2xl font-bold mb-4 uppercase text-blue-800">MODUL AJAR ${mapel}</h1>

                    <!-- BAGIAN A -->
                    <table class="w-full border border-black border-collapse mb-6 text-sm">
                        <tr class="bg-blue-600 text-white border border-black">
                            <td colspan="6" class="p-2 font-bold uppercase text-center">A. INFORMASI UMUM</td>
                        </tr>
                        <tr class="border border-black">
                            <td colspan="2" class="p-2 font-bold bg-blue-50">Nama Penyusun</td>
                            <td colspan="4" class="p-2 bg-white">${namaGuru}</td>
                        </tr>
                        <tr class="border border-black">
                            <td colspan="2" class="p-2 font-bold bg-blue-50">Satuan Pendidikan</td>
                            <td colspan="4" class="p-2 bg-white">${namaSekolah}</td>
                        </tr>
                        <tr class="border border-black">
                            <td colspan="2" class="p-2 font-bold bg-blue-50">Fase / Kelas</td>
                            <td colspan="4" class="p-2 bg-white">${fase} / ${kelas}</td>
                        </tr>
                        <tr class="border border-black">
                            <td colspan="2" class="p-2 font-bold bg-blue-50">Mata Pelajaran</td>
                            <td colspan="4" class="p-2 bg-white">${mapel}</td>
                        </tr>
                        <tr class="border border-black">
                            <td colspan="2" class="p-2 font-bold bg-blue-50">Topik Utama</td>
                            <td colspan="4" class="p-2 bg-white">${topik}</td>
                        </tr>
                        <tr class="border border-black">
                            <td colspan="2" class="p-2 font-bold bg-blue-50">Alokasi Waktu</td>
                            <td colspan="4" class="p-2 bg-white">${alokasi} (${jumlahPertemuan} Pertemuan)</td>
                        </tr>
                        <tr class="border border-black">
                            <td colspan="2" class="p-2 font-bold bg-blue-50">Dimensi Profil Lulusan</td>
                            <td colspan="4" class="p-2 bg-white">
                                <ul class="list-disc list-inside">
                                    ${selectedProfiles.split(', ').map(p => `<li>${p}</li>`).join('')}
                                </ul>
                            </td>
                        </tr>
                        <tr class="border border-black">
                            <td colspan="2" class="p-2 font-bold bg-blue-50">Pendekatan/Metode</td>
                            <td colspan="4" class="p-2 bg-white">(AI: Isi dengan tag <ul class="list-disc list-inside">)</td>
                        </tr>
                    </table>

                    <!-- BAGIAN B -->
                    <table class="w-full border border-black border-collapse mb-6 text-sm">
                        <tr class="bg-emerald-600 text-white border border-black">
                            <td colspan="6" class="p-2 font-bold uppercase text-center">B. KOMPONEN INTI</td>
                        </tr>
                        <tr class="border border-black">
                            <td colspan="6" class="p-2 bg-emerald-100 font-bold text-emerald-900">1. Tujuan Pembelajaran</td>
                        </tr>
                        <tr class="border border-black">
                            <td colspan="6" class="p-2 bg-white">
                                (AI: Isi Tujuan Pembelajaran sesuai Topik. <strong>WAJIB</strong> gunakan tag <code>&lt;ol class="list-decimal list-inside space-y-1"&gt;</code>)
                            </td>
                        </tr>

                        <!-- Langkah Pembelajaran -->
                        <tr class="border border-black">
                            <td colspan="6" class="p-2 bg-emerald-100 font-bold text-emerald-900">2. Langkah Pembelajaran</td>
                        </tr>
                        ${strukturPertemuan}
                    </table>

                    <!-- BAGIAN C -->
                    <table class="w-full border border-black border-collapse mb-10 text-sm">
                        <tr class="bg-amber-500 text-white border border-black">
                            <td colspan="6" class="p-2 font-bold uppercase text-center">C. LAMPIRAN</td>
                        </tr>
                        ${strukturLampiran}
                    </table>

                    <!-- TANDA TANGAN -->
                    <table class="w-full border border-black border-collapse text-sm mb-10 break-inside-avoid">
                        <tr>
                            <td colspan="3" class="p-4 text-center align-top border border-black w-1/2 bg-white">
                                <p class="mb-20">Mengetahui,<br>Kepala Sekolah</p>
                                <p class="font-bold underline">${namaKS}</p>
                                <p>NIP. ${nipKS}</p>
                            </td>
                            <td colspan="3" class="p-4 text-center align-top border border-black w-1/2 bg-white">
                                <p class="mb-20">Jakarta, ............................<br>Guru Kelas ${kelas}</p>
                                <p class="font-bold underline">${namaGuru}</p>
                                <p>NIP. ${nipGuru}</p>
                            </td>
                        </tr>
                    </table>
                </div>
            `;

            try {
                const response = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-goog-api-key': apiKey },
                    body: JSON.stringify({ contents: [{ parts: [{ text: prompt }] }], generationConfig: { temperature: 0.7 } })
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.error?.message || "Error API.");

                let htmlResult = data.candidates[0].content.parts[0].text;
                htmlResult = htmlResult.replace(/```html/g, '').replace(/```/g, '');

                document.getElementById('output').innerHTML = htmlResult;
                toggleLoading(false, true);
            } catch (error) {
                alert("Gagal membuat modul: " + error.message);
                toggleLoading(false, false);
            }
        }

        function toggleLoading(isLoading, hasData = false) {
            const btn = document.getElementById('btnGenerate');
            const txt = document.getElementById('btnText');
            const loading = document.getElementById('loading');
            const empty = document.getElementById('empty');
            const output = document.getElementById('output');
            const btnPrint = document.getElementById('btnPrint');
            const btnSaveDb = document.getElementById('btnSaveDb');

            if (isLoading) {
                btn.disabled = true;
                txt.innerText = "Menyusun Modul...";
                empty.classList.add('hidden'); output.classList.add('hidden');
                loading.classList.remove('hidden'); btnPrint.classList.add('hidden'); btnSaveDb.classList.add('hidden');
            } else {
                btn.disabled = false;
                txt.innerText = "Generate Modul Sekarang";
                loading.classList.add('hidden');
                if (hasData) {
                    output.classList.remove('hidden'); btnPrint.classList.remove('hidden'); btnSaveDb.classList.remove('hidden');
                } else { empty.classList.remove('hidden'); }
            }
        }

        function saveToDatabase() {
            const htmlContent = document.getElementById('output').innerHTML;
            const tingkat = document.getElementById('jenjang').value + " Kelas " + document.getElementById('kelas').value;
            const mapel = document.getElementById('mapel').value;
            const topik = document.getElementById('topik').value;
            const academicYearId = document.getElementById('academicYearId').value;
            const btnSaveDb = document.getElementById('btnSaveDb');

            if (!academicYearId) {
                alert("Tahun Pelajaran belum diatur di database!");
                return;
            }

            btnSaveDb.disabled = true;
            btnSaveDb.innerText = "Menyimpan...";

            fetch("{{ route('modul.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    tingkat: tingkat,
                    mata_pelajaran: mapel,
                    topik: topik,
                    html_content: htmlContent,
                    academic_year_id: academicYearId
                })
            })
            .then(async response => {
                if (!response.ok) {
                    const errData = await response.json().catch(() => ({}));
                    throw new Error(errData.message || "Terjadi kesalahan (Status " + response.status + ")");
                }
                return response.json();
            })
            .then(data => {
                if(data.status === 'success') {
                    alert(data.message);
                } else {
                    alert("Gagal: " + (data.message || "Kesalahan Server"));
                }
            })
            .catch(err => {
                console.error(err);
                alert("Gagal menyimpan: " + err.message);
            })
            .finally(() => {
                btnSaveDb.disabled = false;
                btnSaveDb.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg> Simpan ke Database`;
            });
        }
    </script>
</x-app-layout>