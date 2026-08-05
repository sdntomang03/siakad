<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
            Rekapitulasi Nilai Kelas
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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
                    // Tetap pertahankan value 'all' jika dipilih
                    if(this.$event && this.selectedSubjectId !== 'all') {
                        this.selectedSubjectId = '';
                    }
                }
            }"
            class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">

            <form action="{{ route('assessments.recap') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">1. Pilih Kelas</label>
                    <select name="classroom_id" required x-model="selectedClassId" @change="updateSubjects()"
                        class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500">
                        <option value="">-- Pilih Kelas Anda --</option>
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

                <div x-show="selectedSubjectId" style="display: none;">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">3. Jenis Penilaian</label>
                    <select name="assessment_type_id"
                        class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500">
                        <option value="">-- Semua Jenis --</option>
                        @foreach($assessmentTypes as $type)
                        <option value="{{ $type->id }}" {{ request('assessment_type_id')==$type->id ? 'selected' : ''
                            }}>
                            {{ $type->nama }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div x-show="selectedSubjectId" style="display: none;" class="flex flex-col gap-3">
                    <button type="submit"
                        class="w-full px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-black shadow-md hover:bg-indigo-700 transition">
                        Detail Nilai (Gradebook) &rarr;
                    </button>
                    <button type="submit" formaction="{{ route('assessments.recapByType') }}"
                        class="w-full px-6 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-black shadow-md hover:bg-emerald-700 transition">
                        Rata-Rata per Jenis Penilaian &rarr;
                    </button>
                </div>
            </form>
        </div>

        @if(request('classroom_id') && request('subject_id'))
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col">

            <div
                class="p-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex justify-between items-center">
                <div>
                    <h3 class="text-base font-black text-slate-800 dark:text-white">Buku Nilai (Gradebook)</h3>
                    <p class="text-xs font-bold text-slate-500 mt-1">Total: {{ $students->count() }} Siswa | {{
                        $assessments->count() }} Penilaian</p>
                </div>
            </div>

            @if($assessments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                    <thead
                        class="text-xs text-slate-700 uppercase bg-slate-100/80 dark:bg-slate-900/80 dark:text-slate-300">
                        <!-- Baris Grouping Mata Pelajaran -->
                        @if(request('subject_id') === 'all')
                        <tr>
                            <th colspan="2"
                                class="px-4 py-2 border-r border-b border-slate-200 dark:border-slate-700 sticky left-0 bg-slate-200 dark:bg-slate-800 z-20">
                            </th>

                            @php
                            $groupedAssessments = $assessments->groupBy('subject_id');
                            @endphp

                            @foreach($groupedAssessments as $subjectId => $subjectAssessments)
                            <th colspan="{{ $subjectAssessments->count() }}"
                                class="px-4 py-2 text-center border-b border-r border-slate-200 dark:border-slate-700 bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300 font-bold">
                                {{ $subjectAssessments->first()->subject->nama_mapel ?? 'Mata Pelajaran' }}
                            </th>
                            <!-- Kolom Rata-rata Per Mapel -->
                            <th
                                class="px-2 border-b border-slate-200 dark:border-slate-700 bg-slate-200 dark:bg-slate-800">
                            </th>
                            @endforeach
                            <th class="border-b border-slate-200 dark:border-slate-700"></th>
                        </tr>
                        @endif

                        <!-- Baris Nama Kolom -->
                        <tr>
                            <th class="px-4 py-4 w-10 text-center sticky left-0 bg-slate-100 dark:bg-slate-900 z-10">No
                            </th>
                            <th
                                class="px-4 py-4 w-64 sticky left-10 bg-slate-100 dark:bg-slate-900 z-10 border-r border-slate-200 dark:border-slate-700">
                                Nama Siswa</th>

                            @if(request('subject_id') === 'all')
                            @foreach($groupedAssessments as $subjectId => $subjectAssessments)
                            @foreach($subjectAssessments as $ujian)
                            <th class="px-4 py-4 text-center min-w-[120px] group cursor-default border-r border-slate-200 dark:border-slate-700"
                                title="{{ $ujian->keterangan }}">
                                <span class="block font-black text-indigo-600 dark:text-indigo-400">{{
                                    $ujian->assessmentType->singkatan ?? 'Unknown' }}</span>
                                <span
                                    class="block text-[10px] text-slate-500 mt-1 font-normal truncate max-w-[120px]">{{
                                    $ujian->tanggal->format('d/m') }}</span>
                            </th>
                            @endforeach
                            <th
                                class="px-4 py-4 text-center bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 border-r border-slate-200 dark:border-slate-700">
                                Rata2 Mapel</th>
                            @endforeach
                            @else
                            @foreach($assessments as $ujian)
                            <th class="px-4 py-4 text-center min-w-[120px] group cursor-default"
                                title="{{ $ujian->keterangan }}">
                                <span class="block font-black text-indigo-600 dark:text-indigo-400">{{
                                    $ujian->assessmentType->singkatan ?? 'Unknown' }}</span>
                                <span
                                    class="block text-[10px] text-slate-500 mt-1 font-normal truncate max-w-[120px]">{{
                                    $ujian->tanggal->format('d/m') }}</span>
                            </th>
                            @endforeach
                            <th
                                class="px-4 py-4 text-center bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 border-l border-slate-200 dark:border-slate-700">
                                Rata-Rata</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($students as $index => $siswa)
                        <tr
                            class="border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                            <td
                                class="px-4 py-3 font-bold text-center sticky left-0 bg-white dark:bg-slate-800 group-hover:bg-slate-50 dark:group-hover:bg-slate-700/50">
                                {{ $index + 1 }}</td>
                            <td
                                class="px-4 py-3 sticky left-10 bg-white dark:bg-slate-800 group-hover:bg-slate-50 dark:group-hover:bg-slate-700/50 border-r border-slate-100 dark:border-slate-700">
                                <span class="block font-bold text-slate-800 dark:text-slate-200 truncate max-w-[200px]"
                                    title="{{ $siswa->nama_lengkap }}">{{ $siswa->nama_lengkap }}</span>
                            </td>

                            @if(request('subject_id') === 'all')
                            @foreach($groupedAssessments as $subjectId => $subjectAssessments)
                            @php
                            $totalNilaiMapel = 0;
                            // Total ujian diambil dari jumlah seluruh penilaian yang ada di mapel tersebut
                            $totalUjianMapel = $subjectAssessments->count();
                            @endphp

                            @foreach($subjectAssessments as $ujian)
                            @php
                            $nilai = $matrixScores[$siswa->id][$ujian->id] ?? null;
                            // Nilai ditambahkan. Jika null (kosong), maka ditambah 0
                            $totalNilaiMapel += ($nilai ?? 0);
                            @endphp
                            <td
                                class="px-4 py-3 text-center border-r border-slate-100 dark:border-slate-700 font-semibold {{ $nilai !== null && $nilai < 75 ? 'text-rose-500' : 'text-slate-700 dark:text-slate-300' }}">
                                {{ $nilai ?? '-' }}
                            </td>
                            @endforeach

                            <!-- Rata-rata per Mapel (dibagi total jumlah ujian meskipun nilainya kosong) -->
                            @php
                            $rataRataMapel = $totalUjianMapel > 0 ? round($totalNilaiMapel / $totalUjianMapel, 1) : 0;
                            @endphp
                            <td
                                class="px-4 py-3 text-center font-black bg-indigo-50/50 dark:bg-indigo-900/10 border-r border-slate-200 dark:border-slate-700 {{ $rataRataMapel < 75 ? 'text-rose-600' : 'text-indigo-600' }}">
                                {{ $rataRataMapel }}
                            </td>
                            @endforeach

                            @else
                            <!-- JIKA HANYA 1 MAPEL -->
                            @php
                            $totalNilai = 0;
                            // Total ujian diambil dari seluruh penilaian untuk filter yang dipilih
                            $totalUjian = $assessments->count();
                            @endphp
                            @foreach($assessments as $ujian)
                            @php
                            $nilai = $matrixScores[$siswa->id][$ujian->id] ?? null;
                            // Nilai ditambahkan. Jika null (kosong), maka ditambah 0
                            $totalNilai += ($nilai ?? 0);
                            @endphp
                            <td
                                class="px-4 py-3 text-center font-semibold {{ $nilai !== null && $nilai < 75 ? 'text-rose-500' : 'text-slate-700 dark:text-slate-300' }}">
                                {{ $nilai ?? '-' }}
                            </td>
                            @endforeach

                            <!-- Rata-rata (dibagi total jumlah ujian meskipun nilainya kosong) -->
                            @php
                            $rataRata = $totalUjian > 0 ? round($totalNilai / $totalUjian, 1) : 0;
                            @endphp
                            <td
                                class="px-4 py-3 text-center font-black bg-indigo-50/50 dark:bg-indigo-900/10 border-l border-slate-100 dark:border-slate-700 {{ $rataRata < 75 ? 'text-rose-600' : 'text-indigo-600' }}">
                                {{ $rataRata }}
                            </td>
                            @endif

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-16 text-center">
                <p class="text-slate-500 font-bold mb-2">Belum ada data penilaian.</p>
                <a href="{{ route('assessments.create') }}"
                    class="text-sm text-indigo-600 hover:text-indigo-700 font-bold underline">Mulai buat penilaian
                    pertama di kelas ini.</a>
            </div>
            @endif
        </div>
        @endif

    </div>
</x-app-layout>