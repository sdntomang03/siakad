<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">
                Rekapitulasi Nilai: <span class="text-brand-600">Sidanira</span>
            </h2>

            <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-brand-700 hover:text-brand-900 focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:text-brand-300 dark:hover:text-brand-200">
                Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">

            <div
                class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                <form action="{{ route('grades.recap') }}" method="GET"
                    class="flex flex-col items-stretch gap-4 sm:flex-row sm:items-end">

                    <div class="w-full sm:max-w-xl sm:flex-1">
                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">Pilih Siswa</label>
                        <select name="student_id"
                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
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
                            class="inline-flex min-h-11 w-full items-center justify-center rounded-md bg-brand-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 sm:w-auto">
                            Tampilkan Rekap
                        </button>
                    </div>
                </form>
            </div>

            @if($selectedStudent)
            <div
                class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">

                <div
                    class="flex flex-col gap-3 border-b border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-900/50 sm:flex-row sm:items-center sm:justify-between sm:p-6">
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
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700 sm:w-auto">
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