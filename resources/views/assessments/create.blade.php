<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Buat Penilaian Baru</h2>
    </x-slot>

    <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div x-data="{
                classesData: {{ json_encode($classesData) }},
                selectedClassId: '',
                availableSubjects: [],
                updateSubjects() {
                    // Otomatis ubah daftar mapel saat kelas dipilih
                    this.availableSubjects = this.selectedClassId ? this.classesData[this.selectedClassId].subjects : [];
                }
            }"
            class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-8">

            <form action="{{ route('assessments.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Pilih Kelas</label>
                    <select name="classroom_id" required x-model="selectedClassId" @change="updateSubjects()"
                        class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                        <option value="">-- Pilih Kelas --</option>

                        <template x-for="(data, classId) in classesData" :key="classId">
                            <option :value="classId" x-text="data.nama_kelas"></option>
                        </template>
                    </select>
                </div>

                <div x-show="selectedClassId" x-transition.opacity>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Mata Pelajaran</label>
                    <select name="subject_id" required
                        class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                        <option value="">-- Pilih Mapel --</option>
                        <template x-for="subject in availableSubjects" :key="subject.id">
                            <option :value="subject.id" x-text="subject.nama"></option>
                        </template>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Jenis Penilaian</label>
                        <select name="assessment_type_id" required
                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                            <option value="">-- Pilih Jenis --</option>
                            @foreach($assessmentTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Tanggal Pelaksanaan</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Keterangan / Materi
                        (Opsional)</label>
                    <input type="text" name="keterangan" placeholder="Cth: Ulangan Bab 1: Tumbuhan Sumber Kehidupan"
                        required
                        class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                </div>

                <div class="flex justify-end border-t border-slate-200 pt-5 dark:border-slate-700">
                    <button type="submit"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-md bg-brand-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 sm:w-auto">
                        Lanjut Input Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>