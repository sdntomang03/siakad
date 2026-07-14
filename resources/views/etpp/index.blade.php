<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
            Pengecekan <span class="text-indigo-600">e-TPP Pegawai</span>
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Form Pencarian NIP --}}
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <form action="{{ route('etpp.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1 w-full">
                        <label for="nip" class="block text-xs font-bold text-slate-500 uppercase mb-2">Masukkan NIP
                            Pegawai</label>
                        <input type="text" id="nip" name="nip" value="{{ request('nip', $nip) }}"
                            placeholder="Contoh: 198012312005011002"
                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500">
                    </div>
                    <button type="submit"
                        class="w-full md:w-auto px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-lg hover:bg-indigo-700 transition">
                        Cari Data
                    </button>
                    @if(request('nip'))
                    <a href="{{ route('etpp.index') }}"
                        class="w-full md:w-auto px-6 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg text-sm font-bold text-center hover:bg-slate-200 transition">
                        Reset
                    </a>
                    @endif
                </form>
            </div>

            @if(request('nip') && !$employee)
            {{-- Jika NIP tidak ditemukan --}}
            <div
                class="p-6 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-2xl text-center">
                <svg class="w-12 h-12 text-rose-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-bold text-rose-700 dark:text-rose-400">Pegawai Tidak Ditemukan</h3>
                <p class="text-sm text-rose-600 mt-1">Tidak ada data pegawai dengan NIP: <strong>{{ request('nip')
                        }}</strong>.</p>
            </div>
            @elseif($employee)
            {{-- Jika NIP ditemukan, tampilkan Biodata dan e-TPP --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Kolom Kiri: Profil Pegawai --}}
                <div
                    class="md:col-span-1 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div
                        class="p-6 bg-indigo-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700 text-center">
                        <div
                            class="w-20 h-20 bg-indigo-200 text-indigo-700 rounded-full mx-auto flex items-center justify-center text-2xl font-black mb-3">
                            {{ substr($employee->nama_lengkap, 0, 1) }}
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $employee->nama_lengkap }}</h3>
                        <p class="text-sm text-slate-500 font-mono mt-1">{{ $employee->nip ?? 'NIP Tidak Tersedia' }}
                        </p>
                        <span
                            class="inline-block mt-3 px-3 py-1 bg-indigo-600 text-white text-[10px] font-bold uppercase rounded-full">
                            {{ $employee->kategori_pegawai }}
                        </span>
                    </div>
                    <div class="p-6 space-y-4 text-sm">
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase">Status Kepegawaian</span>
                            <span class="block font-semibold text-slate-800 dark:text-slate-200">{{
                                $employee->status_kepegawaian ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase">Tugas Tambahan</span>
                            <span class="block font-semibold text-slate-800 dark:text-slate-200">{{
                                $employee->tugas_tambahan ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase">NUPTK</span>
                            <span class="block font-semibold text-slate-800 dark:text-slate-200">{{ $employee->nuptk ??
                                '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Rincian e-TPP (Mockup) --}}
                <div class="md:col-span-2 space-y-6">
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Estimasi Penerimaan e-TPP</h3>
                            <span
                                class="px-3 py-1 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-lg text-xs font-bold uppercase">Bulan
                                Aktif</span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div
                                class="p-4 rounded-xl border border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                                <span class="block text-xs font-bold text-slate-500 uppercase mb-1">Skor
                                    Kehadiran</span>
                                <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400">98%</span>
                            </div>
                            <div
                                class="p-4 rounded-xl border border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                                <span class="block text-xs font-bold text-slate-500 uppercase mb-1">Skor Kinerja</span>
                                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">100%</span>
                            </div>
                        </div>

                        <div class="border-t border-slate-200 dark:border-slate-700 pt-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-bold text-slate-600 dark:text-slate-400">Besaran Dasar
                                    TPP</span>
                                <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">Rp
                                    2.500.000</span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-bold text-rose-500">Potongan Pajak (PPh 21)</span>
                                <span class="text-sm font-semibold text-rose-500">- Rp 125.000</span>
                            </div>
                            <div
                                class="flex justify-between items-center mt-4 pt-4 border-t border-dashed border-slate-300 dark:border-slate-600">
                                <span class="text-base font-black text-slate-800 dark:text-white">Total Diterima
                                    (Netto)</span>
                                <span class="text-xl font-black text-emerald-600 dark:text-emerald-400">Rp
                                    2.375.000</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>