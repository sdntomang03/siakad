<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">Buat Penilaian Baru</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div x-data="{
                classesData: {{ json_encode($classesData) }},
                selectedClassId: '',
                availableSubjects: [],
                updateSubjects() {
                    // Otomatis ubah daftar mapel saat kelas dipilih
                    this.availableSubjects = this.selectedClassId ? this.classesData[this.selectedClassId].subjects : [];
                }
            }"
            class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">

            <form action="{{ route('assessments.store') }}" method="POST" class="space-y-6">
                @csrf

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

                <div x-show="selectedClassId" x-transition.opacity>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">2. Mata Pelajaran</label>
                    <select name="subject_id" required
                        class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500">
                        <option value="">-- Pilih Mapel --</option>
                        <template x-for="subject in availableSubjects" :key="subject.id">
                            <option :value="subject.id" x-text="subject.nama"></option>
                        </template>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Jenis Penilaian</label>
                        <select name="assessment_type_id" required
                            class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500">
                            <option value="">-- Pilih Jenis --</option>
                            @foreach($assessmentTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Tanggal Pelaksanaan</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                            class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Keterangan / Materi
                        (Opsional)</label>
                    <input type="text" name="keterangan" placeholder="Cth: Ulangan Bab 1: Tumbuhan Sumber Kehidupan"
                        required
                        class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500">
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                    <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-black shadow-lg hover:bg-indigo-700 transition">
                        Lanjut Input Nilai &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>