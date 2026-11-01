<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Manajemen Hak Akses</h2>
    </x-slot>

    <div class="space-y-6 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
      <div class="mx-auto max-w-screen-2xl space-y-6">

        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
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
                            class="min-h-11 w-full rounded-md border-slate-300 text-sm focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-700">
                        @error('name')
                        <span class="text-xs text-rose-500 font-bold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit"
                        class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-brand-800">
                        Tambah
                    </button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
            <table class="w-full min-w-[36rem] text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 text-xs font-medium text-slate-700 dark:bg-slate-900/50 dark:text-slate-300">
                    <tr>
                        <th class="px-6 py-4">Nama Jabatan (Role)</th>
                        <th class="px-6 py-4">Total Akses</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($roles as $role)
                    <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <td class="px-6 py-4 font-semibold uppercase text-brand-800 dark:text-brand-200">{{ $role->name }}</td>
                        <td class="px-6 py-4 font-medium">{{ $role->permissions->count() }} Izin Khusus</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('superadmin.roles.edit', $role->id) }}"
                                class="inline-flex min-h-11 items-center rounded-md bg-brand-50 px-4 py-2 font-medium text-brand-900 transition-colors hover:bg-brand-700 hover:text-white dark:bg-brand-900 dark:text-brand-100 dark:hover:bg-brand-700">
                                Atur Izin
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
          </div>
        </div>

    </div>
</x-app-layout>