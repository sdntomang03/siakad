<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Data Capaian Pembelajaran</h2>
            <div class="flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('cp.import-form') }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700">
                    Import JSON
                </a>
                <a href="{{ route('cp.create') }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                    Tambah Manual
                </a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-4 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        @if(session('success'))
        <div role="status" class="rounded-md border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-200">{{
            session('success') }}</div>
        @endif

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">

            <!-- Kolom Pencarian & Filter -->
            <div class="border-b border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900/50">
                <form action="{{ route('cp.index') }}" method="GET" class="grid gap-3 md:grid-cols-[minmax(12rem,1fr)_10rem_minmax(14rem,2fr)_auto]">

                    <!-- Dropdown Mata Pelajaran -->
                    <select name="mata_pelajaran"
                        aria-label="Filter mata pelajaran"
                        class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">-- Semua Mata Pelajaran --</option>
                        @foreach($mapelList as $mapel)
                        <option value="{{ $mapel }}" {{ request('mata_pelajaran')==$mapel ? 'selected' : '' }}>
                            {{ $mapel }}
                        </option>
                        @endforeach
                    </select>

                    <!-- Dropdown Fase -->
                    <select name="fase" aria-label="Filter fase"
                        class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">-- Semua Fase --</option>
                        @foreach($faseList as $fase)
                        <option value="{{ $fase }}" {{ request('fase')==$fase ? 'selected' : '' }}>
                            {{ $fase }}
                        </option>
                        @endforeach
                    </select>

                    <!-- Pencarian Teks -->
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari elemen atau deskripsi..."
                        aria-label="Cari elemen atau deskripsi capaian pembelajaran"
                        class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-800 dark:text-white">

                    <div class="flex gap-2">
                        <button type="submit"
                            class="min-h-11 rounded-md bg-brand-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                            Terapkan Filter
                        </button>

                        <!-- Tombol Reset muncul jika ada filter yang aktif -->
                        @if(request()->hasAny(['search', 'mata_pelajaran', 'fase']))
                        <a href="{{ route('cp.index') }}"
                            class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700">
                            Reset
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[48rem] w-full text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase text-slate-600 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-300">
                        <tr>
                            <th class="px-4 py-3 w-12 text-center">No</th>
                            <th class="px-4 py-3">Mapel & Fase</th>
                            <th class="px-4 py-3">Elemen</th>
                            <th class="px-4 py-3 w-1/3">Deskripsi CP</th>
                            <th class="px-4 py-3 w-24 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cps as $index => $item)
                        <tr class="border-b border-slate-100 text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-700/40">
                            <td class="px-4 py-3 text-center font-semibold tabular-nums text-slate-500">{{ $cps->firstItem() + $index }}</td>
                            <td class="px-4 py-3">
                                <span class="block font-semibold text-slate-950 dark:text-white">{{ $item->mata_pelajaran }}</span>
                                <span class="block text-xs font-semibold text-brand-600 mt-1">{{ $item->fase }}</span>
                            </td>
                            <td class="px-4 py-3 font-medium">{{ $item->elemen }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-300">{{ Str::limit($item->deskripsi_cp, 100) }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('cp.edit', $item->id) }}"
                                        class="font-semibold text-brand-700 hover:text-brand-900 focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:text-brand-300 dark:hover:text-brand-200">Edit</a>
                                    <form action="{{ route('cp.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus CP ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="font-semibold text-rose-700 hover:text-rose-900 focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-700 dark:text-rose-300 dark:hover:text-rose-200">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada data Capaian
                                Pembelajaran.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $cps->links() }}
            </div>
        </div>
    </div>
</x-app-layout>