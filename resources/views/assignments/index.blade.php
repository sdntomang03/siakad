<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">Tugas Saya</h2></x-slot>
    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-end mb-5"><a href="{{ route('assignments.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-bold">+ Buat tugas</a></div>
        <div class="grid md:grid-cols-2 gap-5">
            @forelse($assignments as $assignment)
                <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border dark:border-slate-700">
                    <a href="{{ route('assignments.show', $assignment) }}" class="block hover:text-indigo-600">
                    <div class="flex justify-between gap-3"><h3 class="font-bold text-slate-800 dark:text-white">{{ $assignment->title }}</h3><span class="text-xs px-2 py-1 rounded-full bg-indigo-100 text-indigo-700">{{ $assignment->type === 'group' ? 'Kelompok' : 'Individu' }}</span></div>
                    <p class="text-sm text-slate-500 mt-2">{{ $assignment->classroom->nama_kelas }} · {{ $assignment->subject?->nama_mapel ?? 'Tanpa mapel' }}</p>
                    <p class="text-xs text-slate-400 mt-3">{{ $assignment->tasks->count() }} tugas tersedia</p>
                    </a>
                    <div class="flex gap-2 mt-4 pt-3 border-t dark:border-slate-700">
                        <a href="{{ route('assignments.edit', $assignment) }}" class="text-xs font-bold text-indigo-600">Edit tugas & kelompok</a>
                        <form method="POST" action="{{ route('assignments.destroy', $assignment) }}" onsubmit="return confirm('Hapus tugas ini beserta kelompok dan item tugasnya?')">
                            @csrf @method('DELETE')
                            <button class="text-xs font-bold text-red-600">Hapus tugas</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-slate-500">Belum ada tugas yang dibuat.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
