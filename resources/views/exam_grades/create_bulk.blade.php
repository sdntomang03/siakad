<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
                Input Nilai Kelas: <span class="text-indigo-600">Form Massal</span>
            </h2>

            <a href="{{ route('exam-grades.index') }}" class="text-sm font-bold text-slate-500 hover:text-slate-700">
                &larr; Kembali ke Daftar Nilai
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="text-base font-black text-slate-800 dark:text-white flex items-center">
                        <span
                            class="flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-400 text-xs mr-2">1</span>
                        Pengaturan Penilaian
                    </h3>
                </div>

                <form action="{{ route('exam-grades.createBulk') }}" method="GET" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Pilih Kelas</label>
                            <select name="classroom_id"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500"
                                required>
                                <option value="">-- Kelas --</option>
                                @foreach($classrooms as $kelas)
                                <option value="{{ $kelas->id }}" {{ request('classroom_id')==$kelas->id ? 'selected' :
                                    '' }}>
                                    {{ $kelas->nama_kelas }} (Tingkat {{ $kelas->tingkat }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mata Pelajaran</label>
                            <select name="subject_id"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500"
                                required>
                                <option value="">-- Mapel --</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id')==$subject->id ? 'selected' :
                                    '' }}>
                                    {{ $subject->nama_mapel }} (Kls {{ $subject->tingkat }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kategori Ujian</label>
                            <select name="kategori_ujian"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500"
                                required>
                                <option value="">-- Kategori --</option>
                                <option value="Ujian Sekolah" {{ request('kategori_ujian')=='Ujian Sekolah' ? 'selected'
                                    : '' }}>Ujian Sekolah</option>
                                <option value="Ujian Praktik" {{ request('kategori_ujian')=='Ujian Praktik' ? 'selected'
                                    : '' }}>Ujian Praktik</option>
                                <option value="Ujian Tengah Semester" {{
                                    request('kategori_ujian')=='Ujian Tengah Semester' ? 'selected' : '' }}>UTS</option>
                                <option value="Ujian Akhir Semester" {{
                                    request('kategori_ujian')=='Ujian Akhir Semester' ? 'selected' : '' }}>UAS</option>
                            </select>
                        </div>


                    </div>

                    <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-bold shadow-sm hover:bg-indigo-700 transition uppercase text-xs w-full md:w-auto">
                        Tampilkan Form Nilai Siswa
                    </button>
                </form>
            </div>

            @if($selectedClassroom)
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center">
                    <h3 class="text-base font-black text-slate-800 dark:text-white flex items-center">
                        <span
                            class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400 text-xs mr-2">2</span>
                        Form Pengisian Nilai
                    </h3>
                    <span
                        class="text-xs font-bold text-slate-500 bg-white dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700">
                        {{ count($students) }} Siswa
                    </span>
                </div>

                <form action="{{ route('exam-grades.storeBulk') }}" method="POST">
                    @csrf
                    <input type="hidden" name="classroom_id" value="{{ $classroomId }}">
                    <input type="hidden" name="subject_id" value="{{ $subjectId }}">
                    <input type="hidden" name="kategori_ujian" value="{{ $kategoriUjian }}">
                    <input type="hidden" name="semester" value="{{ $semester }}">

                    <div class="overflow-x-auto p-6">
                        <table
                            class="w-full text-sm text-left whitespace-nowrap mb-6 border border-slate-200 dark:border-slate-700">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-100 dark:bg-slate-900/50">
                                <tr>
                                    <th
                                        class="px-6 py-3 border-b border-r border-slate-200 dark:border-slate-700 w-12 text-center">
                                        No</th>
                                    <th class="px-6 py-3 border-b border-r border-slate-200 dark:border-slate-700 w-40">
                                        NISN</th>
                                    <th class="px-6 py-3 border-b border-r border-slate-200 dark:border-slate-700">Nama
                                        Lengkap</th>
                                    <th
                                        class="px-6 py-3 border-b border-slate-200 dark:border-slate-700 w-48 text-center bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400">
                                        Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($students as $index => $student)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                                    <td
                                        class="px-6 py-3 border-r border-slate-200 dark:border-slate-700 text-center font-bold text-slate-400">
                                        {{ $index + 1 }}
                                    </td>
                                    <td
                                        class="px-6 py-3 border-r border-slate-200 dark:border-slate-700 font-medium text-slate-500">
                                        {{ $student->nisn ?? '-' }}
                                    </td>
                                    <td
                                        class="px-6 py-3 border-r border-slate-200 dark:border-slate-700 font-bold text-slate-700 dark:text-slate-300">
                                        {{ $student->nama_lengkap }}
                                    </td>
                                    <td class="px-4 py-2 bg-indigo-50/30 dark:bg-indigo-900/10">
                                        <input type="number" step="0.01" min="0" max="100"
                                            name="nilai[{{ $student->id }}]"
                                            value="{{ $existingGrades[$student->id] ?? '' }}" placeholder="0-100"
                                            class="w-full text-center font-black rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500">
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-500 italic">
                                        Tidak ada siswa di kelas ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <button type="submit"
                            class="w-full md:w-auto bg-emerald-600 text-white px-8 py-3 rounded-xl font-black shadow-lg shadow-emerald-500/30 hover:bg-emerald-700 transition transform hover:-translate-y-0.5 uppercase tracking-wide text-sm flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            Simpan Seluruh Nilai Kelas
                        </button>
                    </div>
                </form>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>