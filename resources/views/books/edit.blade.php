<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Edit Buku</h2>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <section class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-700 sm:px-6">
                    <h3 class="font-semibold text-slate-950 dark:text-white">Informasi Buku</h3>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Perbarui data yang tersimpan untuk buku ini.</p>
                </div>
                <div class="p-5 sm:p-6">
                    <form action="{{ route('books.update', $book) }}" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="title" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Judul</label>
                            <input id="title" type="text" name="title" value="{{ old('title', $book->title) }}"
                                class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white" required>
                            @error('title')<p class="mt-1 text-sm text-rose-700 dark:text-rose-300">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="author" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Pengarang</label>
                                <input id="author" type="text" name="author" value="{{ old('author', $book->author) }}"
                                    class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                            </div>
                            <div>
                                <label for="type_select" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Jenis</label>
                                <select name="type" id="type_select"
                                    class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white" required>
                                    <option value="paket" {{ old('type', $book->type) == 'paket' ? 'selected' : '' }}>Paket</option>
                                    <option value="perpustakaan" {{ old('type', $book->type) == 'perpustakaan' ? 'selected' : '' }}>Perpustakaan</option>
                                </select>
                            </div>
                        </div>
                        <div id="tingkat_wrapper" class="sm:max-w-sm">
                            <label for="tingkat" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Tingkat</label>
                            <select id="tingkat" name="tingkat"
                                class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                                <option value="">Pilih tingkat</option>
                                @for($i=1;$i<=6;$i++) <option value="{{ $i }}" {{ old('tingkat', $book->tingkat) == $i ?
                                    'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                            </select>
                        </div>
                        <div class="sm:max-w-sm">
                            <label for="stock" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Stok</label>
                            <input id="stock" type="number" name="stock" value="{{ old('stock', $book->stock) }}"
                                class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white" min="0">
                        </div>
                        <div>
                            <label for="notes" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Catatan</label>
                            <textarea id="notes" name="notes" rows="3"
                                class="w-full rounded-md border-slate-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white">{{ old('notes', $book->notes) }}</textarea>
                        </div>
                        <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 dark:border-slate-700 sm:flex-row sm:justify-end">
                            <a href="{{ route('books.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700">Batal</a>
                            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">Simpan
                                Perubahan</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.getElementById('type_select');
            const tingkatWrapper = document.getElementById('tingkat_wrapper');

            function toggleTingkat() {
                tingkatWrapper.style.display = typeSelect.value === 'paket' ? '' : 'none';
            }

            typeSelect.addEventListener('change', toggleTingkat);
            toggleTingkat();
        });
    </script>
</x-app-layout>