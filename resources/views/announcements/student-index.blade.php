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

            <div class="mt-5 grid gap-4 md:grid-cols-2">
                @forelse ($announcements as $announcement)
                    <article class="min-w-0 rounded-lg border border-slate-200 bg-white p-5 transition-colors hover:border-brand-300 dark:border-slate-700 dark:bg-slate-800 dark:hover:border-brand-600 sm:p-6">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-brand-800 dark:text-brand-200">
                                    {{ $announcement->audience === 'all' ? 'Pengumuman sekolah' : 'Pesan pribadi' }}
                                </p>
                                <h4 class="mt-1 break-words text-lg font-semibold leading-6 text-slate-900 dark:text-white">{{ $announcement->title }}</h4>
                            </div>
                            <time datetime="{{ $announcement->created_at->toIso8601String() }}" class="shrink-0 text-sm text-slate-600 dark:text-slate-300">{{ $announcement->created_at->format('d M Y, H:i') }}</time>
                        </div>
                        <div class="mt-4 break-words whitespace-pre-line border-t border-slate-200 pt-4 text-sm leading-6 text-slate-800 dark:border-slate-700 dark:text-slate-200 [&_a]:font-medium [&_a]:text-brand-800 [&_a]:underline [&_a]:underline-offset-2 [&_a]:dark:text-brand-200 [&_blockquote]:my-3 [&_blockquote]:border-l-2 [&_blockquote]:border-brand-600 [&_blockquote]:pl-4 [&_h2]:my-3 [&_h2]:text-lg [&_h2]:font-semibold [&_h3]:my-3 [&_h3]:text-base [&_h3]:font-semibold [&_h4]:my-2 [&_h4]:font-semibold [&_hr]:my-4 [&_li]:my-1 [&_ol]:my-3 [&_ol]:list-decimal [&_ol]:pl-5 [&_p]:mb-3 [&_p:last-child]:mb-0 [&_table]:my-3 [&_table]:w-full [&_td]:border [&_td]:border-slate-300 [&_td]:p-2 [&_th]:border [&_th]:border-slate-300 [&_th]:bg-slate-100 [&_th]:p-2 [&_ul]:my-3 [&_ul]:list-disc [&_ul]:pl-5 dark:[&_td]:border-slate-600 dark:[&_th]:border-slate-600 dark:[&_th]:bg-slate-700">
                            {!! $announcement->sanitized_body !!}
                        </div>
                        <footer class="mt-5 border-t border-slate-200 pt-4 dark:border-slate-700">
                            <p class="text-sm text-slate-600 dark:text-slate-300">
                                Dari <span class="font-medium text-slate-800 dark:text-slate-100">{{ $announcement->author?->employee?->nama_lengkap ?? $announcement->author?->name ?? 'Guru' }}</span>
                            </p>
                        </footer>
                    </article>
                @empty
                    <p class="rounded-lg border border-dashed border-slate-300 bg-white px-5 py-8 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 md:col-span-2">Belum ada pengumuman untuk Anda.</p>
                @endforelse
            </div>

            @if ($announcements->hasPages())
                <div class="mt-4">{{ $announcements->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
