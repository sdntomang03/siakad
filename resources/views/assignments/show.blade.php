<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">{{ $assignment->title }}</h2></x-slot>
    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-5">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border dark:border-slate-700">
            <p class="text-sm text-slate-500">{{ $assignment->classroom->nama_kelas }} · {{ $assignment->subject?->nama_mapel ?? 'Tanpa mapel' }}</p>
            <p class="mt-4 whitespace-pre-line text-slate-700 dark:text-slate-200">{{ $assignment->description }}</p>
            <div class="flex gap-2 mt-5">
                <a href="{{ route('assignments.edit', $assignment) }}" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-xs font-bold">Edit tugas & kelompok</a>
                <form method="POST" action="{{ route('assignments.destroy', $assignment) }}" onsubmit="return confirm('Hapus tugas ini beserta kelompok dan item tugasnya?')">
                    @csrf @method('DELETE')
                    <button class="px-4 py-2 rounded-lg bg-red-600 text-white text-xs font-bold">Hapus tugas</button>
                </form>
            </div>
        </div>
        @if($assignment->type === 'group')
            <div class="grid md:grid-cols-2 gap-4">
                @foreach($assignment->groups as $group)
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border dark:border-slate-700">
                        <h3 class="font-bold">{{ $group->name }}</h3>
                        <p class="text-sm text-slate-500 mt-1">Ketua: {{ $group->leader->nama_lengkap }}</p>
                        <ul class="mt-3 text-sm list-disc list-inside">@foreach($group->students as $student)<li>{{ $student->nama_lengkap }}</li>@endforeach</ul>
                        <p class="mt-3 text-xs {{ $group->selectedTask ? 'text-emerald-600' : 'text-amber-600' }}">{{ $group->selectedTask ? 'Sudah memilih: '.$group->selectedTask->title : 'Belum memilih tugas' }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border dark:border-slate-700">
                <h3 class="font-bold mb-3">Tugas individu</h3>
                @foreach($assignment->tasks as $task)<div class="mb-3"><strong>{{ $task->title }}</strong><p class="text-sm text-slate-600 whitespace-pre-line">{{ $task->description }}</p></div>@endforeach
            </div>
        @endif
    </div>
</x-app-layout>
