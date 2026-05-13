<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">Manajemen Hak Akses</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white">Tambah Izin (Permission) Baru</h3>
                    <p class="text-sm text-slate-500">Buat kunci akses baru sebelum diberikan kepada jabatan tertentu.
                    </p>
                </div>

                <form action="{{ route('superadmin.permissions.store') }}" method="POST"
                    class="flex items-start gap-3 w-full md:w-auto">
                    @csrf
                    <div class="flex-1 md:w-64">
                        <input type="text" name="name" placeholder="Cth: hapus siswa" required
                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm focus:ring-indigo-500">
                        @error('name')
                        <span class="text-xs text-rose-500 font-bold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit"
                        class="px-5 py-2.5 bg-slate-800 dark:bg-slate-100 text-white dark:text-slate-800 text-sm font-bold rounded-lg hover:bg-slate-700 dark:hover:bg-white shadow-md transition">
                        + Tambah
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <table class="w-full text-sm text-left text-slate-500">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-6 py-4">Nama Jabatan (Role)</th>
                        <th class="px-6 py-4">Total Akses</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($roles as $role)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <td class="px-6 py-4 font-black uppercase text-indigo-600">{{ $role->name }}</td>
                        <td class="px-6 py-4 font-bold">{{ $role->permissions->count() }} Izin Khusus</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('superadmin.roles.edit', $role->id) }}"
                                class="px-4 py-2 bg-indigo-50 text-indigo-600 font-bold rounded-lg hover:bg-indigo-600 hover:text-white transition">
                                Atur Izin &rarr;
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>