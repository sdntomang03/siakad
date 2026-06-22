<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h2 class="font-semibold text-2xl text-slate-800 dark:text-slate-200">Manajemen Guru / Pengguna</h2>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                @can('create-users')
                {{-- TOMBOL IMPORT EXCEL --}}
                <button @click="$dispatch('open-import-modal')"
                    class="w-full sm:w-auto px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-emerald-700 transition shadow-lg shadow-emerald-500/20 text-center">
                    Import Excel
                </button>

                {{-- TOMBOL TAMBAH PENGGUNA --}}
                <button @click="$dispatch('open-user-modal')"
                    class="w-full sm:w-auto px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/20 text-center">
                    Tambah Pengguna
                </button>
                @endcan
            </div>
        </div>
    </x-slot>

    @php
    // Ambil ID pengguna di halaman ini yang BUKAN user yang sedang login (agar tidak hapus diri sendiri)
    $deletableIds = $users->filter(fn($u) => $u->id !== auth()->id())->pluck('id');
    @endphp

    {{-- Wrapper utama dengan Alpine.js untuk fitur Multi-Delete --}}
    <div class="py-12" x-data="{
        selected: [],
        allIds: {{ $deletableIds->toJson() }},
        get isAllSelected() { return this.selected.length > 0 && this.selected.length === this.allIds.length },
        toggleAll() { this.selected = this.isAllSelected ? [] : [...this.allIds] }
    }">
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

            {{-- BANNER HAPUS TERPILIH (MUNCUL JIKA ADA CHECKBOX YANG DICENTANG) --}}
            @can('delete-users')
            <div x-show="selected.length > 0" x-transition style="display: none;"
                class="mb-4 p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-xl flex items-center justify-between shadow-sm">
                <span class="text-sm font-bold text-rose-700 dark:text-rose-400">
                    <span x-text="selected.length"></span> pengguna dipilih
                </span>

                <form action="{{ route('operator.users.bulk-destroy') }}" method="POST" id="bulk-delete-form"
                    class="inline">
                    @csrf
                    @method('DELETE')
                    {{-- Input hidden yang akan menampung array ID yang dipilih --}}
                    <input type="hidden" name="ids" x-bind:value="selected.join(',')">
                    <button type="button"
                        @click="if(confirm('Yakin ingin menghapus ' + selected.length + ' pengguna terpilih? Data tidak bisa dikembalikan.')) $el.closest('form').submit()"
                        class="px-4 py-2 bg-rose-600 text-white rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-rose-700 transition">
                        Hapus Terpilih
                    </button>
                </form>
            </div>
            @endcan

            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full w-full text-left whitespace-nowrap">
                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                            <tr>
                                @can('delete-users')
                                {{-- MASTER CHECKBOX --}}
                                <th class="px-6 py-4 w-10">
                                    <input type="checkbox" @click="toggleAll()" :checked="isAllSelected"
                                        class="rounded border-slate-300 text-rose-600 shadow-sm focus:ring-rose-500">
                                </th>
                                @endcan
                                <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">Nama / Email</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">Role</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($users as $user)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50"
                                :class="selected.includes({{ $user->id }}) ? 'bg-rose-50/50 dark:bg-rose-900/10' : ''">
                                @can('delete-users')
                                {{-- INDIVIDUAL CHECKBOX --}}
                                <td class="px-6 py-4">
                                    @if($user->id !== auth()->id())
                                    <input type="checkbox" value="{{ $user->id }}" x-model="selected"
                                        class="rounded border-slate-300 text-rose-600 shadow-sm focus:ring-rose-500">
                                    @endif
                                </td>
                                @endcan
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

                                    @if($user->hasRole('siswa'))
                                    <a href="{{ route('students.edit', $user->id) }}"
                                        class="text-emerald-600 hover:underline font-bold text-xs bg-emerald-50 dark:bg-emerald-900/20 px-2 py-1.5 rounded inline-block ml-2 border border-emerald-100">Data
                                        Siswa</a>
                                    @elseif($user->hasRole('superadmin'))
                                    <span
                                        class="text-xs font-bold text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-1.5 rounded inline-block ml-2 border border-slate-200 dark:border-slate-600 cursor-not-allowed"
                                        title="Superadmin tidak memiliki data pegawai">Akun Sistem</span>
                                    @else
                                    <a href="{{ route('employees.edit', $user->id) }}"
                                        class="text-indigo-600 hover:underline font-bold text-xs bg-indigo-50 dark:bg-indigo-900/20 px-2 py-1.5 rounded inline-block ml-2 border border-indigo-100">Data
                                        Pegawai</a>
                                    @endif
                                    @endcan

                                    {{-- TOMBOL HAPUS (INDIVIDU) --}}
                                    @can('delete-users')
                                    @if($user->id !== auth()->id())
                                    <form id="form-delete-{{ $user->id }}"
                                        action="{{ route('operator.users.destroy', $user) }}" method="POST"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                            onclick="confirmAction('form-delete-{{ $user->id }}', 'Hapus Pengguna?', 'Akun ini akan dihapus permanen.')"
                                            class="text-rose-600 hover:underline font-bold text-xs ml-2">Hapus</button>
                                    </form>
                                    @endif
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-slate-500 italic">Belum ada data.
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

        {{-- MODAL TAMBAH PENGGUNA --}}
        <div x-data="{ show: false, formData: { name: '', email: '', role: 'guru', jenis_kelamin: '', nip: '' } }"
            @open-user-modal.window="show = true; formData = { name: '', email: '', role: 'guru', jenis_kelamin: '', nip: '' };"
            x-show="show"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
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
                        <template x-if="formData.role !== 'siswa'">
                            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                                <div class="p-2 bg-slate-50 dark:bg-slate-900/50 rounded text-xs text-slate-500 italic">
                                    Detail Data Kepegawaian Awal</div>
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
    </div> {{-- End Wrapper Alpine --}}

    {{-- MODAL IMPORT EXCEL --}}
    <div x-data="{ showImport: false, importType: 'pegawai' }"
        @open-import-modal.window="showImport = true; importType = 'pegawai'" x-show="showImport"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
        style="display: none;" x-transition>
        <div @click.away="showImport = false"
            class="bg-white dark:bg-slate-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">
            <form action="{{ route('operator.users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Import Data via Excel</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jenis Data yang
                            Diimpor</label>
                        <select name="tipe_import" x-model="importType" required
                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                            <option value="pegawai">Pegawai / Guru / Kepsek</option>
                            <option value="siswa">Data Siswa</option>
                        </select>
                    </div>
                    <div x-show="importType === 'pegawai'"
                        class="p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-100 dark:border-indigo-800 text-sm text-indigo-700 dark:text-indigo-300">
                        <p class="font-bold mb-2">Aturan Excel Pegawai:</p>
                        <ul class="list-disc pl-5 space-y-1 text-xs">
                            <li>Header: <strong>name, email, role, jenis_kelamin, nip, nuptk</strong>.</li>
                            <li>Kolom <strong>role</strong>: <span
                                    class="font-mono bg-indigo-100 dark:bg-indigo-800 px-1 rounded">kepsek</span> atau
                                <span class="font-mono bg-indigo-100 dark:bg-indigo-800 px-1 rounded">guru</span>.</li>
                            <li>Password otomatis: <strong>12345678</strong>.</li>
                        </ul>
                    </div>
                    <div x-show="importType === 'siswa'" style="display: none;"
                        class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-100 dark:border-emerald-800 text-sm text-emerald-700 dark:text-emerald-300">
                        <p class="font-bold mb-2">Aturan Excel Siswa:</p>
                        <ul class="list-disc pl-5 space-y-1 text-xs">
                            <li>Header: <strong>name, email, jenis_kelamin, nisn, nipd</strong>.</li>
                            <li>Role otomatis disetel sebagai Siswa.</li>
                            <li>Kolom <strong>jenis_kelamin</strong>: L atau P.</li>
                            <li>Password otomatis: <strong>12345678</strong>.</li>
                        </ul>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Pilih File (.xlsx, .xls,
                            .csv)</label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                    </div>
                </div>
                <div
                    class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" @click="showImport = false"
                        class="text-xs font-bold text-slate-500 uppercase hover:text-slate-700">Batal</button>
                    <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold uppercase hover:bg-indigo-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Mulai Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>