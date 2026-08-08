<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                Penyusunan Catatan Akhir: <span class="text-indigo-600 dark:text-indigo-400">{{ $student->nama_lengkap
                    ?? $student->nama }}</span>
            </h2>
            <a href="{{ route('catatan_akhir.index', ['classroom_id' => $classroom->id]) }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg font-bold text-xs text-slate-700 dark:text-slate-300 uppercase tracking-widest shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div
                class="p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 rounded-xl font-bold shadow-sm">
                {{ session('success') }}
            </div>
            @endif

            <!-- Kolom API Key AI -->
            <div
                class="bg-indigo-50 dark:bg-indigo-900/30 p-4 rounded-2xl shadow-sm border border-indigo-200 dark:border-indigo-800/50 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600 dark:text-indigo-400 shrink-0"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="m15.5 7.5 2.3 2.3a1 1 0 0 0 1.4 0l2.1-2.1a1 1 0 0 0 0-1.4L19 4l-4 4Z" />
                    <path d="m21 2-9.6 9.6" />
                    <path d="m5.4 16.6-2.1 2.1a1 1 0 0 0 0 1.4l2.2 2.2a1 1 0 0 0 1.4 0l2.1-2.1" />
                    <path d="M12 14v2" />
                    <path d="M14 12h2" />
                </svg>
                <div class="flex-1">
                    <input type="password" id="apiKey" value="{{ env('GEMINI_API_KEY', '') }}"
                        placeholder="Masukkan Gemini API Key untuk mengaktifkan AI Generator..."
                        class="w-full bg-transparent border-none text-sm text-indigo-900 dark:text-indigo-100 placeholder-indigo-400 dark:placeholder-indigo-600 focus:ring-0 p-0 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- KOLOM KIRI: REKAP DATA (Untuk Dibaca AI) --}}
                <div class="lg:col-span-1 space-y-6">

                    {{-- 1. Rekap Absen & Piket --}}
                    <div
                        class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                        <h3
                            class="font-black text-slate-800 dark:text-slate-200 mb-4 border-b border-slate-100 dark:border-slate-700 pb-2 uppercase tracking-wider text-sm">
                            Absensi & Piket Harian</h3>

                        <div class="space-y-5">
                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kehadiran
                                    Kelas</p>
                                <ul class="text-sm space-y-2">
                                    <li
                                        class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 px-3 py-2 rounded-lg border border-slate-100 dark:border-slate-700/50">
                                        <span class="text-slate-600 dark:text-slate-400 font-medium">Sakit</span>
                                        <span class="font-bold text-amber-500" id="valSakit">{{ $sakit }} Hari</span>
                                    </li>
                                    <li
                                        class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 px-3 py-2 rounded-lg border border-slate-100 dark:border-slate-700/50">
                                        <span class="text-slate-600 dark:text-slate-400 font-medium">Izin</span>
                                        <span class="font-bold text-blue-500" id="valIzin">{{ $izin }} Hari</span>
                                    </li>
                                    <li
                                        class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 px-3 py-2 rounded-lg border border-slate-100 dark:border-slate-700/50">
                                        <span class="text-slate-600 dark:text-slate-400 font-medium">Alpha</span>
                                        <span class="font-bold text-rose-500" id="valAlpha">{{ $alpha }} Hari</span>
                                    </li>
                                </ul>
                            </div>

                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tugas
                                    Kebersihan (Piket)</p>
                                <ul class="text-sm space-y-2 mb-2">
                                    <li
                                        class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 px-3 py-2 rounded-lg border border-slate-100 dark:border-slate-700/50">
                                        <span class="text-slate-600 dark:text-slate-400 font-medium">Terlaksana</span>
                                        <span class="font-bold text-emerald-500" id="valPiketBagus">{{ $piketTerlaksana
                                            }}x</span>
                                    </li>
                                    <li
                                        class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 px-3 py-2 rounded-lg border border-slate-100 dark:border-slate-700/50">
                                        <span class="text-slate-600 dark:text-slate-400 font-medium">Mangkir</span>
                                        <span class="font-bold text-rose-500" id="valPiketBuruk">{{ $piketTidak
                                            }}x</span>
                                    </li>
                                </ul>
                                @if($catatanPiket->count() > 0)
                                <div class="text-[11px] text-slate-500 bg-rose-50 dark:bg-rose-900/20 p-2 rounded border border-rose-100 dark:border-rose-800/50"
                                    id="listPiketDetail">
                                    <strong>Alasan Mangkir:</strong>
                                    <ul class="list-disc list-inside mt-1">
                                        @foreach($catatanPiket as $cp)
                                        <li class="item-piket truncate" title="{{ $cp->catatan }}">{{ $cp->catatan }}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 2. Rekap Nilai Ujian/Tes --}}
                    <div
                        class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                        <h3
                            class="font-black text-slate-800 dark:text-slate-200 mb-4 border-b border-slate-100 dark:border-slate-700 pb-2 uppercase tracking-wider text-sm">
                            Nilai Tes Akademik</h3>
                        @if($rekapNilai->isNotEmpty())
                        <ul class="text-sm space-y-2" id="listNilaiTes">
                            @foreach($rekapNilai as $nilai)
                            <li class="item-nilai flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 px-3 py-2 rounded-lg border border-slate-100 dark:border-slate-700/50"
                                data-text="{{ $nilai->nama_mapel }}: {{ $nilai->nilai_akhir }}">
                                <span class="text-slate-600 dark:text-slate-400 font-medium truncate pr-3">{{
                                    $nilai->nama_mapel }}</span>
                                <span class="font-black text-indigo-600 dark:text-indigo-400">{{ $nilai->nilai_akhir
                                    }}</span>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <div
                            class="text-center bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50">
                            <p class="text-sm text-slate-500 italic">Belum ada nilai ujian format tes.</p>
                        </div>
                        @endif
                    </div>

                    {{-- 3. Rekap Observasi Non-Tes --}}
                    <div
                        class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                        <h3
                            class="font-black text-slate-800 dark:text-slate-200 mb-4 border-b border-slate-100 dark:border-slate-700 pb-2 uppercase tracking-wider text-sm">
                            Observasi Praktik / Proyek</h3>
                        @if($rekapObservasi->isNotEmpty())
                        <ul class="text-sm space-y-3" id="listObservasi">
                            @foreach($rekapObservasi as $obs)
                            <li class="item-observasi bg-slate-50 dark:bg-slate-900/50 p-3 rounded-xl border border-slate-100 dark:border-slate-700/50"
                                data-text="Mapel {{ $obs->nama_mapel }} kegiatan {{ $obs->kegiatan }} mendapat predikat {{ $obs->predikat }} (Catatan: {{ $obs->catatan }})">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-bold text-slate-700 dark:text-slate-300 text-xs">{{
                                        $obs->nama_mapel }}</span>
                                    <span
                                        class="text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider
                                            {{ $obs->predikat == 'Sangat Baik' ? 'bg-emerald-100 text-emerald-700' : ($obs->predikat == 'Baik' ? 'bg-blue-100 text-blue-700' : ($obs->predikat == 'Cukup' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700')) }}">
                                        {{ $obs->predikat }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 italic mb-1">{{ $obs->kegiatan }}
                                </p>
                                <p class="text-xs text-slate-600 dark:text-slate-300">"{{ $obs->catatan }}"</p>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <div
                            class="text-center bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50">
                            <p class="text-sm text-slate-500 italic">Belum ada nilai observasi non-tes.</p>
                        </div>
                        @endif
                    </div>

                    {{-- 4. Rekap Catatan Guru --}}
                    <div
                        class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                        <h3
                            class="font-black text-slate-800 dark:text-slate-200 mb-4 border-b border-slate-100 dark:border-slate-700 pb-2 uppercase tracking-wider text-sm">
                            Jurnal Perilaku Guru</h3>
                        @if($teacherNotes->isEmpty())
                        <div class="text-center bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50"
                            id="listCatatan">
                            <p class="text-sm text-slate-500 italic">Tidak ada catatan perilaku/prestasi.</p>
                        </div>
                        @else
                        <ul class="text-sm space-y-3" id="listCatatan">
                            @foreach($teacherNotes as $note)
                            <li
                                class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50">
                                <div class="flex justify-between items-center mb-2">
                                    <span
                                        class="inline-block px-2 py-1 bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 text-[10px] font-bold uppercase rounded tracking-wider type-catatan">{{
                                        $note->jenis_catatan }}</span>
                                </div>
                                <p class="text-slate-600 dark:text-slate-300 leading-relaxed mt-1 isi-catatan">{{
                                    $note->catatan }}</p>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>

                {{-- KOLOM KANAN: FORM WALI KELAS --}}
                <div class="lg:col-span-2">
                    <div
                        class="bg-white dark:bg-slate-800 p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 sticky top-6">

                        <div
                            class="mb-8 border-b border-slate-100 dark:border-slate-700 pb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <h3
                                    class="text-xl font-black text-slate-800 dark:text-slate-200 uppercase tracking-tight">
                                    Verifikasi & Kesimpulan Akhir</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Isi manual atau gunakan AI
                                    untuk memproses ringkasan data di panel kiri.</p>
                            </div>

                            {{-- Tombol Generate AI --}}
                            <button type="button" onclick="generateCatatanAI()" id="btnGenerateAI"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white text-sm font-bold rounded-xl shadow-md transition-all transform hover:-translate-y-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span id="textGenerateAI">Generate Draft Pakai AI</span>
                            </button>
                        </div>

                        <form action="{{ route('catatan_akhir.update', [$student->id, $classroom->id]) }}" method="POST"
                            class="space-y-6">
                            @csrf
                            <input type="hidden" name="academic_year_id" value="{{ $active_academic_year_id }}">
                            <input type="hidden" name="piket_terlaksana" value="{{ $piketTerlaksana }}">
                            <input type="hidden" name="piket_tidak_terlaksana" value="{{ $piketTidak }}">

                            {{-- Penyesuaian Angka Kehadiran (Selalu tersinkronisasi otomatis dengan hasil Live
                            Database) --}}
                            <div
                                class="bg-slate-50 dark:bg-slate-900/30 p-5 rounded-xl border border-slate-100 dark:border-slate-700/50">
                                <h4
                                    class="font-bold text-slate-700 dark:text-slate-300 mb-4 text-sm uppercase tracking-wider">
                                    Validasi Data Kehadiran</h4>
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2">Sakit
                                            (Hari)</label>
                                        <input type="number" name="sakit" value="{{ old('sakit', $sakit) }}" min="0"
                                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2">Izin
                                            (Hari)</label>
                                        <input type="number" name="izin" value="{{ old('izin', $izin) }}" min="0"
                                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2">Alpha
                                            (Hari)</label>
                                        <input type="number" name="alpha" value="{{ old('alpha', $alpha) }}" min="0"
                                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm">
                                    </div>
                                </div>
                            </div>

                            {{-- Catatan Final Wali Kelas --}}
                            <div class="relative">
                                <label
                                    class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kesimpulan
                                    Akhir & Saran (Tercetak di Raport)</label>
                                <textarea name="catatan_akhir" id="catatanAkhir" rows="12" required
                                    class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 p-4 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm leading-relaxed"
                                    placeholder="Cth: Ananda {{ $student->nama_lengkap ?? 'Siswa' }} menunjukkan peningkatan yang luar biasa pada aspek akademik, namun perlu ditingkatkan kembali kedisiplinannya...">{{ old('catatan_akhir', $finalNote->catatan_akhir ?? '') }}</textarea>
                            </div>

                            {{-- Tombol Simpan --}}
                            <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                                <button type="submit"
                                    class="w-full bg-slate-800 hover:bg-slate-900 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md transition flex justify-center items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Simpan Catatan ke Database
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Script Generasi AI Gemini -->
    <script>
        async function generateCatatanAI() {
            const apiKey = document.getElementById('apiKey').value.trim();
            if (!apiKey) {
                alert("Mohon masukkan Gemini API Key Anda terlebih dahulu!");
                return;
            }

            const btn = document.getElementById('btnGenerateAI');
            const txt = document.getElementById('textGenerateAI');
            const targetTextarea = document.getElementById('catatanAkhir');

            // Simpan state awal tombol
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed', 'animate-pulse');
            txt.innerText = "Membaca & Memproses Data...";

            try {
                // 1. Kumpulkan Data Langsung dari Blade (Mencegah input tersangkut)
                const namaSiswa = "{{ $student->nama_lengkap ?? $student->nama }}";
                const sakit = {{ $sakit }};
                const izin = {{ $izin }};
                const alpha = {{ $alpha }};
                const piketBagus = {{ $piketTerlaksana }};
                const piketBuruk = {{ $piketTidak }};

                // Susun Catatan Piket Detail
                let stringPiketDetail = "";
                const listPiket = document.querySelectorAll('.item-piket');
                if(listPiket.length > 0) {
                    let arr = [];
                    listPiket.forEach(li => arr.push(li.innerText));
                    stringPiketDetail = arr.join('; ');
                }

                // Susun Nilai Ujian Tes
                let stringNilaiTes = "";
                const listNilaiTes = document.querySelectorAll('.item-nilai');
                if(listNilaiTes.length > 0) {
                    let arr = [];
                    listNilaiTes.forEach(li => arr.push(li.getAttribute('data-text')));
                    stringNilaiTes = arr.join(', ');
                } else {
                    stringNilaiTes = "Belum ada riwayat nilai ujian tertulis.";
                }

                // Susun Nilai Observasi Non-Tes
                let stringObservasi = "";
                const listObs = document.querySelectorAll('.item-observasi');
                if(listObs.length > 0) {
                    let arr = [];
                    listObs.forEach(li => arr.push(li.getAttribute('data-text')));
                    stringObservasi = arr.join(' | ');
                } else {
                    stringObservasi = "Belum ada penilaian praktik atau proyek.";
                }

                // Susun Catatan Guru Mapel
                let stringCatatanGuru = "";
                const listCatatan = document.querySelectorAll('#listCatatan li');
                if(listCatatan.length > 0) {
                    let arr = [];
                    listCatatan.forEach(li => {
                        let tipe = li.querySelector('.type-catatan') ? li.querySelector('.type-catatan').innerText : '';
                        let isi = li.querySelector('.isi-catatan') ? li.querySelector('.isi-catatan').innerText : '';
                        if(tipe && isi) arr.push(`(${tipe}) ${isi}`);
                    });
                    stringCatatanGuru = arr.join(' | ');
                } else {
                    stringCatatanGuru = "Tidak ada catatan pelanggaran atau prestasi mencolok.";
                }

                // 2. Siapkan Prompt untuk Gemini
                const prompt = `
                Bertindaklah sebagai Wali Kelas yang suportif, bijaksana, dan profesional di sekolah.
                Buatkan paragraf singkat untuk "Catatan Wali Kelas" yang akan dicetak di raport akhir semester milik siswa bernama ${namaSiswa}.

                Analisis data terekam siswa berikut ini:
                - KEHADIRAN: Sakit ${sakit} hari, Izin ${izin} hari, Tanpa Keterangan/Alpha ${alpha} hari.
                - TUGAS KEDISIPLINAN (PIKET): Terlaksana ${piketBagus} kali, Mangkir ${piketBuruk} kali. ${stringPiketDetail ? 'Alasan sering mangkir: ' + stringPiketDetail : ''}
                - RATA-RATA NILAI AKADEMIK (TES): ${stringNilaiTes}.
                - NILAI OBSERVASI (PRAKTIK/PROYEK): ${stringObservasi}.
                - RIWAYAT PERILAKU/JURNAL GURU: ${stringCatatanGuru}.

                Instruksi Penulisan (SANGAT PENTING):
                1. WAJIB tulis HANYA 1 atau 2 paragraf padat. Tidak boleh terlalu panjang.
                2. JANGAN membuat format list (bullet/nomor), JANGAN gunakan markdown tebal/bintang (**), dan JANGAN berikan kalimat pembuka/penutup basa-basi (seperti "Tentu, ini dia catatannya"). Langsung hasilkan teks narasi murni.
                3. Rangkum dan berikan apresiasi pada nilai akademik / observasi yang paling tinggi, tidak perlu menyebutkan semua mata pelajaran.
                4. Jika ada alpha > 3, sering mangkir piket, atau ada jurnal perilaku buruk, selipkan nasihat yang memotivasi (bukan menghakimi).
                5. Gunakan bahasa Indonesia baku yang sopan, rapi, dan menyejukkan hati orang tua saat membaca raport.
                `;

                // 3. Panggil API Gemini
                const response = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-goog-api-key': apiKey },
                    body: JSON.stringify({
                        contents: [{ parts: [{ text: prompt }] }],
                        generationConfig: { temperature: 0.7 }
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error?.message || "Kesalahan pada respon API dari Google.");
                }

                // 4. Masukkan hasil ke dalam textarea
                const resultText = data.candidates[0].content.parts[0].text;
                targetTextarea.value = resultText.trim();

            } catch (error) {
                console.error(error);
                alert("Gagal memproses AI: " + error.message);
            } finally {
                // Kembalikan state tombol
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed', 'animate-pulse');
                txt.innerText = "Generate Ulang dengan AI";
            }
        }
    </script>
</x-app-layout>