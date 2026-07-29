<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
            Input Nilai Observasi / Non-Tes
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- KOLOM API KEY GEMINI -->
        <div
            class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-2xl shadow-sm border border-indigo-100 dark:border-indigo-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-800 rounded-lg text-indigo-600 dark:text-indigo-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                        </path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-indigo-900 dark:text-indigo-300">AI Assistant Aktif</h4>
                    <p class="text-xs text-indigo-700 dark:text-indigo-400">Gunakan AI untuk merapikan catatan mentah
                        Anda.</p>
                </div>
            </div>
            <div class="relative w-full sm:w-72">
                <input type="password" id="geminiApiKey" value="{{ env('GEMINI_API_KEY', '') }}"
                    placeholder="Masukkan Gemini API Key..."
                    class="w-full text-sm rounded-xl border-indigo-200 dark:border-indigo-700 dark:bg-slate-800 focus:ring-indigo-500 focus:border-indigo-500 py-2 pl-3 pr-10 transition shadow-sm">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- KARTU INFORMASI PENILAIAN -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span
                            class="inline-flex items-center gap-1.5 py-0.5 px-3 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 uppercase tracking-wider">
                            Format Observasi (1 - {{ $assessment->scale }})
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 py-0.5 px-3 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            {{ $assessment->assessmentType->nama ?? 'Non-Tes' }}
                        </span>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white">{{ $assessment->keterangan }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        {{ $assessment->classroom->tingkat }} - {{ $assessment->classroom->nama_kelas }} &bull;
                        {{ $assessment->subject->nama_mapel }} &bull;
                        {{ \Carbon\Carbon::parse($assessment->tanggal)->format('d M Y') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('assessments.index') }}"
                        class="inline-flex items-center gap-1 text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition">
                        &larr; Kembali ke Riwayat
                    </a>
                </div>
            </div>
        </div>

        <!-- FORM INPUT NILAI -->
        <form action="{{ route('observations.updateScores', $assessment->id) }}" method="POST" class="space-y-6">
            @csrf

            <!-- LEGEND / DAFTAR KRITERIA -->
            <div
                class="bg-emerald-50 dark:bg-emerald-900/20 p-5 rounded-2xl border border-emerald-100 dark:border-emerald-800/50">
                <h4
                    class="text-sm font-black text-emerald-800 dark:text-emerald-400 mb-3 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                    Daftar Kriteria Observasi
                </h4>
                <ul class="space-y-2.5">
                    @foreach($assessment->criteria as $index => $kriteria)
                    <li
                        class="flex items-start gap-3 text-sm text-slate-700 dark:text-slate-300 bg-white/60 dark:bg-slate-900/40 px-3 py-2 rounded-lg">
                        <span class="font-black text-emerald-600 dark:text-emerald-500 shrink-0 w-8">K{{ $index + 1
                            }}</span>
                        <span class="font-medium">{{ $kriteria->descriptor }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- MATRIKS TABEL -->
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                    <h3 class="text-base font-black text-slate-800 dark:text-white">Matriks Penilaian Siswa</h3>
                    <p class="text-xs font-bold text-slate-500 mt-1">Pilih nilai lalu ketik catatan singkat (opsional).
                        Klik logo AI untuk merapikan catatan.</p>
                </div>

                <div class="overflow-x-auto relative">
                    <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                        <thead
                            class="text-xs text-slate-700 uppercase bg-slate-100 dark:bg-slate-900 dark:text-slate-300 whitespace-nowrap">
                            <tr>
                                <th
                                    class="px-4 py-4 w-12 text-center sticky left-0 bg-slate-100 dark:bg-slate-900 z-20">
                                    No</th>
                                <th
                                    class="px-4 py-4 w-64 sticky left-12 bg-slate-100 dark:bg-slate-900 z-20 border-r-2 border-slate-200 dark:border-slate-700">
                                    Nama Siswa</th>

                                @foreach($assessment->criteria as $index => $kriteria)
                                <th class="px-4 py-4 text-center min-w-[150px] border-r border-slate-200 dark:border-slate-700"
                                    title="{{ $kriteria->descriptor }}">
                                    <span class="block font-black text-emerald-600 dark:text-emerald-400 text-sm">K{{
                                        $index + 1 }}</span>
                                </th>
                                @endforeach

                                <th
                                    class="px-4 py-4 min-w-[300px] border-r border-slate-200 dark:border-slate-700 text-center bg-slate-100 dark:bg-slate-900">
                                    Catatan Tambahan & AI
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $siswa)
                            <tr
                                class="{{ $loop->last ? '' : 'border-b border-slate-100 dark:border-slate-700' }} hover:bg-emerald-50/50 dark:hover:bg-slate-700/50 transition duration-150">
                                <td
                                    class="px-4 py-3 font-bold text-center sticky left-0 bg-white dark:bg-slate-800 z-10 group-hover:bg-emerald-50/50 dark:group-hover:bg-slate-700/50">
                                    {{ $index + 1 }}
                                </td>
                                <td
                                    class="px-4 py-3 sticky left-12 bg-white dark:bg-slate-800 z-10 border-r-2 border-slate-200 dark:border-slate-700 whitespace-nowrap group-hover:bg-emerald-50/50 dark:group-hover:bg-slate-700/50">
                                    <span class="block font-bold text-slate-800 dark:text-slate-200">{{
                                        $siswa->nama_lengkap }}</span>
                                </td>

                                @foreach($assessment->criteria as $kriteria)
                                @php
                                $nilaiTersimpan = $existingScores[$siswa->id][$kriteria->id] ?? '';
                                @endphp
                                <td class="px-2 py-3 text-center border-r border-slate-100 dark:border-slate-700">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <label class="cursor-pointer group" title="Kosongkan">
                                            <input type="radio" name="scores[{{ $siswa->id }}][{{ $kriteria->id }}]"
                                                value="" {{ $nilaiTersimpan=='' ? 'checked' : '' }}
                                                class="peer sr-only">
                                            <div
                                                class="w-7 h-8 flex items-center justify-center rounded-md border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-slate-400 font-black text-sm peer-checked:bg-slate-200 peer-checked:text-slate-700 peer-checked:border-slate-300 dark:peer-checked:bg-slate-700 dark:peer-checked:text-slate-300 hover:bg-slate-100 transition shadow-sm">
                                                -</div>
                                        </label>

                                        @for($i = 1; $i <= $assessment->scale; $i++)
                                            <label class="cursor-pointer group">
                                                <input type="radio" name="scores[{{ $siswa->id }}][{{ $kriteria->id }}]"
                                                    value="{{ $i }}" {{ $nilaiTersimpan==$i ? 'checked' : '' }}
                                                    class="peer sr-only">
                                                <div
                                                    class="w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-black text-sm peer-checked:bg-emerald-500 peer-checked:border-emerald-600 peer-checked:text-white peer-checked:shadow-inner hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 transition shadow-sm">
                                                    {{ $i }}</div>
                                            </label>
                                            @endfor
                                    </div>
                                </td>
                                @endforeach

                                <!-- INPUT CATATAN DENGAN TOMBOL AI -->
                                @php
                                $catatanSiswa = $existingNotes[$siswa->id] ?? '';
                                @endphp
                                <td class="px-3 py-3 border-r border-slate-100 dark:border-slate-700 relative">
                                    <div class="flex gap-2">
                                        <input type="text" id="note_{{ $siswa->id }}" name="notes[{{ $siswa->id }}]"
                                            value="{{ $catatanSiswa }}" placeholder="Tulis draf kasar..."
                                            class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 focus:ring-emerald-500 focus:border-emerald-500 py-1.5 dark:text-white transition shadow-sm">

                                        <button type="button"
                                            onclick="generateAINote({{ $siswa->id }}, '{{ addslashes($siswa->nama_lengkap) }}')"
                                            id="btnAi_{{ $siswa->id }}"
                                            class="shrink-0 p-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white border border-indigo-200 rounded-lg transition-colors shadow-sm flex items-center justify-center"
                                            title="Rapikan catatan dengan AI">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2"
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

            <!-- TOMBOL SIMPAN -->
            <div class="flex justify-end pt-2 pb-6">
                <button type="submit"
                    class="px-8 py-3.5 bg-emerald-600 text-white rounded-xl text-sm font-black shadow-lg shadow-emerald-500/30 hover:bg-emerald-700 transform hover:-translate-y-0.5 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7">
                        </path>
                    </svg>
                    Simpan
                </button>
            </div>
        </form>

    </div>

    <!-- SCRIPT AI GENERATOR[cite: 6] -->
    <script>
        // Siapkan data Kriteria dari PHP ke Array Javascript
        const criteriaData = [
            @foreach($assessment->criteria as $k)
                { id: {{ $k->id }}, text: "{{ addslashes($k->descriptor) }}" },
            @endforeach
        ];

        async function generateAINote(studentId, studentName) {
            const apiKey = document.getElementById('geminiApiKey').value.trim();
            if (!apiKey) {
                alert("Silakan masukkan Gemini API Key di kolom paling atas terlebih dahulu!");
                return;
            }

            const inputField = document.getElementById(`note_${studentId}`);
            const btnAi = document.getElementById(`btnAi_${studentId}`);
            const rawNote = inputField.value.trim();
            const maxScale = {{ $assessment->scale }};

            // Kumpulkan nilai skor yang dipilih untuk siswa ini
            let scoreDetails = [];
            let isAnyAssessed = false;

            criteriaData.forEach((crit, index) => {
                const checkedRadio = document.querySelector(`input[name="scores[${studentId}][${crit.id}]"]:checked`);
                const score = (checkedRadio && checkedRadio.value !== "") ? checkedRadio.value : "Belum dinilai";
                if(score !== "Belum dinilai") isAnyAssessed = true;

                scoreDetails.push(`- Kriteria ${index + 1} (${crit.text}): Skor ${score} dari maksimal ${maxScale}.`);
            });

            if (!isAnyAssessed && !rawNote) {
                alert("Silakan isi nilai matriks observasi atau ketik draf kasar terlebih dahulu sebelum menggunakan AI.");
                return;
            }

            // Susun Prompt
            const prompt = `
                Anda adalah asisten guru profesional yang bertugas menyusun catatan deskripsi nilai raport/observasi.

                Data Siswa: ${studentName}
                Hasil Observasi (Skala 1 - ${maxScale}):
                ${scoreDetails.join('\n')}

                Catatan mentah dari guru: "${rawNote || 'Tidak ada catatan mentah'}"

                Instruksi:
                1. Analisis skor observasi tersebut (skor mendekati ${maxScale} artinya sangat baik, skor 1 artinya kurang).
                2. Jadikan catatan mentah dari guru dan skor di atas menjadi 1-2 kalimat naratif deskriptif yang rapi, profesional, memotivasi, dan mudah dipahami oleh orang tua wali murid.
                3. Jangan bertele-tele, langsung berikan hasilnya. Jangan menggunakan tag markdown tebal/miring.
            `;

            // Animasi Loading
            btnAi.disabled = true;
            btnAi.innerHTML = `<svg class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;

            try {
                const response = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-goog-api-key': apiKey },
                    body: JSON.stringify({ contents: [{ parts: [{ text: prompt }] }], generationConfig: { temperature: 0.7 } })
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.error?.message || "Error API Gemini.");

                // Ambil text dan buang enter/spasi berlebih
                let generatedNote = data.candidates[0].content.parts[0].text.trim().replace(/\n/g, ' ');

                // Isi kembali ke input
                inputField.value = generatedNote;

            } catch (error) {
                alert("Gagal menghubungi AI: " + error.message);
            } finally {
                // Kembalikan tombol ke icon awal
                btnAi.disabled = false;
                btnAi.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.277 1.277L3 12l5.8 1.9a2 2 0 0 1 1.277 1.277L12 21l1.9-5.8a2 2 0 0 1 1.277-1.277L21 12l-5.8-1.9a2 2 0 0 1-1.277-1.277C13.8 4.2 12 3 12 3Z"/></svg>`;
            }
        }
    </script>
</x-app-layout>