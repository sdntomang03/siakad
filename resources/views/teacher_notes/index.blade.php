<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 uppercase tracking-tight">
                    Buku Jurnal & Catatan Kelas
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Tahun Ajaran: {{ $activeYear->tahun_ajaran ?? '-' }} - {{ $activeYear->semester ?? '-' }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                {{-- Tombol Rekap Baru --}}
                @hasanyrole('superadmin|operator|guru|kepsek')
                <a href="{{ route('teacher-notes.report') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-indigo-600 dark:text-indigo-400 shadow-sm hover:bg-slate-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2a4 4 0 00-4-4H5m11 0h2a4 4 0 004 4v2m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Lihat Rekap Per Siswa
                </a>
                @endhasanyrole

                <a href="{{ url()->previous() }}" class="text-sm font-bold text-slate-500 hover:text-slate-700">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 1. PILIH KELAS --}}
            @hasanyrole('superadmin|operator|guru|kepsek')
            <div
                class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <form action="{{ route('teacher-notes.index') }}" method="GET"
                    class="flex flex-col md:flex-row items-end gap-4">
                    <div class="w-full md:w-1/3">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Pilih Kelas</label>
                        <select name="classroom_id" onchange="this.form.submit()"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($myClassrooms as $cls)
                            <option value="{{ $cls->id }}" {{ request('classroom_id')==$cls->id ? 'selected' : '' }}>
                                Kelas {{ $cls->tingkat }} - {{ $cls->nama_kelas }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            @endhasanyrole

            @if($selectedClassroom)

            {{-- 2. FORM INPUT KEJADIAN (Hanya Guru/Admin yang bisa input) --}}
            @hasanyrole('guru|superadmin|operator')
            <form action="{{ route('teacher-notes.store') }}" method="POST" enctype="multipart/form-data"
                x-data="{ selectAll: false }">
                @csrf
                <input type="hidden" name="classroom_id" value="{{ $selectedClassroom->id }}">

                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden mb-8">

                    {{-- HEADER CATATAN --}}
                    <div
                        class="p-6 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700 space-y-4">
                        <h3 class="text-lg font-black text-slate-800 dark:text-white">Form Jurnal Kejadian</h3>

                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="w-full md:w-1/3">
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Jenis
                                    Catatan</label>
                                <select name="jenis_catatan" required
                                    class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 text-sm focus:ring-indigo-500 font-bold text-indigo-700 dark:text-indigo-400">
                                    <option value="Catatan Wali Kelas">Catatan Wali Kelas</option>
                                    <option value="Prestasi Anak">Prestasi / Penghargaan</option>
                                    <option value="Pelanggaran / Kedisiplinan">Pelanggaran / Kedisiplinan</option>
                                    <option value="Perkembangan Sikap">Perkembangan Sikap</option>
                                </select>
                            </div>
                            <div class="w-full md:w-2/3 flex items-center pt-2 md:pt-6">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="is_for_report" value="1" checked
                                        class="w-6 h-6 text-indigo-600 bg-white border-slate-300 rounded focus:ring-indigo-500 dark:bg-slate-800 dark:border-slate-600">
                                    <div>
                                        <span class="block font-bold text-slate-700 dark:text-slate-200">Tampilkan di
                                            Raport E-Rapor</span>
                                        <span class="block text-xs text-slate-500">Hapus centang jika ini hanya untuk
                                            arsip rahasia wali kelas.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>

                            <div class="flex flex-col md:flex-row gap-4">
                                <div class="w-full md:w-2/3">
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Deskripsi
                                        Kejadian / Pesan</label>
                                    <textarea name="catatan" required rows="3"
                                        placeholder="Contoh: Terpilih menjadi perwakilan sekolah..."
                                        class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 text-sm focus:ring-indigo-500"></textarea>
                                </div>
                                <div class="w-full md:w-1/3">
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Lampiran Foto
                                        (Opsional)</label>
                                    <input type="file" name="foto" accept="image/jpeg, image/png, image/webp"
                                        class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/50 dark:file:text-indigo-400 cursor-pointer">
                                    <p class="text-[10px] text-slate-400 mt-2">*Maksimal 2MB. Format: JPG, PNG, WEBP.
                                    </p>
                                    @error('foto')
                                    <p class="text-sm text-rose-500 font-bold mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            @error('student_ids')
                            <p class="text-sm text-rose-500 font-bold mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- DAFTAR SISWA YANG TERLIBAT --}}
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-white">Pilih Siswa yang Terlibat</h4>
                                <p class="text-xs text-slate-500">Centang nama anak yang berkaitan dengan catatan di
                                    atas.</p>
                            </div>
                            <label
                                class="flex items-center gap-2 text-sm font-bold text-indigo-600 cursor-pointer bg-indigo-50 px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition">
                                <input type="checkbox" x-model="selectAll" class="rounded text-indigo-600">
                                Pilih Semua Siswa
                            </label>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($students as $siswa)
                            <label
                                class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer transition">
                                <input type="checkbox" name="student_ids[]" value="{{ $siswa->id }}"
                                    x-bind:checked="selectAll"
                                    class="w-5 h-5 text-indigo-600 bg-white border-slate-300 rounded focus:ring-indigo-500">
                                <div>
                                    <span class="block font-bold text-slate-700 dark:text-slate-200">{{
                                        $siswa->nama_lengkap }}</span>
                                    <span class="block text-[10px] text-slate-400">NISN: {{ $siswa->nisn ?? '-'
                                        }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>

                        <div class="mt-8 border-t border-slate-100 dark:border-slate-700 pt-6 text-right">
                            <button type="submit"
                                class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-bold shadow-lg hover:bg-indigo-700 transition transform hover:-translate-y-1">
                                Simpan Kejadian &rarr;
                            </button>
                        </div>
                    </div>

                </div>
            </form>
            @endhasanyrole

            {{-- 3. TABEL RIWAYAT KEJADIAN KELAS --}}
            {{-- 3. TABEL RIWAYAT KEJADIAN KELAS --}}
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-lg font-black text-slate-800 dark:text-white">Riwayat Jurnal Kelas</h3>
                    <p class="text-sm text-slate-500">Catatan kejadian yang telah tersimpan di semester ini.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead
                            class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                            <tr>
                                <th class="px-6 py-3 w-32">Tanggal</th>
                                <th class="px-6 py-3 w-48">Siswa Terlibat</th>
                                <th class="px-6 py-3 w-40">Jenis</th>
                                <th class="px-6 py-3">Deskripsi Kejadian</th>
                                <th class="px-6 py-3 text-center w-24">Raport</th>
                                <th class="px-6 py-3 text-right w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">

                            {{-- Looping Dimulai di Sini --}}
                            @forelse($riwayatCatatan as $riwayat)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                <td class="px-6 py-4 font-bold text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                    {{ $riwayat->created_at->format('d M Y') }}
                                    <span class="block text-[10px] font-normal text-slate-400">{{
                                        $riwayat->created_at->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">
                                    {{ $riwayat->student->nama_lengkap ?? 'Siswa dihapus' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded text-[10px] font-black uppercase tracking-wider">
                                        {{ $riwayat->jenis_catatan }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-slate-600 dark:text-slate-400">{{ $riwayat->catatan }}</p>

                                    {{-- Tampilkan tautan foto jika ada --}}
                                    @if($riwayat->foto)
                                    {{-- Container Alpine.js untuk Modal Foto --}}
                                    <div x-data="{ imgModal: false }">
                                        {{-- Tombol Pemicu --}}
                                        <button type="button" @click="imgModal = true"
                                            class="inline-flex items-center gap-1 mt-2 px-2 py-1 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-indigo-600 dark:text-indigo-400 rounded text-xs font-bold hover:bg-indigo-50 transition">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            Lihat Lampiran
                                        </button>

                                        {{-- Struktur Modal --}}
                                        <template x-teleport="body">
                                            <div x-show="imgModal" x-transition:enter="transition ease-out duration-300"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="transition ease-in duration-200"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                                class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-slate-900/90 backdrop-blur-sm"
                                                style="display: none;">

                                                {{-- Tombol Tutup di Area Gelap --}}
                                                <div class="absolute inset-0 cursor-zoom-out" @click="imgModal = false">
                                                </div>

                                                {{-- Konten Gambar --}}
                                                <div class="relative max-w-4xl w-full flex flex-col items-center">
                                                    <img :src="'{{ asset('storage/' . $riwayat->foto) }}'"
                                                        class="max-h-[85vh] rounded-lg shadow-2xl border-4 border-white dark:border-slate-700 object-contain">

                                                    <button @click="imgModal = false"
                                                        class="mt-4 px-6 py-2 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-full font-bold shadow-lg hover:bg-rose-500 hover:text-white transition">
                                                        Tutup Gambar
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($riwayat->is_for_report)
                                    <span class="text-emerald-500" title="Akan tercetak di Raport">
                                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </span>
                                    @else
                                    <span class="text-slate-300 dark:text-slate-600" title="Hanya arsip internal">
                                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </span>
                                    @endif
                                </td>

                                {{-- Kolom Aksi berada DI DALAM tr dan forelse --}}
                                <td class="px-6 py-4 flex justify-end gap-2">
                                    @hasanyrole('guru|superadmin|operator')

                                    {{-- Tombol Hapus 1 Siswa Saja --}}
                                    <form
                                        action="{{ route('teacher-notes.destroy', ['id' => $riwayat->id, 'mode' => 'siswa']) }}"
                                        method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Hapus catatan ini HANYA untuk {{ $riwayat->student->nama_lengkap ?? 'siswa ini' }}?')"
                                            class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-lg transition"
                                            title="Hapus untuk siswa ini saja">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>

                                    {{-- Tombol Hapus 1 Kejadian (Semua Siswa Terkait) --}}
                                    <form
                                        action="{{ route('teacher-notes.destroy', ['id' => $riwayat->id, 'mode' => 'kejadian']) }}"
                                        method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Hapus SELURUH kejadian ini? Ini akan ikut menghapus catatan dari teman-temannya yang dicentang bersamaan.')"
                                            class="p-2 text-rose-700 bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/50 dark:text-rose-400 dark:hover:bg-rose-900 border border-rose-100 dark:border-rose-800 rounded-lg transition flex items-center gap-1"
                                            title="Hapus seluruh kejadian">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                    </form>

                                    @endhasanyrole
                                </td>
                            </tr>
                            @empty
                            <tr>
                                {{-- Colspan diperbarui menjadi 6 --}}
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    Belum ada riwayat kejadian yang dicatat di kelas ini.
                                </td>
                            </tr>
                            @endforelse
                            {{-- Looping Berakhir di Sini --}}

                        </tbody>
                    </table>
                </div>
            </div>

            @endif

        </div>
    </div>
</x-app-layout>