<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Lokasi
                    Fisik / Detail Inventaris</span>
                <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight mt-0.5">
                    {{ $namaRuangan }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 italic mt-0.5">{{ $deskripsi }}</p>
            </div>

            <a href="{{ route('rooms.index') }}"
                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl transition flex items-center gap-1.5 self-start">
                ← Kembali ke Daftar Ruangan
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ showModal: false, placement: {}, actionUrl: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div
                class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold shadow-sm">
                {{ session('success') }}
            </div>
            @endif

            {{-- TABEL DATA BARANG DI DALAM RUANGAN INI --}}
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                    <h3 class="text-base font-bold text-slate-700 dark:text-slate-300">Daftar Fasilitas & Aset Terdata
                    </h3>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-sm text-left text-slate-600 dark:text-slate-400">
                        <thead
                            class="text-xs text-slate-500 uppercase bg-slate-100 dark:bg-slate-700/60 dark:text-slate-300">
                            <tr>
                                <th class="px-6 py-4 w-12 text-center">No</th>
                                <th class="px-6 py-4 w-48">Kode Inventaris</th>
                                <th class="px-6 py-4">Nama Item Barang</th>
                                <th class="px-6 py-4 text-center w-24">Jumlah</th>
                                <th class="px-6 py-4 w-36">Kondisi Fisik</th>
                                <th class="px-6 py-4">Keterangan Letak</th>
                                <th class="px-6 py-4 text-center w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($placements as $index => $p)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4 text-center text-slate-400 font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-mono font-bold text-xs text-indigo-600 dark:text-indigo-400">
                                    {{ $p->asset->kode_aset ?? 'MENUNGGU_ACC' }}
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">
                                    {{ $p->asset->nama_aset }}
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-slate-800 dark:text-slate-200">
                                    {{ $p->jumlah }} Unit
                                </td>
                                <td class="px-6 py-4">
                                    @if($p->kondisi === 'Baik')
                                    <span
                                        class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">BAIK</span>
                                    @elseif($p->kondisi === 'Rusak Ringan')
                                    <span
                                        class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">RUSAK
                                        RINGAN</span>
                                    @else
                                    <span
                                        class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">RUSAK
                                        BERAT</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs italic text-slate-500 max-w-xs truncate">{{ $p->keterangan
                                    ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" @click="
                                            placement = {{ json_encode($p) }};
                                            actionUrl = '{{ route('assets.placement.update-condition', $p->id) }}';
                                            showModal = true;
                                        "
                                            class="px-2.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg text-xs transition">
                                            Ubah Kondisi
                                        </button>

                                        <form action="{{ route('assets.placement.destroy', $p->id) }}" method="POST"
                                            onsubmit="return confirm('Keluarkan barang ini dari catatan inventaris ruangan?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="px-2.5 py-1.5 bg-rose-100 hover:bg-rose-600 text-rose-700 hover:text-white font-bold rounded-lg text-xs transition">
                                                Tarik
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7"
                                    class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 italic">
                                    Kosong. Belum ada fasilitas barang yang dicatatkan di dalam ruangan ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- MODAL UBAH KONDISI/KUANTITAS BARANG INLINE --}}
        <div x-show="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            style="display: none;" x-cloak>
            <div @click.outside="showModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden">
                <div class="p-5 border-b bg-slate-50 dark:bg-slate-900/50 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 dark:text-slate-200 text-base">Perbarui Kondisi Barang</h3>
                    <button @click="showModal = false" class="text-slate-400 text-sm">✕</button>
                </div>

                <form :action="actionUrl" method="POST" class="p-6 space-y-4">
                    @csrf @method('PUT')

                    <div class="grid grid-cols-3 gap-2">
                        <div class="col-span-1">
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jumlah</label>
                            <input type="number" name="jumlah" x-model="placement.jumlah" required min="1"
                                class="w-full rounded-xl border-slate-300 text-sm text-center font-bold">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kondisi Fisik</label>
                            <select name="kondisi" x-model="placement.kondisi"
                                class="w-full rounded-xl border-slate-300 text-sm">
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Keterangan Spesifik
                            Letak</label>
                        <input type="text" name="keterangan" x-model="placement.keterangan"
                            class="w-full rounded-xl border-slate-300 text-sm">
                    </div>

                    <div class="pt-4 border-t flex items-center justify-end gap-2">
                        <button type="button" @click="showModal = false"
                            class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl shadow">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
