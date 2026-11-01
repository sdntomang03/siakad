<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">
                Input Nilai Kelas: <span class="text-brand-600">Form Massal</span>
            </h2>

            <a href="{{ route('exam-grades.index') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-brand-700 hover:text-brand-900 focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:text-brand-300 dark:hover:text-brand-200">
                Kembali ke Daftar Nilai
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">

            <div
                class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                <div
                    class="border-b border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-700 dark:bg-slate-900/50 sm:px-6">
                    <h3 class="flex items-center text-base font-semibold text-slate-950 dark:text-white">
                        <span
                            class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-100 text-brand-700 dark:bg-brand-900/50 dark:text-brand-400 text-xs mr-2">1</span>
                        Pengaturan Penilaian
                    </h3>
                </div>

                <form action="{{ route('exam-grades.createBulk') }}" method="GET" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">Pilih Kelas</label>
                            <select name="classroom_id"
                                class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
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
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">Mata Pelajaran</label>
                            <select name="subject_id"
                                class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
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
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">Kategori Ujian</label>
                            <select name="kategori_ujian"
                                class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
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
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-md bg-brand-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 md:w-auto">
                        Tampilkan Form Nilai Siswa
                    </button>
                </form>
            </div>

            @if($selectedClassroom)
            <div
                class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                <div
                    class="flex flex-col gap-2 border-b border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-700 dark:bg-slate-900/50 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <h3 class="flex items-center text-base font-semibold text-slate-950 dark:text-white">
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

                    <div class="overflow-x-auto p-4 sm:p-6">
                        <table
                            class="mb-6 w-full min-w-[42rem] whitespace-nowrap border border-slate-200 text-left text-sm dark:border-slate-700">
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
                                        class="px-6 py-3 border-b border-slate-200 dark:border-slate-700 w-48 text-center bg-brand-50 dark:bg-brand-900/20 text-brand-700 dark:text-brand-400">
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
                                    <td class="px-4 py-2 bg-brand-50/30 dark:bg-brand-900/10">
                                        <input type="number" step="0.01" min="0" max="100"
                                            name="nilai[{{ $student->id }}]"
                                            value="{{ $existingGrades[$student->id] ?? '' }}" placeholder="0-100"
                                            class="w-full text-center font-black rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm focus:ring-brand-500">
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
                            class="w-full md:w-auto bg-emerald-600 text-white px-8 py-3 rounded-md font-black shadow-lg shadow-emerald-500/30 hover:bg-emerald-700 transition transform hover:-translate-y-0.5 uppercase tracking-wide text-sm flex items-center justify-center gap-2">
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