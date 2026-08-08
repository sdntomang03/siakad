<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                Penyusunan Catatan Akhir: <span class="text-indigo-600 dark:text-indigo-400">{{ $student->nama_lengkap
                    ?? $student->nama }}</span>
            </h2>
            <a href="{{ route('catatan_akhir.index', ['classroom_id' => $classroom->id]) }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg font-bold text-xs text-slate-700 dark:text-slate-300 uppercase tracking-widest shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div
                class="p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 rounded-xl font-bold shadow-sm">
                {{ session('success') }}
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- KOLOM KIRI: REKAP INFORMASI --}}
                <div class="lg:col-span-1 space-y-6">

                    {{-- Rekap Absen & Piket --}}
                    <div
                        class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                        <h3
                            class="font-black text-slate-800 dark:text-slate-200 mb-4 border-b border-slate-100 dark:border-slate-700 pb-2 uppercase tracking-wider text-sm">
                            Rekap Kehadiran & Piket</h3>

                        <div class="space-y-5">
                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kehadiran
                                    (Absensi)</p>
                                <ul class="text-sm space-y-2">
                                    <li
                                        class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 px-3 py-2 rounded-lg">
                                        <span class="text-slate-600 dark:text-slate-400 font-medium">Sakit</span>
                                        <span class="font-bold text-amber-500">{{ $finalNote->sakit ?? $sakit }}
                                            Hari</span>
                                    </li>
                                    <li
                                        class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 px-3 py-2 rounded-lg">
                                        <span class="text-slate-600 dark:text-slate-400 font-medium">Izin</span>
                                        <span class="font-bold text-blue-500">{{ $finalNote->izin ?? $izin }}
                                            Hari</span>
                                    </li>
                                    <li
                                        class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 px-3 py-2 rounded-lg">
                                        <span class="text-slate-600 dark:text-slate-400 font-medium">Alpha</span>
                                        <span class="font-bold text-rose-500">{{ $finalNote->alpha ?? $alpha }}
                                            Hari</span>
                                    </li>
                                </ul>
                            </div>

                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kepatuhan
                                    Piket Harian</p>
                                <ul class="text-sm space-y-2">
                                    <li
                                        class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 px-3 py-2 rounded-lg">
                                        <span class="text-slate-600 dark:text-slate-400 font-medium">Tugas
                                            Terlaksana</span>
                                        <span class="font-bold text-emerald-500">{{ $piketTerlaksana }}x</span>
                                    </li>
                                    <li
                                        class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 px-3 py-2 rounded-lg">
                                        <span class="text-slate-600 dark:text-slate-400 font-medium">Tidak/Kabur</span>
                                        <span class="font-bold text-rose-500">{{ $piketTidak }}x</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Rekap Nilai Akademik --}}
                    <div
                        class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                        <h3
                            class="font-black text-slate-800 dark:text-slate-200 mb-4 border-b border-slate-100 dark:border-slate-700 pb-2 uppercase tracking-wider text-sm">
                            Rekap Nilai Akademik</h3>

                        @if(isset($rekapNilai) && count($rekapNilai) > 0)
                        <ul class="text-sm space-y-2">
                            @foreach($rekapNilai as $nilai)
                            <li
                                class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 px-3 py-2 rounded-lg">
                                <span class="text-slate-600 dark:text-slate-400 font-medium truncate pr-3">{{
                                    $nilai->nama_mapel }}</span>
                                <span class="font-black text-indigo-600 dark:text-indigo-400">{{ $nilai->nilai_akhir
                                    }}</span>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <div
                            class="text-center bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50">
                            <p class="text-sm text-slate-500 italic">Data nilai mata pelajaran belum tersedia.</p>
                        </div>
                        @endif
                    </div>

                    {{-- Rekap Catatan Guru --}}
                    <div
                        class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                        <h3
                            class="font-black text-slate-800 dark:text-slate-200 mb-4 border-b border-slate-100 dark:border-slate-700 pb-2 uppercase tracking-wider text-sm">
                            Catatan Kejadian Guru</h3>

                        @if($teacherNotes->isEmpty())
                        <div
                            class="text-center bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50">
                            <p class="text-sm text-slate-500 italic">Tidak ada riwayat catatan perilaku atau prestasi
                                pada semester ini.</p>
                        </div>
                        @else
                        <ul class="text-sm space-y-3 max-h-80 overflow-y-auto pr-2">
                            @foreach($teacherNotes as $note)
                            <li
                                class="bg-slate-50 dark:bg-slate-900/50 p-3 rounded-xl border border-slate-100 dark:border-slate-700/50">
                                <span
                                    class="inline-block px-2 py-1 bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 text-[10px] font-bold uppercase rounded tracking-wider mb-2">{{
                                    $note->jenis_catatan }}</span>
                                <p class="text-slate-600 dark:text-slate-300 leading-relaxed">{{ $note->catatan }}</p>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>

                {{-- KOLOM KANAN: FORM INPUT WALI KELAS --}}
                <div class="lg:col-span-2">
                    <div
                        class="bg-white dark:bg-slate-800 p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 sticky top-6">

                        <div class="mb-8 border-b border-slate-100 dark:border-slate-700 pb-4 text-center sm:text-left">
                            <h3 class="text-xl font-black text-slate-800 dark:text-slate-200 uppercase tracking-tight">
                                Verifikasi & Kesimpulan Akhir</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Isi dan validasi data rapor
                                berdasarkan rekapitulasi di menu samping.</p>
                        </div>

                        <form action="{{ route('catatan_akhir.update', [$student->id, $classroom->id]) }}" method="POST"
                            class="space-y-6">
                            @csrf
                            <input type="hidden" name="academic_year_id" value="{{ $active_academic_year_id }}">
                            <input type="hidden" name="piket_terlaksana" value="{{ $piketTerlaksana }}">
                            <input type="hidden" name="piket_tidak_terlaksana" value="{{ $piketTidak }}">
                            <input type="hidden" name="ringkasan_catatan_guru"
                                value="{{ $teacherNotes->pluck('catatan')->implode(' | ') }}">

                            {{-- Penyesuaian Angka Kehadiran --}}
                            <div
                                class="bg-slate-50 dark:bg-slate-900/30 p-5 rounded-xl border border-slate-100 dark:border-slate-700/50">
                                <h4
                                    class="font-bold text-slate-700 dark:text-slate-300 mb-4 text-sm uppercase tracking-wider">
                                    Form Validasi Absensi Siswa</h4>
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2">Total
                                            Sakit</label>
                                        <input type="number" name="sakit"
                                            value="{{ old('sakit', $finalNote->sakit ?? $sakit) }}" min="0"
                                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2">Total
                                            Izin</label>
                                        <input type="number" name="izin"
                                            value="{{ old('izin', $finalNote->izin ?? $izin) }}" min="0"
                                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2">Total
                                            Alpha</label>
                                        <input type="number" name="alpha"
                                            value="{{ old('alpha', $finalNote->alpha ?? $alpha) }}" min="0"
                                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm">
                                    </div>
                                </div>
                            </div>

                            {{-- Catatan Final Wali Kelas --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Catatan
                                    Perilaku & Akademik (Cetak ke Raport)</label>
                                <textarea name="catatan_akhir" rows="7" required
                                    class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 p-4 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm"
                                    placeholder="Cth: Ananda {{ $student->nama_lengkap ?? 'Siswa' }} menunjukkan peningkatan yang luar biasa pada aspek akademik, namun perlu ditingkatkan kembali kedisiplinannya terkait tugas piket harian sekolah...">{{ old('catatan_akhir', $finalNote->catatan_akhir ?? '') }}</textarea>
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                                <button type="submit"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md transition transform hover:-translate-y-0.5 flex justify-center items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Simpan Catatan ke Database
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>