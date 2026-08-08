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

            <!-- Kolom API Key AI (Berdasarkan Modul Generator) -->
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

                {{-- KOLOM KIRI: REKAP INFORMASI --}}
                <div class="lg:col-span-1 space-y-6">

                    {{-- Rekap Absen & Piket --}}
                    <div
                        class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                        <h3
                            class="font-black text-slate-800 dark:text-slate-200 mb-4 border-b border-slate-100 dark:border-slate-700 pb-2 uppercase tracking-wider text-sm">
                            Rekap Kehadiran & Piket</h3>

                        <div class="space-y-5">
                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kehadiran
                                    (Absensi)</p>
                                <ul class="text-sm space-y-2">
                                    <li
                                        class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 px-3 py-2 rounded-lg border border-slate-100 dark:border-slate-700/50">
                                        <span class="text-slate-600 dark:text-slate-400 font-medium">Sakit</span>
                                        <span class="font-bold text-amber-500" id="valSakit">{{ $finalNote->sakit ??
                                            $sakit }} Hari</span>
                                    </li>
                                    <li
                                        class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 px-3 py-2 rounded-lg border border-slate-100 dark:border-slate-700/50">
                                        <span class="text-slate-600 dark:text-slate-400 font-medium">Izin</span>
                                        <span class="font-bold text-blue-500" id="valIzin">{{ $finalNote->izin ?? $izin
                                            }} Hari</span>
                                    </li>
                                    <li
                                        class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 px-3 py-2 rounded-lg border border-slate-100 dark:border-slate-700/50">
                                        <span class="text-slate-600 dark:text-slate-400 font-medium">Alpha</span>
                                        <span class="font-bold text-rose-500" id="valAlpha">{{ $finalNote->alpha ??
                                            $alpha }} Hari</span>
                                    </li>
                                </ul>
                            </div>

                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kepatuhan
                                    Piket Harian</p>
                                <ul class="text-sm space-y-2">
                                    <li
                                        class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 px-3 py-2 rounded-lg border border-slate-100 dark:border-slate-700/50">
                                        <span class="text-slate-600 dark:text-slate-400 font-medium">Tugas
                                            Terlaksana</span>
                                        <span class="font-bold text-emerald-500" id="valPiketBagus">{{ $piketTerlaksana
                                            }}x</span>
                                    </li>
                                    <li
                                        class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 px-3 py-2 rounded-lg border border-slate-100 dark:border-slate-700/50">
                                        <span class="text-slate-600 dark:text-slate-400 font-medium">Tidak/Kabur</span>
                                        <span class="font-bold text-rose-500" id="valPiketBuruk">{{ $piketTidak
                                            }}x</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Rekap Nilai Akademik --}}
                    <div
                        class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                        <h3
                            class="font-black text-slate-800 dark:text-slate-200 mb-4 border-b border-slate-100 dark:border-slate-700 pb-2 uppercase tracking-wider text-sm">
                            Rekap Nilai Akademik</h3>

                        @if(isset($rekapNilai) && count($rekapNilai) > 0)
                        <ul class="text-sm space-y-2" id="listNilai">
                            @foreach($rekapNilai as $nilai)
                            <li
                                class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 px-3 py-2.5 rounded-lg border border-slate-100 dark:border-slate-700/50">
                                <span class="text-slate-600 dark:text-slate-400 font-medium truncate pr-3"
                                    data-mapel="{{ $nilai->nama_mapel }}">{{ $nilai->nama_mapel }}</span>
                                <span class="font-black text-indigo-600 dark:text-indigo-400"
                                    data-skor="{{ $nilai->nilai_akhir }}">{{ $nilai->nilai_akhir }}</span>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <div
                            class="text-center bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50">
                            <p class="text-sm text-slate-500 italic">Data nilai mata pelajaran belum tersedia.</p>
                        </div>
                        @endif
                    </div>

                    {{-- Rekap Catatan Guru --}}
                    <div
                        class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                        <h3
                            class="font-black text-slate-800 dark:text-slate-200 mb-4 border-b border-slate-100 dark:border-slate-700 pb-2 uppercase tracking-wider text-sm">
                            Catatan Kejadian Guru</h3>

                        @if($teacherNotes->isEmpty())
                        <div class="text-center bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50"
                            id="listCatatan">
                            <p class="text-sm text-slate-500 italic">Tidak ada riwayat catatan.</p>
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
                                    <span
                                        class="text-[10px] text-slate-400 font-bold bg-white dark:bg-slate-800 px-2 py-1 rounded shadow-sm">
                                        {{ \Carbon\Carbon::parse($note->tanggal)->translatedFormat('d M Y') }}
                                    </span>
                                </div>
                                <p class="text-slate-600 dark:text-slate-300 leading-relaxed mt-1 isi-catatan">{{
                                    $note->catatan }}</p>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>

                {{-- KOLOM KANAN: FORM INPUT WALI KELAS --}}
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
                                    untuk memproses data dari panel samping secara otomatis.</p>
                            </div>

                            {{-- Tombol Generate AI --}}
                            <button type="button" onclick="generateCatatanAI()" id="btnGenerateAI"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white text-sm font-bold rounded-xl shadow-md transition-all transform hover:-translate-y-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span id="textGenerateAI">Generate AI</span>
                            </button>
                        </div>

                        <form action="{{ route('catatan_akhir.update', [$student->id, $classroom->id]) }}" method="POST"
                            class="space-y-6">
                            @csrf
                            <input type="hidden" name="academic_year_id" value="{{ $active_academic_year_id }}">
                            <input type="hidden" name="piket_terlaksana" value="{{ $piketTerlaksana }}">
                            <input type="hidden" name="piket_tidak_terlaksana" value="{{ $piketTidak }}">
                            <input type="hidden" name="ringkasan_catatan_guru"
                                value="{{ $teacherNotes->pluck('catatan')->implode(' | ') }}">

                            {{-- Penyesuaian Angka Kehadiran --}}
                            <div
                                class="bg-slate-50 dark:bg-slate-900/30 p-5 rounded-xl border border-slate-100 dark:border-slate-700/50">
                                <h4
                                    class="font-bold text-slate-700 dark:text-slate-300 mb-4 text-sm uppercase tracking-wider">
                                    Validasi Absensi Akhir</h4>
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2">Total
                                            Sakit</label>
                                        <input type="number" name="sakit" id="inputSakit"
                                            value="{{ old('sakit', $finalNote->sakit ?? $sakit) }}" min="0"
                                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2">Total
                                            Izin</label>
                                        <input type="number" name="izin" id="inputIzin"
                                            value="{{ old('izin', $finalNote->izin ?? $izin) }}" min="0"
                                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2">Total
                                            Alpha</label>
                                        <input type="number" name="alpha" id="inputAlpha"
                                            value="{{ old('alpha', $finalNote->alpha ?? $alpha) }}" min="0"
                                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm">
                                    </div>
                                </div>
                            </div>

                            {{-- Catatan Final Wali Kelas --}}
                            <div class="relative">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Catatan
                                    Perilaku & Akademik (Cetak ke Raport)</label>
                                <textarea name="catatan_akhir" id="catatanAkhir" rows="9" required
                                    class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 p-4 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm leading-relaxed"
                                    placeholder="Cth: Ananda {{ $student->nama_lengkap ?? 'Siswa' }} menunjukkan peningkatan yang luar biasa pada aspek akademik, namun perlu ditingkatkan kembali kedisiplinannya..."></textarea>
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                                <button type="submit"
                                    class="w-full bg-slate-800 hover:bg-slate-900 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md transition flex justify-center items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Simpan Catatan Akhir Siswa
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Script Generasi AI -->
    <script>
        async function generateCatatanAI() {
            const apiKey = document.getElementById('apiKey').value.trim();
            if (!apiKey) {
                alert("Mohon masukkan Gemini API Key terlebih dahulu di bagian atas!");
                return;
            }

            const btn = document.getElementById('btnGenerateAI');
            const txt = document.getElementById('textGenerateAI');
            const targetTextarea = document.getElementById('catatanAkhir');

            // Simpan state awal tombol
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            txt.innerText = "Memproses Data...";

            try {
                // 1. Kumpulkan Data dari Panel Kiri & Input Form
                const namaSiswa = "{{ $student->nama_lengkap ?? $student->nama }}";
                const sakit = document.getElementById('inputSakit').value || 0;
                const izin = document.getElementById('inputIzin').value || 0;
                const alpha = document.getElementById('inputAlpha').value || 0;

                const piketBagus = "{{ $piketTerlaksana }}";
                const piketBuruk = "{{ $piketTidak }}";

                // Susun string nilai
                let stringNilai = "";
                const listNilai = document.querySelectorAll('#listNilai li');
                if(listNilai.length > 0) {
                    let mapelArr = [];
                    listNilai.forEach(li => {
                        let m = li.querySelector('[data-mapel]').getAttribute('data-mapel');
                        let s = li.querySelector('[data-skor]').getAttribute('data-skor');
                        mapelArr.push(`${m}: ${s}`);
                    });
                    stringNilai = mapelArr.join(', ');
                } else {
                    stringNilai = "Data nilai belum tersedia.";
                }

                // Susun string catatan guru
                let stringCatatan = "";
                const listCatatan = document.querySelectorAll('#listCatatan li');
                if(listCatatan.length > 0) {
                    let catArr = [];
                    listCatatan.forEach(li => {
                        let tipe = li.querySelector('.type-catatan').innerText;
                        let isi = li.querySelector('.isi-catatan').innerText;
                        catArr.push(`(${tipe}) ${isi}`);
                    });
                    stringCatatan = catArr.join(' | ');
                } else {
                    stringCatatan = "Tidak ada riwayat pelanggaran atau prestasi mencolok.";
                }

                // 2. Siapkan Prompt untuk Gemini
                const prompt = `
                Bertindaklah sebagai Wali Kelas yang suportif, bijaksana, dan profesional.
                Buatkan paragraf singkat "Catatan Wali Kelas" untuk dicetak di raport akhir semester milik siswa bernama ${namaSiswa}.

                Berdasarkan data riwayat siswa berikut:
                - Kehadiran: Sakit ${sakit} hari, Izin ${izin} hari, Tanpa Keterangan ${alpha} hari.
                - Kedisiplinan Tugas (Piket): Terlaksana ${piketBagus} kali, Mangkir/Tidak Terlaksana ${piketBuruk} kali.
                - Rangkuman Nilai Akademik: ${stringNilai}.
                - Riwayat Catatan Guru Mapel: ${stringCatatan}.

                Instruksi Penulisan (SANGAT PENTING):
                1. Tulis 1 atau 2 paragraf saja. Tidak boleh terlalu panjang.
                2. JANGAN membuat format list (bullet/nomor), JANGAN gunakan markdown bintang (**), dan JANGAN berikan kalimat pembuka/penutup seperti "Tentu, ini dia catatannya". Langsung hasilkan teks narasi murni.
                3. Berikan apresiasi pada nilai yang paling menonjol atau prestasi yang baik.
                4. Jika ada kelemahan di nilai, absen, tugas piket, atau catatan buruk, berikan nasihat atau teguran yang memotivasi dan membangun, bukan menghakimi.
                5. Gunakan bahasa baku yang rapi dan mudah dibaca oleh orang tua siswa.
                `;

                // 3. Panggil API Gemini[cite: 24]
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
                    throw new Error(data.error?.message || "Kesalahan pada respon API");
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
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
                txt.innerText = "Generate Ulang AI";
            }
        }
    </script>
</x-app-layout>