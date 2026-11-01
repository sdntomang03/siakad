<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">
            Pengecekan <span class="text-brand-600">e-TPP Pegawai</span>
        </h2>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-5xl space-y-5 px-4 sm:px-6 lg:px-8">

            {{-- Form Pencarian NIP --}}
            <div
                class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                <form action="{{ route('etpp.index') }}" method="GET" class="flex flex-col items-stretch gap-4 sm:flex-row sm:items-end">
                    <div class="flex-1 w-full">
                        <label for="nip" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Masukkan NIP
                            Pegawai</label>
                        <input type="text" id="nip" name="nip" value="{{ request('nip', $nip) }}"
                            placeholder="Contoh: 198012312005011002"
                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                    </div>
                    <button type="submit"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-md bg-brand-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 sm:w-auto">
                        Cari Data
                    </button>
                    @if(request('nip'))
                    <a href="{{ route('etpp.index') }}"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-md border border-slate-300 px-6 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700 sm:w-auto">
                        Reset
                    </a>
                    @endif
                </form>
            </div>

            @if(request('nip') && !$employee)
            {{-- Jika NIP tidak ditemukan --}}
            <div
                class="p-6 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-lg text-center">
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
                    class="md:col-span-1 bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div
                        class="p-6 bg-brand-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700 text-center">
                        <div
                            class="w-20 h-20 bg-brand-200 text-brand-700 rounded-full mx-auto flex items-center justify-center text-2xl font-black mb-3">
                            {{ substr($employee->nama_lengkap, 0, 1) }}
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $employee->nama_lengkap }}</h3>
                        <p class="text-sm text-slate-500 font-mono mt-1">{{ $employee->nip ?? 'NIP Tidak Tersedia' }}
                        </p>
                        <span
                            class="inline-block mt-3 px-3 py-1 bg-brand-600 text-white text-[10px] font-bold uppercase rounded-full">
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
                <div class="md:col-span-2">
                    <section class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                        <h3 class="text-lg font-semibold text-slate-950 dark:text-white">Rincian e-TPP</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">
                            Ringkasan skor dan nominal pada tampilan ini belum terhubung ke data perhitungan e-TPP. Tidak ada nilai estimasi yang ditampilkan agar data contoh tidak disalahartikan sebagai nilai pegawai.
                        </p>
                    </section>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>