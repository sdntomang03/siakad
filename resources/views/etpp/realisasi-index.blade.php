<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">Realisasi Triwulan e-TPP</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola tautan realisasi berdasarkan output target e-Kinerja pada
                triwulan terpilih.</p>
        </div>
    </x-slot>

    @php
    $realisasiByOutput = $realisasiList->whereNotNull('output_target_id')->keyBy('output_target_id');
    $rows = $outputTargets->map(function ($output) use ($realisasiByOutput) {
    $realisasi = $realisasiByOutput->get($output->id);
    return ['output_target_id' => $output->id, 'triwulan' => $output->target_waktu, 'realisasi' =>
    $realisasi?->realisasi ?? '', 'link_referensi' => $realisasi?->link_referensi ?? ''];
    })->values()->all();
    @endphp

    <div class="py-8" x-data="{
        rows: @js($rows),
        outputTargets: @js($outputTargets->map(fn ($output) => ['id' => $output->id, 'name' => $output->deskripsi_output, 'triwulan' => $output->target_waktu])->values()),
        addRow() {
            const usedIds = this.rows.map(row => String(row.output_target_id));
            const output = this.outputTargets.find(item => !usedIds.includes(String(item.id)));
            if (output) this.rows.push({ output_target_id: output.id, triwulan: output.triwulan, realisasi: '', link_referensi: '' });
        },
        removeRow(index) { this.rows.splice(index, 1) }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" action="{{ route('etpp.realisasi.index') }}"
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 flex flex-col sm:flex-row gap-4 items-end">
                <div class="w-full sm:w-48">
                    <label for="tw" class="block text-xs font-bold text-slate-500 uppercase mb-2">Triwulan</label>
                    <select name="tw" onchange="this.form.submit()" class="form-select rounded-md border-gray-300">
                        <option value="semua" {{ $filter_tw=='semua' ? 'selected' : '' }}>Semua Triwulan</option>
                        <option value="TW 1" {{ $filter_tw=='TW 1' ? 'selected' : '' }}>Triwulan I (TW 1)</option>
                        <option value="TW 2" {{ $filter_tw=='TW 2' ? 'selected' : '' }}>Triwulan II (TW 2)</option>
                        <option value="TW 3" {{ $filter_tw=='TW 3' ? 'selected' : '' }}>Triwulan III (TW 3)</option>
                        <option value="TW 4" {{ $filter_tw=='TW 4' ? 'selected' : '' }}>Triwulan IV (TW 4)</option>
                    </select>
                </div>
                <div class="w-full sm:w-40">
                    <label for="tahun" class="block text-xs font-bold text-slate-500 uppercase mb-2">Tahun</label>
                    <input id="tahun" name="tahun" type="number" min="2000" max="2100" value="{{ $tahun }}"
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                </div>
                <button
                    class="px-5 py-2.5 bg-slate-700 text-white rounded-lg font-bold text-sm hover:bg-slate-800">Tampilkan
                    Output</button>
                @if($triwulan)
                <a href="{{ route('etpp.realisasi.pdf', ['triwulan' => $triwulan, 'tahun' => $tahun]) }}"
                    class="px-5 py-2.5 text-indigo-700 bg-indigo-50 rounded-lg font-bold text-sm hover:bg-indigo-100">Unduh
                    PDF</a>
                    @endif
            </form>

            <form action="{{ route('etpp.realisasi.batch') }}" method="POST"
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                @csrf
                @if($triwulan)
                <input type="hidden" name="triwulan" value="{{ $triwulan }}">
                @endif
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <div class="flex justify-between items-center gap-4 mb-5">
                    <div>
                        <h3 class="font-bold text-lg">Output Target {{ $triwulan }}</h3>
                        <p class="text-sm text-slate-500">Setiap baris selalu terhubung ke output target e-Kinerja pada
                            triwulan ini.</p>
                    </div>
                    <button type="button" @click="addRow()" :disabled="rows.length >= outputTargets.length"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold text-sm disabled:cursor-not-allowed disabled:opacity-50">+ Tambah Output</button>
                </div>
                <p x-show="rows.length === 0" class="p-4 rounded-lg bg-amber-50 text-amber-700 text-sm">Belum ada
                    output. Gunakan tombol Tambah Output.</p>
                <template x-for="(row, index) in rows" :key="row.id ? 'saved-'+row.id : 'new-'+index">
                    <section
                        class="mb-4 p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
                        <div class="flex justify-between gap-3 mb-3">
                            <span class="text-xs font-bold px-2 py-1 rounded bg-indigo-100 text-indigo-700">Output
                                Target</span>
                            <button type="button" @click="removeRow(index)"
                                class="text-xs font-bold text-red-600 hover:text-red-800">Hapus Output</button>
                        </div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Output</label>
                        <p class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                            x-text="outputTargets.find(output => String(output.id) === String(row.output_target_id))?.name ?? 'Output tidak ditemukan'"></p>
                        <input type="hidden" :name="'items[' + index + '][output_target_id]'" x-model="row.output_target_id">
                        <input type="hidden" :name="'items[' + index + '][triwulan]'" x-model="row.triwulan">
                        <label class="block text-xs font-bold text-slate-500 uppercase mt-3 mb-1">Link Realisasi</label>
                        <input type="url" :name="'items[' + index + '][link_referensi]'" x-model="row.link_referensi"
                            required maxlength="2048" placeholder="https://..."
                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm">
                    </section>
                </template>
                <button type="submit"
                    class="w-full py-3 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700">Simpan Semua
                    Realisasi</button>
            </form>
        </div>
    </div>
</x-app-layout>