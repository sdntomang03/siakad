<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">Jurnal Piket Harian</h2>

            <!-- Tombol Navigasi Cepat -->
            <div class="flex items-center gap-2">
                <a href="{{ route('piket.jadwal') }}"
                    class="px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-sm font-bold rounded-lg border border-indigo-200 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    Jadwal Master
                </a>
                <a href="{{ route('piket.laporan') }}"
                    class="px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-sm font-bold rounded-lg border border-indigo-200 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Lihat Laporan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-100 text-emerald-700 rounded-lg font-bold">{{ session('success') }}</div>
        @endif

        <!-- Filter Kelas dan Tanggal -->
        <div class="bg-white p-6 rounded-xl shadow-sm mb-6 border border-slate-200">
            <form action="{{ route('piket.jurnal') }}" method="GET" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Pilih Kelas</label>
                    <select name="classroom_id" class="rounded-lg border-slate-300 text-sm"
                        onchange="this.form.submit()">
                        @foreach($classrooms as $kelas)
                        <option value="{{ $kelas->id }}" {{ $classroomId==$kelas->id ? 'selected' : '' }}>
                            {{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Tanggal Pelaksanaan</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" class="rounded-lg border-slate-300 text-sm"
                        onchange="this.form.submit()">
                </div>
            </form>
        </div>

        <!-- Tabel Jurnal -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-800">Daftar Piket Hari {{ $namaHari }}</h3>
                <p class="text-xs text-slate-500">Jika siswa di-absen tidak masuk, baris akan terkunci otomatis.</p>
            </div>

            <form action="{{ route('piket.jurnal.store') }}" method="POST">
                @csrf
                <input type="hidden" name="classroom_id" value="{{ $classroomId }}">
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-slate-700 uppercase bg-slate-100">
                            <tr>
                                <th class="px-4 py-4 w-12 text-center">No</th>
                                <th class="px-4 py-4 w-1/3">Nama Siswa</th>
                                <th class="px-4 py-4">Status Pelaksanaan</th>
                                <th class="px-4 py-4">Catatan (Opsional)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswaPiket as $index => $piket)
                            @php
                            $siswa = $piket->student;

                            // Pengecekan Absensi
                            $absen = $absensiHariIni[$siswa->id] ?? null;
                            $isHadir = !$absen || $absen->status === 'hadir';

                            // Nilai default form jika sudah pernah disimpan
                            $savedStatus = $jurnalTersimpan[$siswa->id]->status ?? 'terlaksana';
                            $savedCatatan = $jurnalTersimpan[$siswa->id]->catatan ?? '';
                            @endphp

                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                <td class="px-4 py-3 text-center font-bold">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-medium">{{ $siswa->nama_lengkap }}</td>

                                @if($isHadir)
                                <!-- Siswa Hadir: Bebas pilih status -->
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="status[{{ $siswa->id }}]" value="terlaksana" {{
                                                $savedStatus=='terlaksana' ? 'checked' : '' }}
                                                class="text-emerald-600 focus:ring-emerald-500">
                                            <span class="text-emerald-700 font-semibold">Terlaksana</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="status[{{ $siswa->id }}]" value="tidak_terlaksana"
                                                {{ $savedStatus=='tidak_terlaksana' ? 'checked' : '' }}
                                                class="text-rose-600 focus:ring-rose-500">
                                            <span class="text-rose-700 font-semibold">Tidak Terlaksana</span>
                                        </label>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="catatan[{{ $siswa->id }}]" value="{{ $savedCatatan }}"
                                        placeholder="Tulis alasan jika kabur..."
                                        class="w-full text-sm border-slate-300 rounded-lg focus:ring-indigo-500">
                                </td>
                                @else
                                <!-- Siswa Tidak Hadir: Terkunci dan terisi otomatis dari absensi -->
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex px-2 py-1 rounded bg-rose-100 text-rose-700 text-xs font-bold uppercase border border-rose-200">
                                        Tidak Masuk ({{ $absen->status }})
                                    </span>
                                    <input type="hidden" name="status[{{ $siswa->id }}]" value="tidak_terlaksana">
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-slate-500 italic text-sm">Disinkronisasi otomatis oleh
                                        sistem.</span>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                                    Tidak ada jadwal piket yang diatur untuk kelas ini pada hari {{ $namaHari }}.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($siswaPiket->count() > 0)
                <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end">
                    <button type="submit"
                        class="px-6 py-2.5 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 transition">
                        Simpan Jurnal Piket
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>
</x-app-layout>