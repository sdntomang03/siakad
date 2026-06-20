<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">Daftar Buku</h2>
            <a href="{{ route('books.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Tambah Buku</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-slate-800">
                    @if(session('success'))
                    <div class="mb-4 text-sm text-green-600">{{ session('success') }}</div>
                    @endif

                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-slate-500">
                                <th class="py-2">Judul</th>
                                <th class="py-2">Jenis</th>
                                <th class="py-2">Tingkat</th>
                                <th class="py-2">Stok</th>
                                <th class="py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($books as $book)
                            <tr class="border-t">
                                <td class="py-2">{{ $book->title }}</td>
                                <td class="py-2">{{ ucfirst($book->type) }}</td>
                                <td class="py-2">{{ $book->tingkat ?? '-' }}</td>
                                <td class="py-2">{{ $book->stock ?? '-' }}</td>
                                <td class="py-2">
                                    <a href="{{ route('books.edit', $book) }}" class="text-indigo-600">Edit</a>
                                    <form action="{{ route('books.destroy', $book) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 ml-3">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-4 text-slate-500">Belum ada buku.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $books->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>