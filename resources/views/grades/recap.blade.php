<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
                Rekapitulasi Nilai: <span class="text-indigo-600">Sidanira</span>
            </h2>

            <a href="{{ route('dashboard') }}" class="text-sm font-bold text-slate-500 hover:text-slate-700">
                &larr; Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <form action="{{ route('grades.recap') }}" method="GET"
                    class="flex flex-col md:flex-row gap-4 items-end">

                    <div class="w-full md:w-1/2">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Pilih Siswa</label>
                        <select name="student_id"
                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500"
                            required>
                            <option value="">-- Cari Berdasarkan Nama / NISN --</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ request('student_id')==$student->id ? 'selected' : ''
                                }}>
                                {{ $student->nisn }} - {{ $student->nama_lengkap }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <button type="submit"
                            class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-bold shadow-sm hover:bg-indigo-700 transition uppercase text-sm">
                            Tampilkan Rekap
                        </button>
                    </div>
                </form>
            </div>

            @if($selectedStudent)
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">

                <div
                    class="p-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Matriks Nilai 5 Semester</h3>
                        <p class="text-sm text-slate-500">
                            Nama: <span class="font-semibold text-slate-700 dark:text-slate-300">{{
                                $selectedStudent->nama_lengkap }}</span> |
                            NISN: <span class="font-semibold text-slate-700 dark:text-slate-300">{{
                                $selectedStudent->nisn }}</span>
                        </p>
                    </div>
                    <button onclick="window.print()"
                        class="text-sm bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-lg font-bold hover:bg-slate-300 transition">
                        Cetak Laporan
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                        <thead class="bg-slate-100 dark:bg-slate-700/50">
                            <tr>
                                <th rowspan="2"
                                    class="px-4 py-3 text-center font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider border-r border-slate-200 dark:border-slate-700 align-middle w-12">
                                    No
                                </th>
                                <th rowspan="2"
                                    class="px-4 py-3 text-left font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider border-r border-slate-200 dark:border-slate-700 align-middle">
                                    Mata Pelajaran
                                </th>
                                <th colspan="2"
                                    class="px-4 py-2 text-center font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider border-b border-r border-slate-200 dark:border-slate-700">
                                    Kelas 4
                                </th>
                                <th colspan="2"
                                    class="px-4 py-2 text-center font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider border-b border-r border-slate-200 dark:border-slate-700">
                                    Kelas 5
                                </th>
                                <th
                                    class="px-4 py-2 text-center font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider border-b border-slate-200 dark:border-slate-700">
                                    Kelas 6
                                </th>
                            </tr>
                            <tr class="bg-slate-50 dark:bg-slate-800/80 text-xs">
                                <th
                                    class="px-4 py-2 text-center text-slate-500 border-r border-slate-200 dark:border-slate-700">
                                    Smt 1</th>
                                <th
                                    class="px-4 py-2 text-center text-slate-500 border-r border-slate-200 dark:border-slate-700">
                                    Smt 2</th>
                                <th
                                    class="px-4 py-2 text-center text-slate-500 border-r border-slate-200 dark:border-slate-700">
                                    Smt 1</th>
                                <th
                                    class="px-4 py-2 text-center text-slate-500 border-r border-slate-200 dark:border-slate-700">
                                    Smt 2</th>
                                <th class="px-4 py-2 text-center text-slate-500">Smt 1</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">

                            @forelse($subjectNames as $namaMapel)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">

                                <td
                                    class="px-4 py-3 text-center font-bold text-slate-500 dark:text-slate-400 border-r border-slate-200 dark:border-slate-700">
                                    {{ $loop->iteration }}
                                </td>

                                <td
                                    class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200 border-r border-slate-200 dark:border-slate-700">
                                    {{ $namaMapel }}
                                </td>

                                <td
                                    class="px-4 py-3 text-center text-slate-600 dark:text-slate-400 border-r border-slate-200 dark:border-slate-700">
                                    {{ $recapData[$namaMapel][4][1] ?? '-' }}
                                </td>
                                <td
                                    class="px-4 py-3 text-center text-slate-600 dark:text-slate-400 border-r border-slate-200 dark:border-slate-700">
                                    {{ $recapData[$namaMapel][4][2] ?? '-' }}
                                </td>

                                <td
                                    class="px-4 py-3 text-center text-slate-600 dark:text-slate-400 border-r border-slate-200 dark:border-slate-700">
                                    {{ $recapData[$namaMapel][5][1] ?? '-' }}
                                </td>
                                <td
                                    class="px-4 py-3 text-center text-slate-600 dark:text-slate-400 border-r border-slate-200 dark:border-slate-700">
                                    {{ $recapData[$namaMapel][5][2] ?? '-' }}
                                </td>

                                <td class="px-4 py-3 text-center text-slate-600 dark:text-slate-400">
                                    {{ $recapData[$namaMapel][6][1] ?? '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-slate-500 italic">
                                    Tidak ada mata pelajaran yang ditemukan.
                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>