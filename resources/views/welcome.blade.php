<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-screen pt-6 bg-gray-100 sm:pt-0 dark:bg-gray-900">
        <div class="w-full max-w-md px-6 py-4 mt-6 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
            <div class="flex flex-col items-center">
                {{-- Anda bisa mengganti SVG ini dengan logo sekolah --}}
                <a href="/">
                    <svg class="w-20 h-20 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M12 11c.839 0 1.5.661 1.5 1.5s-.661 1.5-1.5 1.5-1.5-.661-1.5-1.5.661-1.5 1.5-1.5zM12 4a7 7 0 100 14 7 7 0 000-14z">
                        </path>
                    </svg>
                </a>

                <h1 class="mt-4 text-2xl font-bold text-center text-gray-700 dark:text-gray-200">
                    Pengumuman Kelulusan Online
                </h1>
                <p class="mt-2 text-sm text-center text-gray-500 dark:text-gray-400">
                    Berdasarkan data dari `UserSeeder`, nama sekolah bisa ditaruh di sini, contoh: SDN Tomang 03 Pagi
                </p>
            </div>

            <div class="mt-8">
                @if (session('error'))
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800"
                    role="alert">
                    <span class="font-medium">Gagal!</span> {{ session('error') }}
                </div>
                @endif

                {{-- Form ini mengarah ke route yang menjalankan KelulusanController@cekKelulusan --}}
                <form method="POST" action="{{ route('kelulusan.cek') }}">
                    @csrf

                    <div>
                        <label for="nisn" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Induk
                            Siswa Nasional (NISN)</label>
                        <input id="nisn" type="text" name="nisn" value="{{ old('nisn') }}" required autofocus
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Masukkan NISN Anda...">
                    </div>

                    <div class="mt-4">
                        <label for="tanggal_lahir"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Lahir</label>
                        <input id="tanggal_lahir" type="date" name="tanggal_lahir" required
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <button type="submit"
                            class="inline-flex items-center w-full justify-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25">
                            Periksa Hasil Kelulusan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>