<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">{{ $assignment->title }}</h2></x-slot>
    <div class="mx-auto max-w-6xl space-y-5 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <section class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
            <p class="text-sm text-slate-600 dark:text-slate-300">{{ $assignment->classroom->nama_kelas }} · {{ $assignment->subject?->nama_mapel ?? 'Tanpa mapel' }}</p>
            <div class="rich-content mt-4 text-slate-700 dark:text-slate-200">{!! $assignment->description !!}</div>
            <div class="mt-5 flex flex-col gap-3 border-t border-slate-200 pt-5 dark:border-slate-700 sm:flex-row">
                <a href="{{ route('assignments.edit', $assignment) }}" class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">Edit tugas & kelompok</a>
                <form method="POST" action="{{ route('assignments.destroy', $assignment) }}" onsubmit="return confirm('Hapus tugas ini beserta kelompok dan item tugasnya?')">
                    @csrf @method('DELETE')
                    <button class="inline-flex min-h-11 w-full items-center justify-center rounded-md border border-rose-300 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-700 dark:border-rose-800 dark:text-rose-300 dark:hover:bg-rose-950/40 sm:w-auto">Hapus tugas</button>
                </form>
            </div>
        </section>
        @if($assignment->type === 'group')
            <div class="grid md:grid-cols-2 gap-4">
                @foreach($assignment->groups as $group)
                    <section class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800">
                        <h3 class="font-semibold text-slate-950 dark:text-white">{{ $group->name }}</h3>
                        <p class="text-sm text-slate-500 mt-1">Ketua: {{ $group->leader->nama_lengkap }}</p>
                        <ul class="mt-3 text-sm list-disc list-inside">@foreach($group->students as $student)<li>{{ $student->nama_lengkap }}</li>@endforeach</ul>
                        <p class="mt-3 text-xs {{ $group->selectedTask ? 'text-emerald-600' : 'text-amber-600' }}">{{ $group->selectedTask ? 'Sudah memilih: '.$group->selectedTask->title : 'Belum memilih tugas' }}</p>
                    </section>
                @endforeach
            </div>
        @else
            <section class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                <h3 class="font-bold mb-3">Tugas individu</h3>
                @foreach($assignment->tasks as $task)<div class="mb-3"><strong>{{ $task->title }}</strong><div class="rich-content text-sm text-slate-600">{!! $task->description !!}</div></div>@endforeach
            </section>
        @endif
    </div>
    <style>.rich-content p{margin:.5rem 0}.rich-content ol{list-style:decimal;padding-left:1.5rem}.rich-content ul{list-style:disc;padding-left:1.5rem}.rich-content a{color:#255b4b;text-decoration:underline}.rich-content blockquote{border-left:3px solid #cbd5e1;padding-left:.75rem;color:#64748b}</style>
</x-app-layout>
