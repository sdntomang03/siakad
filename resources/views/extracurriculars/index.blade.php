<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">
            Manajemen Ekstrakurikuler
        </h2>
    </x-slot>

    <div class="mx-auto max-w-5xl space-y-5 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        @if(session('success'))
        <div role="status" class="rounded-md border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div role="alert" class="rounded-md border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-900 dark:border-rose-800 dark:bg-rose-900/30 dark:text-rose-200">
            <ul class="list-inside list-disc">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @role('superadmin')
        <section class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
            <form action="{{ route('extracurriculars.index') }}" method="GET">
                <label for="school_id" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                    Pilih Sekolah
                </label>
                <select id="school_id" name="school_id" onchange="this.form.submit()"
                    class="min-h-11 w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white md:max-w-xl">
                    <option value="">Pilih sekolah</option>
                    @foreach($schools as $school)
                    <option value="{{ $school->id }}" @selected($selectedSchoolId == $school->id)>
                        {{ $school->npsn ? $school->npsn . ' - ' : '' }}{{ $school->nama_sekolah }}
                    </option>
                    @endforeach
                </select>
            </form>
        </section>
        @endrole

        @if(!$selectedSchoolId)
        <section class="rounded-lg border border-slate-200 bg-white px-5 py-12 text-center dark:border-slate-700 dark:bg-slate-800">
            @if(auth()->user()->hasRole('superadmin'))
            <h3 class="font-semibold text-slate-900 dark:text-white">Pilih sekolah</h3>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Pilih sekolah untuk mengelola daftar ekskulnya.</p>
            @else
            <h3 class="font-semibold text-slate-900 dark:text-white">Sekolah belum ditentukan</h3>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Akun admin ini belum terhubung ke sekolah. Hubungi administrator untuk mengatur sekolah aktif.</p>
            @endif
        </section>
        @else
        <div class="grid grid-cols-1 items-start gap-5 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.3fr)]">
            <section class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Tambah ekskul</h3>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Ekskul ini akan tersedia bagi siswa di sekolah yang dipilih.</p>

                <form action="{{ route('extracurriculars.store') }}" method="POST" class="mt-5 space-y-4">
                    @csrf
                    @role('superadmin')
                    <input type="hidden" name="school_id" value="{{ $selectedSchoolId }}">
                    @endrole

                    <div>
                        <label for="nama_ekskul" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">
                            Nama ekskul
                        </label>
                        <input id="nama_ekskul" name="nama_ekskul" type="text" value="{{ old('nama_ekskul') }}" required maxlength="255"
                            class="min-h-11 w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                    </div>

                    <button type="submit"
                        class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-800 active:bg-brand-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-700">
                        Simpan ekskul
                    </button>
                </form>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Daftar ekskul</h3>
                @if($extracurriculars->isEmpty())
                <p class="mt-4 rounded-md bg-slate-50 p-4 text-sm text-slate-700 dark:bg-slate-900/50 dark:text-slate-300">
                    Belum ada ekskul untuk sekolah ini. Tambahkan ekskul agar dapat dipilih siswa.
                </p>
                @else
                <ul class="mt-4 divide-y divide-slate-200 dark:divide-slate-700">
                    @foreach($extracurriculars as $extracurricular)
                    <li class="flex flex-wrap items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">
                        <span class="min-w-0 flex-1 break-words text-sm font-medium text-slate-800 dark:text-slate-100">
                            {{ $extracurricular->nama_ekskul }}
                        </span>
                        <form action="{{ route('extracurriculars.destroy', $extracurricular) }}" method="POST"
                            onsubmit="return confirm('Hapus ekskul ini dari daftar sekolah?')">
                            @csrf
                            @method('DELETE')
                            @role('superadmin')
                            <input type="hidden" name="school_id" value="{{ $selectedSchoolId }}">
                            @endrole
                            <button type="submit"
                                class="inline-flex min-h-11 items-center justify-center rounded-md border border-rose-300 px-3 py-2 text-sm font-medium text-rose-800 transition hover:bg-rose-50 active:bg-rose-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-700 dark:border-rose-800 dark:text-rose-200 dark:hover:bg-rose-900/30 dark:active:bg-rose-900/50">
                                Hapus
                            </button>
                        </form>
                    </li>
                    @endforeach
                </ul>
                @endif
            </section>
        </div>
        @endif
    </div>
</x-app-layout>
