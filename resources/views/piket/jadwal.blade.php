<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 leading-tight dark:text-slate-100">Pengaturan Jadwal Piket Master</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-5 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-100 text-emerald-700 rounded-lg font-bold">{{ session('success') }}</div>
        @endif

        <!-- Filter Kelas -->
        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
            <form action="{{ route('piket.jadwal') }}" method="GET" class="flex flex-col items-stretch gap-4 sm:flex-row sm:items-end">
                <div>
                    <label for="classroom_id" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Pilih Kelas</label>
                    <select id="classroom_id" name="classroom_id" class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white sm:min-w-64"
                        onchange="this.form.submit()">
                        @foreach($classrooms as $kelas)
                        <option value="{{ $kelas->id }}" {{ $classroomId==$kelas->id ? 'selected' : '' }}>
                            {{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <!-- Tabel Jadwal -->
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
            <form action="{{ route('piket.jadwal.store') }}" method="POST">
                @csrf
                <input type="hidden" name="classroom_id" value="{{ $classroomId }}">

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-100 text-xs uppercase text-slate-700 dark:bg-slate-900 dark:text-slate-200">
                            <tr>
                                <th class="px-4 py-4 w-12 text-center">No</th>
                                <th class="px-4 py-4">Nama Siswa</th>
                                @foreach($hariList as $hari)
                                <th class="px-4 py-4 text-center">{{ $hari }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $siswa)
                            <tr class="border-b border-slate-100 hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-700/40">
                                <td class="px-4 py-3 text-center font-bold">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-medium">{{ $siswa->nama_lengkap }}</td>

                                @foreach($hariList as $hari)
                                @php
                                // Cek apakah siswa ini punya jadwal di hari ini dari data yang diambil
                                $isChecked = isset($jadwalTersimpan[$siswa->id][$hari]);
                                @endphp
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" name="jadwal[{{ $siswa->id }}][]" value="{{ $hari }}" {{
                                        $isChecked ? 'checked' : '' }}
                                        class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500 cursor-pointer">
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div                 class="flex justify-end border-t border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-900/50">
                    <button type="submit"
                        class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                        Simpan Jadwal Piket
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>