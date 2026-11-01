<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100 leading-tight">
            Pusat Logistik & Inventaris Aset Sekolah
        </h2>
    </x-slot>

    <div class="py-6 sm:py-8" x-data="{ openApproval: true }">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div
                class="p-4 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold shadow-sm">
                {{ session('success') }}
            </div>
            @endif

            {{-- 1. SECTION VERIFIKASI PENGAJUAN BARU DARI GURU --}}
            @if($pendingAssets->isNotEmpty())
            <div
                class="bg-amber-50/60 dark:bg-slate-900/40 border border-amber-200 dark:border-amber-900 rounded-lg overflow-hidden shadow-sm">
                <div
                    class="flex items-center justify-between gap-3 border-b border-amber-200 bg-amber-50 p-4 dark:bg-amber-950/40">
                    <span class="text-sm font-bold text-amber-800 dark:text-amber-300 flex items-center gap-2">
                    Membutuhkan Persetujuan ({{ $pendingAssets->count() }} Pengajuan Aset Baru)
                    </span>
                </div>
                <div class="p-5 divide-y divide-amber-100 dark:divide-slate-800">
                    @foreach($pendingAssets as $pa)
                    <div class="py-3 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div>
                            <h4 class="font-bold text-slate-800 dark:text-slate-200 text-sm">{{ $pa->nama_aset }}</h4>
                            <p class="text-xs text-slate-500 mt-1">Diajukan oleh: <span class="font-semibold">{{
                                    $pa->pengaju->name ?? 'Guru Kelas' }}</span> pada {{ $pa->created_at->format('d M
                                Y') }}</p>
                        </div>
                        <form action="{{ route('admin.assets.approve', $pa) }}" method="POST"
                            class="flex flex-wrap items-center gap-2">
                            @csrf @method('PATCH')
                            <input type="text" name="kode_aset" placeholder="Kode inventaris resmi" required
                                aria-label="Kode inventaris untuk {{ $pa->nama_aset }}"
                                class="min-h-10 w-full rounded-md border-slate-300 bg-white py-1.5 text-sm text-slate-700 focus:border-amber-600 focus:ring-amber-600 sm:w-64">
                            <button type="submit"
                                class="inline-flex min-h-10 items-center justify-center rounded-md bg-brand-700 px-3 py-1.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                                Setujui
                            </button>
                            <button type="submit" formaction="{{ route('admin.assets.reject', $pa) }}"
                                onclick="return confirm('Tolak pengajuan aset ini?')"
                                class="inline-flex min-h-10 items-center justify-center rounded-md bg-rose-700 px-3 py-1.5 text-sm font-semibold text-white transition hover:bg-rose-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-700">
                                Tolak
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- 2. PANEL MONITORING GLOBAL --}}
            <div
                class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div
                    class="flex flex-col justify-between gap-4 border-b border-slate-100 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-900/50 md:flex-row md:items-center">
                    <div>
                        <h3 class="text-base font-bold text-slate-700 dark:text-slate-300">Buku Induk Inventaris Lokasi
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar pelacakan sebaran seluruh aset resmi sekolah di
                            setiap kelas dan ruangan.</p>
                    </div>

                    {{-- Form Filter Pencarian Lokasi --}}
                    <form method="GET" action="{{ route('admin.asset-tracking.index') }}"
                        class="grid w-full grid-cols-1 gap-2 sm:grid-cols-2 md:w-auto md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto_auto]">
                        <select name="classroom_id" aria-label="Filter kelas"
                            class="min-h-10 rounded-md border-slate-300 bg-white text-sm text-slate-700 focus:border-brand-600 focus:ring-brand-600">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($classrooms as $c)
                            <option value="{{ $c->id }}" {{ request('classroom_id')==$c->id ? 'selected' : '' }}>Kelas
                                {{ $c->tingkat }} {{ $c->nama_kelas }}</option>
                            @endforeach
                        </select>

                        <select name="room_id" aria-label="Filter ruangan"
                            class="min-h-10 rounded-md border-slate-300 bg-white text-sm text-slate-700 focus:border-brand-600 focus:ring-brand-600">
                            <option value="">-- Semua Ruangan --</option>
                            @foreach($rooms as $r)
                            <option value="{{ $r->id }}" {{ request('room_id')==$r->id ? 'selected' : '' }}>{{
                                $r->nama_ruangan }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                            class="inline-flex min-h-10 items-center justify-center rounded-md bg-brand-700 px-3 py-1.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">Filter</button>
                        <a href="{{ route('admin.asset-tracking.index') }}"
                            class="inline-flex min-h-10 items-center justify-center rounded-md border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">Reset</a>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600 dark:text-slate-400">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-100 dark:bg-slate-700">
                            <tr>
                                <th class="px-6 py-4">Kode Inventaris</th>
                                <th class="px-6 py-4">Nama Item Aset</th>
                                <th class="px-6 py-4">Lokasi Penempatan</th>
                                <th class="px-6 py-4 text-center">Jumlah</th>
                                <th class="px-6 py-4">Kondisi Fisik</th>
                                <th class="px-6 py-4">Catatan/Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($placements as $p)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-6 py-4 font-mono font-bold text-xs text-brand-600 dark:text-brand-400">
                                    {{ $p->asset->kode_aset ?? 'PROSES_ACC' }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-200">{{
                                    $p->asset->nama_aset }}</td>
                                <td class="px-6 py-4">
                                    @if($p->classroom_id)
                                    <span
                                        class="inline-flex items-center text-xs font-bold text-brand-700 bg-brand-50 px-2 py-1 rounded-md">
                                        Kelas {{ $p->classroom->tingkat }} {{ $p->classroom->nama_kelas }}</span>
                                    @elseif($p->room_id)
                                    <span
                                        class="inline-flex items-center text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded-md">
                                        Ruang: {{ $p->room->nama_ruangan }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center font-bold">{{ $p->jumlah }} Unit</td>
                                <td class="px-6 py-4">
                                    @if($p->kondisi === 'Baik')
                                    <span
                                        class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800">BAIK</span>
                                    @elseif($p->kondisi === 'Rusak Ringan')
                                    <span
                                        class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-800">RUSAK
                                        RINGAN</span>
                                    @else
                                    <span
                                        class="px-2 py-0.5 rounded text-[11px] font-bold bg-rose-100 text-rose-800">RUSAK
                                        BERAT</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500 italic">{{ $p->keterangan ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500 italic">Tidak ditemukan
                                    record penempatan data aset yang cocok.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
