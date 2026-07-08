<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
                Manajemen <span class="text-indigo-600">Nilai Ujian</span>
            </h2>

            <a href="{{ route('dashboard') }}"
                class="text-sm font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                &larr; Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Alert Messages -->
            @if(session('success'))
            <div
                class="flex items-center p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 text-sm font-bold shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div
                class="flex items-center p-4 rounded-xl bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-400 text-sm font-bold shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('error') }}
            </div>
            @endif

            <!-- Grid Atas: Menu Input Kelas & Form Import -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- KIRI: MENU INPUT NILAI KELAS (BULK) -->
                <div
                    class="bg-indigo-600 rounded-2xl shadow-lg border border-indigo-700 overflow-hidden relative p-8 flex flex-col justify-center items-center text-center">
                    <div class="text-white/80 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-2">Input Nilai Per Kelas</h3>
                    <p class="text-indigo-200 text-sm mb-6">Pilih kelas dan mata pelajaran, lalu ketik nilai seluruh
                        siswa dalam format tabel buku nilai.</p>

                    <a href="{{ route('exam-grades.createBulk') }}"
                        class="bg-white text-indigo-700 hover:bg-slate-50 px-6 py-3 rounded-xl font-black uppercase text-sm shadow-md transition transform hover:-translate-y-0.5 inline-flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                            <path fill-rule="evenodd"
                                d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                clip-rule="evenodd" />
                        </svg>
                        Buka Form Input Kelas
                    </a>
                </div>

                <!-- KANAN: FORM IMPORT EXCEL -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div
                        class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center">
                        <h3 class="text-base font-black text-slate-800 dark:text-white flex items-center">
                            Import Massal (Excel)
                        </h3>
                        <!-- Tombol Unduh Template -->
                        <div class="flex items-center gap-2">
                            <select id="tingkat_template"
                                class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-xs py-1 h-8 focus:ring-indigo-500">

                                <option value="6">Kls 6</option>
                            </select>

                            <button type="button" onclick="unduhTemplateUjian()" title="Unduh Template"
                                class="flex items-center justify-center bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 px-3 py-1 h-8 rounded-lg font-bold border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 transition whitespace-nowrap text-xs">
                                Unduh Template
                            </button>
                        </div>
                    </div>
                    <form action="{{ route('exam-grades.import') }}" method="POST" enctype="multipart/form-data"
                        class="p-6">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kategori Ujian
                                    (Wajib)</label>
                                <select name="kategori_ujian"
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500"
                                    required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="Ujian Sekolah">Ujian Sekolah (Tulis)</option>
                                    <option value="Ujian Praktik">Ujian Praktik</option>
                                    <option value="Ujian Tengah Semester">Ujian Tengah Semester</option>
                                    <option value="Ujian Akhir Semester">Ujian Akhir Semester</option>
                                </select>
                            </div>

                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Unggah
                                    Dokumen</label>
                                <input type="file" name="file_excel" required accept=".csv, .xls, .xlsx"
                                    class="block w-full text-sm text-slate-500 dark:text-slate-400
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-xl file:border-0
                                    file:text-xs file:font-black file:uppercase
                                    file:bg-indigo-50 file:text-indigo-700
                                    dark:file:bg-indigo-900/50 dark:file:text-indigo-400
                                    hover:file:bg-indigo-100 transition cursor-pointer border border-slate-300 dark:border-slate-600 rounded-xl p-1.5 bg-slate-50 dark:bg-slate-900/50">
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full bg-indigo-600 text-white py-2.5 rounded-xl font-black shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 transition transform hover:-translate-y-0.5 uppercase tracking-wide text-xs">
                            Mulai Import Ujian
                        </button>
                    </form>
                </div>

            </div>

            <!-- TABEL DATA NILAI UJIAN & FILTER -->
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <!-- Header & Filter Tabel -->
                <div
                    class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
                    <h3 class="text-base font-black text-slate-800 dark:text-white">
                        Daftar Nilai Masuk
                    </h3>

                    <form action="{{ route('exam-grades.index') }}" method="GET"
                        class="flex flex-wrap items-center gap-2">

                        <!-- Filter Kategori (Jenis Penilaian) -->
                        <select name="kategori_ujian"
                            class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-xs h-9 focus:ring-indigo-500">
                            <option value="">Semua Kategori</option>
                            <option value="Ujian Sekolah" {{ request('kategori_ujian')=='Ujian Sekolah' ? 'selected'
                                : '' }}>Ujian Sekolah</option>
                            <option value="Ujian Praktik" {{ request('kategori_ujian')=='Ujian Praktik' ? 'selected'
                                : '' }}>Ujian Praktik</option>
                            <option value="Ujian Tengah Semester" {{ request('kategori_ujian')=='Ujian Tengah Semester'
                                ? 'selected' : '' }}>UTS</option>
                            <option value="Ujian Akhir Semester" {{ request('kategori_ujian')=='Ujian Akhir Semester'
                                ? 'selected' : '' }}>UAS</option>
                        </select>

                        <!-- Filter Mata Pelajaran -->
                        <select name="subject_id"
                            class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-xs h-9 focus:ring-indigo-500 max-w-[150px] truncate">
                            <option value="">Semua Mapel</option>
                            @foreach($subjects as $subj)
                            <option value="{{ $subj->id }}" {{ request('subject_id')==$subj->id ? 'selected' : '' }}>
                                Kls {{ $subj->tingkat }} - {{ $subj->nama_mapel }}
                            </option>
                            @endforeach
                        </select>




                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 h-9 rounded-lg text-xs font-bold transition shadow-sm">
                            Filter
                        </button>

                        <!-- Tombol Reset (Muncul jika ada filter yang aktif) -->
                        @if(request('kategori_ujian') || request('subject_id') || request('tingkat_kelas') ||
                        request('semester'))
                        <a href="{{ route('exam-grades.index') }}" title="Hapus Filter"
                            class="bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-slate-300 px-3 py-1.5 h-9 rounded-lg text-xs font-bold transition flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                        @endif
                    </form>
                </div>

                <!-- Isi Tabel -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left whitespace-nowrap">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50/80 dark:bg-slate-900/30">
                            <tr>
                                <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700">Nama Siswa</th>
                                <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700">Kategori</th>
                                <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700">Mapel</th>
                                <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700 text-center">
                                    Periode</th>
                                <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700 text-center">Nilai
                                </th>
                                <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700 text-right">Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white dark:bg-slate-800">
                            @forelse($examGrades as $grade)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition">
                                <td class="px-6 py-3">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">{{
                                        $grade->student->nama_lengkap ?? '-' }}</div>
                                    <div class="text-xs text-slate-500">{{ $grade->student->nisn ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-3">
                                    <span
                                        class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 px-2 py-1 rounded text-xs font-bold border border-indigo-100 dark:border-indigo-800">
                                        {{ $grade->kategori_ujian }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 font-semibold text-slate-700 dark:text-slate-300">
                                    {{ $grade->subject->nama_mapel ?? '-' }}
                                </td>
                                <td class="px-6 py-3 text-center text-slate-500">
                                    Kls {{ $grade->tingkat_kelas }} / Smt {{ $grade->semester }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <span
                                        class="font-black text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1.5 rounded-lg border border-emerald-100 dark:border-emerald-800">
                                        {{ rtrim(rtrim(number_format($grade->nilai, 2, '.', ''), '0'), '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <form action="{{ route('exam-grades.destroy', $grade->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus nilai ujian ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-lg transition"
                                            title="Hapus Nilai">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-500 italic">
                                    @if(request('tingkat_kelas') || request('kategori_ujian') || request('subject_id')
                                    || request('semester'))
                                    Tidak ada data nilai yang sesuai dengan filter Anda.
                                    @else
                                    Belum ada data nilai ujian yang diinput.
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Script JavaScript untuk Unduh Template -->
    <script>
        function unduhTemplateUjian() {
            let tingkat = document.getElementById('tingkat_template').value;

            if (!tingkat) {
                alert('Peringatan: Silakan pilih Tingkat Kelas di sebelah tombol unduh terlebih dahulu!');
                return;
            }

            let url = "{{ route('exam-grades.template') }}?tingkat_kelas=" + tingkat;
            window.location.href = url;
        }
    </script>
</x-app-layout>