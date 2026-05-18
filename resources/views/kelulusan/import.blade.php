<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-md font-bold text-gray-700">Data Kelulusan</h2>

            @if($dataKelulusan->count() > 0)
            <form action="{{ route('kelulusan.delete-all') }}" method="POST"
                onsubmit="return confirm('PENTING: Apakah Anda yakin ingin MENGHAPUS SEMUA data kelulusan? Tindakan ini tidak dapat dibatalkan!');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white rounded-lg text-xs font-bold transition-colors border border-rose-200 hover:border-rose-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    Kosongkan Data
                </button>
            </form>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Import Data Kelulusan Murid</h1>
                <p class="text-sm text-gray-600 mt-1">Halaman khusus untuk mengunggah data kelulusan siswa via file
                    Excel.</p>
            </div>

            @if(session('success'))
            <div
                class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg text-emerald-800 text-sm font-medium">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-lg text-rose-800 text-sm font-medium">
                {{ session('error') }}
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-1 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 h-fit"
                    x-data="{ isUploading: false }">

                    <h2 class="text-md font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Unggah Berkas Excel
                    </h2>

                    <form action="{{ route('kelulusan.import.process') }}" method="POST" enctype="multipart/form-data"
                        @submit="isUploading = true">
                        @csrf
                        <div class="mb-5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pilih
                                File (.xlsx, .xls)</label>
                            <input type="file" name="file" required
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-200 p-2 rounded-xl bg-gray-50 outline-none focus:border-indigo-500 transition-all">
                            @error('file')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" :disabled="isUploading"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition-all duration-150 flex justify-center items-center gap-2">

                            <template x-if="!isUploading">
                                <span>Mulai Ekstrak & Simpan</span>
                            </template>

                            <template x-if="isUploading">
                                <div class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span>Sedang Memproses data...</span>
                                </div>
                            </template>
                        </button>
                    </form>

                    <div class="mt-6 bg-amber-50/60 p-4 rounded-xl border border-amber-100">
                        <h3
                            class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-2 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Penting Syarat Excel:
                        </h3>
                        <ul class="text-xs text-amber-900/80 space-y-1.5 list-disc pl-4">
                            <li>Baris pertama <b>wajib</b> berisi nama kolom (Header).</li>
                            <li>Kolom harus berupa: <code
                                    class="bg-amber-100 px-1 py-0.5 rounded font-mono">nama</code>, <code
                                    class="bg-amber-100 px-1 py-0.5 rounded font-mono">nisn</code>, <code
                                    class="bg-amber-100 px-1 py-0.5 rounded font-mono">nipd</code>, <code
                                    class="bg-amber-100 px-1 py-0.5 rounded font-mono">tanggal_lahir</code>, <code
                                    class="bg-amber-100 px-1 py-0.5 rounded font-mono">kelas</code>, dan <code
                                    class="bg-amber-100 px-1 py-0.5 rounded font-mono">keterangan</code>.</li>
                            <li>Format <code class="bg-amber-100 px-1 py-0.5 rounded font-mono">tanggal_lahir</code>
                                diubah ke tipe <b>Text</b> dengan format <code class="font-bold">YYYY-MM-DD</code>
                                (Contoh: 2012-10-25).</li>
                            <li>Pilihan isi kolom keterangan: <span class="font-semibold">LULUS / TIDAK LULUS /
                                    DITUNDA</span>.</li>
                        </ul>
                        <div class="mt-6 border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                            <div
                                class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <h3 class="text-sm font-bold text-gray-700">Contoh Format Excel</h3>
                                </div>
                                <span
                                    class="text-[10px] bg-emerald-100 text-emerald-700 px-2 py-1 rounded font-bold uppercase tracking-wider">Sheet
                                    1</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                                    <thead>
                                        <tr
                                            class="bg-gray-100 text-gray-500 border-b border-gray-200 text-center font-semibold">
                                            <th class="p-2 border-r border-gray-200 w-8"></th>
                                            <th class="p-2 border-r border-gray-200">A</th>
                                            <th class="p-2 border-r border-gray-200">B</th>
                                            <th class="p-2 border-r border-gray-200">C</th>
                                            <th class="p-2 border-r border-gray-200">D</th>
                                            <th class="p-2 border-r border-gray-200">E</th>
                                            <th class="p-2">F</th>
                                        </tr>
                                        <tr class="bg-emerald-100 text-emerald-900 border-b border-emerald-200">
                                            <td
                                                class="p-2 border-r border-gray-200 text-center text-gray-400 font-mono bg-gray-50 font-bold">
                                                1</td>
                                            <th class="p-2 border-r border-emerald-200 font-bold">nama</th>
                                            <th class="p-2 border-r border-emerald-200 font-bold">nisn</th>
                                            <th class="p-2 border-r border-emerald-200 font-bold">nipd</th>
                                            <th class="p-2 border-r border-emerald-200 font-bold">tanggal_lahir</th>
                                            <th class="p-2 border-r border-emerald-200 font-bold">kelas</th>
                                            <th class="p-2 font-bold">keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 bg-white">
                                        <tr class="border-b border-gray-100 hover:bg-emerald-50 transition-colors">
                                            <td
                                                class="p-2 border-r border-gray-100 text-center text-gray-400 font-mono bg-gray-50">
                                                2</td>
                                            <td class="p-2 border-r border-gray-100">Budi Santoso</td>
                                            <td class="p-2 border-r border-gray-100">0123456789</td>
                                            <td class="p-2 border-r border-gray-100">2021001</td>
                                            <td class="p-2 border-r border-gray-100 font-mono text-blue-600">2014-05-24
                                            </td>
                                            <td class="p-2 border-r border-gray-100 text-center">6A</td>
                                            <td class="p-2 font-bold text-emerald-600">LULUS</td>
                                        </tr>
                                        <tr class="hover:bg-emerald-50 transition-colors">
                                            <td
                                                class="p-2 border-r border-gray-100 text-center text-gray-400 font-mono bg-gray-50">
                                                3</td>
                                            <td class="p-2 border-r border-gray-100">Siti Aminah</td>
                                            <td class="p-2 border-r border-gray-100">0123456788</td>
                                            <td class="p-2 border-r border-gray-100">2021002</td>
                                            <td class="p-2 border-r border-gray-100 font-mono text-blue-600">2014-08-17
                                            </td>
                                            <td class="p-2 border-r border-gray-100 text-center">6B</td>
                                            <td class="p-2 font-bold text-amber-500">DITUNDA</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h2 class="text-md font-bold text-gray-700 mb-4">Data Kelulusan Tersimpan</h2>

                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 font-semibold">
                                    <th class="p-4">Nama Lengkap</th>
                                    <th class="p-4">NISN</th>
                                    <th class="p-4">Kelas</th>
                                    <th class="p-4">Tanggal Lahir</th>
                                    <th class="p-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-gray-600">
                                @forelse($dataKelulusan as $siswa)
                                <tr class="hover:bg-gray-50/80 transition-all">
                                    <td class="p-4 font-medium text-gray-800">{{ $siswa->nama }}</td>
                                    <td class="p-4 font-mono text-xs">{{ $siswa->nisn }}</td>
                                    <td class="p-4 text-gray-500">{{ $siswa->kelas ?? '-' }}</td>
                                    <td class="p-4 text-gray-500">{{
                                        \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') }}</td>

                                    <td class="p-4"
                                        x-data="statusUpdater({{ $siswa->id }}, '{{ $siswa->keterangan }}')">
                                        <div class="relative flex items-center justify-center">

                                            <select x-model="status" @change="updateStatus" :disabled="isLoading"
                                                class="appearance-none px-4 py-1.5 rounded-full text-xs font-bold cursor-pointer border-2 focus:ring-0 outline-none transition-all text-center w-32"
                                                :class="{
                                'bg-emerald-50 text-emerald-600 border-emerald-100 focus:border-emerald-300': status === 'LULUS',
                                'bg-rose-50 text-rose-600 border-rose-100 focus:border-rose-300': status === 'TIDAK LULUS',
                                'bg-amber-50 text-amber-600 border-amber-100 focus:border-amber-300': status === 'DITUNDA'
                            }">
                                                <option value="LULUS" class="text-emerald-600 bg-white">LULUS</option>
                                                <option value="TIDAK LULUS" class="text-rose-600 bg-white">TIDAK LULUS
                                                </option>
                                                <option value="DITUNDA" class="text-amber-600 bg-white">DITUNDA</option>
                                            </select>

                                            <div x-show="isLoading" class="absolute -right-6">
                                                <i class="fas fa-circle-notch fa-spin text-indigo-500 text-sm"></i>
                                            </div>
                                        </div>
                                    </td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-gray-400 italic">Belum ada data
                                        kelulusan yang di-import untuk sekolah Anda.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $dataKelulusan->links() }}
                    </div>
                </div>

            </div>

        </div>
    </div>
    <script>
        document.addEventListener('alpine:init', () => {
        Alpine.data('statusUpdater', (siswaId, initialStatus) => ({
            status: initialStatus,
            isLoading: false,

            async updateStatus() {
                this.isLoading = true;

                // Ambil CSRF Token
                const metaToken = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = metaToken ? metaToken.getAttribute('content') : '{{ csrf_token() }}';

                try {
                    // Ganti URL ini sesuai dengan route backend Anda
                    const response = await fetch(`/kelulusan/${siswaId}/update-status`, {
                        method: 'POST', // atau PUT/PATCH
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            keterangan: this.status
                        })
                    });

                    const result = await response.json();

                    if (!response.ok || result.status !== 'success') {
                        throw new Error('Gagal mengupdate status');
                    }

                    // Optional: Bisa tambahkan toast notifikasi sukses di sini

                } catch (error) {
                    alert('Terjadi kesalahan jaringan saat mengubah status.');
                    // Jika gagal, kembalikan dropdown ke status semula
                    this.status = initialStatus;
                } finally {
                    this.isLoading = false;
                }
            }
        }));
    });
    </script>
</x-app-layout>