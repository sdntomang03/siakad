<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 uppercase tracking-tight">
                Rekap Catatan Siswa
            </h2>
            <p class="text-sm text-slate-500">TA: {{ $activeYear->tahun_ajaran ?? '-' }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filter Kelas --}}
            <div
                class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <form action="{{ route('teacher-notes.report') }}" method="GET">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Pilih Kelas untuk Melihat
                        Rekap</label>
                    <select name="classroom_id" onchange="this.form.submit()"
                        class="w-full md:w-1/3 rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($myClassrooms as $cls)
                        <option value="{{ $cls->id }}" {{ request('classroom_id')==$cls->id ? 'selected' : '' }}>
                            Kelas {{ $cls->tingkat }} - {{ $cls->nama_kelas }}
                        </option>
                        @endforeach
                    </select>
                </form>
            </div>

            @if($selectedClassroom)
            {{-- Grid Daftar Siswa --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($students as $siswa)
                <div x-data="{ open: false }"
                    class="bg-white dark:bg-slate-800 p-5 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 hover:border-indigo-400 transition cursor-pointer group"
                    @click="open = true">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center text-xl font-black group-hover:bg-indigo-600 group-hover:text-white transition">
                            {{ substr($siswa->nama_lengkap, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-slate-800 dark:text-white group-hover:text-indigo-600 transition">
                                {{ $siswa->nama_lengkap }}</h3>
                            <p class="text-xs text-slate-500">{{ $siswa->nisn ?? 'Tanpa NISN' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="block text-lg font-black text-slate-800 dark:text-white">{{
                                $siswa->notes->count() }}</span>
                            <span class="block text-[10px] text-slate-400 uppercase font-bold">Catatan</span>
                        </div>
                    </div>

                    {{-- Modal Riwayat (Alpine.js) --}}
                    <div x-show="open" style="display: none;"
                        class="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click.stop="open = false"></div>

                        <div class="relative bg-white dark:bg-slate-800 w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700"
                            @click.stop>
                            {{-- Header Modal --}}
                            <div
                                class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                                <h3 class="font-black text-slate-800 dark:text-white uppercase tracking-tight">Riwayat
                                    Catatan</h3>
                                <button @click="open = false" class="text-slate-400 hover:text-rose-500">&times;
                                    Tutup</button>
                            </div>

                            {{-- Isi Modal --}}
                            <div class="p-6 max-h-[70vh] overflow-y-auto space-y-4">
                                <div class="mb-4 text-center">
                                    <h4 class="text-lg font-bold text-indigo-600">{{ $siswa->nama_lengkap }}</h4>
                                    <p class="text-xs text-slate-500">Detail catatan perkembangan selama semester ini.
                                    </p>
                                </div>

                                @forelse($siswa->notes as $note)
                                <div
                                    class="p-4 rounded-2xl border border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                                    <div class="flex justify-between items-start mb-2">
                                        <span
                                            class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded text-[10px] font-bold uppercase">{{
                                            $note->jenis_catatan }}</span>

                                    </div>
                                    <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">{{
                                        $note->catatan }}</p>

                                    @if($note->foto && is_array($note->foto))
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach($note->foto as $path)
                                        {{-- Container Alpine.js untuk Modal Foto Satuan --}}
                                        <div x-data="{ imgModal: false }">
                                            {{-- Preview Foto Kecil --}}
                                            <div @click="imgModal = true"
                                                class="w-16 h-16 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 cursor-zoom-in hover:ring-2 hover:ring-indigo-500 transition group relative">
                                                <img src="{{ asset('storage/' . $path) }}"
                                                    class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                                <div
                                                    class="absolute inset-0 bg-black/5 group-hover:bg-transparent transition">
                                                </div>
                                            </div>

                                            {{-- Modal Preview Besar --}}
                                            <template x-teleport="body">
                                                <div x-show="imgModal"
                                                    x-transition:enter="transition ease-out duration-300"
                                                    x-transition:enter-start="opacity-0"
                                                    x-transition:enter-end="opacity-100"
                                                    x-transition:leave="transition ease-in duration-200"
                                                    x-transition:leave-start="opacity-100"
                                                    x-transition:leave-end="opacity-0"
                                                    class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/90 backdrop-blur-sm"
                                                    style="display: none;" @keydown.window.escape="imgModal = false">

                                                    {{-- Klik area luar untuk tutup --}}
                                                    <div class="absolute inset-0" @click="imgModal = false"></div>

                                                    {{-- Gambar --}}
                                                    <div class="relative max-w-5xl w-full flex flex-col items-center">
                                                        <img :src="'{{ asset('storage/' . $path) }}'"
                                                            class="max-h-[85vh] rounded-2xl shadow-2xl border-4 border-white dark:border-slate-800 object-contain">

                                                        <button @click="imgModal = false"
                                                            class="mt-4 px-8 py-2.5 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-full font-black shadow-xl hover:bg-rose-500 hover:text-white transition-all uppercase text-xs tracking-widest">
                                                            Tutup
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        @endforeach
                                    </div>
                                    @else
                                    {{-- fallback jika data foto lama masih berupa string tunggal (opsional) --}}
                                    @if($note->foto)
                                    <div class="mt-3">
                                        <a href="{{ asset('storage/'.$note->foto) }}" target="_blank"
                                            class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">Lihat
                                            Lampiran (Format Lama)</a>
                                    </div>
                                    @endif
                                    @endif
                                </div>
                                @empty
                                <p class="text-center text-slate-500 italic py-10">Belum ada catatan untuk siswa ini.
                                </p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            {{-- State Kosong --}}
            <div
                class="text-center py-20 bg-white dark:bg-slate-800 rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-700">
                <p class="text-slate-500 font-bold">Pilih kelas di atas untuk melihat daftar siswa.</p>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>