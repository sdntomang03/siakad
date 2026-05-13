<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
            Atur Akses: <span class="text-indigo-600 uppercase">{{ $role->name }}</span>
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form action="{{ route('superadmin.roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div
                class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <p class="text-sm text-slate-500 mb-6">Centang kotak di bawah ini untuk memberikan izin kepada pengguna
                    dengan jabatan <strong>{{ strtoupper($role->name) }}</strong>.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @forelse($permissions as $permission)
                    <label
                        class="flex items-start gap-3 p-4 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" {{
                            in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                        class="mt-0.5 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-5 h-5
                        cursor-pointer">
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
                        class="px-6 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition">Batal</a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700 shadow-lg transition">Simpan
                        Perubahan &rarr;</button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>