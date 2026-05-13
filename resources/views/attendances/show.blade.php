<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
                    Absensi Kelas: <span class="text-indigo-600">{{ $classroom->tingkat }} - {{ $classroom->nama_kelas
                        }}</span>
                </h2>
                <p class="text-sm text-slate-500 mt-1">Wali Kelas: {{ $classroom->homeroomTeacher->nama_lengkap ??
                    'Belum Diatur' }}</p>
            </div>

            <a href="{{ route('attendances.index') }}" class="text-sm font-bold text-slate-500 hover:text-slate-700">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div
                class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col md:flex-row items-center justify-between gap-4">

                <div class="flex flex-col md:flex-row items-end gap-3 w-full md:w-auto">

                    @if(isset($myClassrooms) && $myClassrooms->count() > 1)
                    <div class="w-full md:w-auto">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ganti Kelas</label>
                        <select onchange="window.location.href=this.value"
                            class="w-full md:w-auto rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-indigo-500">
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

                    <form action="{{ route('attendances.show', $classroom->id) }}" method="GET"
                        class="flex items-end gap-3 w-full md:w-auto">
                        <div class="flex-1 md:flex-none">
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Pilih Tanggal</label>
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
                    class="text-left md:text-right text-sm w-full md:w-auto bg-slate-50 dark:bg-slate-900/50 md:bg-transparent p-3 md:p-0 rounded-lg border md:border-none border-slate-100 dark:border-slate-700">
                    <span class="block text-slate-500">Total Siswa: <span
                            class="font-bold text-slate-800 dark:text-slate-200">{{ $classroom->students->count()
                            }}</span></span>
                </div>
            </div>

            <form action="{{ route('attendances.store', $classroom->id) }}" method="POST">
                @csrf
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">

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

                                            <label class="cursor-pointer relative">
                                                <input type="radio" name="attendance[{{ $siswa->id }}][status]"
                                                    value="hadir" class="peer hidden" {{ $status=='hadir' ? 'checked'
                                                    : '' }}>
                                                <div class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center rounded-full font-bold text-xs border-2 peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500 border-slate-200 text-slate-400 hover:border-emerald-300 transition"
                                                    title="Hadir">H</div>
                                            </label>

                                            <label class="cursor-pointer relative">
                                                <input type="radio" name="attendance[{{ $siswa->id }}][status]"
                                                    value="sakit" class="peer hidden" {{ $status=='sakit' ? 'checked'
                                                    : '' }}>
                                                <div class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center rounded-full font-bold text-xs border-2 peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-500 border-slate-200 text-slate-400 hover:border-amber-300 transition"
                                                    title="Sakit">S</div>
                                            </label>

                                            <label class="cursor-pointer relative">
                                                <input type="radio" name="attendance[{{ $siswa->id }}][status]"
                                                    value="izin" class="peer hidden" {{ $status=='izin' ? 'checked' : ''
                                                    }}>
                                                <div class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center rounded-full font-bold text-xs border-2 peer-checked:bg-blue-500 peer-checked:text-white peer-checked:border-blue-500 border-slate-200 text-slate-400 hover:border-blue-300 transition"
                                                    title="Izin">I</div>
                                            </label>

                                            <label class="cursor-pointer relative">
                                                <input type="radio" name="attendance[{{ $siswa->id }}][status]"
                                                    value="alfa" class="peer hidden" {{ $status=='alfa' ? 'checked' : ''
                                                    }}>
                                                <div class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center rounded-full font-bold text-xs border-2 peer-checked:bg-rose-500 peer-checked:text-white peer-checked:border-rose-500 border-slate-200 text-slate-400 hover:border-rose-300 transition"
                                                    title="Alfa">A</div>
                                            </label>

                                        </div>
                                    </td>
                                    <td class="px-4 md:px-6 py-4">
                                        <input type="text" name="attendance[{{ $siswa->id }}][keterangan]"
                                            value="{{ $keterangan }}" placeholder="Keterangan..."
                                            class="w-full min-w-[150px] rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500">
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
                        class="w-full md:w-auto px-8 py-3.5 bg-indigo-600 text-white rounded-xl text-sm font-black shadow-lg hover:bg-indigo-700 transition transform hover:-translate-y-1">
                        Simpan Absensi &rarr;
                    </button>
                </div>
                @endif
            </form>

        </div>
    </div>
</x-app-layout>