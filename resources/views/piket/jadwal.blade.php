<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Pengaturan Jadwal Piket Master</h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-100 text-emerald-700 rounded-lg font-bold">{{ session('success') }}</div>
        @endif

        <!-- Filter Kelas -->
        <div class="bg-white p-6 rounded-xl shadow-sm mb-6 border border-slate-200">
            <form action="{{ route('piket.jadwal') }}" method="GET" class="flex items-end gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Pilih Kelas</label>
                    <select name="classroom_id" class="rounded-lg border-slate-300 text-sm focus:ring-indigo-500"
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
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <form action="{{ route('piket.jadwal.store') }}" method="POST">
                @csrf
                <input type="hidden" name="classroom_id" value="{{ $classroomId }}">

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-slate-700 uppercase bg-slate-100">
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
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
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
                                        class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 cursor-pointer">
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end">
                    <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition">
                        Simpan Jadwal Piket
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>