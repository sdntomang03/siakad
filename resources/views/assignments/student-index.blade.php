<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Tugas Saya</h2></x-slot>
    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto grid max-w-screen-xl gap-5 md:grid-cols-2">
            @forelse($assignments as $assignment)
                <a href="{{ route('assignments.show', $assignment) }}" class="block rounded-lg border border-slate-200 bg-white p-5 transition-colors hover:border-brand-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-700 dark:bg-slate-800">
                    <div class="flex flex-wrap justify-between gap-3"><h3 class="font-semibold text-slate-950 dark:text-white">{{ $assignment->title }}</h3><span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700 dark:bg-slate-700 dark:text-slate-200">{{ $assignment->type === 'group' ? 'Kelompok' : 'Individu' }}</span></div>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $assignment->classroom->nama_kelas }} · {{ $assignment->subject?->nama_mapel ?? 'Tanpa mapel' }}</p>
                </a>
            @empty
                <p class="rounded-lg border border-slate-200 bg-white px-5 py-8 text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">Belum ada tugas untuk kelas Anda.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
