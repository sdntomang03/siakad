<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">Dialog Kinerja Bulanan</h2>
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
    <div class="py-8" x-data="{
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" action="{{ route('etpp.dialog-kinerja.index') }}" class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 flex flex-col sm:flex-row gap-4 items-end">
                <div class="w-full sm:w-48">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Bulan</label>
                    <select name="bulan" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                        @foreach(range(1, 12) as $number)
                        <option value="{{ $number }}" {{ $bulan === $number ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromDate($tahun, $number, 1)->locale('id')->isoFormat('MMMM') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-40"><label class="block text-xs font-bold text-slate-500 uppercase mb-2">Tahun</label><input name="tahun" type="number" min="2000" max="2100" value="{{ $tahun }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white"></div>
                <button class="px-5 py-2.5 bg-slate-700 text-white rounded-lg font-bold text-sm">Pilih Periode</button>
                <a href="{{ route('etpp.dialog-kinerja.pdf', ['tahun' => $tahun, 'bulan' => $bulan]) }}" class="px-5 py-2.5 text-indigo-700 bg-indigo-50 rounded-lg font-bold text-sm">Unduh PDF</a>
            </form>
            <form action="{{ route('etpp.dialog-kinerja.batch') }}" method="POST" class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                @csrf <input type="hidden" name="tahun" value="{{ $tahun }}"><input type="hidden" name="bulan" value="{{ $bulan }}">
                <div class="mb-5 flex flex-col gap-3 rounded-lg bg-indigo-50 p-3 text-sm text-indigo-800 sm:flex-row sm:items-center sm:justify-between">
                    <span><strong>{{ $triwulan }}</strong> — sinkronkan link dari realisasi berdasarkan output yang sama.</span>
                    <button type="button" @click="syncLinks()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 font-bold text-white hover:bg-indigo-700">
                        <span aria-hidden="true">↻</span> Sinkronkan Link
                    </button>
                </div>
                <p x-show="syncedCount > 0" x-text="syncedCount + ' link siap disimpan ke dialog kinerja.'" class="mb-4 text-sm font-semibold text-emerald-600"></p>
                <div class="flex justify-between items-center gap-4 mt-6 mb-5"><div><h3 class="font-bold text-lg">Output Target {{ $triwulan }}</h3><p class="text-sm text-slate-500">Tambahkan atau hapus baris dari output target yang tersedia.</p></div><button type="button" @click="addRow()" :disabled="rows.length >= outputTargets.length" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold text-sm disabled:cursor-not-allowed disabled:opacity-50">+ Tambah Output</button></div>
                <template x-for="(row, index) in rows" :key="index">
                    <section class="mb-4 rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900/40">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <span class="rounded bg-indigo-100 px-2 py-1 text-xs font-bold text-indigo-700" x-text="'Output ' + (index + 1)"></span>
                            <button type="button" @click="removeRow(index)" class="text-xs font-bold text-red-600">Hapus Output</button>
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
                <button type="submit" class="w-full py-3 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700">Simpan Dialog Kinerja</button>
            </form>
        </div>
    </div>
</x-app-layout>
