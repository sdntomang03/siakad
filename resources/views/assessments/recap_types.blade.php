<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
            Rata-Rata Jenis Penilaian
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- KOTAK PENCARIAN (Sama persis dengan halaman recap agar seragam) -->
        <div x-data="{
                classesData: {{ json_encode($classesData) }},
                selectedClassId: '{{ request('classroom_id') }}',
                selectedSubjectId: '{{ request('subject_id') }}',
                availableSubjects: [],
                init() {
                    this.updateSubjects();
                },
                updateSubjects() {
                    this.availableSubjects = this.selectedClassId && this.classesData[this.selectedClassId]
                        ? this.classesData[this.selectedClassId].subjects
                        : [];
                    if(this.$event && this.selectedSubjectId !== 'all') {
                        this.selectedSubjectId = '';
                    }
                }
            }"
            class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">

            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">1. Pilih Kelas</label>
                    <select name="classroom_id" required x-model="selectedClassId" @change="updateSubjects()"
                        class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500">
                        <option value="">-- Pilih Kelas --</option>
                        <template x-for="(data, classId) in classesData" :key="classId">
                            <option :value="classId" x-text="data.nama_kelas"></option>
                        </template>
                    </select>
                </div>

                <div x-show="selectedClassId" style="display: none;">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">2. Mata Pelajaran</label>
                    <select name="subject_id" required x-model="selectedSubjectId"
                        class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500">
                        <option value="">-- Pilih Mapel --</option>
                        <option value="all" class="font-bold text-indigo-600">Semua Mata Pelajaran</option>
                        <template x-for="subject in availableSubjects" :key="subject.id">
                            <option :value="subject.id" x-text="subject.nama"></option>
                        </template>
                    </select>
                </div>

                <div x-show="selectedSubjectId" style="display: none;" class="md:col-span-2 flex gap-3">
                    <button type="submit" formaction="{{ route('assessments.recap') }}"
                        class="flex-1 px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-black shadow-md hover:bg-indigo-700 transition">
                        Gradebook Detail
                    </button>
                    <button type="submit" formaction="{{ route('assessments.recapByType') }}"
                        class="flex-1 px-6 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-black shadow-md hover:bg-emerald-700 transition">
                        Rata-Rata per Jenis &rarr;
                    </button>
                </div>
            </form>
        </div>

        <!-- AREA MATRIKS -->
        @if(request('classroom_id') && request('subject_id'))
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col">
            <div
                class="p-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex justify-between items-center">
                <div>
                    <h3 class="text-base font-black text-slate-800 dark:text-white">Rata-Rata Berdasarkan Jenis
                        Penilaian</h3>
                    <p class="text-xs font-bold text-slate-500 mt-1">Total: {{ $students->count() }} Siswa</p>
                </div>
            </div>

            @if($subjects->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                    <thead
                        class="text-xs text-slate-700 uppercase bg-slate-100/80 dark:bg-slate-900/80 dark:text-slate-300">

                        <!-- Baris 1: Pengelompokkan Mata Pelajaran -->
                        <tr>
                            <th rowspan="2"
                                class="px-4 py-2 w-10 text-center sticky left-0 bg-slate-200 dark:bg-slate-800 z-20 border-r border-slate-300 dark:border-slate-600">
                                No</th>
                            <th rowspan="2"
                                class="px-4 py-2 w-64 sticky left-10 bg-slate-200 dark:bg-slate-800 z-20 border-r border-slate-300 dark:border-slate-600">
                                Nama Siswa</th>

                            @foreach($subjects as $subject)
                            @php $usedTypes = $usedTypesPerSubject[$subject->id] ?? []; @endphp

                            @if((request('subject_id') === 'all' || request('subject_id') == $subject->id) &&
                            count($usedTypes) > 0)
                            <th colspan="{{ count($usedTypes) }}"
                                class="px-4 py-3 text-center border-b border-r border-slate-300 dark:border-slate-600 bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300 font-bold">
                                {{ $subject->nama_mapel }}
                            </th>
                            @endif
                            @endforeach

                            <!-- Kolom Judul Rata-Rata dan Ranking -->
                            <th rowspan="2"
                                class="px-4 py-2 w-24 text-center border-b border-l border-slate-300 dark:border-slate-600 bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300 font-bold">
                                Rata-Rata<br>Total</th>
                            <th rowspan="2"
                                class="px-4 py-2 w-20 text-center border-b border-l border-slate-300 dark:border-slate-600 bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300 font-bold">
                                Rank</th>
                        </tr>

                        <!-- Baris 2: Nama-Nama Jenis Penilaian -->
                        <tr>
                            @foreach($subjects as $subject)
                            @php $usedTypes = $usedTypesPerSubject[$subject->id] ?? []; @endphp

                            @if((request('subject_id') === 'all' || request('subject_id') == $subject->id) &&
                            count($usedTypes) > 0)
                            @foreach($usedTypes as $type)
                            <th class="px-3 py-3 text-center border-b border-r border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 min-w-[80px]"
                                title="{{ $type->nama }}">
                                {{ $type->singkatan }}
                            </th>
                            @endforeach
                            @endif
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($students as $index => $siswa)
                        <tr
                            class="border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                            <!-- Kolom Ranking No. yang diperbarui karena siswa sudah terurut dari Controller -->
                            <td
                                class="px-4 py-3 font-bold text-center sticky left-0 bg-white dark:bg-slate-800 group-hover:bg-slate-50 dark:group-hover:bg-slate-700/50 border-r border-slate-100 dark:border-slate-700">
                                {{ $index + 1 }}
                            </td>
                            <td
                                class="px-4 py-3 sticky left-10 bg-white dark:bg-slate-800 group-hover:bg-slate-50 dark:group-hover:bg-slate-700/50 border-r border-slate-200 dark:border-slate-700">
                                <span class="block font-bold text-slate-800 dark:text-slate-200 truncate max-w-[200px]"
                                    title="{{ $siswa->nama_lengkap }}">{{ $siswa->nama_lengkap }}</span>
                            </td>

                            @foreach($subjects as $subject)
                            @php $usedTypes = $usedTypesPerSubject[$subject->id] ?? []; @endphp

                            @if((request('subject_id') === 'all' || request('subject_id') == $subject->id) &&
                            count($usedTypes) > 0)
                            @foreach($usedTypes as $type)
                            @php
                            $avg = $averageScores[$siswa->id][$subject->id][$type->id] ?? null;
                            @endphp
                            <td
                                class="px-3 py-3 text-center border-r border-slate-100 dark:border-slate-700 font-semibold {{ $avg !== null && $avg < 75 ? 'text-rose-500' : 'text-slate-700 dark:text-slate-300' }}">
                                {{ $avg ?? '-' }}
                            </td>
                            @endforeach
                            @endif
                            @endforeach

                            <!-- Output Rata-Rata Keseluruhan dan Rank -->
                            @php
                            $overallAvg = $studentAverages[$siswa->id] ?? 0;
                            $rank = $studentRanks[$siswa->id] ?? '-';
                            @endphp
                            <td
                                class="px-4 py-3 text-center border-l border-slate-100 dark:border-slate-700 font-black bg-indigo-50/50 dark:bg-indigo-900/10 {{ $overallAvg > 0 && $overallAvg < 75 ? 'text-rose-600' : 'text-indigo-600 dark:text-indigo-400' }}">
                                {{ $overallAvg > 0 ? $overallAvg : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-center border-l border-slate-100 dark:border-slate-700 font-black bg-amber-50/50 dark:bg-amber-900/10 text-amber-600 dark:text-amber-400">
                                {{ $rank }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-16 text-center">
                <p class="text-slate-500 font-bold mb-2">Belum ada data penilaian di mata pelajaran terpilih.</p>
            </div>
            @endif
        </div>
        @endif

    </div>
</x-app-layout>