<x-guest-layout wide>
    <div class="space-y-6">
        <header class="rounded-2xl bg-gradient-to-r from-indigo-700 to-violet-700 px-6 py-8 text-center text-white shadow-lg">
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-indigo-200">Dokumen Publik</p>
            <h1 class="mt-2 text-2xl font-black">Rekap Dialog Kinerja</h1>
            <p class="mt-2 text-sm font-semibold">{{ $triwulan }} &middot; {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM YYYY') }}</p>
        </header>

        <section class="grid gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:grid-cols-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Nama</p>
                <p class="mt-1 font-semibold text-slate-800">{{ $employee?->nama_lengkap ?? $user->name }}</p>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">NIP</p>
                <p class="mt-1 font-semibold text-slate-800">{{ $employee?->nip ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Sekolah</p>
                <p class="mt-1 font-semibold text-slate-800">{{ $school?->nama_sekolah ?? config('app.name') }}</p>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="font-bold text-slate-800">Daftar Output dan Link Dialog Kinerja</h2>
                <p class="mt-1 text-sm text-slate-500">Link disinkronkan dari realisasi pada output yang sama.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3 text-center">No.</th>
                            <th class="px-5 py-3 text-left">Output</th>
                            <th class="px-5 py-3 text-left">Link Dialog Kinerja</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($dialogKinerja->items as $index => $item)
                        <tr class="align-top">
                            <td class="px-5 py-4 text-center font-semibold text-slate-500">{{ $index + 1 }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-800">{{ $item->outputTarget?->deskripsi_output ?? $item->nama_output }}</td>
                            <td class="px-5 py-4">
                                @if($item->link_referensi)
                                <a class="break-all font-medium text-indigo-600 underline hover:text-indigo-800" target="_blank" rel="noopener noreferrer" href="{{ $item->link_referensi }}">{{ $item->link_referensi }}</a>
                                @else
                                <span class="text-slate-400">Belum ada link</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-5 py-10 text-center text-slate-500">Belum ada data dialog kinerja untuk periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-guest-layout>
