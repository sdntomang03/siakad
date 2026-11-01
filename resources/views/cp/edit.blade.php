<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Edit Capaian Pembelajaran</h2>
    </x-slot>

    <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mb-4">
            <a href="{{ route('cp.index') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-brand-700 hover:text-brand-900 focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:text-brand-300 dark:hover:text-brand-200">
                Batal & Kembali</a>
        </div>

        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-700 sm:px-6">
                <h3 class="font-semibold text-slate-950 dark:text-white">Informasi Capaian Pembelajaran</h3>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Perbarui kode dan deskripsi capaian pembelajaran.</p>
            </div>
            <form action="{{ route('cp.update', $cp->id) }}" method="POST" class="space-y-6 p-5 sm:p-6">
                @csrf
                @method('PUT')

                @if($errors->any())
                <div role="alert" class="rounded-md border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 dark:border-rose-900 dark:bg-rose-950/40 dark:text-rose-200">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="kode_cp" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Kode CP (Unik)</label>
                        <input id="kode_cp" type="text" name="kode_cp" value="{{ old('kode_cp', $cp->kode_cp) }}"
                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white" required>
                    </div>
                    <div>
                        <label for="mata_pelajaran" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Mata Pelajaran</label>
                        <input id="mata_pelajaran" type="text" name="mata_pelajaran"
                            value="{{ old('mata_pelajaran', $cp->mata_pelajaran) }}"
                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white" required>
                    </div>
                    <div>
                        <label for="fase" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Fase</label>
                        <input id="fase" type="text" name="fase" value="{{ old('fase', $cp->fase) }}"
                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white" required>
                    </div>
                    <div>
                        <label for="elemen" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Elemen</label>
                        <input id="elemen" type="text" name="elemen" value="{{ old('elemen', $cp->elemen) }}"
                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white" required>
                    </div>
                </div>

                <div>
                    <label for="deskripsi_cp" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Deskripsi CP</label>
                    <textarea id="deskripsi_cp" name="deskripsi_cp" rows="5"
                        class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                        required>{{ old('deskripsi_cp', $cp->deskripsi_cp) }}</textarea>
                </div>

                <div class="flex justify-end border-t border-slate-200 pt-5 dark:border-slate-700">
                    <button type="submit"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-md bg-brand-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 sm:w-auto">
                        Simpan Perubahan
                    </button>
                </section>
            </form>
        </div>
    </div>
</x-app-layout>