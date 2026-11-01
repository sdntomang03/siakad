<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">
                Import Rekap Nilai <span class="text-brand-600">Sidanira</span>
            </h2>

            <a href="{{ route('dashboard') }}"
                class="inline-flex min-h-11 items-center text-sm font-semibold text-brand-700 hover:text-brand-900 focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:text-brand-300 dark:hover:text-brand-200">
                Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div
                class="mb-6 flex items-center p-4 rounded-md bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 text-sm font-bold shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div
                class="mb-6 flex items-center p-4 rounded-md bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-400 text-sm font-bold shadow-sm">
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
                            class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                            <div
                                class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                                <h3 class="text-base font-semibold text-slate-950 dark:text-white flex items-center">
                                    <span
                                        class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-100 text-brand-700 dark:bg-brand-900/50 dark:text-brand-400 text-xs mr-2">1</span>
                                    Pengaturan & Unduh Template
                                </h3>
                            </div>

                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Tingkat
                                        Kelas</label>
                                    <div class="flex gap-2">
                                        <select name="tingkat_kelas" id="tingkat_kelas"
                                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                                            required>
                                            <option value="">-- Pilih --</option>
                                            <option value="4">Kelas 4</option>
                                            <option value="5">Kelas 5</option>
                                            <option value="6">Kelas 6</option>
                                        </select>

                                        <button type="button" onclick="unduhTemplate()" aria-label="Unduh template Excel"
                                            class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
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
                                        class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Semester</label>
                                    <select name="semester"
                                        class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                                        required>
                                        <option value="">-- Pilih --</option>
                                        <option value="1">1 (Ganjil)</option>
                                        <option value="2">2 (Genap)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <div
                                class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                                <h3 class="text-base font-semibold text-slate-950 dark:text-white flex items-center">
                                    <span
                                        class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-100 text-brand-700 dark:bg-brand-900/50 dark:text-brand-400 text-xs mr-2">2</span>
                                    Unggah Dokumen Nilai
                                </h3>
                            </div>

                            <div class="p-6">
                                <label class="mb-3 block text-sm font-medium text-slate-700 dark:text-slate-200">Pilih File Excel
                                    (.xlsx / .csv)</label>
                                <div class="relative">
                                    <input type="file" name="file_excel" required accept=".csv, .xls, .xlsx"
                                        class="block w-full text-sm text-slate-500 dark:text-slate-400
                                        file:mr-4 file:py-3 file:px-6
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-black file:uppercase file:tracking-wide
                                        file:bg-brand-50 file:text-brand-700
                                        dark:file:bg-brand-900/50 dark:file:text-brand-400
                                        hover:file:bg-brand-100 dark:hover:file:bg-brand-900 transition cursor-pointer border border-dashed border-slate-300 dark:border-slate-600 rounded-md p-2 bg-slate-50 dark:bg-slate-900/50">
                                </div>
                            </div>

                            <div class="p-6 pt-0">
                                <button type="submit"
                                    class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-md bg-brand-700 px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
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
                            class="sticky top-6 rounded-lg border border-brand-100 bg-brand-50 p-5 dark:border-slate-700 dark:bg-slate-800 lg:p-6">
                            <h3 class="mb-4 flex items-center text-lg font-semibold text-brand-900 dark:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 mr-2 text-brand-600 dark:text-brand-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Panduan Import
                            </h3>

                            <ul class="space-y-4 text-sm text-slate-700 dark:text-slate-300 relative">
                                <li class="flex gap-3">
                                    <span
                                        class="flex-shrink-0 w-6 h-6 rounded-full bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 flex items-center justify-center font-bold text-xs shadow-sm border border-brand-100 dark:border-slate-600">1</span>
                                    <p>Pilih <b>Tingkat Kelas</b> lalu klik tombol icon hijau (<svg
                                            class="w-3 h-3 inline text-emerald-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-width="3"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>) untuk mengunduh template Excel.</p>
                                </li>
                                <li class="flex gap-3">
                                    <span
                                        class="flex-shrink-0 w-6 h-6 rounded-full bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 flex items-center justify-center font-bold text-xs shadow-sm border border-brand-100 dark:border-slate-600">2</span>
                                    <p>Buka file Excel tersebut dan isi nilai pada kolom mata pelajaran yang tersedia.
                                        <b>Jangan mengubah format kolom NISN.</b></p>
                                </li>
                                <li class="flex gap-3">
                                    <span
                                        class="flex-shrink-0 w-6 h-6 rounded-full bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 flex items-center justify-center font-bold text-xs shadow-sm border border-brand-100 dark:border-slate-600">3</span>
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