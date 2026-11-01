<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100 leading-tight">
            Pencatatan & Pengajuan Aset Kelas / Ruangan
        </h2>
    </x-slot>

    <div class="py-6 sm:py-8" x-data="{ jenisInput: 'database', lokasi: 'kelas' }">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div
                role="status"
                class="rounded-md border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-200">
                {{ session('success') }}
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                {{-- FORMLIR UTAMA INPUT BARANG --}}
                <div
                    class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 lg:col-span-5 sm:p-6">
                    <div class="mb-4">
                        <h3 class="font-bold text-base text-slate-800 dark:text-slate-200">Form Input Inventaris</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Laporkan keberadaan fasilitas barang ke dalam sistem
                            ruang sekolah.</p>
                    </div>

                    <form action="{{ route('assets.placement.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Tipe
                                Penempatan</label>
                            <div class="grid grid-cols-2 gap-1 rounded-md bg-slate-100 p-1 dark:bg-slate-900">
                                <button type="button" @click="lokasi = 'kelas'" :aria-pressed="(lokasi === 'kelas').toString()"
                                    :class="lokasi === 'kelas' ? 'bg-white font-semibold text-slate-950 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-600 dark:text-slate-300'"
                                    class="min-h-11 rounded-sm px-2 py-2 text-sm transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-inset focus-visible:outline-brand-700">Di Dalam Kelas</button>
                                <button type="button" @click="lokasi = 'ruangan'" :aria-pressed="(lokasi === 'ruangan').toString()"
                                    :class="lokasi === 'ruangan' ? 'bg-white font-semibold text-slate-950 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-600 dark:text-slate-300'"
                                    class="min-h-11 rounded-sm px-2 py-2 text-sm transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-inset focus-visible:outline-brand-700">Ruangan Lain</button>
                            </div>
                        </div>

                        <div x-show="lokasi === 'kelas'">
                            <label
                                class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">Pilih
                                Kelas</label>
                            <select name="classroom_id"
                                class="w-full rounded-md border-slate-300 bg-white text-sm text-slate-700 focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                                <option value="">-- Pilih Kelas Rombel --</option>
                                @foreach($classrooms as $c)
                                <option value="{{ $c->id }}">Kelas {{ $c->tingkat }} {{ $c->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div x-show="lokasi === 'ruangan'" style="display: none;">
                            <label
                                class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">Pilih
                                Ruangan</label>
                            <select name="room_id"
                                class="w-full rounded-md border-slate-300 bg-white text-sm text-slate-700 focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                                <option value="">-- Pilih Ruangan Kerja --</option>
                                @foreach($rooms as $r)
                                <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">Asal/Sumber
                                Data Barang</label>
                            <select name="jenis_input" x-model="jenisInput"
                                class="w-full rounded-md border-slate-300 bg-white text-sm text-slate-700 focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                                <option value="database">Pilih dari Database Aset Sekolah</option>
                                <option value="baru">+ Barang Belum Ada (Ajukan Baru)</option>
                            </select>
                        </div>

                        <div x-show="jenisInput === 'database'">
                            <label
                                class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">Pilih
                                Barang</label>
                            <select name="asset_id"
                                class="w-full rounded-md border-slate-300 bg-white text-sm text-slate-700 focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                                <option value="">-- Pilih Item --</option>
                                @foreach($masterAssets as $ma)
                                <option value="{{ $ma->id }}">{{ $ma->nama_aset }} {{ $ma->kode_aset ?
                                    '['.$ma->kode_aset.']' : '' }}</option>
                                @endforeach
                            </select>
                            {{-- Pesan error jika lupa memilih barang --}}
                            @error('asset_id')
                            <p class="text-xs text-rose-500 font-bold mt-1">⚠️ Anda harus memilih salah satu barang!</p>
                            @enderror
                        </div>

                        <div x-show="jenisInput === 'baru'" style="display: none;">
                            <label
                                class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">Nama
                                Barang Baru yang Diajukan</label>
                            <input type="text" name="nama_aset_baru" placeholder="e.g. Dispenser MIYAKO WD-186"
                                class="w-full rounded-md border-slate-300 bg-white text-slate-700 dark:bg-slate-800 dark:text-slate-100 text-sm focus:ring-brand-500" />
                            <p class="text-[10px] text-amber-600 mt-1">⚠️ Barang ini akan masuk antrean approval Admin
                                terlebih dahulu sebelum kodenya aktif.</p>
                            {{-- Pesan error jika nama aset kosong --}}
                            @error('nama_aset_baru')
                            <p class="text-xs text-rose-500 font-bold mt-1">⚠️ Nama aset baru tidak boleh kosong!</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-1">
                                <label
                                    class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">Jumlah</label>
                                <input type="number" name="jumlah" value="1" min="1"
                                    class="w-full rounded-md border-slate-300 bg-white text-slate-700 dark:bg-slate-800 dark:text-slate-100 text-sm text-center focus:ring-brand-500">
                            </div>
                            <div class="col-span-2">
                                <label
                                    class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">Kondisi
                                    Barang</label>
                                <select name="kondisi"
                                    class="w-full rounded-md border-slate-300 bg-white text-slate-700 dark:bg-slate-800 dark:text-slate-100 text-sm focus:ring-brand-500">
                                    <option value="Baik">Baik</option>
                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Keterangan
                                Spesifik Posisi (Opsional)</label>
                            <input type="text" name="keterangan" placeholder="e.g. Menempel di dinding sebelah pintu"
                                class="w-full rounded-md border-slate-300 bg-white text-slate-700 dark:bg-slate-800 dark:text-slate-100 text-sm focus:ring-brand-500">
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="inline-flex min-h-11 w-full items-center justify-center rounded-md bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                                Simpan Data Fasilitas
                            </button>
                        </div>
                    </form>
                </div>

                {{-- TABEL RIWAYAT INPUT OLEH USER TERKAIT --}}
                <div
                    class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800 lg:col-span-7">
                    <div class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                        <h3 class="font-bold text-base text-slate-700 dark:text-slate-300">Log Pengisian Saya</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar item sarana prasarana sekolah terakhir yang Anda
                            daftarkan.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-[42rem] w-full text-left text-sm text-slate-600 dark:text-slate-300">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-600 dark:bg-slate-900/60 dark:text-slate-300">
                                <tr>
                                    <th class="px-4 py-3.5">Nama Barang</th>
                                    <th class="px-4 py-3.5">Lokasi</th>
                                    <th class="px-4 py-3.5 text-center">Qty</th>
                                    <th class="px-4 py-3.5">Kondisi</th>
                                    <th class="px-4 py-3.5">Status DB</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-xs">
                                @forelse($myPlacements as $mp)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/60">
                                    <td class="px-4 py-3.5">
                                        <div class="font-bold text-slate-800 dark:text-slate-200">{{
                                            $mp->asset->nama_aset }}</div>
                                        <div class="font-mono text-[10px] text-slate-400 mt-0.5">{{
                                            $mp->asset->kode_aset ?? 'MENUNGGU_KODE' }}</div>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @if($mp->classroom_id)
                                        <span class="text-brand-700 font-semibold dark:text-brand-300">Kelas {{ $mp->classroom->tingkat
                                            }} {{ $mp->classroom->nama_kelas }}</span>
                                        @elseif($mp->room_id)
                                        <span class="text-emerald-700 font-semibold dark:text-emerald-300">{{ $mp->room->nama_ruangan
                                            }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-center font-bold text-slate-700 dark:text-slate-300">{{
                                        $mp->jumlah }}</td>
                                    <td class="px-4 py-3.5">
                                        @if($mp->kondisi === 'Baik')
                                        <span
                                            class="px-2 py-0.5 text-[10px] rounded font-bold bg-emerald-100 text-emerald-800">BAIK</span>
                                        @elseif($mp->kondisi === 'Rusak Ringan')
                                        <span
                                            class="px-2 py-0.5 text-[10px] rounded font-bold bg-amber-100 text-amber-800">R.
                                            RINGAN</span>
                                        @else
                                        <span
                                            class="px-2 py-0.5 text-[10px] rounded font-bold bg-rose-100 text-rose-800">R.
                                            BERAT</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @if($mp->asset->status_persetujuan === 'pending')
                                        <span
                                            class="inline-flex items-center text-[10px] text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200 font-medium">Pending
                                            ACC</span>
                                        @elseif($mp->asset->status_persetujuan === 'disetujui')
                                        <span
                                            class="inline-flex items-center text-[10px] text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200 font-medium">Aktif</span>
                                        @else
                                        <span
                                            class="inline-flex items-center text-[10px] text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200 font-medium">Ditolak</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-400 italic">Anda belum
                                        mencatatkan inventaris barang apa pun baru-baru ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(isset($myPlacements) && $myPlacements->hasPages())
                    <div class="p-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                        {{ $myPlacements->links() }}
                    </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
