<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 leading-tight dark:text-slate-100">Jadwal Pelajaran</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-5 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <!-- Filter Kelas -->
        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
            <form action="{{ route('jadwal.index') }}" method="GET">
                <label for="classroom_id" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Pilih Kelas</label>
                <select id="classroom_id" name="classroom_id" class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white sm:max-w-sm"
                    onchange="this.form.submit()">
                    @foreach($classrooms as $kelas)
                    <option value="{{ $kelas->id }}" {{ $classroomId==$kelas->id ? 'selected' : '' }}>
                        {{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}
                    </option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- Tampilan Matrix Jadwal per Hari -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($hariList as $hari)
            <div class="bg-white rounded-md shadow-sm border border-slate-200 overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900/50">
                    <h3 class="font-semibold text-slate-900 dark:text-white">{{ $hari }}</h3>
                    <!-- Tombol untuk membuka modal edit jadwal hari tersebut (opsional) -->
                    @if($classroomId)
                    <a href="{{ route('jadwal.edit', ['classroom' => $classroomId, 'hari' => $hari]) }}"
                        class="inline-flex min-h-10 items-center justify-center rounded-md border border-brand-200 bg-white px-3 py-1 text-sm font-medium text-brand-800 transition hover:bg-brand-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-brand-800 dark:bg-slate-800 dark:text-brand-200 dark:hover:bg-slate-700">
                        Edit Jadwal
                    </a>
                    @endif
                </div>

                <div class="p-0">
                    @if(isset($jadwal[$hari]) && $jadwal[$hari]->count() > 0)
                    <table class="w-full text-sm">
                        <tbody>
                            @foreach($jadwal[$hari] as $item)
                            <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-700/40">
                                <td class="w-28 border-r border-slate-100 px-4 py-3 font-medium text-slate-500 dark:border-slate-700 dark:text-slate-400">
                                    {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($item->subject_id)
                                    <span class="font-medium text-slate-800 dark:text-slate-200">{{ $item->subject->nama_mapel }}</span>
                                    @else
                                    <span class="italic font-bold text-amber-600">{{ $item->keterangan }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="p-6 text-center text-slate-400 italic text-sm">
                        Jadwal belum diatur.
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

    </div>
</x-app-layout>