<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-slate-800 dark:text-slate-200">Manajemen Pengguna</h2>
            <button @click="$dispatch('open-user-modal')"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-indigo-700 shadow-lg shadow-indigo-500/20">
                Tambah User
            </button>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div
                    class="mb-6 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
                    <form method="GET" action="{{ route('superadmin.users.index') }}"
                        class="flex flex-col md:flex-row gap-4 items-center justify-between">

                        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">

                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Cari nama / email..."
                                    class="w-full md:w-64 pl-10 pr-4 py-2 rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 text-sm">
                                <svg class="w-5 h-5 absolute left-3 top-2.5 text-slate-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>

                            <select name="school_id" @change="$el.closest('form').submit()"
                                class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 text-sm">
                                <option value="">Semua Sekolah</option>
                                @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ request('school_id')==$school->id ? 'selected' : ''
                                    }}>
                                    {{ $school->nama_sekolah }}
                                </option>
                                @endforeach
                            </select>

                            <select name="role" @change="$el.closest('form').submit()"
                                class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 text-sm">
                                <option value="">Semua Role</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ request('role')==$role->name ? 'selected' : '' }}>
                                    {{ strtoupper($role->name) }}
                                </option>
                                @endforeach
                            </select>

                            <div class="flex gap-2">
                                <button type="submit"
                                    class="px-4 py-2 bg-slate-800 text-white dark:bg-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-700 transition">Cari</button>
                                @if(request()->hasAny(['search', 'school_id', 'role']))
                                <a href="{{ route('superadmin.users.index') }}"
                                    class="px-4 py-2 bg-rose-50 text-rose-600 border border-rose-200 rounded-lg text-sm font-semibold hover:bg-rose-100 transition">Reset</a>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="text-sm text-slate-500 font-medium">Tampilkan:</label>
                            <select name="per_page" @change="$el.closest('form').submit()"
                                class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 text-sm py-2">
                                <option value="10" {{ request('per_page')==10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page')==25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page')==50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page')==100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border ...">
                    <table class="w-full text-left border-collapse">
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
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
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
                                        class="px-2 py-1 rounded-md text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 uppercase">
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
                                            class="text-amber-600 hover:underline font-bold text-xs">
                                            Reset
                                        </button>
                                    </form>

                                    <button type="button" @click="$dispatch('open-user-modal', {
            user: {{ $user->toJson() }},
            role: '{{ $user->roles->first()->name ?? '' }}'
        })" class="text-indigo-600 hover:underline font-bold text-xs">
                                        Edit
                                    </button>

                                    <form id="form-delete-{{ $user->id }}"
                                        action="{{ route('superadmin.users.destroy', $user) }}" method="POST"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                            onclick="confirmAction('form-delete-{{ $user->id }}', 'Hapus Pengguna?', 'Akun ini akan dihapus permanen dari sistem.')"
                                            class="text-rose-600 hover:underline font-bold text-xs">
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
                class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                style="display: none;">
                <div @click.away="show = false"
                    class="bg-white dark:bg-slate-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden shadow-indigo-500/10">
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
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Username</label>
                                <input type="text" name="username" x-model="formData.username" required
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email</label>
                                <input type="email" name="email" x-model="formData.email" required
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Role / Hak
                                        Akses</label>
                                    <select name="role" x-model="formData.role" required
                                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                        @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ strtoupper($role->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Sekolah</label>
                                    <select name="school_id" x-model="formData.school_id"
                                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
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
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold uppercase hover:bg-indigo-700 transition">Simpan
                                Akun</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</x-app-layout>