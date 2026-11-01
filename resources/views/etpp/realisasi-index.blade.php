<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Realisasi Triwulan e-TPP</h2>
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

    <div class="py-6 sm:py-8" x-data="{
        rows: @js($rows),
        outputTargets: @js($outputTargets->map(fn ($output) => ['id' => $output->id, 'name' => $output->deskripsi_output, 'triwulan' => $output->target_waktu])->values()),
        addRow() {
            const usedIds = this.rows.map(row => String(row.output_target_id));
            const output = this.outputTargets.find(item => !usedIds.includes(String(item.id)));
            if (output) this.rows.push({ output_target_id: output.id, triwulan: output.triwulan, realisasi: '', link_referensi: '' });
        },
        removeRow(index) { this.rows.splice(index, 1) }
    }">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('etpp.realisasi.index') }}"
                class="flex flex-col items-stretch gap-4 rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:flex-row sm:items-end sm:p-6">
                <div class="w-full sm:w-48">
                    <label for="tw" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Triwulan</label>
                    <select id="tw" name="tw" onchange="this.form.submit()"
                        class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                        <option value="semua" {{ $filter_tw=='semua' ? 'selected' : '' }}>Semua Triwulan</option>
                        <option value="TW 1" {{ $filter_tw=='TW 1' ? 'selected' : '' }}>Triwulan I (TW 1)</option>
                        <option value="TW 2" {{ $filter_tw=='TW 2' ? 'selected' : '' }}>Triwulan II (TW 2)</option>
                        <option value="TW 3" {{ $filter_tw=='TW 3' ? 'selected' : '' }}>Triwulan III (TW 3)</option>
                        <option value="TW 4" {{ $filter_tw=='TW 4' ? 'selected' : '' }}>Triwulan IV (TW 4)</option>
                    </select>
                </div>
                <div class="w-full sm:w-40">
                    <label for="tahun" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Tahun</label>
                    <input id="tahun" name="tahun" type="number" min="2000" max="2100" value="{{ $tahun }}"
                        class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                </div>
                <button
                    class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">Tampilkan
                    Output</button>
                @if($triwulan)
                <a href="{{ route('etpp.realisasi.pdf', ['triwulan' => $triwulan, 'tahun' => $tahun]) }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700">Unduh
                    PDF</a>
                    @endif
            </form>

            <form action="{{ route('etpp.realisasi.batch') }}" method="POST"
                class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                @csrf
                @if($triwulan)
                <input type="hidden" name="triwulan" value="{{ $triwulan }}">
                @endif
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-bold text-lg">Output Target {{ $triwulan }}</h3>
                        <p class="text-sm text-slate-500">Setiap baris selalu terhubung ke output target e-Kinerja pada
                            triwulan ini.</p>
                    </div>
                    <button type="button" @click="addRow()" :disabled="rows.length >= outputTargets.length"
                        class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 disabled:cursor-not-allowed disabled:opacity-50">Tambah Output</button>
                </div>
                <p x-show="rows.length === 0" class="rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900 dark:bg-amber-950/30 dark:text-amber-200">Belum ada output target e-Kinerja untuk periode ini.</p>
                <template x-for="(row, index) in rows" :key="row.id ? 'saved-'+row.id : 'new-'+index">
                    <section
                        class="mb-4 p-4 rounded-md border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <span class="text-xs font-bold px-2 py-1 rounded bg-brand-100 text-brand-700">Output
                                Target</span>
                            <button type="button" @click="removeRow(index)"
                                class="inline-flex min-h-10 items-center rounded-md px-3 text-sm font-semibold text-rose-700 hover:bg-rose-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-700 dark:text-rose-300 dark:hover:bg-rose-950/40">Hapus Output</button>
                        </div>
                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">Output</label>
                        <p class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                            x-text="outputTargets.find(output => String(output.id) === String(row.output_target_id))?.name ?? 'Output tidak ditemukan'"></p>
                        <input type="hidden" :name="'items[' + index + '][output_target_id]'" x-model="row.output_target_id">
                        <input type="hidden" :name="'items[' + index + '][triwulan]'" x-model="row.triwulan">
                        <label :for="'realisasi-link-' + index" class="mb-1 mt-3 block text-sm font-medium text-slate-700 dark:text-slate-200">Link Realisasi</label>
                        <input type="url" :name="'items[' + index + '][link_referensi]'" x-model="row.link_referensi"
                            :id="'realisasi-link-' + index"
                            required maxlength="2048" placeholder="https://..."
                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    </section>
                </template>
                <button type="submit"
                    class="inline-flex min-h-11 w-full items-center justify-center rounded-md bg-brand-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">Simpan Semua
                    Realisasi</button>
            </form>
        </div>
    </div>
</x-app-layout>