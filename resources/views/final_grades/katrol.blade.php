<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100 leading-tight">
            Penyesuaian Nilai Akademik (Katrol Linier)
        </h2>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">

            {{-- TAMPILAN NOTIFIKASI SUKSES / ERROR SISTEM --}}
            @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-md font-bold shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-md font-bold shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
            @endif

            {{-- TAMPILAN ERROR VALIDASI (DENGAN PENCEGAH KEBOCORAN KODE VALIDATION.REQUIRED) --}}
            @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-md font-bold shadow-sm mb-6">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <span class="uppercase tracking-widest text-xs">Kesalahan Input:</span>
                </div>
                <ul class="list-disc list-inside text-sm font-medium">
                    @foreach ($errors->all() as $error)
                    {{-- Logika untuk menerjemahkan otomatis jika sistem membocorkan kode 'validation.' --}}
                    @if(str_contains($error, 'validation.'))
                    <li>Semua data pada form (Kelas, Mata Pelajaran, dan Batas KKM) wajib diisi dengan lengkap.</li>
                    @break
                    @else
                    <li>{{ $error }}</li>
                    @endif
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- 1. PANEL FILTER KELAS & MAPEL --}}
            <div
                class="flex flex-col items-stretch justify-between gap-4 rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6 md:flex-row md:items-end">
                <form action="{{ route('rapor.index') }}" method="GET"
                    class="grid w-full grid-cols-1 items-end gap-4 sm:grid-cols-2 md:flex">

                    <div class="w-full md:min-w-48">
                        <label for="classroom_id" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Pilih
                            Kelas</label>
                        <select id="classroom_id" name="classroom_id" onchange="this.form.submit()"
                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classrooms as $cls)
                            <option value="{{ $cls->id }}" {{ request('classroom_id')==$cls->id ? 'selected' : '' }}>
                                Tingkat {{ $cls->tingkat }} - {{ $cls->nama_kelas }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    @if($selectedClassroom)
                    <div class="w-full md:min-w-56">
                        <label for="subject_id" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Mata
                            Pelajaran</label>
                        <select id="subject_id" name="subject_id" onchange="this.form.submit()"
                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
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

                {{-- Tombol Refresh Nilai (Hanya Tampil Jika Filter Sudah Dipilih & Data Sudah Ada) --}}
                @if(request('classroom_id') && request('subject_id') && $grades->isNotEmpty())
                <form action="{{ route('rapor.fetch') }}" method="POST">
                    @csrf
                    <input type="hidden" name="academic_year_id" value="{{ $activeYear->id }}">
                    <input type="hidden" name="classroom_id" value="{{ request('classroom_id') }}">
                    <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">
                    <button type="submit"
                        class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 rounded-md border border-slate-300 bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600"
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

            {{-- CEK APAKAH FILTER SUDAH DIPILIH --}}
            @if(request('classroom_id') && request('subject_id'))

            {{-- 2A. JIKA BELUM ADA DATA SAMA SEKALI --}}
            {{-- JIKA BELUM ADA DATA SAMA SEKALI --}}
            @if($grades->isEmpty())
            <div
                class="flex flex-col items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-16 text-center dark:border-slate-700 dark:bg-slate-800">
                <div
                    class="w-20 h-20 bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Nilai Asli Belum Ditarik</h3>
                <p class="text-sm text-slate-500 mb-6 max-w-md">Sistem belum merekapitulasi rata-rata nilai ujian dan
                    observasi untuk kelas dan mata pelajaran ini. Silakan tarik data sekarang.</p>

                <form action="{{ route('rapor.fetch') }}" method="POST">
                    @csrf
                    <input type="hidden" name="academic_year_id" value="{{ $activeYear->id }}">
                    <input type="hidden" name="classroom_id" value="{{ request('classroom_id') }}">
                    <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">
                    <button type="submit"
                        class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-3 px-8 rounded-md shadow-lg shadow-brand-200 dark:shadow-none transition flex items-center gap-2">
                        Mulai Tarik Rekap Nilai Ujian
                    </button>
                </form>
            </div>

            {{-- 2B. JIKA DATA SUDAH DITARIK --}}
            @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- PANEL PENGATURAN RUMUS KATROL --}}
                <div class="lg:col-span-1">
                    <div
                        class="sticky top-6 rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                        <div class="mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
                            <h3 class="text-lg font-black text-slate-800 dark:text-slate-200 uppercase tracking-tight">
                                Pengaturan Katrol</h3>
                            <p class="text-xs text-slate-500 mt-2 leading-relaxed">Metode Transformasi Linier akan
                                mengangkat nilai siswa terendah menjadi sesuai KKM, dan tertinggi menyentuh Batas
                                Maksimal secara proporsional.</p>
                        </div>

                        <form action="{{ route('rapor.process') }}" method="POST" class="space-y-5">
                            @csrf
                            <input type="hidden" name="academic_year_id" value="{{ $activeYear->id }}">
                            <input type="hidden" name="classroom_id" value="{{ request('classroom_id') }}">
                            <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">

                            @php
                            $nilaiTerendah = $grades->min('nilai_asli');
                            $nilaiTertinggi = $grades->max('nilai_asli');
                            @endphp

                            <div
                                class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-md border border-slate-200 dark:border-slate-700 mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-bold text-slate-500">Nilai Asli Terendah</span>
                                    <span class="text-sm font-black text-rose-500">{{ $nilaiTerendah ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-bold text-slate-500">Nilai Asli Tertinggi</span>
                                    <span class="text-sm font-black text-brand-500">{{ $nilaiTertinggi ?? 0 }}</span>
                                </div>
                            </div>

                            <div>
                                <label for="target_min" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Target
                                    Nilai Terendah (KKM)</label>
                                <input id="target_min" type="number" name="target_min" value="75" min="0" max="100" required
                                    class="w-full rounded-md border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Siswa dengan nilai {{ $nilaiTerendah }}
                                    otomatis akan mendapat nilai ini.</p>
                            </div>

                            <div>
                                <label for="target_max" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Target
                                    Nilai Maksimal</label>
                                <input id="target_max" type="number" name="target_max" value="98" min="0" max="100" required
                                    class="w-full rounded-md border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Siswa dengan nilai {{ $nilaiTertinggi }}
                                    otomatis akan mendapat nilai ini.</p>
                            </div>

                            <button type="submit"
                                onclick="return confirm('Proses ini akan menimpa kolom Nilai Akhir seluruh siswa di mapel ini. Anda yakin?')"
                                class="inline-flex min-h-11 w-full items-center justify-center rounded-md bg-brand-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                                Eksekusi Katrol Nilai
                            </button>
                        </form>
                    </div>
                </div>

                {{-- PANEL TABEL DATA NILAI --}}
                <div class="lg:col-span-2">
                    <div
                        class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
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
                                            class="px-6 py-4 text-center bg-brand-50/50 dark:bg-brand-900/20 text-brand-700 dark:text-brand-300">
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
                                            class="px-6 py-3 text-center bg-brand-50/30 dark:bg-brand-900/10 font-black text-brand-600 dark:text-brand-400 text-base">
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