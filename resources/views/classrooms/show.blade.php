<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
                Anggota Rombel: <span class="text-indigo-600">{{ $classroom->tingkat }} - {{ $classroom->nama_kelas
                    }}</span>
            </h2>
            @can('edit-classes')
            <a href="{{ route('classrooms.index') }}" class="text-sm font-bold text-slate-500 hover:text-slate-700">
                &larr; Kembali ke Daftar Kelas
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div
                class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase">Wali Kelas</p>
                    <p class="font-semibold text-slate-800 dark:text-slate-200">{{
                        $classroom->homeroomTeacher->nama_lengkap ?? 'Belum Ditentukan' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase">Tahun Ajaran</p>
                    <p class="font-semibold text-slate-800 dark:text-slate-200">{{
                        $classroom->academicYear->tahun_ajaran ?? '-'
                        }} ({{ $classroom->academicYear->semester ?? '-' }})</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase">Kapasitas</p>
                    <p
                        class="font-semibold {{ $classroom->students->count() >= $classroom->kapasitas ? 'text-rose-500' : 'text-indigo-600' }}">
                        {{ $classroom->students->count() }} / {{ $classroom->kapasitas }} Siswa
                    </p>
                </div>
                <div class="flex items-center justify-end">
                    @can('edit-classes')
                    <button x-data @click="$dispatch('open-add-student-modal')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-lg hover:bg-indigo-700 transition">
                        + Tambah Siswa
                    </button>
                    @endcan
                </div>
            </div>

            <div x-data="{
                selected: [],
                toggleAll(e) {
                    let checkboxes = document.querySelectorAll('.student-checkbox');
                    this.selected = [];
                    if(e.target.checked) {
                        checkboxes.forEach(cb => { cb.checked = true; this.selected.push(cb.value); });
                    } else {
                        checkboxes.forEach(cb => cb.checked = false);
                    }
                }
            }">
                <form action="{{ route('classrooms.remove-multiple', $classroom->id) }}" method="POST">
                    @csrf @method('DELETE')

                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">

                        <div
                            class="p-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-900/50">
                            <span class="text-sm font-bold text-slate-600 dark:text-slate-300">Daftar Siswa di Kelas
                                Ini</span>

                            <button x-show="selected.length > 0" style="display: none;" type="submit"
                                onclick="return confirm('Keluarkan semua siswa yang dipilih?')"
                                class="px-3 py-1.5 bg-rose-100 text-rose-700 border border-rose-200 hover:bg-rose-600 hover:text-white rounded-lg text-xs font-bold transition">
                                Keluarkan <span x-text="selected.length"></span> Siswa Terpilih
                            </button>
                        </div>

                        <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                            <thead
                                class="text-xs text-slate-700 uppercase bg-slate-100/50 dark:bg-slate-900/50 dark:text-slate-300">
                                <tr>
                                    @can('edit-classes')
                                    <th class="px-6 py-4 w-10">
                                        <input type="checkbox" @change="toggleAll"
                                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    </th>
                                    @endcan
                                    <th class="px-6 py-4">No</th>
                                    <th class="px-6 py-4">NISN / NIPD</th>
                                    <th class="px-6 py-4">Nama Lengkap Siswa</th>
                                    <th class="px-6 py-4">L/P</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($classroom->students as $index => $siswa)
                                <tr
                                    class="border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                    @can('edit-classes')
                                    <td class="px-6 py-4">
                                        <input type="checkbox" name="student_ids[]" value="{{ $siswa->id }}"
                                            x-model="selected"
                                            class="student-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                    </td>
                                    @endcan
                                    <td class="px-6 py-4 font-bold">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <span class="block font-semibold text-slate-800 dark:text-slate-200">{{
                                            $siswa->nisn ?? '-' }}</span>
                                        <span class="block text-xs text-slate-500">{{ $siswa->nipd ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">{{
                                        $siswa->nama_lengkap }}</td>
                                    <td class="px-6 py-4">{{ $siswa->jenis_kelamin }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                        Belum ada siswa yang dimasukkan ke kelas ini.<br>Klik tombol <b>"+ Tambah
                                            Siswa"</b> di atas.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            @can('edit-classes')
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div
                    class="p-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-900/50">
                    <div>
                        <span class="text-sm font-bold text-slate-600 dark:text-slate-300">Penugasan Guru Mata Pelajaran
                            Khusus</span>
                        <p class="text-xs text-slate-500 mt-0.5">Atur pengampu mapel khusus (Agama, PJOK, dll) untuk
                            kelas ini.</p>
                    </div>
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg shrink-0">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                </div>

                <form action="{{ route('classrooms.assign-subjects', $classroom->id) }}" method="POST">
                    @csrf
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                            <thead
                                class="text-xs text-slate-700 uppercase bg-slate-100/50 dark:bg-slate-900/50 dark:text-slate-300">
                                <tr>
                                    <th class="px-6 py-4">Mata Pelajaran</th>
                                    <th class="px-6 py-4 w-32">Kode Mapel</th>
                                    <th class="px-6 py-4">Guru Pengampu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($availableSubjects ?? [] as $index => $subject)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                    <td class="px-6 py-4">
                                        <input type="hidden" name="assignments[{{ $index }}][subject_id]"
                                            value="{{ $subject->id }}">
                                        <span class="font-bold text-slate-800 dark:text-slate-200">{{
                                            $subject->nama_mapel }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-400 font-mono text-xs">{{ $subject->kode_mapel ??
                                        '-' }}</td>
                                    <td class="px-6 py-4">
                                        <select name="assignments[{{ $index }}][employee_id]"
                                            class="w-full md:w-64 rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-xs focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                            {{ !auth()->user()->can('edit-classes') ? 'disabled' : '' }}>
                                            <option value="">-- Kosongkan (Pilih Guru) --</option>
                                            @foreach($teachers ?? [] as $teacher)
                                            <option value="{{ $teacher->id }}" {{ ($currentAssignments[$subject->id] ??
                                                '') == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->nama_lengkap }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-slate-500 italic">
                                        Tidak ada mata pelajaran khusus (Guru Mapel) yang ditemukan untuk tingkat kelas
                                        ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @can('edit-classes')
                    @if(isset($availableSubjects) && $availableSubjects->count() > 0)
                    <div
                        class="p-4 bg-slate-50 dark:bg-slate-900/30 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                        <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white rounded-lg font-bold text-xs shadow-md hover:bg-indigo-700 transition">
                            Simpan Penugasan Guru
                        </button>
                    </div>
                    @endif
                    @endcan
                </form>
            </div>
        </div>
    </div>

    <div x-data="{ show: false, search: '' }" @open-add-student-modal.window="show = true" x-show="show"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
        style="display: none;">

        <form action="{{ route('classrooms.assign', $classroom->id) }}" method="POST" @click.away="show = false"
            class="bg-white dark:bg-slate-800 w-full max-w-lg rounded-2xl shadow-2xl flex flex-col max-h-[90vh] overflow-hidden">
            @csrf

            <div
                class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 z-10 shrink-0">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Masukkan Siswa ke Rombel</h3>
                <p class="text-xs text-slate-500 mt-1 mb-3">Centang nama-nama siswa yang ingin dimasukkan.</p>

                @if($availableStudents->count() > 0)
                <input type="text" x-model="search" placeholder="Cari nama siswa..."
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm">
                @endif
            </div>

            <div class="p-6 overflow-y-auto flex-1 bg-slate-50/50 dark:bg-slate-900/20">
                @if($availableStudents->count() > 0)
                <div class="space-y-2">
                    @foreach($availableStudents as $availSiswa)
                    <label
                        x-show="search === '' || '{{ strtolower($availSiswa->nama_lengkap) }}'.includes(search.toLowerCase())"
                        class="flex items-center gap-4 cursor-pointer p-3 bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-slate-700 rounded-xl transition border border-slate-200 dark:border-slate-700 hover:border-indigo-300 shadow-sm">

                        <input type="checkbox" name="student_ids[]" value="{{ $availSiswa->id }}"
                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-5 h-5 shrink-0">

                        <div class="flex-1">
                            <span class="block text-sm font-bold text-slate-800 dark:text-slate-200">{{
                                $availSiswa->nama_lengkap }}</span>
                            <span class="block text-[10px] text-slate-500 uppercase tracking-wider mt-0.5">
                                {{ $availSiswa->jenis_kelamin == 'L' ? 'Laki-Laki' : 'Perempuan' }} • NISN: {{
                                $availSiswa->nisn ?? '-' }}
                            </span>
                        </div>
                    </label>
                    @endforeach
                </div>
                @else
                <div
                    class="p-4 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 rounded-lg text-sm text-center">
                    Tidak ada siswa tersedia (Semua siswa di sekolah ini sudah masuk ke kelas).
                </div>
                @endif
            </div>

            <div
                class="px-6 py-4 bg-slate-50 dark:bg-slate-900/80 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700 shrink-0">
                <button type="button" @click="show = false"
                    class="text-xs font-bold text-slate-500 uppercase hover:text-slate-700 py-2">Batal</button>

                @if($availableStudents->count() > 0)
                <button type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold uppercase shadow-md hover:bg-indigo-700 transition">
                    Tambahkan Siswa
                </button>
                @endif
            </div>
        </form>
    </div>
    @endcan
</x-app-layout>