<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Edit Detail Penilaian</h2>
    </x-slot>

    <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-8">
            <div class="mb-6 border-b border-slate-200 pb-5 dark:border-slate-700">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Kelas</p>
                <p class="mt-1 font-semibold text-slate-800 dark:text-slate-200">
                    {{ $assessment->classroom->tingkat }} - {{ $assessment->classroom->nama_kelas }}
                </p>
                <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">
                    {{ $assessment->scores_count }} nilai siswa tersimpan. Perubahan detail tidak menghapus nilai, dan rekap akan mengelompokkannya menurut mapel serta jenis yang baru.
                </p>
            </div>

            <form action="{{ route('assessments.update', $assessment) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="subject_id" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Mata Pelajaran
                    </label>
                    <select id="subject_id" name="subject_id" required
                        class="min-h-11 w-full rounded-md border-slate-300 text-sm transition-colors hover:border-slate-400 focus:border-brand-600 focus:ring-brand-600 active:border-brand-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:hover:border-slate-500">
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected(old('subject_id', $assessment->subject_id) == $subject->id)>
                            {{ $subject->nama_mapel }}
                        </option>
                        @endforeach
                    </select>
                    @error('subject_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="assessment_type_id" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Jenis Penilaian
                    </label>
                    <select id="assessment_type_id" name="assessment_type_id" required
                        class="min-h-11 w-full rounded-md border-slate-300 text-sm transition-colors hover:border-slate-400 focus:border-brand-600 focus:ring-brand-600 active:border-brand-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:hover:border-slate-500">
                        @foreach($assessmentTypes as $type)
                        <option value="{{ $type->id }}" @selected(old('assessment_type_id', $assessment->assessment_type_id) == $type->id)>
                            {{ $type->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('assessment_type_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="keterangan" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Keterangan / Materi
                    </label>
                    <input id="keterangan" type="text" name="keterangan" maxlength="255" required
                        value="{{ old('keterangan', $assessment->keterangan) }}"
                        class="min-h-11 w-full rounded-md border-slate-300 text-sm transition-colors hover:border-slate-400 focus:border-brand-600 focus:ring-brand-600 active:border-brand-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:hover:border-slate-500">
                    @error('keterangan')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 dark:border-slate-700 sm:flex-row sm:justify-end">
                    <a href="{{ route('assessments.index') }}"
                        class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 active:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700 dark:active:bg-slate-600">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 active:bg-brand-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 disabled:cursor-not-allowed disabled:opacity-60">
                        Simpan Detail
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
