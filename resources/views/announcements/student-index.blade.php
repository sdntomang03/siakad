<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Pengumuman</h2>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto max-w-screen-lg">
            <div class="border-b border-slate-300 pb-4 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Untuk Anda</h3>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Pengumuman sekolah dan pesan yang ditujukan kepada Anda.</p>
            </div>

            <div class="divide-y divide-slate-200 dark:divide-slate-700">
                @forelse ($announcements as $announcement)
                    <article class="min-w-0 py-6">
                        <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                            <h4 class="break-words text-lg font-semibold text-slate-900 dark:text-white">{{ $announcement->title }}</h4>
                            <time datetime="{{ $announcement->created_at->toIso8601String() }}" class="shrink-0 text-sm text-slate-600 dark:text-slate-300">{{ $announcement->created_at->format('d M Y, H:i') }}</time>
                        </div>
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                            Dari {{ $announcement->author?->employee?->nama_lengkap ?? $announcement->author?->name ?? 'Guru' }}
                        </p>
                        <p class="mt-3 break-words whitespace-pre-line text-sm leading-6 text-slate-800 dark:text-slate-200">{{ $announcement->body }}</p>
                    </article>
                @empty
                    <p class="py-8 text-sm text-slate-600 dark:text-slate-300">Belum ada pengumuman untuk Anda.</p>
                @endforelse
            </div>

            @if ($announcements->hasPages())
                <div class="mt-4">{{ $announcements->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
