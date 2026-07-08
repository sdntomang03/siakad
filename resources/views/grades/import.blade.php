<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
                Import Rekap Nilai <span class="text-indigo-600">Sidanira</span>
            </h2>

            <a href="{{ route('dashboard') }}"
                class="text-sm font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                &larr; Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div
                class="mb-6 flex items-center p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 text-sm font-bold shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div
                class="mb-6 flex items-center p-4 rounded-xl bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-400 text-sm font-bold shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('error') }}
            </div>
            @endif

            <form action="{{ route('grades.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <div class="lg:col-span-2 space-y-6">

                        <div
                            class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <div
                                class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                                <h3 class="text-base font-black text-slate-800 dark:text-white flex items-center">
                                    <span
                                        class="flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-400 text-xs mr-2">1</span>
                                    Pengaturan & Unduh Template
                                </h3>
                            </div>

                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Tingkat
                                        Kelas</label>
                                    <div class="flex gap-2">
                                        <select name="tingkat_kelas" id="tingkat_kelas"
                                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            required>
                                            <option value="">-- Pilih --</option>
                                            <option value="4">Kelas 4</option>
                                            <option value="5">Kelas 5</option>
                                            <option value="6">Kelas 6</option>
                                        </select>

                                        <button type="button" onclick="unduhTemplate()" title="Unduh Template Excel"
                                            class="flex items-center justify-center bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 px-4 rounded-lg font-bold border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition whitespace-nowrap">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="block text-xs font-bold text-slate-500 uppercase mb-2">Semester</label>
                                    <select name="semester"
                                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        required>
                                        <option value="">-- Pilih --</option>
                                        <option value="1">1 (Ganjil)</option>
                                        <option value="2">2 (Genap)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <div
                                class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                                <h3 class="text-base font-black text-slate-800 dark:text-white flex items-center">
                                    <span
                                        class="flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-400 text-xs mr-2">2</span>
                                    Unggah Dokumen Nilai
                                </h3>
                            </div>

                            <div class="p-6">
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-3">Pilih File Excel
                                    (.xlsx / .csv)</label>
                                <div class="relative">
                                    <input type="file" name="file_excel" required accept=".csv, .xls, .xlsx"
                                        class="block w-full text-sm text-slate-500 dark:text-slate-400
                                        file:mr-4 file:py-3 file:px-6
                                        file:rounded-xl file:border-0
                                        file:text-sm file:font-black file:uppercase file:tracking-wide
                                        file:bg-indigo-50 file:text-indigo-700
                                        dark:file:bg-indigo-900/50 dark:file:text-indigo-400
                                        hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900 transition cursor-pointer border border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-2 bg-slate-50 dark:bg-slate-900/50">
                                </div>
                            </div>

                            <div class="p-6 pt-0">
                                <button type="submit"
                                    class="w-full flex justify-center items-center gap-2 bg-indigo-600 text-white py-3.5 rounded-xl font-black shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 transition transform hover:-translate-y-0.5 uppercase tracking-wide text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    Mulai Import Nilai
                                </button>
                            </div>
                        </div>

                    </div>

                    <div class="lg:col-span-1">
                        <div
                            class="bg-indigo-50 dark:bg-slate-800 rounded-2xl shadow-sm border border-indigo-100 dark:border-slate-700 p-6 sticky top-6">
                            <h3 class="text-lg font-black text-indigo-900 dark:text-white mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Panduan Import
                            </h3>

                            <ul class="space-y-4 text-sm text-slate-700 dark:text-slate-300 relative">
                                <li class="flex gap-3">
                                    <span
                                        class="flex-shrink-0 w-6 h-6 rounded-full bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs shadow-sm border border-indigo-100 dark:border-slate-600">1</span>
                                    <p>Pilih <b>Tingkat Kelas</b> lalu klik tombol icon hijau (<svg
                                            class="w-3 h-3 inline text-emerald-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-width="3"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>) untuk mengunduh template Excel.</p>
                                </li>
                                <li class="flex gap-3">
                                    <span
                                        class="flex-shrink-0 w-6 h-6 rounded-full bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs shadow-sm border border-indigo-100 dark:border-slate-600">2</span>
                                    <p>Buka file Excel tersebut dan isi nilai pada kolom mata pelajaran yang tersedia.
                                        <b>Jangan mengubah format kolom NISN.</b></p>
                                </li>
                                <li class="flex gap-3">
                                    <span
                                        class="flex-shrink-0 w-6 h-6 rounded-full bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs shadow-sm border border-indigo-100 dark:border-slate-600">3</span>
                                    <p>Kembali ke halaman ini, pilih <b>Semester</b>, pilih file Excel yang sudah
                                        disimpan, lalu klik <b>Mulai Import Nilai</b>.</p>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        function unduhTemplate() {
            // Ambil nilai dari dropdown tingkat menggunakan ID baru
            let tingkat = document.getElementById('tingkat_kelas').value;

            // Validasi di sisi browser agar lebih cepat
            if (!tingkat) {
                alert('Peringatan: Silakan pilih Tingkat Kelas terlebih dahulu sebelum mengunduh template!');
                return;
            }

            // Arahkan ke URL route download template sambil mengirim parameter tingkat_kelas
            let url = "{{ route('grades.template') }}?tingkat_kelas=" + tingkat;
            window.location.href = url;
        }
    </script>
</x-app-layout>