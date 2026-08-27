<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
            Input Nilai Observasi / Non-Tes
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

        <!-- KOLOM API KEY GEMINI -->
        <div
            class="bg-indigo-50 dark:bg-indigo-900/20 p-3 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="p-1.5 bg-indigo-100 dark:bg-indigo-800 rounded-lg text-indigo-600 dark:text-indigo-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                        </path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-indigo-900 dark:text-indigo-300">AI Assistant Aktif</h4>
                    <p class="text-[10px] text-indigo-700 dark:text-indigo-400">Gunakan AI untuk merapikan catatan
                        mentah Anda.</p>
                </div>
            </div>
            <div class="relative w-full sm:w-64">
                <input type="password" id="geminiApiKey" value="{{ env('GEMINI_API_KEY', '') }}"
                    placeholder="Masukkan Gemini API Key..."
                    class="w-full text-xs rounded-lg border-indigo-200 dark:border-indigo-700 dark:bg-slate-800 focus:ring-indigo-500 focus:border-indigo-500 py-1.5 pl-3 pr-8 transition shadow-sm">
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <svg class="w-3 h-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- KARTU INFORMASI PENILAIAN -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3">
                <div>
                    <div class="flex items-center gap-1.5 mb-1.5">
                        <span
                            class="inline-flex items-center gap-1 py-0.5 px-2.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 uppercase tracking-wider">
                            Format Observasi (1 - {{ $assessment->scale }})
                        </span>
                        <span
                            class="inline-flex items-center gap-1 py-0.5 px-2.5 rounded-full text-[9px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            {{ $assessment->assessmentType->nama ?? 'Non-Tes' }}
                        </span>
                    </div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white">{{ $assessment->keterangan }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        {{ $assessment->classroom->tingkat }} - {{ $assessment->classroom->nama_kelas }} &bull;
                        {{ $assessment->subject->nama_mapel }} &bull;
                        {{ \Carbon\Carbon::parse($assessment->tanggal)->format('d M Y') }}
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('assessments.index') }}"
                        class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition">
                        &larr; Kembali ke Riwayat
                    </a>

                    <!-- TOMBOL EXPORT EXCEL BARU -->
                    <a href="{{ route('observations.export', $assessment->id) }}"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 text-xs font-bold rounded-lg hover:bg-emerald-200 transition shadow-sm border border-emerald-200 dark:border-emerald-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Export Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- FORM INPUT NILAI -->
        <form action="{{ route('observations.updateScores', $assessment->id) }}" method="POST" class="space-y-4">
            @csrf

            <!-- LEGEND / DAFTAR KRITERIA -->
            <div
                class="bg-emerald-50 dark:bg-emerald-900/20 p-3.5 rounded-xl border border-emerald-100 dark:border-emerald-800/50">
                <h4
                    class="text-xs font-black text-emerald-800 dark:text-emerald-400 mb-2 uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                    Daftar Kriteria Observasi
                </h4>
                <ul class="space-y-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-3">
                    @foreach($assessment->criteria as $index => $kriteria)
                    <li
                        class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300 bg-white/60 dark:bg-slate-900/40 px-2 py-1.5 rounded-md border border-transparent focus-within:border-emerald-300 focus-within:ring-1 focus-within:ring-emerald-300 transition-all">
                        <span class="font-black text-emerald-600 dark:text-emerald-500 shrink-0">K{{ $index + 1
                            }}</span>
                        <!-- Ubah text biasa menjadi input field -->
                        <input type="text" name="criteria[{{ $kriteria->id }}]" value="{{ $kriteria->descriptor }}"
                            class="w-full bg-transparent border-none p-0 focus:ring-0 text-xs font-medium text-slate-700 dark:text-slate-300 placeholder-slate-400"
                            placeholder="Masukkan deskripsi kriteria..." required>
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- MATRIKS TABEL -->
            <div
                class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">

                <!-- HEADER MATRIKS & TOMBOL GENERATE MASSAL -->
                <div
                    class="p-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-white">Matriks Penilaian Siswa</h3>
                        <p class="text-[10px] font-medium text-slate-500 mt-0.5">Isi skor & ketik catatan mentah. Lalu
                            klik <strong class="text-indigo-600">Generate AI Semua Siswa</strong>.</p>
                    </div>

                    <button type="button" id="btnGenerateAll" onclick="generateAllAINotesBatch()"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition shadow-sm shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="m12 3-1.9 5.8a2 2 0 0 1-1.277 1.277L3 12l5.8 1.9a2 2 0 0 1 1.277 1.277L12 21l1.9-5.8a2 2 0 0 1 1.277-1.277L21 12l-5.8-1.9a2 2 0 0 1-1.277-1.277C13.8 4.2 12 3 12 3Z" />
                        </svg>
                        Generate AI Semua Siswa
                    </button>
                </div>

                <div class="overflow-x-auto relative">
                    <table class="w-full text-left text-slate-500 dark:text-slate-400">
                        <thead
                            class="text-[10px] text-slate-700 uppercase bg-slate-100 dark:bg-slate-900 dark:text-slate-300 whitespace-nowrap">
                            <tr>
                                <th
                                    class="px-2 py-2 w-10 min-w-[40px] text-center sticky left-0 bg-slate-100 dark:bg-slate-900 z-20 border-r border-slate-200 dark:border-slate-700">
                                    No</th>
                                <th
                                    class="px-2 py-2 w-48 min-w-[120px] max-w-[192px] sticky left-10 bg-slate-100 dark:bg-slate-900 z-20 border-r-2 border-slate-200 dark:border-slate-700">
                                    Nama Siswa</th>

                                @foreach($assessment->criteria as $index => $kriteria)
                                <th class="px-1.5 py-2 text-center min-w-[90px] border-r border-slate-200 dark:border-slate-700"
                                    title="{{ $kriteria->descriptor }}">
                                    <span class="block font-black text-emerald-600 dark:text-emerald-400">K{{ $index + 1
                                        }}</span>
                                </th>
                                @endforeach

                                <th
                                    class="px-3 py-2 min-w-[220px] border-r border-slate-200 dark:border-slate-700 text-center bg-slate-100 dark:bg-slate-900">
                                    Catatan Tambahan & AI
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $siswa)
                            <tr
                                class="{{ $loop->last ? '' : 'border-b border-slate-100 dark:border-slate-700' }} hover:bg-emerald-50/50 dark:hover:bg-slate-700/50 transition duration-150">
                                <td
                                    class="px-2 py-1.5 text-xs font-bold text-center sticky left-0 bg-white dark:bg-slate-800 z-10 group-hover:bg-emerald-50/50 dark:group-hover:bg-slate-700/50 border-r border-slate-100 dark:border-slate-700">
                                    {{ $index + 1 }}
                                </td>
                                <td
                                    class="px-2 py-1.5 sticky left-10 bg-white dark:bg-slate-800 z-10 border-r-2 border-slate-200 dark:border-slate-700 group-hover:bg-emerald-50/50 dark:group-hover:bg-slate-700/50">
                                    <span
                                        class="block text-xs font-bold text-slate-800 dark:text-slate-200 truncate max-w-[180px]">{{
                                        $siswa->nama_lengkap }}</span>
                                </td>

                                @foreach($assessment->criteria as $kriteria)
                                @php
                                $nilaiTersimpan = $existingScores[$siswa->id][$kriteria->id] ?? '';
                                @endphp
                                <td class="px-1 py-1.5 text-center border-r border-slate-100 dark:border-slate-700">
                                    <div class="flex items-center justify-center gap-1 flex-wrap">
                                        <label class="cursor-pointer group" title="Kosongkan">
                                            <input type="radio" name="scores[{{ $siswa->id }}][{{ $kriteria->id }}]"
                                                value="" {{ $nilaiTersimpan=='' ? 'checked' : '' }}
                                                class="peer sr-only">
                                            <div
                                                class="w-6 h-6 flex items-center justify-center rounded border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-slate-400 font-black text-[10px] peer-checked:bg-slate-200 peer-checked:text-slate-700 peer-checked:border-slate-300 hover:bg-slate-100 transition shadow-sm">
                                                -
                                            </div>
                                        </label>

                                        @for($i = 1; $i <= $assessment->scale; $i++)
                                            <label class="cursor-pointer group">
                                                <input type="radio" name="scores[{{ $siswa->id }}][{{ $kriteria->id }}]"
                                                    value="{{ $i }}" {{ $nilaiTersimpan==$i ? 'checked' : '' }}
                                                    class="peer sr-only">
                                                <div
                                                    class="w-6 h-6 flex items-center justify-center rounded border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-black text-[10px] peer-checked:bg-emerald-500 peer-checked:border-emerald-600 peer-checked:text-white peer-checked:shadow-inner hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 transition shadow-sm">
                                                    {{ $i }}
                                                </div>
                                            </label>
                                            @endfor
                                    </div>
                                </td>
                                @endforeach

                                <!-- INPUT CATATAN DENGAN TOMBOL AI (Individu) -->
                                @php
                                $catatanSiswa = $existingNotes[$siswa->id] ?? '';
                                @endphp
                                <td class="px-2 py-1.5 border-r border-slate-100 dark:border-slate-700 relative">
                                    <div class="flex gap-1.5 items-center">
                                        <input type="text" id="note_{{ $siswa->id }}" name="notes[{{ $siswa->id }}]"
                                            value="{{ $catatanSiswa }}" placeholder="Tulis draf..."
                                            class="w-full text-[11px] rounded-md border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 focus:ring-emerald-500 focus:border-emerald-500 py-1 px-2 dark:text-white transition shadow-sm">

                                        <button type="button"
                                            onclick="generateAINote({{ $siswa->id }}, '{{ addslashes($siswa->nama_lengkap) }}')"
                                            id="btnAi_{{ $siswa->id }}"
                                            class="shrink-0 w-6 h-6 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white border border-indigo-200 rounded-md transition-colors shadow-sm flex items-center justify-center"
                                            title="Rapikan catatan">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path
                                                    d="m12 3-1.9 5.8a2 2 0 0 1-1.277 1.277L3 12l5.8 1.9a2 2 0 0 1 1.277 1.277L12 21l1.9-5.8a2 2 0 0 1 1.277-1.277L21 12l-5.8-1.9a2 2 0 0 1-1.277-1.277C13.8 4.2 12 3 12 3Z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TOMBOL SIMPAN (FORM) -->
            <div class="flex justify-end pt-1 pb-4">
                <button type="submit"
                    class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg text-xs font-black shadow-lg shadow-emerald-500/30 hover:bg-emerald-700 transform hover:-translate-y-0.5 transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7">
                        </path>
                    </svg>
                    Simpan Penilaian Observasi
                </button>
            </div>
        </form>
    </div>

    <!-- SCRIPT AI GENERATOR TETAP SAMA SEPERTI SEBELUMNYA -->
    <script>
        // Siapkan referensi ke kriteria dan daftar siswa dari Laravel
        const criteriaData = [
            @foreach($assessment->criteria as $k)
                { id: {{ $k->id }}, text: "{{ addslashes($k->descriptor) }}" },
            @endforeach
        ];

        const allStudents = [
            @foreach($students as $siswa)
                { id: {{ $siswa->id }}, name: "{{ addslashes($siswa->nama_lengkap) }}" },
            @endforeach
        ];

        const maxScale = {{ $assessment->scale }};

        // ==============================================
        // 1. FUNGSI GENERATE AI MASSAL (BATCH)
        // ==============================================
        async function generateAllAINotesBatch() {
            const apiKey = document.getElementById('geminiApiKey').value.trim();
            if (!apiKey) {
                alert("Silakan masukkan Gemini API Key di kolom paling atas terlebih dahulu!");
                return;
            }

            // Kumpulkan data semua siswa yang sudah memiliki skor atau catatan mentah
            let studentsData = [];

            allStudents.forEach(student => {
                const inputField = document.getElementById(`note_${student.id}`);
                const rawNote = inputField.value.trim();

                let scoreDetails = [];
                let isAnyAssessed = false;

                criteriaData.forEach((crit, index) => {
                    const checkedRadio = document.querySelector(`input[name="scores[${student.id}][${crit.id}]"]:checked`);
                    const score = (checkedRadio && checkedRadio.value !== "") ? checkedRadio.value : null;
                    if(score) {
                        isAnyAssessed = true;
                        scoreDetails.push(`K${index + 1}=${score}`);
                    }
                });

                if (isAnyAssessed || rawNote) {
                    studentsData.push({
                        id: student.id,
                        nama: student.name,
                        skor: scoreDetails.join(', '),
                        catatan_mentah: rawNote || "-"
                    });
                }
            });

            if (studentsData.length === 0) {
                alert("Pilih nilai matriks observasi atau isi catatan mentah untuk setidaknya satu siswa terlebih dahulu.");
                return;
            }

            // Animasi Loading Tombol
            const btnGenerateAll = document.getElementById('btnGenerateAll');
            const originalBtnText = btnGenerateAll.innerHTML;
            btnGenerateAll.disabled = true;
            btnGenerateAll.innerHTML = `<svg class="w-3.5 h-3.5 animate-spin inline mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> AI Menyusun ${studentsData.length} Catatan...`;

            // Prompt Batch yang mewajibkan balasan berformat JSON
            const prompt = `
                Tugas Anda: Menyusun deskripsi nilai rapor (1-2 kalimat) untuk siswa berdasarkan data observasi.
                Gunakan bahasa Indonesia yang profesional, positif, memotivasi, dan mudah dipahami wali murid.

                Kriteria Observasi (Skor Maksimal ${maxScale}):
                ${criteriaData.map((c, i) => {
                    const inputEl = document.querySelector(`input[name="criteria[${c.id}]"]`);
                    const realText = inputEl ? inputEl.value : c.text;
                    return `K${i+1}: ${realText}`;
                }).join('\n')}

                Data Siswa (Format JSON input):
                ${JSON.stringify(studentsData)}

                ATURAN SANGAT PENTING:
                Anda WAJIB memberikan balasan HANYA dalam format array JSON murni persis seperti struktur di bawah ini. Jangan tambahkan kata pembuka/penutup, jangan bungkus dengan tag markdown (\`\`\`json).
                [
                  {"id": 1, "catatan": "Ananda sangat baik dalam... namun perlu bimbingan di..."},
                  {"id": 2, "catatan": "..."}
                ]
            `;

            try {
                const response = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-goog-api-key': apiKey },
                    body: JSON.stringify({ contents: [{ parts: [{ text: prompt }] }], generationConfig: { temperature: 0.7 } })
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.error?.message || "Error API Gemini.");

                let rawText = data.candidates[0].content.parts[0].text.trim();

                // Membersihkan tag markdown jika AI bandel
                rawText = rawText.replace(/```json/g, '').replace(/```/g, '').trim();

                const parsedResult = JSON.parse(rawText);

                // Kembalikan narasi AI ke dalam masing-masing input textbox
                parsedResult.forEach(item => {
                    const inputField = document.getElementById(`note_${item.id}`);
                    if (inputField) {
                        inputField.value = item.catatan;

                        // Berikan efek highlight hijau sekilas agar user tahu form berhasil diisi
                        inputField.classList.add('bg-emerald-50', 'border-emerald-500');
                        setTimeout(() => inputField.classList.remove('bg-emerald-50', 'border-emerald-500'), 1500);
                    }
                });

            } catch (error) {
                alert("Gagal memproses AI secara massal (format salah/koneksi putus). Coba ulangi kembali.\nError: " + error.message);
            } finally {
                btnGenerateAll.disabled = false;
                btnGenerateAll.innerHTML = originalBtnText;
            }
        }

        // ==============================================
        // 2. FUNGSI GENERATE AI INDIVIDU
        // ==============================================
        async function generateAINote(studentId, studentName) {
            const apiKey = document.getElementById('geminiApiKey').value.trim();
            if (!apiKey) {
                alert("Silakan masukkan Gemini API Key di kolom paling atas terlebih dahulu!");
                return;
            }

            const inputField = document.getElementById(`note_${studentId}`);
            const btnAi = document.getElementById(`btnAi_${studentId}`);
            const rawNote = inputField.value.trim();

            let scoreDetails = [];
            let isAnyAssessed = false;

criteriaData.forEach((crit, index) => {
                const checkedRadio = document.querySelector(`input[name="scores[${studentId}][${crit.id}]"]:checked`);
                const score = (checkedRadio && checkedRadio.value !== "") ? checkedRadio.value : "Belum dinilai";
                if(score !== "Belum dinilai") isAnyAssessed = true;

                // Ambil teks kriteria terbaru langsung dari input form
                const critInput = document.querySelector(`input[name="criteria[${crit.id}]"]`);
                const currentCritText = critInput ? critInput.value : crit.text;

                scoreDetails.push(`- Kriteria ${index + 1} (${currentCritText}): Skor ${score} dari maksimal ${maxScale}.`);
            });

            if (!isAnyAssessed && !rawNote) {
                alert("Silakan isi nilai matriks observasi atau ketik draf kasar terlebih dahulu.");
                return;
            }

    const prompt = `
                Anda adalah sistem penyusun laporan akademik profesional yang bertugas menulis catatan hasil observasi/non-tes siswa secara faktual dan objektif. Anda memahami konteks pendidikan dan penilaian akademik. Gunakan bahasa Indonesia yang baku, lugas, dan profesional.

                Data Siswa: ${studentName}
                Hasil Observasi (Skala 1 - ${maxScale}):
                ${scoreDetails.join('\n')}
                Catatan dari guru: "${rawNote || 'Tidak ada catatan'}"

                Instruksi:
                Susun 1-2 kalimat deskriptif yang melaporkan kondisi atau capaian siswa murni berdasarkan kombinasi skor dan catatan di atas.
                Gunakan bahasa baku yang lugas, rapi, dan profesional.
                DILARANG menambahkan opini, kalimat motivasi, nasihat, atau kata-kata berbunga-bunga. Laporkan apa adanya sesuai data.
            `;

            btnAi.disabled = true;
            btnAi.innerHTML = `<svg class="w-3.5 h-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;

            try {
                const response = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-goog-api-key': apiKey },
                    body: JSON.stringify({ contents: [{ parts: [{ text: prompt }] }], generationConfig: { temperature: 0.7 } })
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.error?.message || "Error API Gemini.");

                let generatedNote = data.candidates[0].content.parts[0].text.trim().replace(/\n/g, ' ');
                inputField.value = generatedNote;
            } catch (error) {
                alert("Gagal menghubungi AI: " + error.message);
            } finally {
                btnAi.disabled = false;
                btnAi.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.277 1.277L3 12l5.8 1.9a2 2 0 0 1 1.277 1.277L12 21l1.9-5.8a2 2 0 0 1 1.277-1.277L21 12l-5.8-1.9a2 2 0 0 1-1.277-1.277C13.8 4.2 12 3 12 3Z"/></svg>`;
            }
        }
    </script>
</x-app-layout>