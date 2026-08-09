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
            @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl font-bold shadow-sm">
                {{ session('error') }}
            </div>
            @endif

            {{-- 1. PANEL FILTER KELAS & MAPEL --}}
            <div
                class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <form action="{{ route('katrol.index') }}" method="GET"
                    class="flex flex-col md:flex-row items-end gap-4">

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
            </div>

            @if(request('classroom_id') && request('subject_id'))
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
                                    @forelse($grades as $index => $grade)
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
                                                class="inline-flex items-center justify-center w-6 h-6 rounded bg-slate-100 dark:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300">
                                                {{ $grade->predikat }}
                                            </span>
                                            @else
                                            -
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="py-12 text-center text-slate-500 italic">Belum ada data
                                            nilai mentah di tabel untuk diolah.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            @endif

        </div>
    </div>
</x-app-layout>