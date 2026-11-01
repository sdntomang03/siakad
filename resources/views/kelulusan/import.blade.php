<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Data Kelulusan</h2>

            @if($dataKelulusan->count() > 0)
            <form action="{{ route('kelulusan.delete-all') }}" method="POST"
                onsubmit="return confirm('PENTING: Apakah Anda yakin ingin MENGHAPUS SEMUA data kelulusan? Tindakan ini tidak dapat dibatalkan!');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-md border border-rose-200 px-3 py-1.5 text-sm font-medium text-rose-700 transition hover:bg-rose-700 hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-700">
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

    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">

            <div>
                <h1 class="text-2xl font-semibold text-slate-950 dark:text-white">Import Data Kelulusan Murid</h1>
                <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-slate-300">Halaman untuk mengunggah data kelulusan siswa melalui file
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

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

                <div class="h-fit rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 lg:col-span-1 sm:p-6"
                    x-data="{ isUploading: false }">

                    <h2 class="text-md font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Unggah Berkas Excel
                    </h2>

                    <form action="{{ route('kelulusan.import.process') }}" method="POST" enctype="multipart/form-data"
                        @submit="isUploading = true">
                        @csrf
                        <div class="mb-5">
                            <label for="file" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Pilih
                                File (.xlsx, .xls)</label>
                            <input id="file" type="file" name="file" required
                                class="block min-h-11 w-full rounded-md border border-slate-300 bg-white p-2 text-sm text-slate-700 file:mr-4 file:min-h-9 file:border-0 file:bg-brand-50 file:px-4 file:font-semibold file:text-brand-800 hover:file:bg-brand-100 focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">
                            @error('file')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" :disabled="isUploading"
                            class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-md bg-brand-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 disabled:cursor-wait disabled:opacity-60">

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

                    <div class="mt-6 bg-amber-50/60 p-4 rounded-md border border-amber-100">
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
                            <li>Urutan kolom: <code class="bg-amber-100 px-1 py-0.5 rounded font-mono">nama</code>,
                                <code class="bg-amber-100 px-1 py-0.5 rounded font-mono">nisn</code>,
                                <code class="bg-amber-100 px-1 py-0.5 rounded font-mono">nipd</code>,
                                <code class="bg-amber-100 px-1 py-0.5 rounded font-mono">tempat_lahir</code>,
                                <code class="bg-amber-100 px-1 py-0.5 rounded font-mono">tanggal_lahir</code>,
                                <code class="bg-amber-100 px-1 py-0.5 rounded font-mono">kelas</code>,
                                <code class="bg-amber-100 px-1 py-0.5 rounded font-mono">nomor_skl</code>, dan
                                <code class="bg-amber-100 px-1 py-0.5 rounded font-mono">keterangan</code>.</li>
                            <li>Atur kolom <code class="bg-amber-100 px-1 py-0.5 rounded font-mono">tanggal_lahir</code>
                                sebagai teks dengan format <code class="font-bold">YYYY-MM-DD</code>.</li>
                            <li>Pilihan isi kolom keterangan: <span class="font-semibold">LULUS / TIDAK LULUS /
                                    DITUNDA</span>.</li>
                        </ul>
                        <div class="mt-6 border border-gray-200 rounded-md overflow-hidden shadow-sm">
                            <div
                                class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <h3 class="text-sm font-semibold text-slate-800">Struktur Kolom Excel</h3>
                                </div>
                                <span
                                    class="text-xs font-medium text-slate-500">A-H</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse text-left text-xs whitespace-nowrap">
                                    <thead>
                                        <tr class="border-b border-slate-200 bg-slate-100 text-center font-semibold text-slate-500">
                                            <th class="border-r border-slate-200 p-2">Kolom</th>
                                            <th class="border-r border-slate-200 p-2">A</th>
                                            <th class="border-r border-slate-200 p-2">B</th>
                                            <th class="border-r border-slate-200 p-2">C</th>
                                            <th class="border-r border-slate-200 p-2">D</th>
                                            <th class="border-r border-slate-200 p-2">E</th>
                                            <th class="border-r border-slate-200 p-2">F</th>
                                            <th class="border-r border-slate-200 p-2">G</th>
                                            <th class="p-2">H</th>
                                        </tr>
                                        <tr class="bg-brand-50 text-brand-900">
                                            <th class="border-r border-slate-200 p-2 text-left">Nama field</th>
                                            <th class="border-r border-slate-200 p-2">nama</th>
                                            <th class="border-r border-slate-200 p-2">nisn</th>
                                            <th class="border-r border-slate-200 p-2">nipd</th>
                                            <th class="border-r border-slate-200 p-2">tempat_lahir</th>
                                            <th class="border-r border-slate-200 p-2">tanggal_lahir</th>
                                            <th class="border-r border-slate-200 p-2">kelas</th>
                                            <th class="border-r border-slate-200 p-2">nomor_skl</th>
                                            <th class="p-2">keterangan</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <p class="px-4 py-3 text-sm text-slate-600">Contoh ini hanya menunjukkan nama kolom. Isi file dengan data siswa yang sebenarnya.</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6 lg:col-span-2">
                    <h2 class="mb-4 text-lg font-semibold text-slate-950 dark:text-white">Data Kelulusan Tersimpan</h2>

                    <div class="overflow-x-auto rounded-md border border-slate-200 dark:border-slate-700">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 font-semibold">
                                    <th class="p-4">Nama Lengkap</th>
                                    <th class="p-4">NISN</th>
                                    <th class="p-4">Kelas</th>
                                    <th class="p-4">Tempat Lahir</th>
                                    <th class="p-4">Tanggal Lahir</th>
                                    <th class="p-4 text-center">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 dark:divide-slate-700 dark:text-slate-300">
                                @forelse($dataKelulusan as $siswa)
                                <tr class="hover:bg-gray-50/80 transition-all">
                                    <td class="p-4 font-medium text-gray-800">{{ $siswa->nama }}</td>
                                    <td class="p-4 font-mono text-xs">{{ $siswa->nisn }}</td>
                                    <td class="p-4 text-gray-500">{{ $siswa->kelas ?? '-' }}</td>
                                    <td class="p-4 text-gray-500">{{ $siswa->tempat_lahir ?? '-' }}</td>
                                    <td class="p-4">
                                        {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') }}
                                    </td>
                                    <td class="p-4"
                                        x-data="statusUpdater({{ $siswa->id }}, '{{ $siswa->keterangan }}')">
                                        <div class="relative flex items-center justify-center">

                                            <select x-model="status" @change="updateStatus" :disabled="isLoading"
                                                aria-label="Status kelulusan {{ $siswa->nama }}"
                                                class="min-h-10 appearance-none rounded-md border px-3 py-1.5 text-sm font-medium text-center focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700"
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
                                                <i class="fas fa-circle-notch fa-spin text-brand-500 text-sm"></i>
                                            </div>
                                        </div>
                                    </td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-500 italic">Belum ada data
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