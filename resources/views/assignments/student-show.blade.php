<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">{{ $assignment->title }}</h2></x-slot>
    <div class="mx-auto max-w-3xl space-y-5 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <section class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
            <p class="text-sm text-slate-600 dark:text-slate-300">{{ $assignment->classroom->nama_kelas }} · {{ $assignment->subject?->nama_mapel ?? 'Tanpa mapel' }}</p>
            <div class="rich-content mt-4">{!! $assignment->description !!}</div>
        </section>
        @if($assignment->type === 'group')
            <section class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                <h3 class="text-lg font-semibold text-slate-950 dark:text-white">{{ $group->name }}</h3>
                <p class="text-sm text-slate-500 mt-1">Ketua: {{ $group->leader->nama_lengkap }}</p>
                <div class="mt-4"><h4 class="text-xs font-bold uppercase text-slate-500">Anggota kelompok</h4><ul class="mt-2 space-y-1">@foreach($group->students as $member)<li class="text-sm">{{ $member->nama_lengkap }} @if($member->id === $group->leader_student_id)<span class="text-xs text-brand-600">(Ketua)</span>@endif</li>@endforeach</ul></div>
                @if($selectedTask)
                    <div class="mt-5 rounded-md border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900 dark:bg-emerald-950/30"><h4 class="font-semibold text-emerald-900 dark:text-emerald-200">{{ $selectedTask->title }}</h4><div class="rich-content mt-2 text-sm text-emerald-900 dark:text-emerald-200">{!! $selectedTask->description !!}</div></div>
                @else
                    <form method="POST" action="{{ route('assignments.select-task', $assignment) }}" class="mt-5">@csrf<button class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">Acak tugas kelompok</button></form>
                    <p class="text-xs text-slate-500 mt-2">Tombol ini dapat ditekan oleh anggota mana pun; setelah terpilih, tugas yang sama akan terlihat oleh seluruh anggota.</p>
                @endif
            </section>
        @else
            <section class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                <h3 class="font-bold">{{ $selectedTask?->title }}</h3><p class="mt-2 whitespace-pre-line">{{ $selectedTask?->description }}</p>
            </section>
        @endif
    </div>
    <style>.rich-content p{margin:.5rem 0}.rich-content ol{list-style:decimal;padding-left:1.5rem}.rich-content ul{list-style:disc;padding-left:1.5rem}.rich-content a{color:#255b4b;text-decoration:underline}.rich-content blockquote{border-left:3px solid #cbd5e1;padding-left:.75rem;color:#64748b}</style>
</x-app-layout>
