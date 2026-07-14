<x-guest-layout>
    {{-- Header / Judul --}}
    <div class="text-center mb-6">
        <h2 class="text-xl sm:text-2xl font-black text-gray-800 dark:text-gray-200 leading-tight">
            Cek <span class="text-indigo-600 dark:text-indigo-400">e-TPP Pegawai</span>
        </h2>
        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Masukkan NIP untuk melihat rincian.</p>
    </div>

    {{-- Form Pencarian NIP --}}
    <form action="{{ route('etpp.search') }}" method="POST">
        @csrf
        <div>
            <label for="nip" class="block font-medium text-sm text-gray-700 dark:text-gray-300">NIP Pegawai</label>
            <input id="nip"
                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm sm:text-sm text-base"
                type="number" name="nip" value="{{ old('nip', $nip) }}" required
                placeholder="Contoh: 198012312005011002" autofocus />
            <p class="text-[10px] text-gray-400 mt-1">Pastikan angka NIP dimasukkan tanpa spasi.</p>
        </div>

        {{-- Tombol Responsif: Bertumpuk di HP, Bersebelahan di Tablet/Laptop --}}
        <div class="flex flex-col-reverse sm:flex-row items-center justify-end mt-5 gap-3 sm:gap-4">
            @if($nip)
            <a class="w-full sm:w-auto text-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none transition"
                href="{{ route('etpp.show') }}">
                Reset
            </a>
            @endif
            <button type="submit"
                class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                Cari Data
            </button>
        </div>
    </form>

    {{-- HASIL PENCARIAN --}}
    @if($nip && !$employee)
    {{-- Jika NIP Tidak Ditemukan --}}
    <div
        class="mt-6 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-center animate-pulse">
        <p class="text-sm text-red-600 dark:text-red-400">Pegawai dengan NIP <br> <strong class="text-lg">{{ $nip
                }}</strong> <br> tidak ditemukan.</p>
    </div>
    @elseif($employee)
    {{-- Jika NIP Ditemukan --}}
    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">

        {{-- Biodata Singkat --}}
        <div class="text-center mb-6">
            <div
                class="w-16 h-16 sm:w-20 sm:h-20 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 rounded-full mx-auto flex items-center justify-center text-2xl sm:text-3xl font-black mb-3 uppercase shadow-sm">
                {{ substr($employee->nama_lengkap, 0, 1) }}
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white leading-tight">{{
                $employee->nama_lengkap }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 font-mono mt-1">{{ $employee->nip ?? '-' }}</p>
            <span
                class="inline-block mt-2 px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-600 text-[10px] sm:text-xs font-bold uppercase rounded-full tracking-wider">
                {{ $employee->kategori_pegawai }}
            </span>
        </div>

        {{-- Tabel Rincian e-TPP --}}
        <div
            class="bg-gray-50 dark:bg-gray-900/50 p-4 sm:p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-inner">
            <h4
                class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                Rincian e-TPP (Bulan Ini)</h4>

            <div class="space-y-3">
                <div class="flex flex-col sm:flex-row sm:justify-between text-sm gap-1">
                    <span class="text-gray-600 dark:text-gray-400">Skor Kehadiran</span>
                    <span class="font-bold text-gray-800 dark:text-gray-200 text-right">100%</span>
                </div>
                <div class="flex flex-col sm:flex-row sm:justify-between text-sm gap-1">
                    <span class="text-gray-600 dark:text-gray-400">Skor Kinerja</span>
                    <span class="font-bold text-gray-800 dark:text-gray-200 text-right">100%</span>
                </div>

                <div
                    class="pt-3 mt-1 border-t border-dashed border-gray-300 dark:border-gray-600 flex flex-col sm:flex-row sm:justify-between text-sm gap-1">
                    <span class="text-gray-600 dark:text-gray-400">Besaran Dasar</span>
                    <span class="font-bold text-gray-800 dark:text-gray-200 text-right">Rp 2.000.000</span>
                </div>
                <div class="flex flex-col sm:flex-row sm:justify-between text-sm gap-1">
                    <span class="text-red-500 dark:text-red-400">Potongan Pajak (PPh 21)</span>
                    <span class="font-bold text-red-500 dark:text-red-400 text-right">- Rp 100.000</span>
                </div>

                <div
                    class="pt-4 mt-2 border-t-2 border-gray-300 dark:border-gray-600 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1">
                    <span class="font-black text-gray-800 dark:text-gray-200 text-sm sm:text-base">Penerimaan
                        Bersih</span>
                    <span class="font-black text-green-600 dark:text-green-400 text-xl sm:text-2xl text-right">Rp
                        1.900.000</span>
                </div>
            </div>
        </div>

    </div>
    @endif
</x-guest-layout>