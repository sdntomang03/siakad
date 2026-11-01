<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Buat Penilaian Observasi / Non-Tes</h2>
    </x-slot>

    <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <!-- Tambahkan properti descriptors pada Alpine data -->
        <div x-data="{
                classesData: {{ json_encode($classesData) }},
                selectedClassId: '',
                availableSubjects: [],
                descriptors: [''], // Mulai dengan 1 input kriteria kosong
                updateSubjects() {
                    this.availableSubjects = this.selectedClassId ? this.classesData[this.selectedClassId].subjects : [];
                },
                addDescriptor() {
                    this.descriptors.push('');
                },
                removeDescriptor(index) {
                    if (this.descriptors.length > 1) {
                        this.descriptors.splice(index, 1);
                    }
                }
            }"
            class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-8">

            <!-- Ubah route form menuju observations.store -->
            <form action="{{ route('observations.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="classroom_id" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Pilih Kelas</label>
                    <select id="classroom_id" name="classroom_id" required x-model="selectedClassId" @change="updateSubjects()"
                        class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                        <option value="">-- Pilih Kelas --</option>
                        <template x-for="(data, classId) in classesData" :key="classId">
                            <option :value="classId" x-text="data.nama_kelas"></option>
                        </template>
                    </select>
                </div>

                <div x-show="selectedClassId" x-transition.opacity>
                    <label for="subject_id" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Mata Pelajaran</label>
                    <select id="subject_id" name="subject_id" required
                        class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                        <option value="">-- Pilih Mapel --</option>
                        <template x-for="subject in availableSubjects" :key="subject.id">
                            <option :value="subject.id" x-text="subject.nama"></option>
                        </template>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="assessment_type_id" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Jenis Penilaian</label>
                        <select id="assessment_type_id" name="assessment_type_id" required
                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                            <option value="">-- Pilih Jenis --</option>
                            @foreach($assessmentTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tanggal" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Tanggal Pelaksanaan</label>
                        <input id="tanggal" type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <label for="keterangan" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Nama Penilaian /
                            Topik</label>
                        <input id="keterangan" type="text" name="keterangan" placeholder="Contoh: Observasi proyek"
                            required
                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                    </div>
                    <!-- Pilihan Skala Observasi -->
                    <div>
                        <label for="scale" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Skala Nilai</label>
                        <select id="scale" name="scale" required
                            class="w-full rounded-md border-slate-300 text-sm font-semibold focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                            <option value="1">1 Saja</option>
                            <option value="2">1 - 2</option>
                            <option value="3">1 - 3</option>
                            <option value="4">1 - 4</option>
                            <option value="5">1 - 5</option>
                        </select>
                    </div>
                </div>

                <!-- Bagian Input Dinamis Kriteria/Deskriptor -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-3">Daftar Kriteria
                        Observasi</label>

                    <div class="space-y-3">
                        <template x-for="(desc, index) in descriptors" :key="index">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold text-slate-400" x-text="(index + 1) + '.'"></span>
                                <input type="text" x-model="descriptors[index]" :name="'descriptors['+index+']'"
                                    placeholder="Cth: Peserta didik mampu bekerja sama secara tim" required
                                    class="flex-1 rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-emerald-500">

                                <button type="button" @click="removeDescriptor(index)" x-show="descriptors.length > 1"
                                    class="px-3 py-2 bg-rose-50 text-rose-600 hover:bg-rose-100 hover:text-rose-700 font-bold rounded-md transition"
                                    title="Hapus Kriteria">
                                    &times;
                                </button>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addDescriptor()"
                        class="mt-4 inline-flex items-center gap-1 text-sm font-bold text-emerald-600 hover:text-emerald-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        Tambah Kriteria Baru
                    </button>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                    <button type="submit"
                        class="px-6 py-2.5 bg-emerald-600 text-white rounded-md text-sm font-black shadow-lg hover:bg-emerald-700 transition">
                        Lanjut Input Nilai &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>