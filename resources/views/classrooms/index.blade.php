<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">
                Manajemen Kelas & Rombel
            </h2>

            @can('edit-classes')

            <button x-data @click="$dispatch('open-class-modal')"
                class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                + Tambah Kelas
            </button>

            @endcan
        </div>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto max-w-screen-2xl space-y-6">

            @role('superadmin')
            <div
                class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                <form action="{{ route('classrooms.index') }}" method="GET">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Pilih Sekolah Target</label>
                    <select name="school_id" onchange="this.form.submit()"
                        class="min-h-11 w-full rounded-md border-slate-300 text-sm focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-700 md:w-1/2">
                        <option value="">-- Pilih Sekolah untuk Menampilkan Data Kelas --</option>
                        @foreach($schools as $sekolah)
                        <option value="{{ $sekolah->id }}" {{ request('school_id')==$sekolah->id ? 'selected' : '' }}>
                            {{ $sekolah->npsn }} - {{ $sekolah->nama_sekolah }}
                        </option>
                        @endforeach
                    </select>
                </form>
            </div>
            @endrole

            @if(auth()->user()->hasRole('superadmin') && !request('school_id'))
            <div
                class="rounded-lg border border-slate-200 bg-white px-5 py-12 text-center dark:border-slate-700 dark:bg-slate-800">
                <div
                    class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-md bg-brand-50 text-brand-800 dark:bg-brand-900 dark:text-brand-200">
                    <svg aria-hidden="true" class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">Pilih Sekolah Terlebih Dahulu</h3>
                <p class="text-slate-500 dark:text-slate-400 mt-2 max-w-md mx-auto">Anda masuk sebagai Superadmin.
                    Silakan pilih sekolah dari dropdown di atas untuk melihat dan mengelola data kelas & rombel.</p>
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($classrooms as $kelas)
                <div
                    class="flex flex-col overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 p-5 dark:border-slate-700 sm:p-6">
                        <div>
                            <span class="rounded-md bg-brand-50 px-2 py-1 text-xs font-medium text-brand-900 dark:bg-brand-900 dark:text-brand-100">Kelas {{
                                $kelas->tingkat }}</span>
                            <h3 class="text-2xl font-black text-slate-800 dark:text-white mt-2">{{ $kelas->nama_kelas }}
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">TA: {{ $kelas->academicYear->tahun_ajaran ?? 'Tidak
                                Set' }}</p>
                        </div>

                        <div x-data="{ open: false }" class="relative">
                            <button type="button" aria-label="Buka opsi kelas" @click="open = !open" @click.away="open = false"
                                class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 hover:text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                                    </path>
                                </svg>
                            </button>
                            <div x-show="open"
                                class="absolute right-0 z-10 mt-2 w-40 rounded-md border border-slate-200 bg-white shadow-lg dark:border-slate-600 dark:bg-slate-700"
                                style="display: none;">
                                <button
                                    @click="$dispatch('open-class-modal', { kelas: {{ json_encode($kelas) }} }); open = false"
                                    class="w-full text-left px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-600">Edit
                                    Info</button>

                                <form action="{{ route('classrooms.destroy', $kelas->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Hapus kelas ini? Semua data rombel di dalamnya akan hilang.')"
                                        class="w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-600">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 space-y-4 bg-slate-50/50 p-5 dark:bg-slate-800/50 sm:p-6">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold">
                                {{ substr($kelas->homeroomTeacher->nama_lengkap ?? '?', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Wali Kelas</p>
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{
                                    $kelas->homeroomTeacher->nama_lengkap ?? 'Belum Ditentukan' }}</p>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-bold text-slate-500">Kapasitas Terisi</span>
                                <span
                                    class="font-bold {{ $kelas->students_count >= $kelas->kapasitas ? 'text-rose-500' : 'text-brand-600' }}">{{
                                    $kelas->students_count }} / {{ $kelas->kapasitas }}</span>
                            </div>
                            <div                             class="h-2 w-full rounded-full bg-slate-200 dark:bg-slate-700">
                            <div class="h-2 rounded-full bg-brand-700"
                                    style="width: {{ min(($kelas->students_count / $kelas->kapasitas) * 100, 100) }}%">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-white dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700">
                        <a href="{{ route('classrooms.show', $kelas->hashid) }}"
                            class="block w-full text-center py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                            Daftar Siswa &rarr;
                        </a>
                    </div>
                </div>
                @empty
                <div
                    class="col-span-1 rounded-lg border border-slate-200 bg-white px-5 py-12 text-center dark:border-slate-700 dark:bg-slate-800 md:col-span-3">
                    <p class="text-slate-500 dark:text-slate-400">Belum ada kelas yang ditambahkan di sekolah ini.</p>
                </div>
                @endforelse
            </div>
            @endif
        </div>
    </div>

    <div x-data="{
            show: false, isEdit: false, title: 'Tambah Kelas', action: '{{ route('classrooms.store') }}', method: 'POST',
            form: { academic_year_id: '', tingkat: '', nama_kelas: '', homeroom_teacher_id: '', kapasitas: 30 }
        }" @open-class-modal.window="
            show = true;
            if ($event.detail && $event.detail.kelas) {
                isEdit = true;
                title = 'Edit Kelas';
                action = '{{ url('classrooms') }}/' + $event.detail.kelas.id;
                method = 'PUT';
                form = {
                    academic_year_id: $event.detail.kelas.academic_year_id,
                    tingkat: $event.detail.kelas.tingkat,
                    nama_kelas: $event.detail.kelas.nama_kelas,
                    homeroom_teacher_id: $event.detail.kelas.homeroom_teacher_id,
                    kapasitas: $event.detail.kelas.kapasitas
                };
            } else {
                isEdit = false; title = 'Tambah Kelas'; action = '{{ route('classrooms.store') }}'; method = 'POST';
                form = { academic_year_id: '', tingkat: '', nama_kelas: '', homeroom_teacher_id: '', kapasitas: 30 };
            }
        " x-show="show"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 "
        style="display: none;">

        <div @click.away="show = false"
            class="w-full max-w-md overflow-hidden rounded-lg border border-slate-200 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-800">
            <form :action="action" method="POST">
                @csrf
                <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>

                @if(auth()->user()->hasRole('superadmin'))
                <input type="hidden" name="school_id" value="{{ request('school_id') }}">
                @endif

                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="title"></h3>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tahun Ajaran</label>
                        <select name="academic_year_id" x-model="form.academic_year_id" required
                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm">
                            <option value="">-- Pilih TA --</option>
                            @foreach($academicYears as $ta)
                            <option value="{{ $ta->id }}">{{ $ta->tahun_ajaran }} - {{ $ta->semester }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tingkat</label>
                            <input type="text" name="tingkat" x-model="form.tingkat" placeholder="Contoh: 1, 2, 4"
                                required
                                class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Rombel</label>
                            <input type="text" name="nama_kelas" x-model="form.nama_kelas" placeholder="Contoh: 4B"
                                required
                                class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Wali Kelas</label>
                        <select name="homeroom_teacher_id" x-model="form.homeroom_teacher_id"
                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm">
                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach($teachers as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kapasitas Maksimal</label>
                        <input type="number" name="kapasitas" x-model="form.kapasitas" required
                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm">
                    </div>
                </div>

                <div
                    class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" @click="show = false"
                        class="text-xs font-bold text-slate-500 uppercase hover:text-slate-700">Batal</button>
                    <button type="submit"
                        class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-6 py-2 text-sm font-semibold text-white transition-colors hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>