<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Edit Jadwal: {{ $classroom->tingkat }} {{ $classroom->nama_kelas }} - Hari {{ $hari }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('jadwal.index', ['classroom_id' => $classroom->id]) }}"
                class="inline-flex min-h-10 items-center gap-1 font-medium text-brand-700 hover:text-brand-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:text-brand-300 dark:hover:text-brand-200">
                &larr; Kembali ke Jadwal
            </a>
            <button type="button" onclick="tambahBaris()"
                class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                Tambah Jam
            </button>
        </div>

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
            <form action="{{ route('jadwal.store') }}" method="POST">
                @csrf
                <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
                <input type="hidden" name="hari" value="{{ $hari }}">

                <!-- INPUT DURASI GLOBAL -->
                <div class="flex flex-wrap items-end gap-4 border-b border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-900/50">
                    <div>
                        <label for="durasi_menit" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Durasi per Mapel
                            (Menit)</label>
                        <input type="number" id="durasi_menit" value="35"
                            class="w-28 rounded-md border-slate-300 text-sm font-medium focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    </div>
                    <div class="text-sm leading-6 text-slate-600 dark:text-slate-300 flex-1">
                        <strong>Tips:</strong> Pilih Mata Pelajaran, maka jam akan terisi otomatis meneruskan baris di
                        atasnya. Jika opsi Mapel <strong>dikosongkan</strong> (untuk Istirahat/Upacara), jam harus Anda
                        ketik manual.
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-100 text-xs uppercase text-slate-700 dark:bg-slate-900 dark:text-slate-200">
                            <tr>
                                <th class="px-4 py-4 w-20 text-center">Jam Ke-</th>
                                <th class="px-4 py-4 w-36">Jam Mulai</th>
                                <th class="px-4 py-4 w-36">Jam Selesai</th>
                                <th class="px-4 py-4">Mata Pelajaran</th>
                                <th class="px-4 py-4">Keterangan (Opsional)</th>
                                <th class="px-4 py-4 w-16 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="jadwal-tbody">
                            @forelse($jadwal as $index => $item)
                            <tr class="baris-jadwal border-b border-slate-100 hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-700/40">
                                <td class="px-4 py-2 text-center">
                                    <input type="number" name="urutan_jam[]" value="{{ $item->urutan_jam }}"
                                        class="w-16 text-center text-sm border-slate-300 rounded-lg focus:ring-brand-500"
                                        required>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="time" name="jam_mulai[]"
                                        value="{{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}"
                                        class="w-full text-sm border-slate-300 rounded-lg focus:ring-brand-500"
                                        onchange="otomatisWaktu(this)" required>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="time" name="jam_selesai[]"
                                        value="{{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}"
                                        class="w-full text-sm border-slate-300 rounded-lg focus:ring-brand-500"
                                        required>
                                </td>
                                <td class="px-4 py-2">
                                    <select name="subject_id[]" onchange="otomatisWaktu(this)"
                                        class="w-full text-sm border-slate-300 rounded-lg focus:ring-brand-500">
                                        <option value="">-- Kosong (Istirahat / Upacara) --</option>
                                        @foreach($subjects as $mapel)
                                        <option value="{{ $mapel->id }}" {{ $item->subject_id == $mapel->id ? 'selected'
                                            : '' }}>
                                            {{ $mapel->nama_mapel }}
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="text" name="keterangan[]" value="{{ $item->keterangan }}"
                                        placeholder="Misal: Istirahat"
                                        class="w-full text-sm border-slate-300 rounded-lg focus:ring-brand-500">
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" onclick="hapusBaris(this)"
                                        class="text-rose-600 hover:text-rose-800 font-bold p-2">✕</button>
                                </td>
                            </tr>
                            @empty
                            <!-- Kosong: Akan di-handle oleh DOMContentLoaded -->
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div                 class="flex justify-end border-t border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-900/50">
                    <button type="submit"
                        class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT LOGIKA JADWAL -->
    <script>
        function otomatisWaktu(element) {
            // Ambil input durasi dari kolom paling atas
            const durasiInput = document.getElementById('durasi_menit');
            const durasi = parseInt(durasiInput.value) || 35; // Default 35 jika error/dikosongi

            // Cari elemen-elemen yang ada di baris yang sama dengan yang diklik
            const row = element.closest('tr');
            const selectMapel = row.querySelector('select[name="subject_id[]"]');
            const inputMulai = row.querySelector('input[name="jam_mulai[]"]');
            const inputSelesai = row.querySelector('input[name="jam_selesai[]"]');

            // JIKA MAPEL KOSONG (Istirahat), jangan lakukan apapun secara otomatis, biarkan manual
            if (selectMapel.value === "") {
                return;
            }

            // Jika fungsi ini dipicu karena user MEMILIH MAPEL
            if (element.tagName === 'SELECT') {
                const prevRow = row.previousElementSibling; // Cek baris di atasnya
                if (prevRow) {
                    const prevSelesai = prevRow.querySelector('input[name="jam_selesai[]"]').value;
                    if (prevSelesai) {
                        // Set jam mulai baris ini = jam selesai baris atasnya
                        inputMulai.value = prevSelesai;
                    }
                }
            }

            // Kalkulasi jam selesai (Jam Mulai + Durasi)
            if (inputMulai.value) {
                let [jam, menit] = inputMulai.value.split(':').map(Number);
                menit += durasi;

                if (menit >= 60) {
                    jam += Math.floor(menit / 60);
                    menit = menit % 60;
                }

                if (jam >= 24) {
                    jam = jam % 24; // Reset ke jam 00 jika lewat jam 23
                }

                // Format angka jadi 2 digit (misal: 07:05)
                const jamFormat = String(jam).padStart(2, '0');
                const menitFormat = String(menit).padStart(2, '0');

                inputSelesai.value = `${jamFormat}:${menitFormat}`;
            }
        }

        function tambahBaris() {
            const tbody = document.getElementById('jadwal-tbody');
            const rowCount = tbody.querySelectorAll('tr').length + 1;

            let opsiMapel = `<option value="">-- Kosong (Istirahat / Upacara) --</option>`;
            @foreach($subjects as $mapel)
                opsiMapel += `<option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>`;
            @endforeach

            const tr = document.createElement('tr');
            tr.className = 'border-b border-slate-100 baris-jadwal hover:bg-slate-50';
            tr.innerHTML = `
                <td class="px-4 py-2 text-center">
                    <input type="number" name="urutan_jam[]" value="${rowCount}" class="w-16 text-center text-sm border-slate-300 rounded-lg focus:ring-brand-500" required>
                </td>
                <td class="px-4 py-2">
                    <input type="time" name="jam_mulai[]" class="w-full text-sm border-slate-300 rounded-lg focus:ring-brand-500" onchange="otomatisWaktu(this)" required>
                </td>
                <td class="px-4 py-2">
                    <input type="time" name="jam_selesai[]" class="w-full text-sm border-slate-300 rounded-lg focus:ring-brand-500" required>
                </td>
                <td class="px-4 py-2">
                    <select name="subject_id[]" onchange="otomatisWaktu(this)" class="w-full text-sm border-slate-300 rounded-lg focus:ring-brand-500">
                        ${opsiMapel}
                    </select>
                </td>
                <td class="px-4 py-2">
                    <input type="text" name="keterangan[]" placeholder="Misal: Istirahat" class="w-full text-sm border-slate-300 rounded-lg focus:ring-brand-500">
                </td>
                <td class="px-4 py-2 text-center">
                    <button type="button" onclick="hapusBaris(this)" class="text-rose-600 hover:text-rose-800 font-bold p-2">✕</button>
                </td>
            `;
            tbody.appendChild(tr);
        }

        function hapusBaris(button) {
            const tr = button.closest('tr');
            tr.remove();

            // Susun ulang nomor "Jam Ke-" secara otomatis
            const semuaBaris = document.querySelectorAll('.baris-jadwal');
            semuaBaris.forEach((baris, index) => {
                baris.querySelector('input[name="urutan_jam[]"]').value = index + 1;
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById('jadwal-tbody');
            if(tbody.querySelectorAll('tr').length === 0) {
                tambahBaris();
            }
        });
    </script>
</x-app-layout>