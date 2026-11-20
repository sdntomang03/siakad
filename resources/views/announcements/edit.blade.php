<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Edit pengumuman</h2>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <section class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-7">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Ubah pengumuman</h3>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                    @if ($announcement->audience === 'class_personalized')
                        Perbarui pesan untuk masing-masing siswa. Penerima pengumuman tidak dapat diubah.
                    @else
                        Perbarui judul dan isi pengumuman.
                    @endif
                </p>

                @if ($errors->any())
                    <div role="alert" class="mt-5 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-950/40 dark:text-red-200">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('announcements.update', $announcement) }}" method="POST" class="mt-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <p class="mb-1.5 text-sm font-semibold text-slate-800 dark:text-slate-100">Penerima</p>
                        <p class="text-sm text-slate-600 dark:text-slate-300">
                            @if ($announcement->audience === 'all')
                                Semua siswa aktif
                            @elseif ($announcement->audience === 'individual')
                                {{ $announcement->recipient?->nama_lengkap ?? 'Siswa yang dipilih' }}
                            @else
                                Kelas {{ $announcement->classroom?->tingkat }} {{ $announcement->classroom?->nama_kelas }}
                            @endif
                        </p>
                    </div>

                    <div>
                        <label for="title" class="mb-1.5 block text-sm font-semibold text-slate-800 dark:text-slate-100">Judul</label>
                        <input id="title" name="title" type="text" maxlength="180" value="{{ old('title', $announcement->title) }}" required class="min-h-11 w-full rounded-md border-slate-300 bg-white text-sm text-slate-900 focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                    </div>

                    @if ($announcement->audience === 'class_personalized')
                        <div class="space-y-4">
                            @foreach ($students as $student)
                                <div class="rounded-md border border-slate-200 p-4 dark:border-slate-700">
                                    <label for="student-body-{{ $student->id }}" class="mb-1.5 block text-sm font-semibold text-slate-800 dark:text-slate-100">
                                        {{ $student->nama_lengkap }}
                                        @if ($student->nisn)
                                            <span class="font-normal text-slate-600 dark:text-slate-300">— NISN {{ $student->nisn }}</span>
                                        @endif
                                    </label>
                                    <textarea id="student-body-{{ $student->id }}" name="student_bodies[{{ $student->id }}]" rows="5" maxlength="5000" required class="w-full rounded-md border-slate-300 bg-white text-sm text-slate-900 focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white">{{ old('student_bodies.'.$student->id, $student->pivot->body) }}</textarea>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div>
                            <label for="body" class="mb-1.5 block text-sm font-semibold text-slate-800 dark:text-slate-100">Isi pengumuman</label>
                            <textarea id="body" name="body" rows="8" maxlength="5000" required class="w-full rounded-md border-slate-300 bg-white text-sm text-slate-900 focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white">{{ old('body', $announcement->body) }}</textarea>
                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Isi pesan mendukung HTML yang diizinkan.</p>
                        </div>
                    @endif

                    <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-5 dark:border-slate-700">
                        <a href="{{ route('announcements.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-700">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                            Simpan perubahan
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
