<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Buat Program Remedial</h1>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        @if ($errors->any())
            <div role="alert" class="rounded-md border border-rose-700 bg-rose-50 px-4 py-3 text-sm text-rose-900 dark:border-rose-500 dark:bg-rose-950/40 dark:text-rose-100">
                <p class="font-semibold">Periksa kembali pilihan dan nilai yang dimasukkan.</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-200 pb-5 dark:border-slate-700">
            <div>
                <p class="text-sm font-medium text-brand-800 dark:text-brand-200">Perhitungan berdasarkan KKM mata pelajaran</p>
                <p class="mt-1 max-w-3xl text-sm text-slate-700 dark:text-slate-300">
                    Pilih satu atau beberapa penilaian. Rerata dihitung setelah semua nilai yang dipilih tersedia untuk siswa.
                </p>
            </div>
            <a href="{{ route('remedials.index') }}"
                class="inline-flex min-h-11 items-center rounded-md px-3 text-sm font-semibold text-brand-800 hover:bg-brand-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:text-brand-200 dark:hover:bg-brand-900/40">
                Kembali ke daftar
            </a>
        </div>

        @if (! $activeYear)
            <div role="alert" class="rounded-md border border-amber-700 bg-amber-50 px-4 py-3 text-sm text-amber-950 dark:border-amber-500 dark:bg-amber-950/40 dark:text-amber-100">
                Tahun ajaran aktif belum ditentukan. Hubungi operator sekolah untuk mengatur tahun ajaran.
            </div>
        @elseif (empty($classesData))
            <div class="rounded-lg border border-slate-300 bg-white px-5 py-8 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                Belum ada kelas dan mata pelajaran yang ditugaskan kepada Anda pada tahun ajaran aktif.
            </div>
        @else
            <form method="GET" action="{{ route('remedials.create') }}"
                x-data="{
                    loading: false,
                    selectionChanged: false,
                    selectedAssessmentIds: @js(array_map('strval', $selectedAssessmentIds)),
                    classesData: @js($classesData),
                    selectedClassId: @js((string) $classroom?->id),
                    selectedSubjectId: @js((string) $subject?->id),
                    subjects: [],
                    updateSubjects() {
                        this.subjects = this.classesData[this.selectedClassId]
                            ? Object.values(this.classesData[this.selectedClassId].subjects)
                            : [];
                        if (!this.subjects.some(subject => subject.id == this.selectedSubjectId)) {
                            this.selectedSubjectId = '';
                        }
                    }
                }"
                x-init="$nextTick(() => updateSubjects())"
                @submit="loading = true"
                class="space-y-5 rounded-lg border border-slate-300 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="classroom_id" class="mb-2 block text-sm font-medium text-slate-800 dark:text-slate-100">Kelas</label>
                        <select id="classroom_id" name="classroom_id" required x-model="selectedClassId" @change="updateSubjects(); selectionChanged = true; selectedAssessmentIds = []"
                            class="min-h-11 w-full rounded-md border-slate-400 bg-white text-sm text-slate-900 focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                            <option value="">Pilih kelas</option>
                            @foreach ($classesData as $id => $classData)
                                <option value="{{ $id }}">{{ $classData['nama_kelas'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="subject_id" class="mb-2 block text-sm font-medium text-slate-800 dark:text-slate-100">Mata pelajaran</label>
                        <select id="subject_id" name="subject_id" required x-model="selectedSubjectId" @change="selectionChanged = true; selectedAssessmentIds = []"
                            class="min-h-11 w-full rounded-md border-slate-400 bg-white text-sm text-slate-900 focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                            <option value="">Pilih mata pelajaran</option>
                            <template x-for="subject in subjects" :key="subject.id">
                                <option :value="subject.id" :selected="subject.id == selectedSubjectId" x-text="subject.nama"></option>
                            </template>
                        </select>
                    </div>
                </div>

                @if ($classroom && $subject)
                    <div class="flex flex-wrap items-baseline gap-x-4 gap-y-1 border-t border-slate-200 pt-4 text-sm dark:border-slate-700">
                        <p class="font-semibold text-slate-900 dark:text-white">{{ $classroom->tingkat }} - {{ $classroom->nama_kelas }} / {{ $subject->nama_mapel }}</p>
                        <p class="text-slate-700 dark:text-slate-300">KKM: <span class="font-semibold tabular-nums">{{ $subject->kkm }}</span></p>
                    </div>

                    @if ($assessments->isEmpty())
                        <p class="rounded-md bg-slate-100 px-4 py-3 text-sm text-slate-800 dark:bg-slate-900 dark:text-slate-200">
                            Belum ada penilaian tes untuk kelas dan mata pelajaran ini.
                        </p>
                    @else
                        <p x-show="selectionChanged" x-cloak role="status" class="rounded-md bg-slate-100 px-4 py-3 text-sm text-slate-800 dark:bg-slate-900 dark:text-slate-200">
                            Pilihan kelas atau mata pelajaran berubah. Tampilkan penilaian untuk pilihan baru terlebih dahulu.
                        </p>
                        <fieldset x-show="!selectionChanged" :disabled="selectionChanged"
                            class="space-y-3 border-t border-slate-200 pt-4 dark:border-slate-700">
                            <legend class="px-1 text-sm font-semibold text-slate-900 dark:text-white">Penilaian sebagai dasar rerata</legend>
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                @foreach ($assessments as $assessment)
                                    <label for="assessment-{{ $assessment->id }}"
                                        class="flex min-h-11 cursor-pointer items-start gap-3 rounded-md border border-slate-300 px-3 py-2.5 hover:bg-brand-50 dark:border-slate-600 dark:hover:bg-slate-700">
                                        <input id="assessment-{{ $assessment->id }}" type="checkbox" name="assessment_ids[]"
                                            value="{{ $assessment->id }}"
                                            x-model="selectedAssessmentIds"
                                            @checked(in_array($assessment->id, $selectedAssessmentIds, true))
                                            class="mt-1 h-4 w-4 rounded border-slate-500 text-brand-700 focus:ring-brand-700">
                                        <span class="min-w-0 text-sm text-slate-900 dark:text-slate-100">
                                            <span class="block font-medium">{{ $assessment->keterangan }}</span>
                                            <span class="mt-0.5 block text-slate-700 dark:text-slate-300">
                                                {{ $assessment->assessmentType?->nama ?? 'Penilaian' }} · {{ $assessment->tanggal->format('d/m/Y') }} · {{ $assessment->scores_count }} siswa memiliki nilai
                                            </span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                    @endif
                @endif

                <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between">
                    <p x-show="loading" x-cloak role="status" class="text-sm font-medium text-brand-800 dark:text-brand-200">
                        Memuat penilaian dan perhitungan...
                    </p>
                    <button type="submit"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 active:bg-brand-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 disabled:cursor-not-allowed disabled:opacity-60 sm:ml-auto sm:w-auto"
                        :disabled="loading">
                        <span x-text="loading ? 'Memuat...' : (selectionChanged ? 'Tampilkan penilaian baru' : (selectedAssessmentIds.length > 0 ? 'Hitung nilai siswa' : 'Tampilkan penilaian'))"></span>
                    </button>
                </div>
            </form>

            @if (count($selectedAssessmentIds) > 0)
                @php
                    $eligibleCount = $rows->where('eligible', true)->count();
                    $completeCount = $rows->where('is_complete', true)->count();
                @endphp
                <section aria-labelledby="hasil-perhitungan" class="space-y-4">
                    <div class="flex flex-wrap items-end justify-between gap-3">
                        <div>
                            <h2 id="hasil-perhitungan" class="text-lg font-semibold text-slate-900 dark:text-white">Hasil perhitungan</h2>
                            <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">
                                {{ $eligibleCount }} siswa di bawah KKM dari {{ $completeCount }} siswa dengan nilai lengkap.
                            </p>
                        </div>
                    </div>

                    @if ($rows->isEmpty())
                        <div class="rounded-md border border-slate-300 bg-white px-4 py-5 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                            Belum ada siswa yang terdaftar di kelas ini.
                        </div>
                    @else
                        <form method="POST" action="{{ route('remedials.store') }}"
                            x-data="{ loading: false }" @submit="loading = true"
                            class="space-y-4">
                            @csrf
                            <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
                            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                            @foreach ($selectedAssessmentIds as $assessmentId)
                                <input type="hidden" name="assessment_ids[]" value="{{ $assessmentId }}">
                            @endforeach

                            @if ($errors->has('records'))
                                <p class="text-sm font-medium text-rose-800 dark:text-rose-200">{{ $errors->first('records') }}</p>
                            @endif

                            <div class="overflow-x-auto rounded-lg border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-800">
                                <table class="w-full min-w-[860px] divide-y divide-slate-200 text-left text-sm dark:divide-slate-700">
                                    <caption class="sr-only">Rerata nilai dan status remedial siswa</caption>
                                    <thead class="bg-slate-50 text-slate-800 dark:bg-slate-900/50 dark:text-slate-100">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 font-semibold">Siswa</th>
                                            <th scope="col" class="px-4 py-3 text-center font-semibold">Nilai lengkap</th>
                                            <th scope="col" class="px-4 py-3 text-center font-semibold">Rerata</th>
                                            <th scope="col" class="px-4 py-3 font-semibold">Hasil</th>
                                            <th scope="col" class="px-4 py-3 font-semibold">Status remedial</th>
                                            <th scope="col" class="px-4 py-3 font-semibold">Nilai remedial</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                        @foreach ($rows as $row)
                                            <tr class="text-slate-800 dark:text-slate-100">
                                                <th scope="row" class="px-4 py-3 font-medium">{{ $row->student->nama_lengkap }}</th>
                                                <td class="px-4 py-3 text-center tabular-nums">{{ $row->score_count }}/{{ $row->assessment_count }}</td>
                                                <td class="px-4 py-3 text-center tabular-nums">{{ $row->is_complete ? number_format($row->average, 2) : '-' }}</td>
                                                <td class="px-4 py-3">
                                                    @if (! $row->is_complete)
                                                        <span>Nilai belum lengkap</span>
                                                    @elseif ($row->eligible)
                                                        <span class="font-semibold text-amber-800 dark:text-amber-200">Di bawah KKM</span>
                                                    @else
                                                        <span class="font-semibold text-brand-800 dark:text-brand-200">Tuntas</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3">
                                                    @if ($row->eligible)
                                                        <label for="student-{{ $row->student_id }}-completed" class="flex min-h-11 cursor-pointer items-center gap-2">
                                                            <input id="student-{{ $row->student_id }}-completed" type="checkbox"
                                                                name="records[{{ $row->student_id }}][completed]" value="1"
                                                                @checked(old('records.'.$row->student_id.'.completed'))
                                                                class="h-4 w-4 rounded border-slate-500 text-brand-700 focus:ring-brand-700">
                                                            <span>Sudah remedial</span>
                                                        </label>
                                                    @else
                                                        <span class="text-slate-600 dark:text-slate-300">Tidak perlu dicatat</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3">
                                                    @if ($row->eligible)
                                                        <label for="student-{{ $row->student_id }}-score" class="mb-1 block text-xs font-medium text-slate-800 dark:text-slate-200">Nilai remedial</label>
                                                        <input id="student-{{ $row->student_id }}-score" type="number"
                                                            name="records[{{ $row->student_id }}][score]" min="0" max="100" step="0.01"
                                                            value="{{ old('records.'.$row->student_id.'.score') }}"
                                                            class="min-h-11 w-32 rounded-md border-slate-400 bg-white text-sm text-slate-900 focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                                                            placeholder="Opsional">
                                                    @else
                                                        <span aria-hidden="true">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($eligibleCount > 0)
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="max-w-2xl text-sm text-slate-700 dark:text-slate-300">
                                        Centang siswa yang sudah remedial atau isi nilai remedial. Mengisi nilai juga menandai remedial sebagai selesai.
                                    </p>
                                    <button type="submit" :disabled="loading"
                                        class="inline-flex min-h-11 w-full items-center justify-center rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 active:bg-brand-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto">
                                        <span x-text="loading ? 'Menyimpan...' : 'Simpan program remedial'"></span>
                                    </button>
                                </div>
                                <p x-show="loading" x-cloak role="status" class="text-sm font-medium text-brand-800 dark:text-brand-200">Menyimpan program remedial...</p>
                            @elseif ($completeCount > 0)
                                <p class="rounded-md border border-brand-600 bg-brand-50 px-4 py-3 text-sm text-brand-900 dark:border-brand-500 dark:bg-brand-900/30 dark:text-brand-100">
                                    Semua siswa dengan nilai lengkap sudah mencapai KKM. Tidak ada catatan remedial yang perlu disimpan.
                                </p>
                            @else
                                <p class="rounded-md border border-amber-700 bg-amber-50 px-4 py-3 text-sm text-amber-950 dark:border-amber-500 dark:bg-amber-950/40 dark:text-amber-100">
                                    Nilai belum tersedia lengkap untuk penilaian yang dipilih. Lengkapi nilai terlebih dahulu agar rerata dapat dihitung.
                                </p>
                            @endif
                        </form>
                    @endif
                </section>
            @endif
        @endif
    </div>
</x-app-layout>
