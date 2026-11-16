<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Program Remedial</h1>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        @if (session('success'))
            <div role="status" class="rounded-md border border-brand-600 bg-brand-50 px-4 py-3 text-sm text-brand-900 dark:border-brand-500 dark:bg-brand-900/30 dark:text-brand-100">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="rounded-md border border-rose-700 bg-rose-50 px-4 py-3 text-sm text-rose-900 dark:border-rose-500 dark:bg-rose-950/40 dark:text-rose-100">
                <p class="font-semibold">Program belum dapat disimpan.</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="flex flex-col gap-4 border-b border-slate-200 pb-5 dark:border-slate-700 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Daftar program</h2>
                <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">
                    Pilih penilaian sebagai dasar perhitungan, lalu catat keikutsertaan dan nilai remedial siswa.
                </p>
            </div>
            <a href="{{ route('remedials.create') }}"
                class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 active:bg-brand-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                Buat program remedial
            </a>
        </section>

        @if ($programs->isEmpty())
            <div class="rounded-lg border border-slate-300 bg-white px-5 py-10 text-center dark:border-slate-700 dark:bg-slate-800">
                <h3 class="font-semibold text-slate-900 dark:text-white">Belum ada program remedial</h3>
                <p class="mt-2 text-sm text-slate-700 dark:text-slate-300">Program yang Anda simpan akan tampil di daftar ini.</p>
                <a href="{{ route('remedials.create') }}"
                    class="mt-5 inline-flex min-h-11 items-center justify-center rounded-md border border-brand-700 px-4 py-2 text-sm font-semibold text-brand-800 hover:bg-brand-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-brand-400 dark:text-brand-100 dark:hover:bg-brand-900/40">
                    Pilih penilaian
                </a>
            </div>
        @else
            <div class="overflow-x-auto rounded-lg border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-800">
                <table class="w-full min-w-[620px] divide-y divide-slate-200 text-left text-sm dark:divide-slate-700">
                    <caption class="sr-only">Program remedial yang pernah disimpan</caption>
                    <thead class="bg-slate-50 text-slate-700 dark:bg-slate-900/50 dark:text-slate-200">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-semibold">Kelas</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Mata pelajaran</th>
                            <th scope="col" class="px-4 py-3 text-center font-semibold">Siswa remedial</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Dibuat</th>
                            <th scope="col" class="px-4 py-3 text-right font-semibold">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach ($programs as $program)
                            <tr class="text-slate-800 dark:text-slate-100">
                                <td class="px-4 py-3">{{ $program->classroom->tingkat }} - {{ $program->classroom->nama_kelas }}</td>
                                <td class="px-4 py-3">{{ $program->subject->nama_mapel }}</td>
                                <td class="px-4 py-3 text-center tabular-nums">{{ $program->records_count }}</td>
                                <td class="px-4 py-3">{{ $program->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('remedials.show', $program) }}"
                                        class="inline-flex min-h-11 items-center rounded-md px-3 font-semibold text-brand-800 hover:bg-brand-50 hover:text-brand-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:text-brand-200 dark:hover:bg-brand-900/40">
                                        Buka program
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div>{{ $programs->links() }}</div>
        @endif
    </div>
</x-app-layout>
