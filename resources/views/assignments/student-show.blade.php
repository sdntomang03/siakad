<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">{{ $assignment->title }}</h2></x-slot>
    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-5">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border dark:border-slate-700">
            <p class="text-sm text-slate-500">{{ $assignment->classroom->nama_kelas }} · {{ $assignment->subject?->nama_mapel ?? 'Tanpa mapel' }}</p>
            <div class="rich-content mt-4">{!! $assignment->description !!}</div>
        </div>
        @if($assignment->type === 'group')
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border dark:border-slate-700">
                <h3 class="font-bold text-lg">{{ $group->name }}</h3>
                <p class="text-sm text-slate-500 mt-1">Ketua: {{ $group->leader->nama_lengkap }}</p>
                <div class="mt-4"><h4 class="text-xs font-bold uppercase text-slate-500">Anggota kelompok</h4><ul class="mt-2 space-y-1">@foreach($group->students as $member)<li class="text-sm">{{ $member->nama_lengkap }} @if($member->id === $group->leader_student_id)<span class="text-xs text-indigo-600">(Ketua)</span>@endif</li>@endforeach</ul></div>
                @if($selectedTask)
                    <div class="mt-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200"><h4 class="font-bold text-emerald-900">{{ $selectedTask->title }}</h4><div class="rich-content mt-2 text-sm text-emerald-900">{!! $selectedTask->description !!}</div></div>
                @else
                    <form method="POST" action="{{ route('assignments.select-task', $assignment) }}" class="mt-5">@csrf<button class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-bold">Acak tugas kelompok</button></form>
                    <p class="text-xs text-slate-500 mt-2">Tombol ini dapat ditekan oleh anggota mana pun; setelah terpilih, tugas yang sama akan terlihat oleh seluruh anggota.</p>
                @endif
            </div>
        @else
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border dark:border-slate-700">
                <h3 class="font-bold">{{ $selectedTask?->title }}</h3><p class="mt-2 whitespace-pre-line">{{ $selectedTask?->description }}</p>
            </div>
        @endif
    </div>
    <style>.rich-content p{margin:.5rem 0}.rich-content ol{list-style:decimal;padding-left:1.5rem}.rich-content ul{list-style:disc;padding-left:1.5rem}.rich-content a{color:#4f46e5;text-decoration:underline}.rich-content blockquote{border-left:3px solid #cbd5e1;padding-left:.75rem;color:#64748b}</style>
</x-app-layout>
