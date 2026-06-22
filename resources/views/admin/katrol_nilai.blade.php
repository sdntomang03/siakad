<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Alat Katrol Nilai Massal (Skala Linear)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                <div class="p-6 bg-indigo-50 border-b border-indigo-100">
                    <h3 class="font-bold text-indigo-800">Panduan Format Excel (.xlsx / .csv)</h3>
                    <p class="text-sm text-indigo-700 mt-2">Pastikan struktur file Excel Anda persis seperti ini. Sistem
                        akan otomatis mendeteksi berapapun jumlah mata pelajaran setelah kolom Nama.</p>
                    <div class="mt-3 overflow-x-auto">
                        <table class="w-full text-sm text-left bg-white border border-indigo-200">
                            <thead class="bg-indigo-100 font-bold">
                                <tr>
                                    <th class="px-4 py-2 border-r border-indigo-200">NIS (Kolom A)</th>
                                    <th class="px-4 py-2 border-r border-indigo-200">Nama Siswa (Kolom B)</th>
                                    <th class="px-4 py-2 border-r border-indigo-200">MTK (Kolom C)</th>
                                    <th class="px-4 py-2 border-r border-indigo-200">IPA (Kolom D)</th>
                                    <th class="px-4 py-2">IPS (Kolom E...)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-4 py-2 border-r border-indigo-200">1001</td>
                                    <td class="px-4 py-2 border-r border-indigo-200">Andi</td>
                                    <td class="px-4 py-2 border-r border-indigo-200">45</td>
                                    <td class="px-4 py-2 border-r border-indigo-200">60</td>
                                    <td class="px-4 py-2">55</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <form action="{{ route('katrol.process') }}" method="POST" enctype="multipart/form-data"
                    class="p-6 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nilai KKM Target (Batas
                                Bawah)</label>
                            <input type="number" name="kkm" value="75" required
                                class="w-full rounded-xl border-slate-300 focus:ring-indigo-500">
                            <span class="text-xs text-slate-500">Siswa dengan nilai terendah di kelas akan otomatis
                                menjadi nilai ini.</span>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nilai Maksimal (Batas
                                Atas)</label>
                            <input type="number" name="target_max" value="100" required
                                class="w-full rounded-xl border-slate-300 focus:ring-indigo-500">
                            <span class="text-xs text-slate-500">Siswa dengan nilai tertinggi di kelas akan otomatis
                                menjadi nilai ini.</span>
                        </div>
                    </div>

                    <div
                        class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center bg-slate-50 hover:bg-slate-100 transition">
                        <label class="block text-sm font-bold text-slate-700 mb-3">Upload File Nilai Mentah</label>
                        <input type="file" name="file_excel" accept=".xlsx, .xls, .csv" required
                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer mx-auto">
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg transition">
                        🚀 Proses & Download Hasil Katrol
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
