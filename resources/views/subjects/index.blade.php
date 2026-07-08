<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
            Manajemen Mata Pelajaran
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @role('superadmin')
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <form action="{{ route('subjects.index') }}" method="GET">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Pilih Sekolah</label>
                <select name="school_id" onchange="this.form.submit()"
                    class="w-full md:w-1/2 rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500">
                    <option value="">-- Pilih Sekolah Terlebih Dahulu --</option>
                    @foreach($schools as $sekolah)
                    <option value="{{ $sekolah->id }}" {{ $selectedSchoolId==$sekolah->id ? 'selected' : '' }}>
                        {{ $sekolah->npsn }} - {{ $sekolah->nama_sekolah }}
                    </option>
                    @endforeach
                </select>
            </form>
        </div>
        @endrole

        @if(auth()->user()->hasRole('superadmin') && !$selectedSchoolId)
        <div
            class="text-center py-16 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <h3 class="text-xl font-bold text-slate-800 dark:text-white">Pilih Sekolah</h3>
            <p class="text-slate-500 mt-2">Pilih sekolah di atas untuk mengelola mata pelajarannya.</p>
        </div>
        @else

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-1">
                <div
                    class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 sticky top-6">
                    <h3 class="text-lg font-black text-slate-800 dark:text-white mb-1">Tambah Mapel Cepat</h3>
                    <p class="text-xs text-slate-500 mb-6">Pilih banyak mapel sekaligus untuk diterapkan ke kelas.</p>

                    <form action="{{ route('subjects.store') }}" method="POST" class="space-y-5">
                        @csrf
                        @if(auth()->user()->hasRole('superadmin'))
                        <input type="hidden" name="school_id" value="{{ $selectedSchoolId }}">
                        @endif

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">1. Pilih Tingkat
                                Kelas</label>
                            <div class="grid grid-cols-3 gap-2">
                                @for($i = 1; $i <= 6; $i++) <label
                                    class="flex items-center gap-1.5 p-2 border border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                    <input type="checkbox" name="tingkat[]" value="{{ $i }}"
                                        class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4 cursor-pointer">
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Kelas {{ $i
                                        }}</span>
                                    </label>
                                    @endfor
                            </div>
                            @error('tingkat') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">2. Pilih Mata
                                Pelajaran</label>

                            @php
                            $defaultSubjects = [
                            'Pendidikan Agama dan Budi Pekerti',
                            'Pendidikan Pancasila',
                            'Bahasa Indonesia',
                            'Matematika',
                            'Pendidikan Jasmani Olahraga dan Kesehatan',
                            'Ilmu Pengetahuan Alam dan Sosial',
                            'Seni dan Budaya',
                            'Pendidikan Lingkungan dan Budaya Jakarta',
                            'Bahasa Inggris'
                            ];
                            @endphp

                            <div
                                class="space-y-1.5 max-h-48 overflow-y-auto p-3 border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/30">
                                @foreach($defaultSubjects as $subject)
                                <label class="flex items-start gap-2 cursor-pointer group">
                                    <input type="checkbox" name="mapel_default[]" value="{{ $subject }}"
                                        class="mt-0.5 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4 cursor-pointer">
                                    <span
                                        class="text-xs font-semibold text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 transition">{{
                                        $subject }}</span>
                                </label>
                                @endforeach
                            </div>
                            @error('mapel_error') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div
                            class="p-3 border border-dashed border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50/50 dark:bg-slate-800/50">
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">+ Mapel Kustom
                                (Opsional)</label>
                            <input type="text" name="nama_mapel_kustom" placeholder="Nama Mapel (Cth: Muatan Lokal)"
                                class="w-full mb-2 rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-xs focus:ring-indigo-500">
                            <input type="text" name="kode_mapel_kustom" placeholder="Kode Mapel (Opsional)"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-xs focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">3. Pengaturan
                                Atribut</label>
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <label
                                    class="flex items-center gap-2 p-2 border border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer hover:bg-slate-50 transition bg-white dark:bg-slate-800">
                                    <input type="radio" name="pengampu" value="guru_kelas" checked
                                        class="text-indigo-600 focus:ring-indigo-500 w-3 h-3">
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Wali Kelas</span>
                                </label>
                                <label
                                    class="flex items-center gap-2 p-2 border border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer hover:bg-slate-50 transition bg-white dark:bg-slate-800">
                                    <input type="radio" name="pengampu" value="guru_mapel"
                                        class="text-indigo-600 focus:ring-indigo-500 w-3 h-3">
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Guru Mapel</span>
                                </label>
                            </div>

                            <div
                                class="flex items-center justify-between border border-slate-200 dark:border-slate-700 p-2 rounded-lg bg-white dark:bg-slate-800">
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 ml-1">Nilai KKM
                                    Minimum</span>
                                <input type="number" name="kkm" value="75" min="0" max="100" required
                                    class="w-16 h-8 text-center rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-xs font-bold text-slate-700 p-1 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="w-full px-4 py-3 bg-indigo-600 text-white rounded-xl text-sm font-black shadow-lg hover:bg-indigo-700 transition transform hover:-translate-y-0.5">
                                Eksekusi Tambah Mapel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                @forelse($subjects as $tingkat => $mapels)

                <form action="{{ route('subjects.bulkDestroy') }}" method="POST"
                    onsubmit="return confirm('Yakin ingin menghapus semua mata pelajaran yang dipilih di Kelas {{ $tingkat }}?');">
                    @csrf
                    @method('DELETE')

                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">

                        <div
                            class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-lg flex items-center justify-center font-black">
                                    {{ $tingkat }}
                                </div>
                                <h4 class="font-black text-slate-800 dark:text-white uppercase tracking-tight">Tingkat
                                    Kelas {{ $tingkat }}</h4>
                            </div>

                            <div class="flex items-center gap-2">
                                <span
                                    class="text-xs font-bold text-slate-500 bg-white dark:bg-slate-800 px-2 py-1 rounded-md border border-slate-200 dark:border-slate-700">
                                    {{ count($mapels) }} Mapel
                                </span>

                                <button type="button" onclick="simpanUrutanMassal({{ $tingkat }})"
                                    class="text-xs font-bold text-white bg-indigo-500 hover:bg-indigo-600 px-3 py-1.5 rounded-lg shadow-sm transition">
                                    Simpan Urutan
                                </button>

                                <button type="submit"
                                    class="text-xs font-bold text-white bg-rose-500 hover:bg-rose-600 px-3 py-1.5 rounded-lg shadow-sm transition">
                                    Hapus Terpilih
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-slate-500 uppercase bg-slate-50/50 dark:bg-slate-900/30">
                                    <tr>
                                        <th class="px-6 py-3 w-16">Kode</th>
                                        <th class="px-6 py-3">Nama Mata Pelajaran</th>
                                        <th class="px-6 py-3 text-center w-24">Urutan</th>
                                        <th class="px-6 py-3 text-center w-24">KKM</th>
                                        <th class="px-6 py-3 text-center w-28">Sidanira</th>
                                        <th class="px-6 py-3 text-right w-24">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                    @foreach($mapels as $mapel)
                                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition group">
                                        <td class="px-6 py-3 font-bold text-slate-400">{{ $mapel->kode_mapel ?? '-' }}
                                        </td>
                                        <td class="px-6 py-3 font-bold text-slate-700 dark:text-slate-200">{{
                                            $mapel->nama_mapel }}</td>
                                        <td class="px-6 py-3 text-center">
                                            <input type="number" min="1" value="{{ $mapel->urutan ?? 0 }}"
                                                data-id="{{ $mapel->id }}"
                                                class="input-urutan-{{ $tingkat }} w-16 text-center rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500 py-1 font-bold text-indigo-700">
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            <span
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 font-bold text-xs">
                                                {{ $mapel->kkm }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-3 text-center">
                                            @if(in_array($tingkat, [4, 5, 6]))
                                            <button type="button"
                                                onclick="toggleSidaniraInstan('{{ route('subjects.toggle-sidanira', $mapel->id) }}', this)"
                                                data-active="{{ $mapel->is_sidanira ? 'true' : 'false' }}"
                                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center justify-center rounded-full focus:outline-none transition-colors duration-200 ease-in-out {{ $mapel->is_sidanira ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-600' }}"
                                                title="Klik untuk mengubah status">
                                                <span aria-hidden="true"
                                                    class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $mapel->is_sidanira ? 'translate-x-2.5' : '-translate-x-2.5' }}"></span>
                                            </button>
                                            @else
                                            <span class="text-xs text-slate-400 italic">N/A</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-3 text-right">
                                            <form action="{{ route('subjects.destroy', $mapel->id) }}" method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus mapel {{ $mapel->nama_mapel }} untuk kelas {{ $tingkat }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-lg transition"
                                                    title="Hapus Mapel">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>

                @foreach($mapels as $mapel)
                <form id="form-hapus-satuan-{{ $mapel->id }}" action="{{ route('subjects.destroy', $mapel->id) }}"
                    method="POST" class="hidden" onsubmit="return confirm('Hapus mapel {{ $mapel->nama_mapel }}?');">
                    @csrf
                    @method('DELETE')
                </form>
                @endforeach

                @empty
                @endforelse
            </div>

        </div>
        @endif
    </div>
    <script>
        function toggleSelectAll(tingkat) {
            // Ambil status dari checkbox "Pilih Semua"
            let selectAllCheckbox = document.getElementById('selectAll-' + tingkat);
            let isChecked = selectAllCheckbox.checked;

            // Cari semua checkbox individu di dalam tabel tingkat tersebut
            let rowCheckboxes = document.querySelectorAll('.row-checkbox-' + tingkat);

            // Samakan statusnya
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
        }
    </script>
    <script>
        function toggleSidaniraInstan(url, btnElement) {
            // 1. Ambil status saat ini
            let isActive = btnElement.getAttribute('data-active') === 'true';
            let newState = !isActive;
            let spanElement = btnElement.querySelector('span');

            // 2. Ubah UI secara instan (Optimistic UI Update) biar terasa cepat tanpa loading
            if (newState) {
                // Berubah jadi ON (Biru)
                btnElement.classList.remove('bg-slate-200', 'dark:bg-slate-600');
                btnElement.classList.add('bg-indigo-600');
                spanElement.classList.remove('-translate-x-2.5');
                spanElement.classList.add('translate-x-2.5');
            } else {
                // Berubah jadi OFF (Abu-abu)
                btnElement.classList.remove('bg-indigo-600');
                btnElement.classList.add('bg-slate-200', 'dark:bg-slate-600');
                spanElement.classList.remove('translate-x-2.5');
                spanElement.classList.add('-translate-x-2.5');
            }

            // Update data attribute
            btnElement.setAttribute('data-active', newState ? 'true' : 'false');

            // 3. Lakukan request ke server di latar belakang (Background Request)
            fetch(url, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // Jika gagal dari server, tampilkan alert dan kembalikan UI seperti semula (refresh)
                    alert('Gagal mengubah status: ' + data.message);
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan jaringan.');
                window.location.reload();
            });
        }
    </script>
    <script>
        function simpanUrutanMassal(tingkat) {
            // Ambil semua input urutan pada kelas yang dipilih
            let inputs = document.querySelectorAll('.input-urutan-' + tingkat);
            let dataUrutan = {};

            // Format ke dalam bentuk Object: { id_mapel: nilai_urutan }
            inputs.forEach(input => {
                let id = input.getAttribute('data-id');
                let val = input.value;
                if(val) {
                    dataUrutan[id] = val;
                }
            });

            // Kirim data ke server
            fetch('{{ route("subjects.bulk-update-urutan") }}', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ urutan: dataUrutan })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert(data.message);
                    window.location.reload(); // Muat ulang untuk melihat hasil susunan baru
                } else {
                    alert('Gagal menyimpan urutan.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan jaringan.');
            });
        }
    </script>
</x-app-layout>