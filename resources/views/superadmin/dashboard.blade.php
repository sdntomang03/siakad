<x-app-layout>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto max-w-screen-2xl space-y-6">

            <div
                class="flex items-center justify-between gap-4 rounded-lg bg-brand-900 p-6 text-white md:p-8">
                <div>
                    <h3 class="mb-1 text-2xl font-semibold">Selamat datang, {{ auth()->user()->name }}</h3>
                    <p class="text-sm leading-6 text-brand-100">Berikut adalah ringkasan statistik dari seluruh sekolah yang
                        terdaftar di sistem.</p>
                </div>
                <div class="hidden md:block">
                    <svg aria-hidden="true" class="h-16 w-16 text-brand-100" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
            </div>

            <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

                <div
                    class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Total Sekolah</p>
                            <h4 class="text-3xl font-bold text-slate-900 dark:text-white">{{
                                number_format($stats['total_sekolah']) }}</h4>
                        </div>
                        <div class="rounded-md bg-brand-50 p-3 dark:bg-brand-900">
                            <svg aria-hidden="true" class="h-8 w-8 text-brand-800 dark:text-brand-200" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14v6m-3-2.818l-6-3.182m6 3.182l6-3.182"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Pengguna Akun</p>
                            <h4 class="text-3xl font-bold text-slate-900 dark:text-white">{{
                                number_format($stats['total_pengguna']) }}</h4>
                        </div>
                        <div class="rounded-md bg-brand-50 p-3 dark:bg-brand-900">
                            <svg aria-hidden="true" class="h-8 w-8 text-brand-800 dark:text-brand-200" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Total Guru</p>
                            <h4 class="text-3xl font-bold text-slate-900 dark:text-white">{{
                                number_format($stats['total_guru_nasional']) }}</h4>
                        </div>
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 rounded-md">
                            <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Total Siswa</p>
                            <h4 class="text-3xl font-bold text-slate-900 dark:text-white">{{
                                number_format($stats['total_siswa_nasional']) }}</h4>
                        </div>
                        <div class="rounded-md bg-brand-50 p-3 dark:bg-brand-900">
                            <svg aria-hidden="true" class="h-8 w-8 text-brand-800 dark:text-brand-200" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <section class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                <div class="grid grid-cols-1 items-end gap-4 md:grid-cols-[minmax(0,1fr)_auto]">
                    <div>
                        <h3 class="font-semibold text-slate-900 dark:text-white">Rekap ekstrakurikuler siswa</h3>
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                            Pilih sekolah untuk mengunduh rekap semua siswanya dalam format Excel.
                        </p>
                    </div>
                    <form action="{{ route('extracurriculars.export') }}" method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <div class="min-w-0 flex-1 sm:min-w-64">
                            <label for="extracurricular_school_id" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">
                                Sekolah
                            </label>
                            <select id="extracurricular_school_id" name="school_id" required
                                class="min-h-11 w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                                <option value="">Pilih sekolah</option>
                                @foreach($allSchools as $school)
                                <option value="{{ $school->id }}">{{ $school->nama_sekolah }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                            class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-800 active:bg-brand-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-700">
                            Unduh rekap Excel
                        </button>
                    </form>
                </div>
            </section>

            <div
                class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                <div
                    class="flex flex-col justify-between gap-4 border-b border-slate-200 px-5 py-5 dark:border-slate-700 sm:flex-row sm:items-center sm:px-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Daftar Sekolah Terbaru</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Sekolah yang baru saja bergabung ke
                            dalam sistem.</p>
                    </div>
                    <a href="{{ route('superadmin.schools.create') }}"
                        class="inline-flex min-h-11 items-center justify-center rounded-md border border-transparent bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                        <svg aria-hidden="true" class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Sekolah
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full w-full text-left whitespace-nowrap">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    NPSN</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Nama Sekolah</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Tingkat</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Alamat</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($schools as $school)
                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300 font-medium">
                                    {{ $school->npsn }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            aria-hidden="true"
                                            class="mr-3 flex h-8 w-8 items-center justify-center rounded-md bg-brand-50 text-xs font-semibold text-brand-800 dark:bg-brand-900 dark:text-brand-200">
                                            {{ substr($school->nama_sekolah, 0, 1) }}
                                        </div>
                                        <span class="text-sm font-semibold text-slate-900 dark:text-white">{{
                                            $school->nama_sekolah }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center rounded-md bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-900 dark:bg-brand-900 dark:text-brand-100">
                                        {{ $school->tingkat }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 truncate max-w-xs">
                                    {{ $school->alamat }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <a href="{{ route('superadmin.schools.index') }}"
                                        class="inline-flex min-h-11 items-center rounded-sm font-medium text-brand-800 underline underline-offset-4 hover:text-brand-900 dark:text-brand-200 dark:hover:text-white">Kelola</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                    <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                    <p class="text-base font-semibold">Belum ada sekolah</p>
                                    <p class="text-sm mt-1">Sistem saat ini belum memiliki data sekolah satupun.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
