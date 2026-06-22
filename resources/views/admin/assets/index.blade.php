<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                Pusat Logistik & Inventaris Aset Sekolah
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Dashboard monitoring distribusi aset global dan
                verifikasi pengajuan guru.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- NOTIFIKASI / ALERTS --}}
            @if(session('success'))
            <div
                class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm font-semibold shadow-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
            @endif

            {{-- PANEL STATISTIK RINGKASAN ASET --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div
                    class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-4">
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 11m8 4V3.5M4 11v10l8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase">Total Aset Tersebar</span>
                        <span class="text-xl font-black text-slate-800 dark:text-slate-100">{{
                            $placements->sum('jumlah') }} <span
                                class="text-xs font-normal text-slate-500">Unit</span></span>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-4">
                    <div
                        class="p-3 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase">Kondisi Baik</span>
                        <span class="text-xl font-black text-emerald-600 dark:text-emerald-400">{{
                            $placements->where('kondisi', 'Baik')->sum('jumlah') }} <span
                                class="text-xs font-normal text-slate-500">Unit</span></span>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-4">
                    <div class="p-3 bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase">Rusak Ringan</span>
                        <span class="text-xl font-black text-amber-500">{{ $placements->where('kondisi', 'Rusak
                            Ringan')->sum('jumlah') }} <span
                                class="text-xs font-normal text-slate-500">Unit</span></span>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-4">
                    <div class="p-3 bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase">Rusak Berat</span>
                        <span class="text-xl font-black text-rose-600">{{ $placements->where('kondisi', 'Rusak
                            Berat')->sum('jumlah') }} <span
                                class="text-xs font-normal text-slate-500">Unit</span></span>
                    </div>
                </div>
            </div>

            {{-- LAYOUT ATAS: FORM INPUT MANDIRI & VERIFIKASI GURU --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                {{-- Kiri: Form Tambah Master Aset Baru --}}
                <div
                    class="lg:col-span-5 bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="mb-4">
                        <h3 class="font-bold text-sm text-slate-700 dark:text-slate-200 uppercase tracking-wider">➕
                            Tambah Master Aset Baru</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftarkan jenis barang baru langsung ke database induk
                            sekolah.</p>
                    </div>

                    <form action="{{ route('admin.assets.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Nama
                                Item/Barang</label>
                            <input type="text" name="nama_aset" placeholder="e.g. Kursi Belajar Siswa Chitose" required
                                class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm focus:ring-indigo-500">
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="col-span-2">
                                <label
                                    class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Kode
                                    Inventaris</label>
                                <input type="text" name="kode_aset" placeholder="e.g. INV/KRS/2026" required
                                    class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm focus:ring-indigo-500">
                            </div>
                            <div class="col-span-1">
                                <label
                                    class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Total
                                    Stok</label>
                                <input type="number" name="total_stok" value="10" min="0" required
                                    class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm text-center focus:ring-indigo-500">
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow">
                            Simpan ke Database Induk
                        </button>
                    </form>
                </div>

                {{-- Kanan: Daftar Tunggu Approval Pengajuan Guru --}}
                <div
                    class="lg:col-span-7 bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm min-h-[240px]">
                    <div class="mb-3">
                        <h3 class="font-bold text-sm text-slate-700 dark:text-slate-200 uppercase tracking-wider">🔔
                            Verifikasi Pengajuan Barang Guru</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar item baru yang dilaporkan guru dan memerlukan
                            penerbitan kode inventaris.</p>
                    </div>

                    <div
                        class="divide-y divide-slate-100 dark:divide-slate-700/60 max-h-[175px] overflow-y-auto no-scrollbar">
                        @forelse($pendingAssets as $pa)
                        <div
                            class="py-3 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b last:border-0 border-slate-100 dark:border-slate-700">
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 text-xs">{{ $pa->nama_aset }}
                                </h4>
                                <span class="text-[10px] text-slate-400">Oleh: {{ $pa->pengaju->name ?? 'Guru' }}</span>
                            </div>

                            <form action="{{ route('admin.assets.approve', $pa) }}" method="POST"
                                class="flex items-center gap-1.5">
                                @csrf @method('PATCH')
                                <input type="text" name="kode_aset" placeholder="Kode Inv" required
                                    class="rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-[11px] py-1 w-36 focus:ring-indigo-500">
                                <button type="submit"
                                    class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-[10px] transition">Setujui</button>
                                <button type="submit" formaction="{{ route('admin.assets.reject', $pa) }}"
                                    onclick="return confirm('Tolak pengajuan barang ini?')"
                                    class="px-2 py-1 bg-rose-100 hover:bg-rose-600 text-rose-700 hover:text-white font-bold rounded-lg text-[10px] transition">Tolak</button>
                            </form>
                        </div>
                        @empty
                        <div class="text-xs text-slate-400 italic text-center py-10">Tidak ada pengajuan barang baru
                            yang tertunda.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- 3. PANEL MONITORING UTAMA: BUKU INDUK INVENTARIS LOKASI --}}
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div
                    class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-700 dark:text-slate-300">Buku Induk Sebaran Aset Aktif
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Seluruh log penempatan fasilitas barang di lingkungan
                            sekolah.</p>
                    </div>

                    {{-- Penyaringan Data / Filter --}}
                    <form method="GET" action="{{ route('admin.asset-tracking.index') }}"
                        class="flex flex-wrap items-center gap-2">
                        <select name="classroom_id"
                            class="rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs focus:ring-indigo-500 shadow-sm cursor-pointer">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($classrooms as $c)
                            <option value="{{ $c->id }}" {{ request('classroom_id')==$c->id ? 'selected' : '' }}>Kelas
                                {{ $c->tingkat }} {{ $c->nama_kelas }}</option>
                            @endforeach
                        </select>

                        <select name="room_id"
                            class="rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs focus:ring-indigo-500 shadow-sm cursor-pointer">
                            <option value="">-- Semua Ruangan --</option>
                            @foreach($rooms as $r)
                            <option value="{{ $r->id }}" {{ request('room_id')==$r->id ? 'selected' : '' }}>{{
                                $r->nama_ruangan }}</option>
                            @endforeach
                        </select>

                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow transition">Filter</button>
                        @if(request()->filled('classroom_id') || request()->filled('room_id'))
                        <a href="{{ route('admin.asset-tracking.index') }}"
                            class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition">Reset</a>
                        @endif
                    </form>
                </div>

                {{-- TABEL INVENTARIS GLOBAL --}}
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-sm text-left text-slate-600 dark:text-slate-400">
                        <thead
                            class="text-xs text-slate-500 uppercase bg-slate-100 dark:bg-slate-700/60 dark:text-slate-300">
                            <tr>
                                <th class="px-6 py-4 w-44">Kode Inventaris</th>
                                <th class="px-6 py-4">Nama Aset Fasilitas</th>
                                <th class="px-6 py-4">Lokasi Penempatan</th>
                                <th class="px-6 py-4 text-center w-24">Jumlah</th>
                                <th class="px-6 py-4 w-32">Kondisi Fisik</th>
                                <th class="px-6 py-4">Keterangan Letak</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($placements as $p)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-xs text-indigo-600 dark:text-indigo-400">
                                    {{ $p->asset->kode_aset ?? 'PROSES_VERIFIKASI' }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $p->asset->nama_aset }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($p->classroom_id)
                                    <span
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/40 px-2.5 py-1 rounded-lg border border-indigo-100 dark:border-indigo-900/60">
                                        🏫 Kelas {{ $p->classroom->tingkat }} {{ $p->classroom->nama_kelas }}
                                    </span>
                                    @elseif($p->room_id)
                                    <span
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 rounded-lg border border-emerald-100 dark:border-emerald-900/60">
                                        🚪 Ruang: {{ $p->room->nama_ruangan }}
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-slate-800 dark:text-slate-200">
                                    {{ $p->jumlah }} <span class="text-xs font-normal text-slate-400">Unit</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($p->kondisi === 'Baik')
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold tracking-wider bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">BAIK</span>
                                    @elseif($p->kondisi === 'Rusak Ringan')
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold tracking-wider bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-400 border border-amber-200 dark:border-amber-900">RUSAK
                                        RINGAN</span>
                                    @else
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold tracking-wider bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-400 border border-rose-200 dark:border-rose-900">RUSAK
                                        BERAT</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400 italic max-w-xs truncate"
                                    title="{{ $p->keterangan }}">
                                    {{ $p->keterangan ?? '—' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6"
                                    class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 italic">
                                    Tidak ada data penempatan inventaris yang cocok atau terdaftar saat ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
