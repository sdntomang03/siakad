<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Jadwal Pelajaran</h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Filter Kelas -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <form action="{{ route('jadwal.index') }}" method="GET">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Pilih Kelas</label>
                <select name="classroom_id" class="rounded-lg border-slate-300 text-sm w-64 focus:ring-indigo-500"
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
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 bg-indigo-600 border-b border-indigo-700 flex justify-between items-center">
                    <h3 class="font-bold text-white uppercase tracking-wider">{{ $hari }}</h3>
                    <!-- Tombol untuk membuka modal edit jadwal hari tersebut (opsional) -->
                    @if($classroomId)
                    <a href="{{ route('jadwal.edit', ['classroom' => $classroomId, 'hari' => $hari]) }}"
                        class="text-xs bg-white text-indigo-600 px-3 py-1 rounded hover:bg-indigo-50 font-bold transition shadow-sm">
                        Edit Jadwal
                    </a>
                    @endif
                </div>

                <div class="p-0">
                    @if(isset($jadwal[$hari]) && $jadwal[$hari]->count() > 0)
                    <table class="w-full text-sm">
                        <tbody>
                            @foreach($jadwal[$hari] as $item)
                            <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50">
                                <td class="px-4 py-3 w-28 text-slate-500 border-r border-slate-100 font-medium">
                                    {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($item->subject_id)
                                    <span class="font-bold text-slate-800">{{ $item->subject->nama_mapel }}</span>
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