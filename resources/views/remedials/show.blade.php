<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Detail Program Remedial</h1>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        @if (session('success'))
            <div role="status" class="rounded-md border border-brand-600 bg-brand-50 px-4 py-3 text-sm text-brand-900 dark:border-brand-500 dark:bg-brand-900/30 dark:text-brand-100">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="rounded-md border border-rose-700 bg-rose-50 px-4 py-3 text-sm text-rose-900 dark:border-rose-500 dark:bg-rose-950/40 dark:text-rose-100">
                <p class="font-semibold">Perubahan belum dapat disimpan.</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="space-y-4 border-b border-slate-200 pb-5 dark:border-slate-700">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                        {{ $remedialProgram->classroom->tingkat }} - {{ $remedialProgram->classroom->nama_kelas }} / {{ $remedialProgram->subject->nama_mapel }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">KKM saat program dibuat: {{ $remedialProgram->records->first()?->kkm ?? $remedialProgram->subject->kkm }}</p>
                </div>
                <a href="{{ route('remedials.index') }}"
                    class="inline-flex min-h-11 items-center rounded-md px-3 text-sm font-semibold text-brand-800 hover:bg-brand-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:text-brand-200 dark:hover:bg-brand-900/40">
                    Kembali ke daftar
                </a>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Penilaian yang dirata-ratakan</h3>
                <ul class="mt-2 flex flex-wrap gap-2">
                    @foreach ($remedialProgram->assessments as $assessment)
                        <li class="rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-800 dark:border-slate-600 dark:text-slate-100">
                            {{ $assessment->keterangan }} ({{ $assessment->assessmentType?->nama ?? 'Penilaian' }})
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        @if ($remedialProgram->records->isEmpty())
            <div class="rounded-lg border border-slate-300 bg-white px-5 py-8 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                Tidak ada siswa yang perlu remedial saat program ini dibuat.
            </div>
        @else
            <form method="POST" action="{{ route('remedials.update', $remedialProgram) }}"
                x-data="{ loading: false }" @submit="loading = true" class="space-y-4">
                @csrf
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Status siswa</h3>
                    <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">Ubah status atau nilai remedial, lalu simpan perubahan.</p>
                </div>

                <div class="overflow-x-auto rounded-lg border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-800">
                    <table class="w-full min-w-[780px] divide-y divide-slate-200 text-left text-sm dark:divide-slate-700">
                        <caption class="sr-only">Status remedial dan rerata nilai siswa</caption>
                        <thead class="bg-slate-50 text-slate-800 dark:bg-slate-900/50 dark:text-slate-100">
                            <tr>
                                <th scope="col" class="px-4 py-3 font-semibold">Siswa</th>
                                <th scope="col" class="px-4 py-3 text-center font-semibold">Rerata awal</th>
                                <th scope="col" class="px-4 py-3 text-center font-semibold">KKM</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Sudah remedial</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Nilai remedial</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @foreach ($remedialProgram->records as $record)
                                <tr class="text-slate-800 dark:text-slate-100">
                                    <th scope="row" class="px-4 py-3 font-medium">{{ $record->student->nama_lengkap }}</th>
                                    <td class="px-4 py-3 text-center tabular-nums">{{ number_format($record->average_score, 2) }}</td>
                                    <td class="px-4 py-3 text-center tabular-nums">{{ $record->kkm }}</td>
                                    <td class="px-4 py-3">
                                        <label for="record-{{ $record->id }}-completed" class="flex min-h-11 cursor-pointer items-center gap-2">
                                            <input id="record-{{ $record->id }}-completed" type="checkbox"
                                                name="records[{{ $record->id }}][completed]" value="1"
                                                @checked(old('records.'.$record->id.'.completed', $record->is_completed))
                                                class="h-4 w-4 rounded border-slate-500 text-brand-700 focus:ring-brand-700">
                                            <span>Sudah remedial</span>
                                        </label>
                                    </td>
                                    <td class="px-4 py-3">
                                        <label for="record-{{ $record->id }}-score" class="mb-1 block text-xs font-medium text-slate-800 dark:text-slate-200">Nilai remedial</label>
                                        <input id="record-{{ $record->id }}-score" type="number"
                                            name="records[{{ $record->id }}][score]" min="0" max="100" step="0.01"
                                            value="{{ old('records.'.$record->id.'.score', $record->remedial_score) }}"
                                            class="min-h-11 w-32 rounded-md border-slate-400 bg-white text-sm text-slate-900 focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                                            placeholder="Opsional">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-700 dark:text-slate-300">Nilai remedial yang diisi otomatis menandai siswa sudah remedial.</p>
                    <button type="submit" :disabled="loading"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 active:bg-brand-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto">
                        <span x-text="loading ? 'Menyimpan...' : 'Simpan perubahan'"></span>
                    </button>
                </div>
                <p x-show="loading" x-cloak role="status" class="text-sm font-medium text-brand-800 dark:text-brand-200">Menyimpan perubahan...</p>
            </form>
        @endif
    </div>
</x-app-layout>
