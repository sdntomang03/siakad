<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">Tugas Saya</h2></x-slot>
    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-5">
            @forelse($assignments as $assignment)
                <a href="{{ route('assignments.show', $assignment) }}" class="block bg-white dark:bg-slate-800 p-5 rounded-2xl border dark:border-slate-700 hover:border-indigo-400">
                    <div class="flex justify-between gap-3"><h3 class="font-bold text-slate-800 dark:text-white">{{ $assignment->title }}</h3><span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600">{{ $assignment->type === 'group' ? 'Kelompok' : 'Individu' }}</span></div>
                    <p class="text-sm text-slate-500 mt-2">{{ $assignment->classroom->nama_kelas }} · {{ $assignment->subject?->nama_mapel ?? 'Tanpa mapel' }}</p>
                </a>
            @empty
                <p class="text-slate-500">Belum ada tugas untuk kelas Anda.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
