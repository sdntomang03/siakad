<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                    Katalog Master Aset Sekolah
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Daftar monitoring kuantitas total stok,
                    volume sebaran, dan sisa logistik gudang.</p>
            </div>

            @hasanyrole('superadmin|operator')
            <a href="{{ route('admin.asset-tracking.index') }}"
                class="px-4 py-2 bg-slate-800 text-white dark:bg-slate-200 dark:text-slate-900 font-bold text-xs rounded-xl shadow hover:bg-slate-700 transition flex items-center gap-1.5 self-start sm:self-auto">
                📊 Ruang Kontrol Admin
            </a>
            @endhasanyrole
        </div>
    </x-slot>

    {{-- INISIALISASI MODAL DENGAN DETEKSI OTOMATIS JIKA ADA ERROR VALIDASI --}}
    <div class="py-8" x-data="{
        showEditModal: {{ $errors->any() ? 'true' : 'false' }},
        editAsset: {
            nama_aset: '{{ old('nama_aset') }}',
            kode_aset: '{{ old('kode_aset') }}',
            total_stok: {{ old('total_stok') ?? 0 }}
        },
        actionUrl: '{{ session('failed_action_url') ?? '' }}'
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- NOTIFIKASI SUKSES --}}
            @if(session('success'))
            <div
                class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm font-semibold shadow-sm">
                {{ session('success') }}
            </div>
            @endif

            {{-- NOTIFIKASI ERROR VALIDASI GLOBAL --}}
            @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold shadow-sm">
                ⚠️ Gagal memperbarui data. Silakan periksa kembali inputan Anda pada modal.
            </div>
            @endif

            {{-- BAR PENCARIAN --}}
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <form method="GET" action="{{ route('admin.assets.list') }}"
                    class="w-full sm:w-96 flex items-center gap-2">
                    <div class="relative w-full">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama aset atau kode inventaris..."
                            class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs py-2.5 pl-3 pr-10 focus:ring-indigo-500 shadow-sm">
                        @if(request()->filled('search'))
                        <a href="{{ route('admin.assets.list') }}"
                            class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs">✕</a>
                        @endif
                    </div>
                    <button type="submit"
                        class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow transition">Cari</button>
                </form>

                <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                    Menampilkan total <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $assets->total()
                        }}</span> jenis barang resmi.
                </div>
            </div>

            {{-- TABEL KATALOG BARANG --}}
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-sm text-left text-slate-600 dark:text-slate-400">
                        <thead
                            class="text-xs text-slate-500 uppercase bg-slate-100 dark:bg-slate-700/60 dark:text-slate-300">
                            <tr>
                                <th class="px-6 py-4 w-12 text-center">No</th>
                                <th class="px-6 py-4 w-52">Kode Induk</th>
                                <th class="px-6 py-4">Nama Item Aset</th>
                                <th class="px-6 py-4 text-center">Total Stok</th>
                                <th class="px-6 py-4 text-center">Tersebar</th>
                                <th class="px-6 py-4 text-center">Sisa Gudang</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($assets as $index => $asset)
                            @php
                            $sisaGudang = $asset->total_stok - $asset->total_tersebar;
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4 text-center text-slate-400 font-medium">
                                    {{ $assets->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 font-mono font-bold text-xs text-indigo-600 dark:text-indigo-400">
                                    {{ $asset->kode_aset ?? 'BELUM_DITERBITKAN' }}
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">
                                    {{ $asset->nama_aset }}
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-slate-700 dark:text-slate-300">
                                    {{ $asset->total_stok }} Unit
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $asset->total_tersebar }} Unit
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($sisaGudang > 0)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $sisaGudang }} Unit
                                    </span>
                                    @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-100">
                                        Habis
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- AMAN DENGAN JSON_ENCODE UNTUK MENCEGAH JAVASCRIPT CRASH --}}
                                        <button type="button" @click="
                                            showEditModal = true;
                                            editAsset.nama_aset = {{ json_encode($asset->nama_aset) }};
                                            editAsset.kode_aset = {{ json_encode($asset->kode_aset) }};
                                            editAsset.total_stok = {{ (int)($asset->total_stok ?? 0) }};
                                            actionUrl = '{{ route('admin.assets.update', $asset) }}';
                                        "
                                            class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg text-xs shadow-sm transition">
                                            Edit
                                        </button>

                                        <form action="{{ route('admin.assets.destroy', $asset) }}" method="POST"
                                            onsubmit="return confirm('Peringatan: Menghapus master aset ini juga akan menghapus SEMUA catatan penempatan barang ini di setiap kelas dan ruangan! Lanjutkan?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-xs shadow-sm transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7"
                                    class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 italic">
                                    Belum ada jenis master aset yang terdaftar atau cocok dengan pencarian Anda.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($assets->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                    {{ $assets->appends(request()->query())->links() }}
                </div>
                @endif
            </div>

        </div>

        {{-- WADAH MODAL POP-UP --}}
        <div x-show="showEditModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            style="display: none;" x-cloak>

            <div @click.outside="showEditModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden transform transition-all">

                <div
                    class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-slate-200 text-base">Ubah Data Master Aset</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Perbarui nama, kode induk, atau kapasitas stok sekolah.
                        </p>
                    </div>
                    <button type="button" @click="showEditModal = false"
                        class="text-slate-400 hover:text-slate-600 text-sm">✕</button>
                </div>

                <form :action="actionUrl" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Nama
                            Item Aset</label>
                        <input type="text" name="nama_aset" x-model="editAsset.nama_aset" required
                            class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 text-sm focus:ring-indigo-500 shadow-sm">
                        @error('nama_aset')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div class="col-span-2">
                            <label
                                class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Kode
                                Inventaris</label>
                            <input type="text" name="kode_aset" x-model="editAsset.kode_aset" required
                                class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 text-sm font-mono focus:ring-indigo-500 shadow-sm">
                            @error('kode_aset')
                            <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-1">
                            <label
                                class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Total
                                Stok</label>
                            <input type="number" name="total_stok" x-model="editAsset.total_stok" required min="0"
                                class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 text-sm text-center font-bold focus:ring-indigo-500 shadow-sm">
                            @error('total_stok')
                            <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div
                        class="pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-end gap-2">
                        <button type="button" @click="showEditModal = false"
                            class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
