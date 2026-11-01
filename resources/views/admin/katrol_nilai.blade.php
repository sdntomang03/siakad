<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Alat Katrol Nilai Massal (Skala Linear)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6 overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                <div class="border-b border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-900/50 sm:p-6">
                    <h3 class="font-semibold text-slate-950 dark:text-white">Panduan Format Excel (.xlsx / .csv)</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Gunakan urutan kolom berikut. Sistem
                        akan mendeteksi mata pelajaran setelah kolom Nama.</p>
                    <div class="mt-3 overflow-x-auto">
                        <table class="w-full border border-slate-200 bg-white text-left text-sm dark:border-slate-700 dark:bg-slate-800">
                            <thead class="bg-slate-100 font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-200">
                                <tr>
                                    <th class="border-r border-slate-200 px-4 py-2 dark:border-slate-700">NIS (Kolom A)</th>
                                    <th class="border-r border-slate-200 px-4 py-2 dark:border-slate-700">Nama Siswa (Kolom B)</th>
                                    <th class="border-r border-slate-200 px-4 py-2 dark:border-slate-700">Mata Pelajaran (Kolom C dan seterusnya)</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <form action="{{ route('katrol.process') }}" method="POST" enctype="multipart/form-data"
                    class="p-6 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="kkm" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Nilai KKM Target (Batas
                                Bawah)</label>
                            <input id="kkm" type="number" name="kkm" value="75" required
                                class="w-full rounded-md border-slate-300 focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                            <span class="mt-1 block text-sm text-slate-500 dark:text-slate-400">Siswa dengan nilai terendah di kelas akan otomatis
                                menjadi nilai ini.</span>
                        </div>

                        <div>
                            <label for="target_max" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Nilai Maksimal (Batas
                                Atas)</label>
                            <input id="target_max" type="number" name="target_max" value="100" required
                                class="w-full rounded-md border-slate-300 focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                            <span class="mt-1 block text-sm text-slate-500 dark:text-slate-400">Siswa dengan nilai tertinggi di kelas akan otomatis
                                menjadi nilai ini.</span>
                        </div>
                    </div>

                    <div
                        class="rounded-md border border-dashed border-slate-300 bg-slate-50 p-5 transition hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-900/50 dark:hover:bg-slate-900 sm:p-8">
                        <label for="file_excel" class="mb-3 block text-sm font-medium text-slate-700 dark:text-slate-200">Upload File Nilai Mentah</label>
                        <input id="file_excel" type="file" name="file_excel" accept=".xlsx, .xls, .csv" required
                            class="block min-h-11 w-full text-sm text-slate-600 file:mr-4 file:min-h-11 file:border-0 file:bg-brand-50 file:px-4 file:font-semibold file:text-brand-800 hover:file:bg-brand-100 dark:text-slate-300">
                    </div>

                    <button type="submit"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-md bg-brand-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                        Proses & Download Hasil Katrol
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
