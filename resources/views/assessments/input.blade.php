<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">
            Input Nilai: <span class="text-brand-600">{{ $assessment->assessmentType->nama }}</span>
        </h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <div
            class="grid grid-cols-1 gap-4 rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:grid-cols-2 md:grid-cols-4">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Kelas</p>
                <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $assessment->classroom->tingkat }} - {{
                    $assessment->classroom->nama_kelas }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Mata Pelajaran</p>
                <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $assessment->subject->nama_mapel }}</p>
            </div>
            <div class="sm:col-span-2 md:col-span-2">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Materi / Keterangan</p>
                <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $assessment->keterangan }}</p>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div
                class="border-b border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900/50">
                <span class="text-sm font-bold text-slate-600 dark:text-slate-300">Daftar Siswa ({{ $students->count()
                    }} Orang)</span>
            </div>

            <form action="{{ route('assessments.update-scores', $assessment->id) }}" method="POST">
                @csrf

                <div class="overflow-x-auto">
                    <table class="min-w-[42rem] w-full text-left text-sm text-slate-600 dark:text-slate-300">
                        <thead
                            class="text-xs text-slate-700 uppercase bg-slate-100/50 dark:bg-slate-900/50 dark:text-slate-300">
                            <tr>
                                <th class="px-6 py-4 w-16 text-center">No</th>
                                <th class="px-6 py-4 w-40">NISN / NIPD</th>
                                <th class="px-6 py-4">Nama Lengkap Siswa</th>
                                <th class="px-6 py-4 w-40 text-center">Skor / Nilai (0-100)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $siswa)
                            <tr
                                class="border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                <td class="px-6 py-4 font-bold text-center">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <span class="block font-semibold text-slate-800 dark:text-slate-200">{{ $siswa->nisn
                                        ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">{{
                                    $siswa->nama_lengkap }}</td>
                                <td class="px-6 py-4">
                                    <input type="number" name="scores[{{ $siswa->id }}]" data-nisn="{{ $siswa->nisn }}"
                                        value="{{ $existingScores[$siswa->id] ?? '' }}" min="0" max="100" step="0.01"
                                        class="score-input w-full rounded-md border-slate-300 text-center font-semibold text-brand-700 focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-brand-300"
                                        placeholder="-">
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                    Belum ada siswa di kelas ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($students->count() > 0)
                <div
                    class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900/30 sm:flex-row sm:justify-end sm:p-6">

                    {{-- Tombol Baru: Import JSON --}}
                    <button type="button" onclick="importJSON()"
                        class="inline-flex min-h-11 items-center justify-center rounded-md bg-slate-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-700 dark:bg-slate-600 dark:hover:bg-slate-500">
                        Paste JSON CBT
                    </button>

                    <a href="{{ route('assessments.create') }}"
                        class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700">Batal</a>

                    <button type="submit"
                        class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                        Simpan Nilai
                    </button>
                </div>
                @endif
            </form>
        </div>

    </div>

    {{-- Tambahkan script SweetAlert2 jika belum ada di layout global --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function importJSON() {
            Swal.fire({
                title: 'Import Nilai CBT',
                input: 'textarea',
                inputPlaceholder: 'Paste (CTRL+V) kode JSON yang dicopy dari CBT di sini...',
                inputAttributes: {
                    'aria-label': 'Paste JSON',
                    'rows': 5
                },
                showCancelButton: true,
                confirmButtonText: 'Proses Import',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#4f46e5', // Warna indigo
                inputValidator: (value) => {
                    if (!value) {
                        return 'Teks JSON tidak boleh kosong!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    try {
                        // Mencoba mengubah teks menjadi format objek/array
                        const data = JSON.parse(result.value);
                        let countSuccess = 0;
                        let countNotFound = 0;

                        // Perulangan untuk mencocokkan tiap data dari CBT ke tabel Siakad
                        data.forEach(item => {
                            // Mengabaikan jika NISN kosong/null dari JSON
                            if (!item.nisn) return;

                            // Mencari elemen input yang atribut data-nisn nya sama persis
                            const inputField = document.querySelector(`.score-input[data-nisn="${item.nisn}"]`);

                            if (inputField) {
                                inputField.value = item.nilai;
                                countSuccess++;
                            } else {
                                countNotFound++;
                            }
                        });

                        // Menampilkan notifikasi sukses
                        Swal.fire({
                            icon: 'success',
                            title: 'Selesai Import!',
                            text: `${countSuccess} nilai siswa berhasil diisi ke dalam form. ${countNotFound > 0 ? '('+countNotFound+' NISN tidak cocok/tidak ada di kelas ini)' : ''}`,
                        });

                    } catch (e) {
                        // Error handling jika yang di-paste bukan JSON (misal asal ketik text)
                        Swal.fire({
                            icon: 'error',
                            title: 'Format Gagal Dibaca',
                            text: 'Pastikan data yang di-paste adalah JSON yang valid hasil copy dari tombol CBT.',
                        });
                    }
                }
            });
        }
    </script>
</x-app-layout>