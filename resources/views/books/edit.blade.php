<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Edit Buku</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-slate-800">
                    <form action="{{ route('books.update', $book) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm text-slate-600">Judul</label>
                            <input type="text" name="title" value="{{ old('title', $book->title) }}"
                                class="w-full rounded px-3 py-2 border" required>
                            @error('title')<div class="text-xs text-red-500">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-slate-600">Pengarang</label>
                            <input type="text" name="author" value="{{ old('author', $book->author) }}"
                                class="w-full rounded px-3 py-2 border">
                        </div>
                        <div>
                            <label class="block text-sm text-slate-600">Jenis</label>
                            <select name="type" id="type_select" class="w-full rounded px-3 py-2 border" required>
                                <option value="paket" {{ old('type', $book->type) == 'paket' ? 'selected' : '' }}>Paket
                                </option>
                                <option value="perpustakaan" {{ old('type', $book->type) == 'perpustakaan' ? 'selected'
                                    : '' }}>Perpustakaan</option>
                            </select>
                        </div>
                        <div id="tingkat_wrapper">
                            <label class="block text-sm text-slate-600">Tingkat</label>
                            <select name="tingkat" class="w-full rounded px-3 py-2 border">
                                <option value="">Pilih tingkat</option>
                                @for($i=1;$i<=6;$i++) <option value="{{ $i }}" {{ old('tingkat', $book->tingkat) == $i ?
                                    'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-slate-600">Stok</label>
                            <input type="number" name="stock" value="{{ old('stock', $book->stock) }}"
                                class="w-full rounded px-3 py-2 border" min="0">
                        </div>
                        <div>
                            <label class="block text-sm text-slate-600">Catatan</label>
                            <textarea name="notes"
                                class="w-full rounded px-3 py-2 border">{{ old('notes', $book->notes) }}</textarea>
                        </div>
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('books.index') }}" class="px-4 py-2 border rounded">Batal</a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan
                                Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
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