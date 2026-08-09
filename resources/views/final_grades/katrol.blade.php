<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            Penyesuaian Nilai Akademik (Katrol Linier)
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl font-bold shadow-sm">
                {{ session('success') }}
            </div>
            @endif

            {{-- TAMPILAN ERROR VALIDASI (MENCEGAH ERROR validation.required) --}}
            @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl font-bold shadow-sm mb-6">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm">Terjadi kesalahan validasi. Silakan periksa kembali inputan Anda.</span>
                </div>
                <ul class="list-disc list-inside text-sm font-medium">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- 1. PANEL FILTER KELAS & MAPEL --}}
            <div
                class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col md:flex-row items-end justify-between gap-4">
                <form action="{{ route('katrol.index') }}" method="GET"
                    class="flex flex-col md:flex-row items-end gap-4 w-full">

                    <div class="w-full md:w-1/3">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih
                            Kelas</label>
                        <select name="classroom_id" onchange="this.form.submit()"
                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500 transition">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classrooms as $cls)
                            <option value="{{ $cls->id }}" {{ request('classroom_id')==$cls->id ? 'selected' : '' }}>
                                Tingkat {{ $cls->tingkat }} - {{ $cls->nama_kelas }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    @if($selectedClassroom)
                    <div class="w-full md:w-1/3">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mata
                            Pelajaran</label>
                        <select name="subject_id" onchange="this.form.submit()"
                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500 transition">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($subjects as $subj)
                            <option value="{{ $subj->id }}" {{ request('subject_id')==$subj->id ? 'selected' : '' }}>
                                {{ $subj->nama_mapel }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </form>

                {{-- Tombol Refresh Nilai (Hanya Tampil Jika Filter Sudah Dipilih) --}}
                @if(request('classroom_id') && request('subject_id') && $grades->isNotEmpty())
                <form action="{{ route('katrol.fetch') }}" method="POST">
                    @csrf
                    <input type="hidden" name="academic_year_id" value="{{ $activeYear->id }}">
                    <input type="hidden" name="classroom_id" value="{{ request('classroom_id') }}">
                    <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">
                    <button type="submit"
                        class="shrink-0 inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-xs font-bold py-2.5 px-4 rounded-xl transition border border-slate-300 dark:border-slate-600 shadow-sm"
                        title="Tarik ulang data ujian">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        Refresh Tarikan Nilai
                    </button>
                </form>
                @endif
            </div>

            @if(request('classroom_id') && request('subject_id'))

            {{-- JIKA BELUM ADA DATA SAMA SEKALI --}}
            @if($grades->isEmpty())
            <div
                class="py-20 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 text-center flex flex-col items-center justify-center">
                <div
                    class="w-20 h-20 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Nilai Asli Belum Ditarik</h3>
                <p class="text-sm text-slate-500 mb-6 max-w-md">Sistem belum merekapitulasi rata-rata nilai ujian dan
                    observasi untuk kelas dan mata pelajaran ini. Silakan tarik data sekarang.</p>

                <form action="{{ route('katrol.fetch') }}" method="POST">
                    @csrf
                    <input type="hidden" name="academic_year_id" value="{{ $activeYear->id }}">
                    <input type="hidden" name="classroom_id" value="{{ request('classroom_id') }}">
                    <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-indigo-200 dark:shadow-none transition flex items-center gap-2">
                        Mulai Tarik Rekap Nilai Ujian
                    </button>
                </form>
            </div>

            {{-- JIKA DATA SUDAH DITARIK --}}
            @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- 2. PANEL PENGATURAN RUMUS KATROL --}}
                <div class="lg:col-span-1">
                    <div
                        class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 sticky top-6">
                        <div class="mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
                            <h3 class="text-lg font-black text-slate-800 dark:text-slate-200 uppercase tracking-tight">
                                Pengaturan Katrol</h3>
                            <p class="text-xs text-slate-500 mt-2 leading-relaxed">Metode Transformasi Linier akan
                                mengangkat nilai siswa terendah menjadi sesuai KKM, dan tertinggi menyentuh Batas
                                Maksimal secara proporsional.</p>
                        </div>

                        <form action="{{ route('katrol.process') }}" method="POST" class="space-y-5">
                            @csrf
                            <input type="hidden" name="academic_year_id" value="{{ $activeYear->id }}">
                            <input type="hidden" name="classroom_id" value="{{ request('classroom_id') }}">
                            <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">

                            @php
                            $nilaiTerendah = $grades->min('nilai_asli');
                            $nilaiTertinggi = $grades->max('nilai_asli');
                            @endphp

                            <div
                                class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700 mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-bold text-slate-500">Nilai Asli Terendah</span>
                                    <span class="text-sm font-black text-rose-500">{{ $nilaiTerendah ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-bold text-slate-500">Nilai Asli Tertinggi</span>
                                    <span class="text-sm font-black text-indigo-500">{{ $nilaiTertinggi ?? 0 }}</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Target
                                    Nilai Terendah (KKM)</label>
                                <input type="number" name="target_min" value="75" min="0" max="100" required
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="text-[10px] text-slate-500 mt-1">Siswa dengan nilai {{ $nilaiTerendah }}
                                    otomatis akan mendapat nilai ini.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Target
                                    Nilai Maksimal</label>
                                <input type="number" name="target_max" value="98" min="0" max="100" required
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="text-[10px] text-slate-500 mt-1">Siswa dengan nilai {{ $nilaiTertinggi }}
                                    otomatis akan mendapat nilai ini.</p>
                            </div>

                            <button type="submit"
                                onclick="return confirm('Proses ini akan menimpa kolom Nilai Akhir seluruh siswa di mapel ini. Lanjutkan?')"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition mt-4">
                                Eksekusi Katrol Nilai
                            </button>
                        </form>
                    </div>
                </div>

                {{-- 3. PANEL TABEL DATA NILAI --}}
                <div class="lg:col-span-2">
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div
                            class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                            <h4 class="font-black text-slate-800 dark:text-white uppercase tracking-tight">Daftar Nilai
                                Siswa</h4>
                            <span class="text-xs font-bold text-slate-500 italic">{{ $grades->count() }} Siswa
                                Diolah</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead
                                    class="text-xs text-slate-500 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-900/30">
                                    <tr>
                                        <th class="px-6 py-4 text-center w-12">No</th>
                                        <th class="px-6 py-4">Nama Lengkap</th>
                                        <th
                                            class="px-6 py-4 text-center border-l border-slate-200 dark:border-slate-700">
                                            Nilai Asli</th>
                                        <th
                                            class="px-6 py-4 text-center bg-indigo-50/50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300">
                                            Nilai Akhir</th>
                                        <th class="px-6 py-4 text-center">Predikat</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                    @foreach($grades as $index => $grade)
                                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition duration-150">
                                        <td class="px-6 py-3 text-center text-slate-500 font-medium">{{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-3 font-bold text-slate-800 dark:text-slate-200">{{
                                            $grade->student->nama_lengkap ?? 'Siswa' }}</td>

                                        <td
                                            class="px-6 py-3 text-center border-l border-slate-100 dark:border-slate-700 text-slate-500 font-mono">
                                            {{ $grade->nilai_asli }}
                                        </td>

                                        <td
                                            class="px-6 py-3 text-center bg-indigo-50/30 dark:bg-indigo-900/10 font-black text-indigo-600 dark:text-indigo-400 text-base">
                                            {{ $grade->nilai_akhir }}
                                        </td>

                                        <td class="px-6 py-3 text-center">
                                            @if($grade->predikat)
                                            <span
                                                class="inline-flex items-center justify-center px-2 py-1 rounded bg-slate-100 dark:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300">
                                                {{ $grade->predikat }}
                                            </span>
                                            @else
                                            -
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            @endif
            @endif

        </div>
    </div>
</x-app-layout>