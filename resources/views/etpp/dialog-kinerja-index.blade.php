<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Dialog Kinerja Bulanan</h2>
            <p class="mt-1 text-sm text-slate-500">Output diambil dari target e-Kinerja pada triwulan sesuai bulan yang dipilih.</p>
        </div>
    </x-slot>
    @php
        $dialogItemsByOutput = $dialogKinerja?->items->whereNotNull('output_target_id')->keyBy('output_target_id') ?? collect();
        $rows = $outputTargets->map(function ($output) use ($dialogItemsByOutput, $realisasiByOutput) {
            $item = $dialogItemsByOutput->get($output->id);
            return ['output_target_id' => $output->id, 'uraian' => $item?->uraian ?? '', 'link_referensi' => $item?->link_referensi ?? ''];
        })->values()->all();
    @endphp
    <div class="py-6 sm:py-8" x-data="{
        rows: @js($rows),
        outputTargets: @js($outputTargets->map(fn ($output) => ['id' => $output->id, 'name' => $output->deskripsi_output])->values()),
        realisasiByOutput: @js($realisasiByOutput->map(fn ($realisasi) => $realisasi->link_referensi)->all()),
        syncedCount: 0,
        addRow() {
            const usedIds = this.rows.map(row => String(row.output_target_id));
            const availableOutput = this.outputTargets.find(output => !usedIds.includes(String(output.id)));
            if (availableOutput) {
                this.rows.push({ output_target_id: availableOutput.id, uraian: '', link_referensi: '' });
            }
        },
        removeRow(index) { this.rows.splice(index, 1) },
        syncLinks() {
            this.rows.forEach(row => {
                const link = this.realisasiByOutput[row.output_target_id];
                if (link) row.link_referensi = link;
            });
            this.syncedCount = this.rows.filter(row => row.link_referensi).length;
        }
    }">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('etpp.dialog-kinerja.index') }}" class="grid grid-cols-1 items-end gap-4 rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:grid-cols-2 sm:p-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,0.8fr)_minmax(0,1fr)_auto_auto]">
                <div class="w-full sm:w-48">
                    <label for="bulan" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Bulan</label>
                    <select id="bulan" name="bulan" class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                        @foreach(range(1, 12) as $number)
                        <option value="{{ $number }}" {{ $bulan === $number ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromDate($tahun, $number, 1)->locale('id')->isoFormat('MMMM') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-40"><label for="tahun" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Tahun</label><input id="tahun" name="tahun" type="number" min="2000" max="2100" value="{{ $tahun }}" class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white"></div>
                <div class="w-full sm:w-48">
                    <label for="dialog-periode" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Periode</label>
                    <select id="dialog-periode" name="periode" class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                        <option value="1" {{ $periode === 1 ? 'selected' : '' }}>Periode 1 (Tanggal 1–15)</option>
                        <option value="2" {{ $periode === 2 ? 'selected' : '' }}>Periode 2 (Tanggal 16–akhir bulan)</option>
                    </select>
                </div>
                <button class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">Pilih Periode</button>
                <a id="dialog-pdf-link" href="{{ route('etpp.dialog-kinerja.pdf', ['tahun' => $tahun, 'bulan' => $bulan, 'periode' => $periode]) }}" data-url-template="{{ route('etpp.dialog-kinerja.pdf', ['tahun' => $tahun, 'bulan' => $bulan, 'periode' => '__PERIODE__']) }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700">Unduh PDF</a>
            </form>
            <script>
                document.getElementById('dialog-periode').addEventListener('change', function () {
                    const pdfLink = document.getElementById('dialog-pdf-link');
                    pdfLink.href = pdfLink.dataset.urlTemplate.replace('__PERIODE__', this.value);
                });
            </script>
            <form action="{{ route('etpp.dialog-kinerja.batch') }}" method="POST" class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                @csrf <input type="hidden" name="tahun" value="{{ $tahun }}"><input type="hidden" name="bulan" value="{{ $bulan }}">
                <div class="mb-5 flex flex-col gap-3 rounded-lg bg-brand-50 p-3 text-sm text-brand-800 sm:flex-row sm:items-center sm:justify-between">
                    <span><strong>{{ $triwulan }}</strong> — sinkronkan link dari realisasi berdasarkan output yang sama.</span>
                    <button type="button" @click="syncLinks()" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                        Sinkronkan Link
                    </button>
                </div>
                <p x-show="syncedCount > 0" x-text="syncedCount + ' link siap disimpan ke dialog kinerja.'" class="mb-4 text-sm font-medium text-emerald-700 dark:text-emerald-300" role="status"></p>
                <div class="mb-5 mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><div><h3 class="text-lg font-semibold text-slate-950 dark:text-white">Output Target {{ $triwulan }}</h3><p class="text-sm text-slate-500 dark:text-slate-400">Tambahkan atau hapus baris dari output target yang tersedia.</p></div><button type="button" @click="addRow()" :disabled="rows.length >= outputTargets.length" class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 disabled:cursor-not-allowed disabled:opacity-50">Tambah Output</button></div>
                <p x-show="rows.length === 0" class="mb-4 rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900 dark:bg-amber-950/30 dark:text-amber-200">Belum ada output target e-Kinerja untuk periode ini.</p>
                <template x-for="(row, index) in rows" :key="index">
                    <section class="mb-4 rounded-md border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900/40">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <span class="rounded bg-brand-100 px-2 py-1 text-xs font-bold text-brand-700" x-text="'Output ' + (index + 1)"></span>
                            <button type="button" @click="removeRow(index)" class="inline-flex min-h-10 items-center rounded-md px-3 text-sm font-semibold text-rose-700 hover:bg-rose-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-700 dark:text-rose-300 dark:hover:bg-rose-950/40">Hapus Output</button>
                        </div>
                        <label class="mb-1 block text-xs font-bold uppercase text-slate-500">Output Target</label>
                        <p class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                            x-text="outputTargets.find(output => String(output.id) === String(row.output_target_id))?.name ?? 'Output tidak ditemukan'"></p>
                        <input type="hidden" :name="'items['+index+'][output_target_id]'" x-model="row.output_target_id">
                        <label class="mb-1 mt-3 block text-xs font-bold uppercase text-slate-500">Link dari Realisasi</label>
                        <input type="url" :name="'items['+index+'][link_referensi]'" x-model="row.link_referensi" readonly
                            placeholder="Link belum diisi pada realisasi"
                            class="w-full rounded-lg border-slate-300 bg-white text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <p x-show="!row.link_referensi" class="mt-1 text-xs text-amber-600">Isi link pada halaman Realisasi Triwulan terlebih dahulu.</p>
                    </section>
                </template>
                <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-md bg-brand-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">Simpan Dialog Kinerja</button>
            </form>
        </div>
    </div>
</x-app-layout>
