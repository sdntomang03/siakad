<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">
            Atur Akses: <span class="text-brand-800 dark:text-brand-200">{{ $role->name }}</span>
        </h2>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
      <div class="mx-auto max-w-screen-2xl">
        <form action="{{ route('superadmin.roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div
                class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
                <p class="text-sm text-slate-500 mb-6">Centang kotak di bawah ini untuk memberikan izin kepada pengguna
                    dengan jabatan <strong>{{ strtoupper($role->name) }}</strong>.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @forelse($permissions as $permission)
                    <label
                        class="flex min-h-11 cursor-pointer items-start gap-3 rounded-md border border-slate-200 p-4 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-700/50">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" {{
                            in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                        class="mt-0.5 h-5 w-5 cursor-pointer rounded border-slate-300 text-brand-700 focus:ring-brand-700">
                        <div>
                            <span class="block font-bold text-slate-700 dark:text-slate-300">{{ $permission->name
                                }}</span>
                        </div>
                    </label>
                    @empty
                    <div class="col-span-3 text-center py-6 text-slate-500">
                        Belum ada permission yang didaftarkan di database.
                    </div>
                    @endforelse
                </div>

                <div class="mt-8 flex gap-4 justify-end">
                    <a href="{{ route('superadmin.roles.index') }}"
                        class="inline-flex min-h-11 items-center justify-center rounded-md bg-slate-100 px-6 py-2.5 font-semibold text-slate-800 transition-colors hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Batal</a>
                    <button type="submit"
                        class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-6 py-2.5 font-semibold text-white transition-colors hover:bg-brand-800">Simpan
                        Perubahan</button>
                  </div>
                </div>
            </div>
        </form>
    </div>
    </div>
</x-app-layout>