<x-guest-layout>
    {{-- Header / Judul --}}
    <div class="text-center mb-6">
        <h2 class="text-xl font-black text-gray-800 dark:text-gray-200">
            Cek <span class="text-indigo-600 dark:text-indigo-400">e-TPP Pegawai</span>
        </h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Masukkan NIP untuk melihat rincian.</p>
    </div>

    {{-- Form Pencarian NIP --}}
    <form action="{{ route('etpp.search') }}" method="POST">
        @csrf
        <div>
            <label for="nip" class="block font-medium text-sm text-gray-700 dark:text-gray-300">NIP Pegawai</label>
            <input id="nip"
                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                type="text" name="nip" value="{{ old('nip', $nip) }}" required placeholder="Contoh: 198012312005011002"
                autofocus />
        </div>

        <div class="flex items-center justify-end mt-4">
            @if($nip)
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 mr-4"
                href="{{ route('etpp.show') }}">
                Reset
            </a>
            @endif
            <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-400 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-300 focus:bg-indigo-700 dark:focus:bg-indigo-300 active:bg-indigo-900 dark:active:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                Cari Data
            </button>
        </div>
    </form>

    {{-- HASIL PENCARIAN --}}
    @if($nip && !$employee)
    {{-- Jika NIP Tidak Ditemukan --}}
    <div class="mt-6 p-4 rounded-md bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-center">
        <p class="text-sm text-red-600 dark:text-red-400">Pegawai dengan NIP <strong>{{ $nip }}</strong> tidak
            ditemukan.</p>
    </div>
    @elseif($employee)
    {{-- Jika NIP Ditemukan --}}
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">

        {{-- Biodata Singkat --}}
        <div class="text-center mb-6">
            <div
                class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 rounded-full mx-auto flex items-center justify-center text-2xl font-black mb-3 uppercase">
                {{ substr($employee->nama_lengkap, 0, 1) }}
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $employee->nama_lengkap }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $employee->nip ?? '-' }}</p>
            <span
                class="inline-block mt-2 px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-600 text-[10px] font-bold uppercase rounded-full">
                {{ $employee->kategori_pegawai }}
            </span>
        </div>

        {{-- Tabel Rincian e-TPP --}}
        <div
            class="space-y-3 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
            <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Rincian e-TPP
                (Bulan Ini)</h4>

            <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Skor Kehadiran</span>
                <span class="font-bold text-gray-800 dark:text-gray-200">100%</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Skor Kinerja</span>
                <span class="font-bold text-gray-800 dark:text-gray-200">100%</span>
            </div>

            <div
                class="flex justify-between text-sm pt-3 mt-1 border-t border-dashed border-gray-300 dark:border-gray-600">
                <span class="text-gray-600 dark:text-gray-400">Besaran Dasar</span>
                <span class="font-bold text-gray-800 dark:text-gray-200">Rp 2.000.000</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-red-500 dark:text-red-400">Potongan Pajak (PPh 21)</span>
                <span class="font-bold text-red-500 dark:text-red-400">- Rp 100.000</span>
            </div>

            <div class="flex justify-between text-base pt-3 mt-1 border-t border-gray-300 dark:border-gray-600">
                <span class="font-black text-gray-800 dark:text-gray-200">Penerimaan Bersih</span>
                <span class="font-black text-green-600 dark:text-green-400">Rp 1.900.000</span>
            </div>
        </div>

    </div>
    @endif
</x-guest-layout>