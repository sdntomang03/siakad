<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Siswa - {{ $classroom->nama_kelas ?? 'Kelas' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900 antialiased p-4 sm:p-8">

    <div class="max-w-6xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-gray-800">Daftar Siswa</h2>
                <p class="text-sm text-gray-500 mt-1">Pengisian Catatan Akhir Kelas: <strong class="text-indigo-600">{{
                        $classroom->nama_kelas ?? 'Nama Kelas' }}</strong></p>
            </div>

            <a href="{{ url()->previous() }}"
                class="inline-flex items-center justify-center px-5 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-50 transition shadow-sm">
                &larr; Kembali
            </a>
        </div>

        {{-- Tabel Daftar Siswa --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-indigo-50 text-indigo-800 text-xs uppercase tracking-wider border-b border-gray-200">
                            <th class="py-4 px-6 text-center w-16 font-bold">No</th>
                            <th class="py-4 px-6 font-bold">NIS / NISN</th>
                            <th class="py-4 px-6 font-bold">Nama Lengkap</th>
                            <th class="py-4 px-6 font-bold text-center">L/P</th>
                            <th class="py-4 px-6 text-center font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        @forelse($students as $index => $student)
                        <tr class="hover:bg-indigo-50/50 transition duration-150">
                            <td class="py-3 px-6 text-center text-gray-500 font-medium">{{ $index + 1 }}</td>

                            <td class="py-3 px-6 font-mono text-gray-600">
                                {{ $student->nis ?? '-' }}
                            </td>

                            <td class="py-3 px-6 font-bold text-gray-800">
                                {{ $student->nama }}
                            </td>

                            <td class="py-3 px-6 text-center text-gray-600 font-medium">
                                {{ $student->jenis_kelamin ?? '-' }}
                            </td>

                            <td class="py-3 px-6 text-center">
                                {{-- Tombol Menuju Catatan Akhir Siswa --}}
                                <a href="{{ route('catatan_akhir.edit', ['student_id' => $student->id, 'classroom_id' => $classroom->id]) }}"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-100 text-indigo-700 hover:bg-indigo-600 hover:text-white rounded-lg font-bold transition-colors text-xs shadow-sm">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                    Isi Catatan Akhir
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                        </path>
                                    </svg>
                                    <span class="text-sm font-medium">Belum ada siswa yang terdaftar di kelas
                                        ini.</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>

</html>