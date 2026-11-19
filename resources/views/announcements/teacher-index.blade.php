<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Pengumuman</h2>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto max-w-screen-xl">
            @if (session('success'))
                <p role="status" class="mb-5 rounded-md border border-brand-600 bg-brand-50 px-4 py-3 text-sm font-medium text-brand-900 dark:border-brand-600 dark:bg-brand-900/40 dark:text-brand-100">
                    {{ session('success') }}
                </p>
            @endif

            <div class="grid items-start gap-8 xl:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
                <section aria-labelledby="create-announcement-title" class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-7">
                    <h3 id="create-announcement-title" class="text-lg font-semibold text-slate-900 dark:text-white">Buat pengumuman</h3>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Kirim satu pesan ke semua siswa aktif di sekolah atau pilih satu siswa.</p>

                    @if ($errors->any())
                        <div role="alert" class="mt-5 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-950/40 dark:text-red-200">
                            <p class="font-semibold">Pengumuman belum dikirim.</p>
                            <ul class="mt-2 list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form x-data="{ submitting: false }" @submit="submitting = true" action="{{ route('announcements.store') }}" method="POST" class="mt-6 space-y-5">
                        @csrf
                        <div>
                            <label for="audience" class="mb-1.5 block text-sm font-semibold text-slate-800 dark:text-slate-100">Penerima</label>
                            <select id="audience" name="audience" required class="min-h-11 w-full rounded-md border-slate-300 bg-white text-sm text-slate-900 focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                                <option value="all" @selected(old('audience', 'all') === 'all')>Semua siswa aktif di sekolah</option>
                                <option value="individual" @selected(old('audience') === 'individual')>Satu siswa</option>
                            </select>
                            @error('audience')
                                <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="student_id" class="mb-1.5 block text-sm font-semibold text-slate-800 dark:text-slate-100">Siswa tujuan, untuk pengumuman individu</label>
                            <select id="student_id" name="student_id" class="min-h-11 w-full rounded-md border-slate-300 bg-white text-sm text-slate-900 focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                                <option value="">Pilih siswa</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}" @selected((string) old('student_id') === (string) $student->id)>
                                        {{ $student->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($students->isEmpty())
                                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Belum ada siswa aktif. Pengumuman individu belum dapat dikirim.</p>
                            @endif
                            @error('student_id')
                                <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="title" class="mb-1.5 block text-sm font-semibold text-slate-800 dark:text-slate-100">Judul</label>
                            <input id="title" name="title" type="text" maxlength="180" value="{{ old('title') }}" required class="min-h-11 w-full rounded-md border-slate-300 bg-white text-sm text-slate-900 placeholder:text-slate-500 focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400" placeholder="Contoh: Perubahan jadwal kegiatan">
                            @error('title')
                                <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="body" class="mb-1.5 block text-sm font-semibold text-slate-800 dark:text-slate-100">Isi pengumuman</label>
                            <textarea id="body" name="body" rows="6" maxlength="5000" required class="w-full rounded-md border-slate-300 bg-white text-sm text-slate-900 placeholder:text-slate-500 focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400" placeholder="Tulis informasi yang perlu diketahui siswa.">{{ old('body') }}</textarea>
                            @error('body')
                                <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-5 dark:border-slate-700">
                            <p x-cloak x-show="submitting" role="status" aria-live="polite" class="text-sm text-slate-600 dark:text-slate-300">Mengirim pengumuman...</p>
                            <button type="submit" :disabled="submitting" class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-brand-800 active:bg-brand-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 disabled:cursor-not-allowed disabled:opacity-60">
                                Kirim pengumuman
                            </button>
                        </div>
                    </form>
                </section>

                <section aria-labelledby="announcement-history-title" class="min-w-0">
                    <div class="flex flex-wrap items-end justify-between gap-2 border-b border-slate-300 pb-3 dark:border-slate-700">
                        <div>
                            <h3 id="announcement-history-title" class="text-lg font-semibold text-slate-900 dark:text-white">Pengumuman yang dikirim</h3>
                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Riwayat pesan yang Anda buat.</p>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse ($announcements as $announcement)
                            <article class="min-w-0 py-5 first:pt-4">
                                <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                                    <h4 class="break-words text-base font-semibold text-slate-900 dark:text-white">{{ $announcement->title }}</h4>
                                    <time datetime="{{ $announcement->created_at->toIso8601String() }}" class="shrink-0 text-sm text-slate-600 dark:text-slate-300">{{ $announcement->created_at->format('d M Y, H:i') }}</time>
                                </div>
                                <p class="mt-2 text-sm font-medium text-brand-800 dark:text-brand-200">
                                    @if ($announcement->audience === 'all')
                                        Semua siswa aktif ({{ $announcement->students_count }} penerima)
                                    @else
                                        Untuk {{ $announcement->recipient?->nama_lengkap ?? 'siswa yang dipilih' }}
                                    @endif
                                </p>
                                <p class="mt-2 break-words whitespace-pre-line text-sm leading-6 text-slate-700 dark:text-slate-200">{{ $announcement->body }}</p>
                            </article>
                        @empty
                            <p class="py-6 text-sm text-slate-600 dark:text-slate-300">Belum ada pengumuman. Gunakan formulir untuk mengirim pesan pertama.</p>
                        @endforelse
                    </div>

                    @if ($announcements->hasPages())
                        <div class="mt-4">{{ $announcements->links() }}</div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
