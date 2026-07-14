<x-guest-layout>
    <div class="py-8 min-h-screen bg-slate-50 dark:bg-slate-900">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Judul Halaman --}}
            <div class="text-center mb-8">
                <h2 class="text-2xl font-black text-slate-800 dark:text-slate-200">
                    Pengecekan e-TPP Pegawai
                </h2>
                <p class="text-sm text-slate-500 mt-2">Silakan masukkan NIP untuk melihat rincian e-TPP Anda.</p>
            </div>

            {{-- Form Pencarian NIP --}}
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <form action="{{ route('etpp.search') }}" method="POST"
                    class="flex flex-col md:flex-row gap-4 items-end">
                    @csrf
                    <div class="flex-1 w-full">
                        <label for="nip" class="block text-xs font-bold text-slate-500 uppercase mb-2">Masukkan NIP
                            Pegawai</label>
                        <input type="text" id="nip" name="nip" value="{{ old('nip', $nip) }}" required
                            placeholder="Contoh: 198012312005011002"
                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500">
                    </div>
                    <button type="submit"
                        class="w-full md:w-auto px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-lg hover:bg-indigo-700 transition">
                        Cari e-TPP
                    </button>
                    @if($nip)
                    <a href="{{ route('etpp.show') }}"
                        class="w-full md:w-auto px-6 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg text-sm font-bold text-center hover:bg-slate-200 transition">
                        Reset
                    </a>
                    @endif
                </form>
            </div>

            @if($nip && !$employee)
            {{-- Hasil Pencarian Tidak Ditemukan --}}
            <div
                class="p-6 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-2xl text-center">
                <h3 class="text-lg font-bold text-rose-700 dark:text-rose-400">Pegawai Tidak Ditemukan</h3>
                <p class="text-sm text-rose-600 mt-1">Tidak ada data pegawai dengan NIP: <strong>{{ $nip }}</strong>.
                </p>
            </div>
            @elseif($employee)
            {{-- Hasil Pencarian Ditemukan --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Profil Pegawai --}}
                <div
                    class="md:col-span-1 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 text-center">
                    <div
                        class="w-20 h-20 bg-indigo-100 text-indigo-700 rounded-full mx-auto flex items-center justify-center text-2xl font-black mb-3">
                        {{ substr($employee->nama_lengkap, 0, 1) }}
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $employee->nama_lengkap }}</h3>
                    <p class="text-sm text-slate-500 font-mono">{{ $employee->nip ?? '-' }}</p>
                </div>

                {{-- Rincian e-TPP --}}
                <div
                    class="md:col-span-2 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">Rincian e-TPP</h3>
                    <!-- Detail Rincian e-TPP Anda -->
                    <div class="text-center py-10 text-slate-500">
                        Data perhitungan e-TPP belum tersedia.
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-guest-layout>