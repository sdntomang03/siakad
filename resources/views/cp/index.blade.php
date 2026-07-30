<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">Data Capaian Pembelajaran</h2>
            <div class="flex gap-2">
                <a href="{{ route('cp.import-form') }}"
                    class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-bold rounded-lg hover:bg-slate-300 transition">
                    Import JSON
                </a>
                <a href="{{ route('cp.create') }}"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition shadow-sm">
                    + Tambah Manual
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-100 text-emerald-700 rounded-lg font-bold border border-emerald-200">{{
            session('success') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            <!-- Kolom Pencarian & Filter -->
            <div class="p-4 border-b border-slate-100 bg-slate-50">
                <form action="{{ route('cp.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">

                    <!-- Dropdown Mata Pelajaran -->
                    <select name="mata_pelajaran"
                        class="text-sm border-slate-300 rounded-lg focus:ring-indigo-500 md:w-64">
                        <option value="">-- Semua Mata Pelajaran --</option>
                        @foreach($mapelList as $mapel)
                        <option value="{{ $mapel }}" {{ request('mata_pelajaran')==$mapel ? 'selected' : '' }}>
                            {{ $mapel }}
                        </option>
                        @endforeach
                    </select>

                    <!-- Dropdown Fase -->
                    <select name="fase" class="text-sm border-slate-300 rounded-lg focus:ring-indigo-500 md:w-40">
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
                        class="w-full text-sm border-slate-300 rounded-lg focus:ring-indigo-500">

                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-5 py-2 bg-slate-800 text-white text-sm font-bold rounded-lg hover:bg-slate-900 transition shrink-0">
                            Terapkan Filter
                        </button>

                        <!-- Tombol Reset muncul jika ada filter yang aktif -->
                        @if(request()->hasAny(['search', 'mata_pelajaran', 'fase']))
                        <a href="{{ route('cp.index') }}"
                            class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-bold rounded-lg hover:bg-slate-300 transition shrink-0 flex items-center">
                            Reset
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-slate-700 uppercase bg-slate-100 border-b border-slate-200">
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
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="px-4 py-3 text-center font-bold">{{ $cps->firstItem() + $index }}</td>
                            <td class="px-4 py-3">
                                <span class="block font-bold text-slate-800">{{ $item->mata_pelajaran }}</span>
                                <span class="block text-xs font-semibold text-indigo-600 mt-1">{{ $item->fase }}</span>
                            </td>
                            <td class="px-4 py-3 font-medium">{{ $item->elemen }}</td>
                            <td class="px-4 py-3 text-xs text-slate-600">{{ Str::limit($item->deskripsi_cp, 100) }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('cp.edit', $item->id) }}"
                                        class="text-indigo-600 hover:text-indigo-900 font-bold">Edit</a>
                                    <form action="{{ route('cp.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus CP ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-rose-600 hover:text-rose-900 font-bold">Hapus</button>
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