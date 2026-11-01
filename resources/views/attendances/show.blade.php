<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">
                    Absensi Kelas: <span class="text-brand-600">{{ $classroom->tingkat }} - {{ $classroom->nama_kelas
                        }}</span>
                </h2>
                <p class="text-sm text-slate-500 mt-1">Wali Kelas: {{ $classroom->homeroomTeacher->nama_lengkap ??
                    'Belum Diatur' }}</p>
            </div>

            <a href="{{ route('attendances.index') }}"
                class="inline-flex min-h-11 items-center text-sm font-semibold text-brand-700 hover:text-brand-900 focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:text-brand-300 dark:hover:text-brand-200">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">

            <div
                class="flex flex-col items-stretch justify-between gap-4 rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 md:flex-row md:items-center sm:p-6">

                <div class="flex flex-col md:flex-row items-end gap-3 w-full md:w-auto">

                    @if(isset($myClassrooms) && $myClassrooms->count() > 1)
                    <div class="w-full md:w-auto">
                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">Ganti Kelas</label>
                        <select onchange="window.location.href=this.value"
                            class="w-full md:w-auto rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-brand-500">
                            @foreach($myClassrooms as $myClass)
                            <option
                                value="{{ route('attendances.show', ['classroom' => $myClass->id, 'tanggal' => $tanggal]) }}"
                                {{ $classroom->id == $myClass->id ? 'selected' : '' }}>
                                Kelas {{ $myClass->tingkat }} - {{ $myClass->nama_kelas }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <form action="{{ route('attendances.show', $classroom->hashid) }}" method="GET"
                        class="flex w-full items-end gap-3 md:w-auto">
                        <div class="flex-1 md:flex-none">
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">Pilih Tanggal</label>
                            <input type="date" name="tanggal" value="{{ $tanggal }}"
                                class="w-full md:w-auto rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm font-bold text-slate-700 dark:text-slate-200">
                        </div>
                        <button type="submit"
                            class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-bold hover:bg-slate-200 transition">
                            Tampilkan
                        </button>
                    </form>
                </div>

                <div
                    class="w-full rounded-md border border-slate-200 bg-slate-50 p-3 text-left text-sm dark:border-slate-700 dark:bg-slate-900/50 md:w-auto md:border-0 md:bg-transparent md:p-0 md:text-right">
                    <span class="block text-slate-500">Total Siswa: <span
                            class="font-bold text-slate-800 dark:text-slate-200">{{ $classroom->students->count()
                            }}</span></span>
                </div>
            </div>

            <form action="{{ route('attendances.store', $classroom->hashid) }}" method="POST">
                @csrf
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                <div
                    class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                            <thead
                                class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-900/50 dark:text-slate-300 whitespace-nowrap">
                                <tr>
                                    <th class="px-4 md:px-6 py-4 w-12 md:w-16 text-center md:text-left">No</th>
                                    <th class="px-4 md:px-6 py-4 min-w-[180px]">Nama Siswa</th>
                                    <th class="px-4 md:px-6 py-4 text-center min-w-[220px]">Kehadiran</th>
                                    <th class="px-4 md:px-6 py-4 min-w-[200px]">Keterangan (Opsional)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($classroom->students as $index => $siswa)
                                @php
                                $status = $existingAttendances->has($siswa->id) ?
                                $existingAttendances[$siswa->id]->status : 'hadir';
                                $keterangan = $existingAttendances->has($siswa->id) ?
                                $existingAttendances[$siswa->id]->keterangan : '';
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                    <td class="px-4 md:px-6 py-4 font-bold text-center md:text-left">{{ $index + 1 }}
                                    </td>
                                    <td class="px-4 md:px-6 py-4">
                                        <span
                                            class="block font-bold text-slate-800 dark:text-slate-200 whitespace-nowrap">{{
                                            $siswa->nama_lengkap }}</span>
                                        <span class="block text-xs text-slate-500">{{ $siswa->nisn ?? '-' }} • {{
                                            $siswa->jenis_kelamin == 'L' ? 'Laki-Laki' : 'Perempuan' }}</span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4">
                                        <div class="flex justify-center gap-1 md:gap-2">

                                            <label class="relative cursor-pointer">
                                                <input type="radio" name="attendance[{{ $siswa->id }}][status]"
                                                    value="hadir" aria-label="Hadir" class="peer sr-only" {{ $status=='hadir' ? 'checked'
                                                    : '' }}>
                                                <span class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-slate-300 text-xs font-semibold text-slate-600 transition hover:border-emerald-500 peer-checked:border-emerald-600 peer-checked:bg-emerald-600 peer-checked:text-white peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-300"
                                                    title="Hadir">H</span>
                                            </label>

                                            <label class="relative cursor-pointer">
                                                <input type="radio" name="attendance[{{ $siswa->id }}][status]"
                                                    value="sakit" aria-label="Sakit" class="peer sr-only" {{ $status=='sakit' ? 'checked'
                                                    : '' }}>
                                                <span class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-slate-300 text-xs font-semibold text-slate-600 transition hover:border-amber-500 peer-checked:border-amber-600 peer-checked:bg-amber-600 peer-checked:text-white peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-300"
                                                    title="Sakit">S</span>
                                            </label>

                                            <label class="relative cursor-pointer">
                                                <input type="radio" name="attendance[{{ $siswa->id }}][status]"
                                                    value="izin" aria-label="Izin" class="peer sr-only" {{ $status=='izin' ? 'checked' : ''
                                                    }}>
                                                <span class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-slate-300 text-xs font-semibold text-slate-600 transition hover:border-blue-500 peer-checked:border-blue-700 peer-checked:bg-blue-700 peer-checked:text-white peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-300"
                                                    title="Izin">I</span>
                                            </label>

                                            <label class="relative cursor-pointer">
                                                <input type="radio" name="attendance[{{ $siswa->id }}][status]"
                                                    value="alfa" aria-label="Alfa" class="peer sr-only" {{ $status=='alfa' ? 'checked' : ''
                                                    }}>
                                                <span class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-slate-300 text-xs font-semibold text-slate-600 transition hover:border-rose-500 peer-checked:border-rose-700 peer-checked:bg-rose-700 peer-checked:text-white peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-300"
                                                    title="Alfa">A</span>
                                            </label>

                                        </div>
                                    </td>
                                    <td class="px-4 md:px-6 py-4">
                                        <input type="text" name="attendance[{{ $siswa->id }}][keterangan]"
                                            value="{{ $keterangan }}" placeholder="Keterangan..."
                                            class="w-full min-w-[150px] rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                        Belum ada siswa di kelas ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($classroom->students->count() > 0)
                <div class="mt-6 flex flex-col md:flex-row justify-end">
                    <button type="submit"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-md bg-brand-700 px-8 py-3.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 md:w-auto">
                        Simpan Absensi
                    </button>
                </div>
                @endif
            </form>

        </div>
    </div>
</x-app-layout>