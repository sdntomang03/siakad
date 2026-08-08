<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>Kesimpulan Catatan Akhir Siswa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900 antialiased p-8">

    <div class="max-w-6xl mx-auto">
        <h2 class="text-2xl font-bold mb-6">Penyusunan Catatan Akhir: <span class="text-indigo-600">{{ $student->nama
                }}</span></h2>

        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg font-bold">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- KOLOM KIRI: REKAP INFORMASI --}}
            <div class="md:col-span-1 space-y-4">

                {{-- Rekap Absen --}}
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="font-bold text-gray-700 mb-3 border-b pb-2">Rekap Kehadiran</h3>
                    <ul class="text-sm space-y-2">
                        <li class="flex justify-between"><span>Sakit:</span> <span class="font-bold text-blue-600">{{
                                $finalNote->sakit ?? $sakit }} Hari</span></li>
                        <li class="flex justify-between"><span>Izin:</span> <span class="font-bold text-yellow-600">{{
                                $finalNote->izin ?? $izin }} Hari</span></li>
                        <li class="flex justify-between"><span>Alpha:</span> <span class="font-bold text-red-600">{{
                                $finalNote->alpha ?? $alpha }} Hari</span></li>
                    </ul>
                </div>

                {{-- Rekap Piket --}}
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="font-bold text-gray-700 mb-3 border-b pb-2">Kepatuhan Piket</h3>
                    <ul class="text-sm space-y-2">
                        <li class="flex justify-between"><span>Terlaksana:</span> <span
                                class="font-bold text-green-600">{{ $piketTerlaksana }}x</span></li>
                        <li class="flex justify-between"><span>Tidak/Kabur:</span> <span
                                class="font-bold text-red-600">{{ $piketTidak }}x</span></li>
                    </ul>
                </div>

                {{-- Rekap Catatan Guru --}}
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="font-bold text-gray-700 mb-3 border-b pb-2">Catatan Guru (Selama 1 Semester)</h3>
                    @if($teacherNotes->isEmpty())
                    <p class="text-sm text-gray-500 italic">Tidak ada catatan perilaku/prestasi.</p>
                    @else
                    <ul class="text-sm space-y-3">
                        @foreach($teacherNotes as $note)
                        <li class="bg-gray-50 p-2 rounded border border-gray-100">
                            <span class="block text-xs font-bold text-indigo-600 mb-1">{{ $note->jenis_catatan }}</span>
                            {{ $note->catatan }}
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>

            {{-- KOLOM KANAN: FORM INPUT WALI KELAS --}}
            <div class="md:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <form action="{{ route('catatan_akhir.update', [$student->id, $classroom->id]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="academic_year_id" value="{{ $active_academic_year_id }}">

                    {{-- Hidden input agar hasil perhitungan database ikut tersimpan ke tabel summary --}}
                    <input type="hidden" name="piket_terlaksana" value="{{ $piketTerlaksana }}">
                    <input type="hidden" name="piket_tidak_terlaksana" value="{{ $piketTidak }}">

                    {{-- Menyimpan gabungan string catatan guru --}}
                    <input type="hidden" name="ringkasan_catatan_guru"
                        value="{{ $teacherNotes->pluck('catatan')->implode(' | ') }}">

                    <h3 class="font-bold text-lg text-gray-800 mb-4">Verifikasi & Kesimpulan Akhir</h3>

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Total Sakit</label>
                            <input type="number" name="sakit" value="{{ old('sakit', $finalNote->sakit ?? $sakit) }}"
                                class="w-full rounded border-gray-300 px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Total Izin</label>
                            <input type="number" name="izin" value="{{ old('izin', $finalNote->izin ?? $izin) }}"
                                class="w-full rounded border-gray-300 px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Total Alpha</label>
                            <input type="number" name="alpha" value="{{ old('alpha', $finalNote->alpha ?? $alpha) }}"
                                class="w-full rounded border-gray-300 px-3 py-2">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Final Wali Kelas (Untuk di
                            Raport)</label>
                        <p class="text-xs text-gray-500 mb-2">Buat kesimpulan berdasarkan data absensi, kedisiplinan
                            piket, dan masukan guru mata pelajaran di sebelah kiri.</p>
                        <textarea name="catatan_akhir" rows="6" required
                            class="w-full rounded border-gray-300 p-3 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Cth: Pertahankan prestasimu dan tingkatkan kedisiplinan dalam melaksanakan tugas piket harian...">{{ old('catatan_akhir', $finalNote->catatan_akhir ?? '') }}</textarea>
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded hover:bg-indigo-700 transition">
                        Simpan Catatan Akhir
                    </button>
                </form>
            </div>

        </div>
    </div>
</body>

</html>