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
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Kirim pesan sekolah, pilih satu siswa, atau tulis isi berbeda untuk setiap siswa di kelas yang Anda ampu.</p>

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

                    <form x-data="announcementComposer({{ Js::from($classrooms) }}, {{ Js::from(old('student_bodies', [])) }}, @js(old('audience', 'all')), @js((string) old('classroom_id', '')), @js(old('class_input_method', 'excel')))" @submit="submitting = true" action="{{ route('announcements.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-5">
                        @csrf
                        <div>
                            <label for="audience" class="mb-1.5 block text-sm font-semibold text-slate-800 dark:text-slate-100">Penerima</label>
                            <select id="audience" name="audience" x-model="audience" required class="min-h-11 w-full rounded-md border-slate-300 bg-white text-sm text-slate-900 focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                                <option value="all" @selected(old('audience', 'all') === 'all')>Semua siswa aktif di sekolah</option>
                                <option value="individual" @selected(old('audience') === 'individual')>Satu siswa</option>
                                <option value="class_personalized" @selected(old('audience') === 'class_personalized')>Isi berbeda untuk setiap siswa di kelas saya</option>
                            </select>
                            @error('audience')
                                <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-cloak x-show="audience === 'individual'">
                            <label for="student_id" class="mb-1.5 block text-sm font-semibold text-slate-800 dark:text-slate-100">Siswa tujuan, untuk pengumuman individu</label>
                            <select id="student_id" name="student_id" :required="audience === 'individual'" :disabled="audience !== 'individual'" class="min-h-11 w-full rounded-md border-slate-300 bg-white text-sm text-slate-900 focus:border-brand-700 focus:ring-brand-700 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
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

                        <div x-cloak x-show="audience === 'class_personalized'" class="space-y-4">
                            <div>
                                <label for="classroom_id" class="mb-1.5 block text-sm font-semibold text-slate-800 dark:text-slate-100">Kelas yang diampu</label>
                                <select id="classroom_id" name="classroom_id" x-model="classroomId" @change="studentBodies = {}; if ($refs.excelFile) $refs.excelFile.value = ''" :required="audience === 'class_personalized'" :disabled="audience !== 'class_personalized'" class="min-h-11 w-full rounded-md border-slate-300 bg-white text-sm text-slate-900 focus:border-brand-700 focus:ring-brand-700 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                                    <option value="">Pilih kelas</option>
                                    @foreach ($classrooms as $classroom)
                                        <option value="{{ $classroom->id }}">{{ $classroom->tingkat }} {{ $classroom->nama_kelas }}</option>
                                    @endforeach
                                </select>
                                @if ($classrooms->isEmpty())
                                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Belum ada kelas yang Anda ampu.</p>
                                @endif
                                @error('classroom_id')
                                    <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="class_input_method" class="mb-1.5 block text-sm font-semibold text-slate-800 dark:text-slate-100">Cara mengisi pesan</label>
                                <select id="class_input_method" name="class_input_method" x-model="classInputMethod" :required="audience === 'class_personalized'" :disabled="audience !== 'class_personalized'" class="min-h-11 w-full rounded-md border-slate-300 bg-white text-sm text-slate-900 focus:border-brand-700 focus:ring-brand-700 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                                    <option value="excel">Unggah Excel</option>
                                    <option value="manual">Isi manual di halaman ini</option>
                                </select>
                            </div>

                            <div x-cloak x-show="classInputMethod === 'excel'" class="space-y-3 rounded-md border border-slate-200 p-4 dark:border-slate-700">
                                <div>
                                    <a x-cloak x-show="selectedClassroom && selectedStudents.length > 0 && !selectedStudents.some(student => !student.nisn)" :href="selectedClassroom ? selectedClassroom.template_url : '#'" class="inline-flex min-h-10 items-center rounded-md border border-brand-700 px-4 py-2 text-sm font-semibold text-brand-800 transition-colors hover:bg-brand-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:text-brand-200 dark:hover:bg-brand-900/40">
                                        Unduh format Excel kelas ini
                                    </a>
                                    <p x-cloak x-show="selectedStudents.some(student => !student.nisn)" class="text-sm text-red-700 dark:text-red-300">Lengkapi NISN seluruh siswa kelas sebelum mengunduh format Excel.</p>
                                    <p x-cloak x-show="classroomId && selectedStudents.length === 0" class="text-sm text-slate-600 dark:text-slate-300">Kelas ini belum memiliki siswa aktif.</p>
                                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Isi kolom Pesan untuk setiap siswa. Jangan mengubah kolom NISN, lalu unggah kembali file Excel.</p>
                                </div>
                                <div>
                                    <label for="excel_file" class="mb-1.5 block text-sm font-semibold text-slate-800 dark:text-slate-100">File Excel berisi pesan siswa</label>
                                    <input x-ref="excelFile" id="excel_file" name="excel_file" type="file" accept=".xlsx,.xls,.csv" :required="audience === 'class_personalized' && classInputMethod === 'excel'" :disabled="audience !== 'class_personalized' || classInputMethod !== 'excel'" class="block min-h-11 w-full rounded-md border border-slate-300 bg-white text-sm text-slate-900 file:mr-3 file:min-h-10 file:border-0 file:bg-slate-100 file:px-3 file:font-semibold file:text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:file:bg-slate-700 dark:file:text-slate-100">
                                    @error('excel_file')
                                        <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div x-cloak x-show="classInputMethod === 'manual'" class="space-y-4">
                                <template x-for="student in selectedStudents" :key="student.id">
                                    <div class="rounded-md border border-slate-200 p-4 dark:border-slate-700">
                                        <label :for="`student-body-${student.id}`" class="mb-1.5 block text-sm font-semibold text-slate-800 dark:text-slate-100" x-text="student.nama_lengkap"></label>
                                        <textarea :id="`student-body-${student.id}`" :name="`student_bodies[${student.id}]`" x-model="studentBodies[student.id]" :required="audience === 'class_personalized' && classInputMethod === 'manual'" :disabled="audience !== 'class_personalized' || classInputMethod !== 'manual'" rows="3" maxlength="5000" class="w-full rounded-md border-slate-300 bg-white text-sm text-slate-900 placeholder:text-slate-500 focus:border-brand-700 focus:ring-brand-700 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400" placeholder="Tulis pesan khusus untuk siswa ini."></textarea>
                                    </div>
                                </template>
                                <p x-cloak x-show="audience === 'class_personalized' && classroomId && selectedStudents.length === 0" class="text-sm text-slate-600 dark:text-slate-300">Belum ada siswa aktif di kelas ini.</p>
                                @error('student_bodies')
                                    <p class="text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="title" class="mb-1.5 block text-sm font-semibold text-slate-800 dark:text-slate-100">Judul</label>
                            <input id="title" name="title" type="text" maxlength="180" value="{{ old('title') }}" required class="min-h-11 w-full rounded-md border-slate-300 bg-white text-sm text-slate-900 placeholder:text-slate-500 focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400" placeholder="Contoh: Perubahan jadwal kegiatan">
                            @error('title')
                                <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-cloak x-show="audience !== 'class_personalized'">
                            <label for="body" class="mb-1.5 block text-sm font-semibold text-slate-800 dark:text-slate-100">Isi pengumuman</label>
                            <textarea id="body" name="body" rows="6" maxlength="5000" :required="audience !== 'class_personalized'" :disabled="audience === 'class_personalized'" class="w-full rounded-md border-slate-300 bg-white text-sm text-slate-900 placeholder:text-slate-500 focus:border-brand-700 focus:ring-brand-700 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400" placeholder="Tulis informasi yang perlu diketahui siswa.">{{ old('body') }}</textarea>
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
                                    @elseif ($announcement->audience === 'class_personalized')
                                        Pesan pribadi untuk kelas {{ $announcement->classroom?->tingkat }} {{ $announcement->classroom?->nama_kelas }} ({{ $announcement->students_count }} siswa)
                                    @else
                                        Untuk {{ $announcement->recipient?->nama_lengkap ?? 'siswa yang dipilih' }}
                                    @endif
                                </p>
                                @if ($announcement->audience !== 'class_personalized')
                                    <p class="mt-2 break-words whitespace-pre-line text-sm leading-6 text-slate-700 dark:text-slate-200">{{ $announcement->body }}</p>
                                @endif
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
    <script>
        function announcementComposer(classrooms, studentBodies, audience, classroomId, classInputMethod) {
            return {
                classrooms,
                studentBodies,
                audience,
                classroomId,
                classInputMethod,
                submitting: false,
                get selectedClassroom() {
                    return this.classrooms.find(item => String(item.id) === String(this.classroomId));
                },
                get selectedStudents() {
                    const classroom = this.selectedClassroom;

                    return classroom ? classroom.students : [];
                },
            };
        }
    </script>
</x-app-layout>
