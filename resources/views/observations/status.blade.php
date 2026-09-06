<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800">
            Status Penilaian: {{ $assessment->classroom->nama_kelas }} - {{ $assessment->subject->nama_mapel }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

        <!-- Tombol Navigasi -->
        <div class="flex justify-start">
            <a href="{{ route('observations.input', $assessment->id) }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 text-sm font-bold transition-colors">
                <i class="fas fa-arrow-left"></i> Kembali ke Form Input
            </a>
        </div>

        <!-- Tabel 1: Belum Dinilai -->
        <div class="bg-white rounded-xl shadow-sm border border-rose-200 overflow-hidden">
            <div class="bg-rose-50 border-b border-rose-200 p-4">
                <h3 class="font-black text-rose-700">Belum Dinilai ({{ $belumDinilai->count() }} Siswa)</h3>
            </div>
            <div class="overflow-x-auto p-4">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-black">
                        <tr>
                            <th class="px-4 py-3 w-16 text-center">No</th>
                            <th class="px-4 py-3">Nama Siswa</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($belumDinilai as $index => $siswa)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-center font-bold">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $siswa->nama_lengkap }}</td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="px-2 py-1 bg-rose-100 text-rose-600 rounded-md text-[10px] font-bold uppercase">Kosong</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-slate-400">Semua siswa telah dinilai!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel 2: Sudah Dinilai -->
        <div class="bg-white rounded-xl shadow-sm border border-emerald-200 overflow-hidden">
            <div class="bg-emerald-50 border-b border-emerald-200 p-4">
                <h3 class="font-black text-emerald-700">Sudah Dinilai ({{ $sudahDinilai->count() }} Siswa)</h3>
            </div>
            <div class="overflow-x-auto p-4">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-black">
                        <tr>
                            <th class="px-4 py-3 w-16 text-center">No</th>
                            <th class="px-4 py-3">Nama Siswa</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($sudahDinilai as $index => $siswa)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-center font-bold">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $siswa->nama_lengkap }}</td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-md text-[10px] font-bold uppercase"><i
                                        class="fas fa-check"></i> Selesai</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-slate-400">Belum ada siswa yang dinilai.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>