<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-slate-800 dark:text-slate-200">Manajemen Guru / Pengguna</h2>
            @can('create-users')
            <button @click="$dispatch('open-user-modal')"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/20">
                Tambah Pengguna
            </button>
            @endcan
        </div>
    </x-slot>

    <div class="py-12" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div
                class="mb-6 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
                <form method="GET" action="{{ route('operator.users.index') }}"
                    class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="flex gap-3 w-full md:w-auto">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama / email..."
                            class="w-full md:w-64 rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                        <button type="submit"
                            class="px-4 py-2 bg-slate-800 text-white dark:bg-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-700 transition">Cari</button>
                        @if(request()->has('search'))
                        <a href="{{ route('operator.users.index') }}"
                            class="px-4 py-2 bg-rose-50 text-rose-600 border border-rose-200 rounded-lg text-sm font-semibold">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full w-full text-left whitespace-nowrap">
                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">Nama / Email</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">Role</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($users as $user)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $user->name }}
                                    </div>
                                    <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 rounded-md text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 uppercase">
                                        {{ $user->roles->first()->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    @can('edit-users')
                                    {{-- TOMBOL RESET PASSWORD --}}
                                    <form id="form-reset-{{ $user->id }}"
                                        action="{{ route('operator.users.reset-password', $user) }}" method="POST"
                                        class="inline">
                                        @csrf @method('PATCH')
                                        <button type="button"
                                            onclick="confirmAction('form-reset-{{ $user->id }}', 'Reset Password?', 'Password akan diubah menjadi 12345678.')"
                                            class="text-amber-600 hover:underline font-bold text-xs">Reset</button>
                                    </form>

                                    {{-- LOGIKA PEMISAHAN TOMBOL EDIT BERDASARKAN ROLE --}}
                                    @if($user->hasRole('siswa'))
                                    <a href="{{ route('students.edit', $user->id) }}"
                                        class="text-emerald-600 hover:underline font-bold text-xs bg-emerald-50 dark:bg-emerald-900/20 px-2 py-1.5 rounded inline-block ml-2 border border-emerald-100">
                                        Data Siswa
                                    </a>
                                    @elseif($user->hasRole('superadmin'))
                                    <span
                                        class="text-xs font-bold text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-1.5 rounded inline-block ml-2 border border-slate-200 dark:border-slate-600 cursor-not-allowed"
                                        title="Superadmin tidak memiliki data pegawai">
                                        Akun Sistem
                                    </span>
                                    @else
                                    <a href="{{ route('employees.edit', $user->id) }}"
                                        class="text-indigo-600 hover:underline font-bold text-xs bg-indigo-50 dark:bg-indigo-900/20 px-2 py-1.5 rounded inline-block ml-2 border border-indigo-100">
                                        Data Pegawai
                                    </a>
                                    @endif
                                    @endcan

                                    {{-- TOMBOL HAPUS --}}
                                    @can('delete-users')
                                    @if($user->id !== auth()->id())
                                    <form id="form-delete-{{ $user->id }}"
                                        action="{{ route('operator.users.destroy', $user) }}" method="POST"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                            onclick="confirmAction('form-delete-{{ $user->id }}', 'Hapus Pengguna?', 'Akun ini akan dihapus permanen.')"
                                            class="text-rose-600 hover:underline font-bold text-xs">Hapus</button>
                                    </form>
                                    @endif
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-slate-500 italic">Belum ada data.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        <div x-data="{
        show: false,
        formData: { name: '', email: '', role: 'guru', jenis_kelamin: '', nip: '' }
    }" @open-user-modal.window="
        show = true;
        // Reset form setiap kali modal dibuka
        formData = { name: '', email: '', role: 'guru', jenis_kelamin: '', nip: '' };
    " x-show="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            style="display: none;">

            <div @click.away="show = false"
                class="bg-white dark:bg-slate-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">

                <form action="{{ route('operator.users.store') }}" method="POST">
                    @csrf

                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Pengguna Baru</h3>
                    </div>

                    <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">

                        <div
                            class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-100 dark:border-indigo-800 text-xs text-indigo-700 dark:text-indigo-300">
                            Password default untuk pengguna baru adalah: <strong>12345678</strong>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama
                                    Lengkap</label>
                                <input type="text" name="name" x-model="formData.name" required
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                            </div>

                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email (Untuk
                                    Login)</label>
                                <input type="email" name="email" x-model="formData.email" required
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                            </div>

                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Role / Hak
                                    Akses</label>
                                <select name="role" x-model="formData.role" required
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ strtoupper($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Form Tambahan: Hanya muncul jika Role BUKAN siswa --}}
                        <template x-if="formData.role !== 'siswa'">
                            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                                <div class="p-2 bg-slate-50 dark:bg-slate-900/50 rounded text-xs text-slate-500 italic">
                                    Detail Data Kepegawaian Awal
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jenis
                                            Kelamin</label>
                                        <select name="jenis_kelamin" x-model="formData.jenis_kelamin"
                                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm"
                                            :required="formData.role !== 'siswa'">
                                            <option value="">-- Pilih --</option>
                                            <option value="L">Laki-Laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">NIP
                                            (Opsional)</label>
                                        <input type="text" name="nip" x-model="formData.nip"
                                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    </div>
                                </div>
                            </div>
                        </template>

                    </div>

                    <div
                        class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" @click="show = false"
                            class="text-xs font-bold text-slate-500 uppercase hover:text-slate-700">Batal</button>
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold uppercase hover:bg-indigo-700 transition">Buat
                            Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>