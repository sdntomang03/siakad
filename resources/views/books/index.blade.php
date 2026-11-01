<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Daftar Buku</h2>
            <a href="{{ route('books.create') }}"
                class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                Tambah Buku
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div role="status"
                    class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            <section
                class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                <div class="overflow-x-auto">
                    <table class="min-w-[38rem] w-full divide-y divide-slate-200 text-left text-sm dark:divide-slate-700">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600 dark:bg-slate-900/60 dark:text-slate-300">
                            <tr>
                                <th scope="col" class="px-5 py-3">Judul</th>
                                <th scope="col" class="px-5 py-3">Jenis</th>
                                <th scope="col" class="px-5 py-3">Tingkat</th>
                                <th scope="col" class="px-5 py-3">Stok</th>
                                <th scope="col" class="px-5 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($books as $book)
                                <tr class="text-slate-700 transition-colors hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700/40">
                                    <td class="px-5 py-4 font-medium text-slate-950 dark:text-white">{{ $book->title }}</td>
                                    <td class="px-5 py-4">{{ ucfirst($book->type) }}</td>
                                    <td class="px-5 py-4">{{ $book->tingkat ?? '-' }}</td>
                                    <td class="px-5 py-4 tabular-nums">{{ $book->stock ?? '-' }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-4">
                                            <a href="{{ route('books.edit', $book) }}"
                                                class="font-semibold text-brand-700 hover:text-brand-900 focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:text-brand-300 dark:hover:text-brand-200">
                                                Edit
                                            </a>
                                            <form action="{{ route('books.destroy', $book) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="font-semibold text-rose-700 hover:text-rose-900 focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-700 dark:text-rose-300 dark:hover:text-rose-200">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-slate-600 dark:text-slate-300">
                                        Belum ada buku.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">
                    {{ $books->links() }}
                </div>
            </section>
        </div>
    </div>
</x-app-layout>