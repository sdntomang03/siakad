<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100 sm:text-2xl">Manajemen Pengguna</h2>
            <button type="button" @click="$dispatch('open-user-modal')"
                class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                Tambah User
            </button>
        </div>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8" x-data="{ open: false }">
        <div class="mx-auto max-w-screen-2xl">
            <div
                class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                <div
                    class="mb-5 rounded-lg border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800">
                    <form method="GET" action="{{ route('superadmin.users.index') }}"
                        class="flex flex-col items-stretch justify-between gap-4 md:flex-row md:items-center">

                        <div class="flex w-full flex-col gap-3 md:w-auto md:flex-row md:flex-wrap">

                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Cari nama / email..."
                                    class="min-h-11 w-full rounded-md border-slate-300 pl-10 pr-4 py-2 text-sm focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-700 dark:text-white md:w-64">
                                <svg aria-hidden="true" class="absolute left-3 top-2.5 h-5 w-5 text-slate-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>

                            <select name="school_id" @change="$el.closest('form').submit()"
                                class="min-h-11 rounded-md border-slate-300 text-sm focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                                <option value="">Semua Sekolah</option>
                                @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ request('school_id')==$school->id ? 'selected' : ''
                                    }}>
                                    {{ $school->nama_sekolah }}
                                </option>
                                @endforeach
                            </select>

                            <select name="role" @change="$el.closest('form').submit()"
                                class="min-h-11 rounded-md border-slate-300 text-sm focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                                <option value="">Semua Role</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ request('role')==$role->name ? 'selected' : '' }}>
                                    {{ strtoupper($role->name) }}
                                </option>
                                @endforeach
                            </select>

                            <div class="flex gap-2">
                                <button type="submit"
                                    class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-brand-800">Cari</button>
                                @if(request()->hasAny(['search', 'school_id', 'role']))
                                <a href="{{ route('superadmin.users.index') }}"
                                    class="inline-flex min-h-11 items-center justify-center rounded-md border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-800 transition-colors hover:bg-red-100">Reset</a>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="text-sm text-slate-500 font-medium">Tampilkan:</label>
                            <select name="per_page" @change="$el.closest('form').submit()"
                                class="min-h-11 rounded-md border-slate-300 py-2 text-sm focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                                <option value="10" {{ request('per_page')==10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page')==25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page')==50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page')==100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="overflow-x-auto border-t border-slate-200 dark:border-slate-700">
                    <table class="w-full min-w-[42rem] border-collapse text-left">
                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">Nama / Email</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">Sekolah</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">Role</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($users as $user)
                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $user->name }}
                                    </div>
                                    <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    {{ $user->school->nama_sekolah ?? 'Pusat' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex rounded-md bg-brand-50 px-2 py-1 text-xs font-medium text-brand-900 dark:bg-brand-900 dark:text-brand-100">
                                        {{ $user->roles->first()->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <form id="form-reset-{{ $user->id }}"
                                        action="{{ route('superadmin.users.reset-password', $user) }}" method="POST"
                                        class="inline">
                                        @csrf @method('PATCH')
                                        <button type="button"
                                            onclick="confirmAction('form-reset-{{ $user->id }}', 'Reset Password?', 'Password pengguna ini akan diubah menjadi 12345678.')"
                                            class="inline-flex min-h-11 items-center text-sm font-medium text-amber-800 underline underline-offset-4 hover:text-amber-900 dark:text-amber-300">
                                            Reset
                                        </button>
                                    </form>

                                    <button type="button" @click="$dispatch('open-user-modal', {
            user: {{ $user->toJson() }},
            role: '{{ $user->roles->first()->name ?? '' }}'
        })" class="inline-flex min-h-11 items-center text-sm font-medium text-brand-800 underline underline-offset-4 hover:text-brand-900 dark:text-brand-200">
                                        Edit
                                    </button>

                                    <form id="form-delete-{{ $user->id }}"
                                        action="{{ route('superadmin.users.destroy', $user) }}" method="POST"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                            onclick="confirmAction('form-delete-{{ $user->id }}', 'Hapus Pengguna?', 'Akun ini akan dihapus permanen dari sistem.')"
                                            class="inline-flex min-h-11 items-center text-sm font-medium text-red-700 underline underline-offset-4 hover:text-red-900 dark:text-red-300">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>

            <div x-data="{
                show: false,
                title: '',
                action: '',
                method: 'POST',
                formData: { name: '', username: '', email: '', school_id: '', role: '' }
            }" @open-user-modal.window="
                show = true;
                if ($event.detail && $event.detail.user) {
                    title = 'Edit Pengguna';
                    action = '{{ url('admin/users') }}/' + $event.detail.user.id;
                    method = 'PUT';
                    formData = {
                        name: $event.detail.user.name,
                        username: $event.detail.user.username,
                        email: $event.detail.user.email,
                        school_id: $event.detail.user.school_id || '',
                        role: $event.detail.role
                    };
                } else {
                    title = 'Tambah Pengguna';
                    action = '{{ route('superadmin.users.store') }}';
                    method = 'POST';
                    formData = { name: '', username: '', email: '', school_id: '', role: 'operator' };
                }
            " x-show="show"
                class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto bg-slate-900/60 p-4"
                style="display: none;">
                <div @click.away="show = false"
                    class="my-auto w-full max-w-md overflow-hidden rounded-lg border border-slate-200 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-800">
                    <form :action="action" method="POST">
                        @csrf
                        <template x-if="method === 'PUT'">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="title"></h3>
                        </div>

                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama
                                    Lengkap</label>
                                <input type="text" name="name" x-model="formData.name" required
                                    class="min-h-11 w-full rounded-md border-slate-300 focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Username</label>
                                <input type="text" name="username" x-model="formData.username" required
                                    class="min-h-11 w-full rounded-md border-slate-300 focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email</label>
                                <input type="email" name="email" x-model="formData.email" required
                                    class="min-h-11 w-full rounded-md border-slate-300 focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Role / Hak
                                        Akses</label>
                                    <select name="role" x-model="formData.role" required
                                        class="min-h-11 w-full rounded-md border-slate-300 text-sm focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                                        @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ strtoupper($role->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Sekolah</label>
                                    <select name="school_id" x-model="formData.school_id"
                                        class="min-h-11 w-full rounded-md border-slate-300 text-sm focus:border-brand-700 focus:ring-brand-700 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                                        <option value="">-- Pusat --</option>
                                        @foreach($schools as $school)
                                        <option value="{{ $school->id }}">{{ $school->nama_sekolah }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 flex justify-end gap-3">
                            <button type="button" @click="show = false"
                                class="text-xs font-bold text-slate-500 uppercase hover:text-slate-700">Batal</button>
                            <button type="submit"
                                class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-6 py-2 text-sm font-semibold text-white transition-colors hover:bg-brand-800">Simpan
                                Akun</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</x-app-layout>