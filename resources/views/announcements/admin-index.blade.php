<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Pengumuman</h2>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto max-w-screen-lg">
            @if (session('success'))
                <p role="status" class="mb-5 rounded-md border border-brand-600 bg-brand-50 px-4 py-3 text-sm font-medium text-brand-900 dark:border-brand-600 dark:bg-brand-900/40 dark:text-brand-100">
                    {{ session('success') }}
                </p>
            @endif

            <div class="border-b border-slate-300 pb-4 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Kelola pengumuman</h3>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Periksa pengumuman dan hapus yang tidak lagi diperlukan.</p>
            </div>

            <div class="mt-5 space-y-4">
                @forelse ($announcements as $announcement)
                    <article class="min-w-0 rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-brand-800 dark:text-brand-200">
                                    @if ($announcement->audience === 'all')
                                        Pengumuman sekolah
                                    @elseif ($announcement->audience === 'class_personalized')
                                        Pesan pribadi untuk kelas {{ $announcement->classroom?->tingkat }} {{ $announcement->classroom?->nama_kelas }}
                                    @else
                                        Pesan pribadi
                                    @endif
                                </p>
                                <h4 class="mt-1 break-words text-lg font-semibold leading-6 text-slate-900 dark:text-white">{{ $announcement->title }}</h4>
                            </div>
                            <time datetime="{{ $announcement->created_at->toIso8601String() }}" class="shrink-0 text-sm text-slate-600 dark:text-slate-300">{{ $announcement->created_at->format('d M Y, H:i') }}</time>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-x-6 gap-y-2 border-t border-slate-200 pt-4 text-sm text-slate-600 dark:border-slate-700 dark:text-slate-300">
                            <p>Dari <span class="font-medium text-slate-800 dark:text-slate-100">{{ $announcement->author?->employee?->nama_lengkap ?? $announcement->author?->name ?? 'Pengguna' }}</span></p>
                            @if ($announcement->school)
                                <p>{{ $announcement->school->nama_sekolah }}</p>
                            @endif
                            @if ($announcement->audience === 'all')
                                <p>{{ $announcement->students_count }} penerima</p>
                            @elseif ($announcement->audience === 'individual')
                                <p>Untuk {{ $announcement->recipient?->nama_lengkap ?? 'siswa yang dipilih' }}</p>
                            @else
                                <p>{{ $announcement->students_count }} siswa</p>
                            @endif
                        </div>

                        @if ($announcement->audience !== 'class_personalized')
                            <p class="mt-4 break-words whitespace-pre-line text-sm leading-6 text-slate-800 dark:text-slate-200">{{ $announcement->body }}</p>
                        @else
                            <p class="mt-4 text-sm text-slate-600 dark:text-slate-300">Isi pesan berbeda untuk setiap siswa.</p>
                        @endif

                        <div class="mt-5 flex justify-end border-t border-slate-200 pt-4 dark:border-slate-700">
                            <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-md border border-red-700 px-4 py-2 text-sm font-semibold text-red-800 transition-colors hover:bg-red-50 active:bg-red-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700 dark:border-red-400 dark:text-red-300 dark:hover:bg-red-950/40 dark:active:bg-red-950/70">
                                    Hapus pengumuman
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <p class="rounded-lg border border-dashed border-slate-300 bg-white px-5 py-8 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">Belum ada pengumuman yang dapat dikelola.</p>
                @endforelse
            </div>

            @if ($announcements->hasPages())
                <div class="mt-5">{{ $announcements->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
