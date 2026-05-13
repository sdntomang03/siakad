<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-slate-800 dark:text-slate-200 leading-tight">
                {{ __('Manajemen Sekolah') }}
            </h2>
            <button @click="$dispatch('open-school-modal')"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Sekolah
            </button>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full w-full text-left whitespace-nowrap">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">NPSN</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Sekolah</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Tingkat</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase text-right">Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($schools as $school)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-slate-600 dark:text-slate-300">{{
                                    $school->npsn }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-slate-900 dark:text-white">{{
                                        $school->nama_sekolah }}</div>
                                    <div class="text-xs text-slate-500 truncate max-w-xs">{{ $school->alamat }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                        {{ $school->tingkat }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="flex items-center text-sm {{ $school->status ? 'text-emerald-600' : 'text-rose-600' }}">
                                        <span
                                            class="h-2 w-2 rounded-full {{ $school->status ? 'bg-emerald-500' : 'bg-rose-500' }} mr-2"></span>
                                        {{ $school->status ? 'Aktif' : 'Suspend' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button @click="$dispatch('open-school-modal', { school: {{ $school->toJson() }} })"
                                        class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">Edit</button>

                                    <form id="form-delete-school-{{ $school->id }}"
                                        action="{{ route('superadmin.schools.destroy', $school) }}" method="POST"
                                        class="inline">
                                        @csrf @method('DELETE')

                                        <button type="button"
                                            onclick="confirmAction('form-delete-school-{{ $school->id }}', 'Hapus Sekolah?', 'Apakah Anda yakin? Semua data pengguna dan sistem yang terkait dengan sekolah ini akan ikut terhapus permanen.')"
                                            class="text-rose-600 hover:text-rose-900 font-medium text-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-500 italic">Belum ada data
                                    sekolah.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
                    {{ $schools->links() }}
                </div>
            </div>
        </div>

        <div x-data="{
                show: false,
                title: '',
                action: '',
                method: 'POST',
                formData: { npsn: '', nama_sekolah: '', tingkat: '', alamat: '', status: true }
            }" @open-school-modal.window="
                show = true;
                if ($event.detail && $event.detail.school) {
                    title = 'Edit Sekolah';
                    action = '{{ url('admin/schools') }}/' + $event.detail.school.id;
                    method = 'PUT';
                    formData = { ...$event.detail.school };
                } else {
                    title = 'Tambah Sekolah Baru';
                    action = '{{ route('superadmin.schools.store') }}';
                    method = 'POST';
                    formData = { npsn: '', nama_sekolah: '', tingkat: 'SD', alamat: '', status: true };
                }
            " x-show="show" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">

            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div @click="show = false" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm">
                </div>

                <div
                    class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-slate-800 rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form :action="action" method="POST">
                        @csrf
                        <template x-if="method === 'PUT'">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div
                            class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="title"></h3>
                            <button type="button" @click="show = false"
                                class="text-slate-400 hover:text-slate-600">&times;</button>
                        </div>

                        <div class="px-6 py-6 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">NPSN</label>
                                <input type="text" name="npsn" x-model="formData.npsn" required
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 focus:ring-indigo-500 shadow-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama
                                    Sekolah</label>
                                <input type="text" name="nama_sekolah" x-model="formData.nama_sekolah" required
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 focus:ring-indigo-500 shadow-sm">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tingkat</label>
                                    <select name="tingkat" x-model="formData.tingkat"
                                        class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 shadow-sm">
                                        <option value="SD">SD</option>
                                        <option value="SMP">SMP</option>
                                        <option value="SMA">SMA</option>
                                        <option value="SMK">SMK</option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                                    <div class="mt-2 flex items-center">
                                        <input type="checkbox" name="status" x-model="formData.status" value="1"
                                            class="rounded border-slate-300 text-indigo-600 shadow-sm">
                                        <span class="ml-2 text-sm text-slate-600 dark:text-slate-400">Aktif</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-300">Alamat</label>
                                <textarea name="alamat" x-model="formData.alamat" rows="3"
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 shadow-sm"></textarea>
                            </div>
                        </div>

                        <div
                            class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-700 text-right space-x-3">
                            <button type="button" @click="show = false"
                                class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900">Batal</button>
                            <button type="submit"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition shadow-md shadow-indigo-500/20">Simpan
                                Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>