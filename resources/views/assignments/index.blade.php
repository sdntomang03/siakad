<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Tugas Saya</h2></x-slot>
    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto max-w-screen-xl">
        <div class="mb-5 flex justify-end">
            <a href="{{ route('assignments.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">Buat tugas</a>
        </div>
        <div class="grid gap-5 md:grid-cols-2">
            @forelse($assignments as $assignment)
                <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800">
                    <a href="{{ route('assignments.show', $assignment) }}" class="block rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                    <div class="flex flex-wrap items-start justify-between gap-3"><h3 class="font-semibold text-slate-950 hover:text-brand-800 dark:text-white dark:hover:text-brand-200">{{ $assignment->title }}</h3><span class="rounded-md bg-brand-50 px-2 py-1 text-xs font-medium text-brand-900 dark:bg-brand-900 dark:text-brand-100">{{ $assignment->type === 'group' ? 'Kelompok' : 'Individu' }}</span></div>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $assignment->classroom->nama_kelas }} · {{ $assignment->subject?->nama_mapel ?? 'Tanpa mapel' }}</p>
                    <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">{{ $assignment->tasks->count() }} tugas tersedia</p>
                    </a>
                    <div class="mt-4 flex flex-wrap items-center gap-4 border-t border-slate-200 pt-3 dark:border-slate-700">
                        <a href="{{ route('assignments.edit', $assignment) }}" class="inline-flex min-h-11 items-center text-sm font-medium text-brand-800 underline underline-offset-4 hover:text-brand-900 dark:text-brand-200 dark:hover:text-white">Edit tugas & kelompok</a>
                        <form method="POST" action="{{ route('assignments.destroy', $assignment) }}" onsubmit="return confirm('Hapus tugas ini beserta kelompok dan item tugasnya?')">
                            @csrf @method('DELETE')
                            <button class="inline-flex min-h-11 items-center text-sm font-medium text-red-700 underline underline-offset-4 hover:text-red-900 dark:text-red-300">Hapus tugas</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="rounded-lg border border-slate-200 bg-white px-5 py-8 text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">Belum ada tugas yang dibuat.</p>
            @endforelse
        </div>
        </div>
    </div>
</x-app-layout>
