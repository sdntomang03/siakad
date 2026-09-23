<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>E-Kinerja Saya</title>

    <!-- Fonts & Scripts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50 dark:bg-gray-900 dark:text-gray-100 min-h-screen">

    <div class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-6">

            {{-- Judul Halaman --}}
            <div class="text-center mb-10">
                <h2 class="text-2xl sm:text-3xl font-black text-gray-800 dark:text-gray-200">
                    Dokumen <span class="text-indigo-600 dark:text-indigo-400">e-Kinerja Saya</span>
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Daftar Rencana Aksi dan Output milik Anda
                    berdasarkan Target Waktu (TW).</p>
            </div>

            {{-- Menampilkan Notifikasi Sukses/Error --}}
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                <strong class="font-bold">Gagal!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif

            @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Filter periode dokumen --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sm:p-8">
                <form action="{{ route('etpp.ku') }}" method="GET"
                    class="flex flex-col sm:flex-row gap-4 items-end justify-center">

                    {{-- Select Filter TW --}}
                    <div class="w-full sm:w-1/2">
                        <label for="tw"
                            class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Pilih
                            Target Waktu (TW)</label>
                        <select id="tw" name="tw"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-base sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition">
                            <option value="semua" {{ $filter_tw=='semua' ? 'selected' : '' }}>-- Tampilkan Semua TW --
                            </option>
                            <option value="TW 1" {{ $filter_tw=='TW 1' ? 'selected' : '' }}>Triwulan 1 (TW 1)</option>
                            <option value="TW 2" {{ $filter_tw=='TW 2' ? 'selected' : '' }}>Triwulan 2 (TW 2)</option>
                            <option value="TW 3" {{ $filter_tw=='TW 3' ? 'selected' : '' }}>Triwulan 3 (TW 3)</option>
                            <option value="TW 4" {{ $filter_tw=='TW 4' ? 'selected' : '' }}>Triwulan 4 (TW 4)</option>
                        </select>
                    </div>
                    <div class="w-full sm:w-1/4">
                        <label for="tahun"
                            class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Tahun</label>
                        <input id="tahun" name="tahun" type="number" min="2000" max="2100" value="{{ $tahun }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-base sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition">
                    </div>
                    <div class="w-full sm:w-1/4">
                        <label for="bulan"
                            class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Bulan Dialog</label>
                        <select id="bulan" name="bulan"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-base sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition">
                            @foreach(range(1, 12) as $nomorBulan)
                            <option value="{{ $nomorBulan }}" {{ $bulan === $nomorBulan ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate($tahun, $nomorBulan, 1)->locale('id')->isoFormat('MMMM') }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="w-full sm:w-auto">
                        <button type="submit"
                            class="w-full sm:w-auto px-8 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-md hover:bg-indigo-700 hover:shadow-lg transition">
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <section class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Realisasi Triwulan</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Simpan realisasi untuk setiap output e-Kinerja.</p>
                        </div>
                        <form action="{{ route('etpp.realisasi.pdf') }}" method="GET" class="shrink-0">
                            <input type="hidden" name="tahun" value="{{ $tahun }}">
                            <div class="flex items-center gap-2">
                                <select name="triwulan" aria-label="Triwulan PDF realisasi"
                                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-xs py-1">
                                    @foreach(['TW 1', 'TW 2', 'TW 3', 'TW 4'] as $triwulan)
                                    <option value="{{ $triwulan }}" {{ $filter_tw === $triwulan ? 'selected' : '' }}>{{ $triwulan }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                                    Unduh PDF
                                </button>
                            </div>
                        </form>
                    </div>

                    <form action="{{ route('etpp.realisasi.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1" for="realisasi_tahun">Tahun</label>
                                <input id="realisasi_tahun" name="tahun" type="number" min="2000" max="2100" value="{{ old('tahun', $tahun) }}" required
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1" for="triwulan">Triwulan</label>
                                <select id="triwulan" name="triwulan" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm">
                                    @foreach(['TW 1', 'TW 2', 'TW 3', 'TW 4'] as $triwulan)
                                    <option value="{{ $triwulan }}" {{ old('triwulan', $filter_tw === 'semua' ? '' : $filter_tw) === $triwulan ? 'selected' : '' }}>{{ $triwulan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1" for="output_target_id">Output e-Kinerja</label>
                            <select id="output_target_id" name="output_target_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm">
                                <option value="">Pilih output</option>
                                @foreach($outputTargets as $outputTarget)
                                <option value="{{ $outputTarget->id }}" {{ old('output_target_id') == $outputTarget->id ? 'selected' : '' }}>
                                    {{ $outputTarget->target_waktu }} - {{ \Illuminate\Support\Str::limit($outputTarget->deskripsi_output, 90) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1" for="realisasi">Uraian Realisasi</label>
                            <textarea id="realisasi" name="realisasi" rows="4" required maxlength="10000"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm"
                                placeholder="Jelaskan hasil atau capaian realisasi...">{{ old('realisasi') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1" for="realisasi_link_referensi">Link Profil Realisasi</label>
                            <input id="realisasi_link_referensi" name="link_referensi" type="url" value="{{ old('link_referensi') }}"
                                maxlength="2048" placeholder="https://contoh.go.id/profil-realisasi"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Opsional. Link akan muncul di bawah daftar realisasi dan pada PDF.</p>
                        </div>
                        <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 transition">
                            Simpan Realisasi
                        </button>
                    </form>

                    @if($realisasiList->isNotEmpty())
                    <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-700 space-y-2">
                        <p class="text-xs font-bold text-gray-500 uppercase">
                            Daftar Realisasi {{ $filter_tw === 'semua' ? 'Semua Triwulan' : $filter_tw }} Tahun {{ $tahun }}
                        </p>
                        @foreach($realisasiList as $realisasiItem)
                        <details class="text-sm p-3 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                            <summary class="cursor-pointer">
                                <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $realisasiItem->triwulan }}</span>
                                <span class="text-gray-700 dark:text-gray-200">— {{ $realisasiItem->outputTarget->deskripsi_output }}</span>
                            </summary>
                            <form action="{{ route('etpp.realisasi.update', $realisasiItem) }}" method="POST" class="mt-3 space-y-2">
                                @csrf
                                @method('PUT')
                                <textarea name="realisasi" rows="3" required maxlength="10000"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm">{{ $realisasiItem->realisasi }}</textarea>
                                <input name="link_referensi" type="url" value="{{ $realisasiItem->link_referensi }}" maxlength="2048"
                                    placeholder="https://contoh.go.id/profil-realisasi"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm">
                                <div class="flex flex-wrap items-center gap-3">
                                    <button type="submit" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">Perbarui</button>
                                    @if($realisasiItem->link_referensi)
                                    <a href="{{ $realisasiItem->link_referensi }}" target="_blank" rel="noopener noreferrer"
                                        class="text-xs font-bold text-emerald-600 hover:text-emerald-800 dark:text-emerald-400">
                                        Buka Profil Realisasi
                                    </a>
                                    @endif
                                </div>
                            </form>
                            <form action="{{ route('etpp.realisasi.destroy', $realisasiItem) }}" method="POST" class="mt-2"
                                onsubmit="return confirm('Hapus realisasi ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-800 dark:text-red-400">Hapus</button>
                            </form>
                        </details>
                        @endforeach
                    </div>
                    @endif
                </section>

                <section class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Dialog Kinerja Bulanan</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Catat hasil dialog kinerja untuk setiap bulan.</p>
                        </div>
                        <form action="{{ route('etpp.dialog-kinerja.pdf') }}" method="GET" class="shrink-0">
                            <input type="hidden" name="tahun" value="{{ $tahun }}">
                            <input type="hidden" name="bulan" value="{{ $bulan }}">
                            <button type="submit" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                                Unduh PDF
                            </button>
                        </form>
                    </div>

                    <form action="{{ route('etpp.dialog-kinerja.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1" for="dialog_tahun">Tahun</label>
                                <input id="dialog_tahun" name="tahun" type="number" min="2000" max="2100" value="{{ old('tahun', $tahun) }}" required
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1" for="dialog_bulan">Bulan</label>
                                <select id="dialog_bulan" name="bulan" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm">
                                    @foreach(range(1, 12) as $nomorBulan)
                                    <option value="{{ $nomorBulan }}" {{ (int) old('bulan', $bulan) === $nomorBulan ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromDate($tahun, $nomorBulan, 1)->locale('id')->isoFormat('MMMM') }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1" for="uraian">Hasil Dialog Kinerja</label>
                            <textarea id="uraian" name="uraian" rows="7" required maxlength="10000"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm"
                                placeholder="Tuliskan pokok pembahasan, arahan, dan tindak lanjut...">{{ old('uraian', $dialogKinerja?->uraian) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1" for="dialog_link_referensi">Link Profil Dialog Kinerja</label>
                            <input id="dialog_link_referensi" name="link_referensi" type="url" value="{{ old('link_referensi', $dialogKinerja?->link_referensi) }}"
                                maxlength="2048" placeholder="https://contoh.go.id/profil-dialog-kinerja"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Opsional. Link akan dapat dibuka dari profil dialog dan PDF.</p>
                        </div>
                        <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 transition">
                            Simpan Dialog Kinerja
                        </button>
                    </form>

                    @if($dialogKinerja)
                    <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <p class="text-xs font-bold text-gray-500 uppercase">Profil Dialog Kinerja Terpilih</p>
                        <div class="mt-2 flex flex-wrap items-center gap-3">
                            @if($dialogKinerja->link_referensi)
                            <a href="{{ $dialogKinerja->link_referensi }}" target="_blank" rel="noopener noreferrer"
                                class="text-sm font-bold text-emerald-600 hover:text-emerald-800 dark:text-emerald-400">
                                Buka Profil Dialog Kinerja
                            </a>
                            @else
                            <span class="text-sm text-gray-500 dark:text-gray-400">Belum ada link profil.</span>
                            @endif
                            <form action="{{ route('etpp.dialog-kinerja.destroy', $dialogKinerja) }}" method="POST"
                                onsubmit="return confirm('Hapus dialog kinerja bulan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-800 dark:text-red-400">Hapus Dialog</button>
                            </form>
                        </div>
                    </div>
                    @endif
                </section>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- KIRI: Profil Pegawai Anda --}}
                <div
                    class="md:col-span-1 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden self-start sticky top-6">
                    <div
                        class="p-6 bg-indigo-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 text-center">
                        <div
                            class="w-24 h-24 bg-indigo-200 text-indigo-700 rounded-full mx-auto flex items-center justify-center text-3xl font-black mb-4 uppercase shadow-inner">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">
                            {{ $employee->nama_lengkap ?? auth()->user()->name }}
                        </h3>
                        <p class="text-sm text-gray-500 font-mono mt-1">{{ $employee->nip ?? 'NIP Belum Terdaftar' }}
                        </p>

                        @if(isset($employee->kategori_pegawai))
                        <span
                            class="inline-block mt-3 px-3 py-1 bg-indigo-600 text-white text-[10px] font-bold uppercase rounded-full tracking-widest">
                            {{ $employee->kategori_pegawai }}
                        </span>
                        @endif
                    </div>

                    @if($employee)
                    <div class="p-6 space-y-4 text-sm bg-white dark:bg-gray-800">
                        <div>
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Status
                                Kepegawaian</span>
                            <span class="block font-semibold text-gray-800 dark:text-gray-200">{{
                                $employee->status_kepegawaian ?? 'Belum Diatur' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Tugas
                                Tambahan</span>
                            <span class="block font-semibold text-gray-800 dark:text-gray-200">{{
                                $employee->tugas_tambahan ?? 'Tidak Ada' }}</span>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- KANAN: Daftar Dokumen Output e-Kinerja Saya --}}
                <div class="md:col-span-2 space-y-6">

                    @if(isset($data_kategori) && count($data_kategori) > 0)
                    @foreach($data_kategori as $kategori)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

                        {{-- Header Kategori --}}
                        <div class="bg-gray-50 dark:bg-gray-900/50 p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-bold text-gray-800 dark:text-gray-200 text-lg flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                    </path>
                                </svg>
                                {{ $kategori->nama_kategori }}
                            </h3>
                        </div>

                        {{-- Body Daftar RHK & Aksi --}}
                        <div class="p-5 space-y-6">
                            @php
                            /*
                            * Menyaring RHK: Hanya simpan RHK yang memiliki
                            * minimal 1 Rencana Aksi dengan Output sesuai filter TW
                            */
                            $filteredRhk = $kategori->rhk->filter(function($rhk) {
                            return $rhk->rencanaAksi->contains(function($ra) {
                            return count($ra->outputTarget) > 0;
                            });
                            });
                            @endphp

                            @forelse($filteredRhk as $rhk)
                            <div class="space-y-3">
                                {{-- Judul RHK --}}
                                <h4 class="font-semibold text-sm text-indigo-700 dark:text-indigo-400 leading-snug">
                                    {{ $rhk->deskripsi_rhk }}
                                </h4>

                                {{-- Indentasi untuk Rencana Aksi --}}
                                <div class="pl-4 border-l-2 border-indigo-100 dark:border-indigo-900/50 space-y-4">
                                    @foreach($rhk->rencanaAksi as $ra)

                                    {{-- Hanya tampilkan jika Rencana Aksi memiliki Output --}}
                                    @if(count($ra->outputTarget) > 0)
                                    <div
                                        class="bg-gray-50/50 dark:bg-gray-800/50 rounded-lg p-4 border border-gray-100 dark:border-gray-700">

                                        <div class="mb-4">
                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                Rencana Aksi</p>
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2">{{
                                                $ra->deskripsi_ra }}</p>

                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                Kriteria Keberhasilan</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{
                                                $ra->kriteria_keberhasilan }}</p>
                                        </div>

                                        {{-- Output & Target Waktu --}}
                                        <div>
                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                                Target, Output & Bukti Dukung</p>
                                            <ul class="space-y-4">
                                                @foreach($ra->outputTarget as $output)
                                                <li
                                                    class="flex flex-col gap-3 bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm relative overflow-hidden">

                                                    {{-- Detail Target & Output --}}
                                                    <div class="flex items-start gap-3">
                                                        <span
                                                            class="shrink-0 mt-0.5 px-2 py-1 bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300 text-[11px] font-black tracking-wider uppercase rounded">
                                                            {{ $output->target_waktu }}
                                                        </span>
                                                        <span
                                                            class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                                                            {{ $output->deskripsi_output }}
                                                        </span>
                                                    </div>

                                                    <div class="pl-12 space-y-3">
                                                        {{-- Daftar Bukti Dukung yang Sudah Diupload --}}
                                                        @if(isset($output->buktiDukung) && $output->buktiDukung->count()
                                                        > 0)
                                                        <div class="space-y-2 mt-3 mb-4">

                                                            {{-- Header Bukti & Tombol Hapus Semua (Per Output) --}}
                                                            <div
                                                                class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-1">
                                                                <p
                                                                    class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                                                                    Bukti Terlampir:</p>

                                                                <form
                                                                    action="{{ route('etpp.destroy_bukti_output', $output->id) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus SEMUA dokumen bukti pada output ini?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="text-[11px] font-bold text-red-500 hover:text-red-700 transition flex items-center gap-1">
                                                                        <svg class="w-3 h-3" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                            </path>
                                                                        </svg>
                                                                        Hapus Semua
                                                                    </button>
                                                                </form>
                                                            </div>

                                                            {{-- Daftar Individual File & Link Bukti --}}
                                                            <div class="flex flex-wrap gap-2">
                                                                @foreach($output->buktiDukung as $bukti)
                                                                <div
                                                                    class="inline-flex items-center bg-indigo-50 dark:bg-indigo-900/20 rounded-md border border-indigo-100 dark:border-indigo-800/50 overflow-hidden">

                                                                    {{-- Tentukan URL (Link Eksternal atau File
                                                                    Internal) --}}
                                                                    @php
                                                                    $urlBukti = $bukti->jenis_bukti === 'link' ?
                                                                    $bukti->tautan : asset('storage/' .
                                                                    $bukti->file_path);
                                                                    @endphp

                                                                    {{-- Link Lihat Dokumen --}}
                                                                    <a href="{{ $urlBukti }}" target="_blank"
                                                                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition"
                                                                        title="Lihat Dokumen">

                                                                        {{-- Ikon berubah tergantung jenis (File / Link)
                                                                        --}}
                                                                        @if($bukti->jenis_bukti === 'link')
                                                                        <svg class="w-4 h-4 shrink-0" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                                                            </path>
                                                                        </svg>
                                                                        @else
                                                                        <svg class="w-4 h-4 shrink-0" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                                                            </path>
                                                                        </svg>
                                                                        @endif

                                                                        <span
                                                                            class="truncate max-w-[150px] sm:max-w-[200px]">{{
                                                                            $bukti->nama_bukti }}</span>
                                                                    </a>

                                                                    {{-- Tombol Hapus Individual --}}
                                                                    <form
                                                                        action="{{ route('etpp.destroy_bukti', $bukti->id) }}"
                                                                        method="POST"
                                                                        onsubmit="return confirm('Hapus dokumen ini?');"
                                                                        class="flex">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="px-2 py-1.5 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white dark:bg-red-900/30 dark:hover:bg-red-600 dark:hover:text-white transition"
                                                                            title="Hapus Dokumen">
                                                                            <svg class="w-4 h-4" fill="none"
                                                                                stroke="currentColor"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M6 18L18 6M6 6l12 12"></path>
                                                                            </svg>
                                                                        </button>
                                                                    </form>

                                                                </div>
                                                                @endforeach
                                                            </div>

                                                        </div>
                                                        @endif

                                                        {{-- Form Upload Bukti Baru (Dinamis: Link / File) --}}
                                                        <form action="{{ route('etpp.upload_bukti') }}" method="POST"
                                                            enctype="multipart/form-data"
                                                            class="flex flex-col sm:flex-row gap-3 items-center bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 mt-2">
                                                            @csrf

                                                            {{-- Hidden ID Output Target --}}
                                                            <input type="hidden" name="output_target_id"
                                                                value="{{ $output->id }}">

                                                            {{-- Dropdown Pilih Jenis Bukti --}}
                                                            <select name="jenis_bukti"
                                                                onchange="toggleBuktiInput(this, {{ $output->id }})"
                                                                class="w-full sm:w-1/4 text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white px-2 py-1.5 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                                                <option value="link" selected>Tautkan Link</option>
                                                                <option value="file">Unggah File</option>
                                                            </select>

                                                            {{-- Input 1: Link (Tampil secara default) --}}
                                                            <input type="url" name="link_bukti"
                                                                id="input_link_{{ $output->id }}" required
                                                                placeholder="Contoh: https://drive.google.com/..."
                                                                style="display: block;"
                                                                class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white px-2 py-1.5 focus:ring-indigo-500 focus:border-indigo-500 transition">

                                                            {{-- Input 2: File (Sembunyi secara default) --}}
                                                            <input type="file" name="file_bukti"
                                                                id="input_file_{{ $output->id }}" style="display: none;"
                                                                accept=".pdf,.jpg,.jpeg,.png"
                                                                class="w-full text-xs text-gray-500 dark:text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200 cursor-pointer transition">

                                                            {{-- Tombol Submit --}}
                                                            <button type="submit"
                                                                class="w-full sm:w-auto shrink-0 bg-indigo-600 text-white text-xs px-5 py-2 rounded-md hover:bg-indigo-700 font-bold transition shadow-sm flex items-center justify-center gap-1.5">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12">
                                                                    </path>
                                                                </svg>
                                                                Simpan
                                                            </button>
                                                        </form>

                                                    </div>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                    </div>
                                    @endif

                                    @endforeach
                                </div>
                            </div>

                            {{-- Hanya tampilkan pemisah jika bukan elemen terakhir yang di loop --}}
                            @if(!$loop->last)
                            <hr class="border-gray-100 dark:border-gray-700">
                            @endif

                            @empty
                            <p class="text-sm text-gray-500 italic">Belum ada Rencana Hasil Kerja yang sesuai dengan
                                filter.</p>
                            @endforelse
                        </div>
                    </div>
                    @endforeach
                    @else
                    {{-- Tampilan jika Data Kosong / Belum Import Data e-Kinerja --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center flex flex-col items-center justify-center h-full min-h-[300px]">
                        <div
                            class="relative w-20 h-20 mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center text-gray-400 dark:text-gray-500">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300">Dokumen Tidak Ditemukan</h3>
                        <p class="text-sm text-gray-500 mt-1 max-w-sm">Anda belum mengimpor data e-Kinerja, atau tidak
                            ada output yang sesuai dengan filter <strong class="text-gray-600 dark:text-gray-400">{{
                                $filter_tw }}</strong>.</p>

                        <a href="{{ route('etpp.import.form') }}"
                            class="mt-6 px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-md hover:bg-indigo-700 transition">
                            Import Data e-Kinerja Sekarang
                        </a>
                    </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

    {{-- Script untuk Toggle Form Input Bukti (File vs Link) --}}
    <script>
        function toggleBuktiInput(selectObj, outputId) {
            const fileInput = document.getElementById('input_file_' + outputId);
            const linkInput = document.getElementById('input_link_' + outputId);

            if (selectObj.value === 'file') {
                fileInput.style.display = 'block';
                fileInput.setAttribute('required', 'required');

                linkInput.style.display = 'none';
                linkInput.removeAttribute('required');
                linkInput.value = ''; // Reset isian link
            } else {
                fileInput.style.display = 'none';
                fileInput.removeAttribute('required');
                fileInput.value = ''; // Reset isian file

                linkInput.style.display = 'block';
                linkInput.setAttribute('required', 'required');
            }
        }
    </script>

</body>

</html>