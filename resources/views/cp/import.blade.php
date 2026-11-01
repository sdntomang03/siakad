<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">
            Import Data Capaian Pembelajaran (JSON)
        </h2>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-4 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        @if(session('success'))
        <div role="status" class="rounded-md border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-200">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div role="alert" class="rounded-md border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-800 dark:border-rose-900 dark:bg-rose-950/40 dark:text-rose-200">
            {{ session('error') }}
        </div>
        @endif

        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-700 sm:px-6">
                <h3 class="font-semibold text-slate-950 dark:text-white">Unggah file JSON</h3>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Sistem mendeteksi mata pelajaran dan fase
                    berdasarkan penamaan di dalam file JSON.</p>
            </div>

            <form action="{{ route('cp.import-process') }}" method="POST" enctype="multipart/form-data"
                class="space-y-6 p-5 sm:p-6">
                @csrf

                <div
                    class="rounded-md border border-slate-300 bg-slate-50/60 p-5 text-left dark:border-slate-600 dark:bg-slate-900/40 sm:p-6">
                    <svg class="mb-3 h-8 w-8 text-brand-700 dark:text-brand-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                        </path>
                    </svg>
                    <label for="file_json" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Pilih file JSON</label>
                    <input id="file_json" type="file" name="file_json" accept=".json" required class="block w-full text-sm text-slate-600 dark:text-slate-300
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-brand-50 file:text-brand-700
                        dark:file:bg-brand-900/50 dark:file:text-brand-200 hover:file:bg-brand-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                </div>

                <div class="flex justify-end border-t border-slate-200 pt-5 dark:border-slate-700">
                    <button type="submit"
                        class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-md bg-brand-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 sm:w-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Mulai Import Data
                    </button>
                </section>
            </form>
        </div>
    </div>
</x-app-layout>