<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            Manajemen Ruangan & Wilayah Sekolah
        </h2>
        <p class="text-xs text-slate-500 mt-1">Daftar inventaris ruang fisik untuk melacak sebaran fasilitas sarana
            prasarana sekolah.</p>
    </x-slot>

    <div class="py-8" x-data="{
        showModal: false,
        isEdit: false,
        roomData: { id: '', nama_ruangan: '', deskripsi: '' },
        actionUrl: '{{ route('rooms.store') }}',

        openCreate() {
            this.isEdit = false;
            this.roomData = { id: '', nama_ruangan: '', deskripsi: '' };
            this.actionUrl = '{{ route('rooms.store') }}';
            this.showModal = true;
        },

        openEdit(room, url) {
            this.isEdit = true;
            this.roomData = { id: room.id, nama_ruangan: room.nama_ruangan, deskripsi: room.deskripsi };
            this.actionUrl = url;
            this.showModal = true;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div
                class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold shadow-sm">
                {{ session('success') }}
            </div>
            @endif

            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div
                    class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h3 class="text-base font-bold text-slate-700 dark:text-slate-300">Daftar Seluruh Ruang Fisik</h3>
                    <button @click="openCreate()"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow transition">
                        ➕ Tambah Ruangan Non-Kelas
                    </button>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-sm text-left text-slate-600 dark:text-slate-400">
                        <thead
                            class="text-xs text-slate-500 uppercase bg-slate-100 dark:bg-slate-700/60 dark:text-slate-300">
                            <tr>
                                <th class="px-6 py-4 w-12 text-center">No</th>
                                <th class="px-6 py-4">Nama Ruangan / Area</th>
                                <th class="px-6 py-4 w-36">Tipe Tempat</th>
                                <th class="px-6 py-4">Keterangan Fungsi</th>
                                <th class="px-6 py-4 text-center">Volume Barang</th>
                                <th class="px-6 py-4 text-center w-44">Opsi Administrasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($rooms as $index => $room)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4 text-center text-slate-400 font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('rooms.show-assets', ['type' => $room['tipe'] === 'Kelas' ? 'kelas' : 'ruangan', 'id' => $room['id']]) }}"
                                        class="font-bold text-slate-800 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                        {{ $room['nama_ruangan'] }} →
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    @if($room['tipe'] === 'Kelas')
                                    <span
                                        class="inline-flex items-center text-[11px] font-bold bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded border border-indigo-200">🏫
                                        Ruang Kelas</span>
                                    @else
                                    <span
                                        class="inline-flex items-center text-[11px] font-bold bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded border border-emerald-200">🚪
                                        Ruangan Lain</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs italic text-slate-500">{{ $room['deskripsi'] }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2 py-0.5 rounded-full text-xs font-bold {{ $room['placements_count'] > 0 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-400' }}">
                                        {{ $room['placements_count'] }} Unit Barang
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($room['tipe'] === 'Ruangan Lain')
                                        {{-- Tombol Edit khusus Ruangan Umum --}}
                                        <button
                                            @click="openEdit({{ json_encode($room['raw_data']) }}, '{{ route('rooms.update', $room['id']) }}')"
                                            class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg text-xs shadow-sm transition">
                                            Edit
                                        </button>
                                        <form action="{{ route('rooms.destroy', $room['id']) }}" method="POST"
                                            onsubmit="return confirm('Hapus ruangan ini beserta log penempatan aset di dalamnya?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-xs shadow-sm transition">Hapus</button>
                                        </form>
                                        @else
                                        {{-- Penanda Lock Kunci Otomatis untuk Rombel Akademik --}}
                                        <span
                                            class="text-[11px] font-medium text-slate-400 italic bg-slate-50 dark:bg-slate-700/40 px-3 py-1 rounded-lg">
                                            🔒 Dikelola di Rombel
                                        </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Belum ada wilayah
                                    ruangan terdaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- INTERFASE MODAL POP-UP (HANYA UNTUK TAMBAH/EDIT RUANGAN UMUM) --}}
        <div x-show="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            style="display: none;" x-cloak>
            <div @click.outside="showModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden">
                <div class="p-5 border-b bg-slate-50 dark:bg-slate-900/50 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 dark:text-slate-200 text-base"
                        x-text="isEdit ? 'Ubah Informasi Ruangan' : 'Tambah Ruangan Non-Kelas Baru'"></h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 text-sm">✕</button>
                </div>

                <form :action="actionUrl" method="POST" class="p-6 space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        @method('PUT')
                    </template>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Ruangan</label>
                        <input type="text" name="nama_ruangan" x-model="roomData.nama_ruangan" required
                            placeholder="e.g. Laboratorium Kimia Utama"
                            class="w-full rounded-xl border-slate-300 text-sm focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Fungsi / Deskripsi
                            Pendukung</label>
                        <textarea name="deskripsi" x-model="roomData.deskripsi" rows="3"
                            class="w-full rounded-xl border-slate-300 text-sm focus:ring-indigo-500"
                            placeholder="Keterangan isi atau peruntukan ruangan..."></textarea>
                    </div>

                    <div class="pt-4 border-t flex items-center justify-end gap-2">
                        <button type="button" @click="showModal = false"
                            class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow"
                            x-text="isEdit ? 'Simpan Perubahan' : 'Daftarkan Ruangan'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
