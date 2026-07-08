<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
            Manajemen <span class="text-rose-600">Hari Libur (Tanggal Merah)</span>
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div
                class="mb-6 flex items-center p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 text-sm font-bold shadow-sm">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div
                class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-400 text-sm font-bold shadow-sm">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-1">
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden sticky top-6">
                        <div
                            class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                            <h3 class="text-base font-black text-slate-800 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-rose-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                Tambah Hari Libur Baru
                            </h3>
                        </div>

                        <form action="{{ route('holidays.store') }}" method="POST" class="p-6">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Pilih
                                    Tanggal</label>
                                <input type="date" name="tanggal" value="{{ old('tanggal') }}" required
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm focus:ring-rose-500">
                            </div>

                            <div class="mb-6">
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Keterangan / Nama
                                    Libur</label>
                                <input type="text" name="keterangan" value="{{ old('keterangan') }}" required
                                    placeholder="Contoh: Hari Kemerdekaan RI"
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm focus:ring-rose-500">
                            </div>

                            <button type="submit"
                                class="w-full flex justify-center items-center gap-2 bg-rose-600 text-white py-2.5 rounded-xl font-black shadow-lg shadow-rose-500/30 hover:bg-rose-700 transition transform hover:-translate-y-0.5 uppercase tracking-wide text-xs">
                                Simpan Hari Libur
                            </button>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div
                            class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center">
                            <h3 class="text-base font-black text-slate-800 dark:text-white">
                                Daftar Hari Libur Terdaftar
                            </h3>
                            <span
                                class="text-xs font-bold text-slate-500 bg-white dark:bg-slate-800 px-3 py-1 rounded-full border border-slate-200 dark:border-slate-700">
                                Total: {{ count($holidays) }}
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left whitespace-nowrap">
                                <thead class="text-xs text-slate-500 uppercase bg-slate-50/80 dark:bg-slate-900/30">
                                    <tr>
                                        <th
                                            class="px-6 py-3 border-b border-slate-100 dark:border-slate-700 text-center w-10">
                                            No</th>
                                        <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700">Tanggal
                                        </th>
                                        <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700">Keterangan
                                        </th>
                                        <th
                                            class="px-6 py-3 border-b border-slate-100 dark:border-slate-700 text-right">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                    @forelse($holidays as $index => $holiday)
                                    <tr class="hover:bg-rose-50/50 dark:hover:bg-slate-700/50 transition">
                                        <td class="px-6 py-4 text-center font-bold text-slate-400">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 font-black text-rose-600 dark:text-rose-400">
                                            {{ \Carbon\Carbon::parse($holiday->tanggal)->locale('id')->isoFormat('D MMMM
                                            YYYY') }}
                                            <span class="block text-[10px] font-medium text-slate-500 uppercase">
                                                {{
                                                \Carbon\Carbon::parse($holiday->tanggal)->locale('id')->isoFormat('dddd')
                                                }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300">
                                            {{ $holiday->keterangan }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <form action="{{ route('holidays.destroy', $holiday->id) }}" method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data libur ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-100 dark:hover:bg-rose-900/30 rounded-lg transition"
                                                    title="Hapus Libur">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-slate-500 italic">
                                            Belum ada tanggal merah yang ditambahkan.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>