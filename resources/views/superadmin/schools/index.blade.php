<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-slate-950 dark:text-slate-100 sm:text-2xl">
                {{ __('Manajemen Sekolah') }}
            </h2>
            <button type="button" @click="$dispatch('open-school-modal')"
                class="inline-flex min-h-11 items-center justify-center rounded-md border border-transparent bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                <svg aria-hidden="true" class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Sekolah
            </button>
        </div>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8" x-data="{ open: false }">
        <div class="mx-auto max-w-screen-2xl">
            <div
                class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full w-full text-left">
                        <thead class="bg-slate-50 dark:bg-slate-900/50">
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
                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                <td class="px-6 py-4 text-sm font-medium text-slate-600 dark:text-slate-300">{{
                                    $school->npsn }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-slate-900 dark:text-white">{{
                                        $school->nama_sekolah }}</div>
                                    <div class="text-xs text-slate-500 truncate max-w-xs">{{ $school->alamat }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex rounded-md bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-900 dark:bg-brand-900 dark:text-brand-100">
                                        {{ $school->tingkat }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="flex items-center text-sm {{ $school->status ? 'text-emerald-600' : 'text-rose-600' }}">
                                        <span
                                            aria-hidden="true"
                                            class="mr-2 h-2 w-2 rounded-full {{ $school->status ? 'bg-emerald-600' : 'bg-red-600' }}"></span>
                                        {{ $school->status ? 'Aktif' : 'Suspend' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button @click="$dispatch('open-school-modal', { school: {{ $school->toJson() }} })"
                                        class="inline-flex min-h-11 items-center rounded-sm text-sm font-medium text-brand-800 underline underline-offset-4 hover:text-brand-900 dark:text-brand-200 dark:hover:text-white">Edit</button>

                                    <form id="form-delete-school-{{ $school->id }}"
                                        action="{{ route('superadmin.schools.destroy', $school) }}" method="POST"
                                        class="inline">
                                        @csrf @method('DELETE')

                                        <button type="button"
                                            onclick="confirmAction('form-delete-school-{{ $school->id }}', 'Hapus Sekolah?', 'Apakah Anda yakin? Semua data pengguna dan sistem yang terkait dengan sekolah ini akan ikut terhapus permanen.')"
                                            class="inline-flex min-h-11 items-center rounded-sm text-sm font-medium text-red-700 underline underline-offset-4 hover:text-red-900 dark:text-red-300">
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
                <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700 sm:px-6">
                    {{ $schools->links() }}
                </div>
            </div>
        </div>

        <div x-data="{
                show: false,
                title: '',
                action: '',
                method: 'POST',
                formData: { npsn: '', nama_sekolah: '', kepala_sekolah: '', nip: '', tingkat: '', alamat: '', kelurahan: '', kecamatan: '', kota: '', provinsi: '', status: true }
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
                    formData = { npsn: '', nama_sekolah: '', kepala_sekolah: '', nip: '', tingkat: 'SD', alamat: '', kelurahan: '', kecamatan: '', kota: '', provinsi: '', status: true };
                }
            " x-show="show" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">

            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div @click="show = false" class="fixed inset-0 bg-slate-900/60 transition-opacity">
                </div>

                <div
                    class="inline-block w-full max-w-lg transform overflow-hidden rounded-lg border border-slate-200 bg-white text-left align-bottom shadow-lg transition-all dark:border-slate-700 dark:bg-slate-800 sm:my-8 sm:align-middle">
                    <form :action="action" method="POST">
                        @csrf
                        <template x-if="method === 'PUT'">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div
                            class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-700 sm:px-6">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="title"></h3>
                            <button type="button" aria-label="Tutup formulir" @click="show = false"
                                class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-md text-slate-600 hover:bg-slate-100 hover:text-slate-950 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white">&times;</button>
                        </div>

                        <div class="max-h-[70vh] space-y-4 overflow-y-auto px-5 py-5 sm:px-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">NPSN</label>
                                <input type="text" name="npsn" x-model="formData.npsn" required
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 focus:ring-brand-500 shadow-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama
                                    Sekolah</label>
                                <input type="text" name="nama_sekolah" x-model="formData.nama_sekolah" required
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 focus:ring-brand-500 shadow-sm">
                            </div>

                            <!-- Input Kepala Sekolah dan NIP -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Kepala
                                        Sekolah</label>
                                    <input type="text" name="kepala_sekolah" x-model="formData.kepala_sekolah"
                                        class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 focus:ring-brand-500 shadow-sm">
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300">NIP</label>
                                    <input type="text" name="nip" x-model="formData.nip"
                                        class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 focus:ring-brand-500 shadow-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
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
                                            class="rounded border-slate-300 text-brand-600 shadow-sm">
                                        <span class="ml-2 text-sm text-slate-600 dark:text-slate-400">Aktif</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-300">Alamat</label>
                                <textarea name="alamat" x-model="formData.alamat" rows="2"
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 shadow-sm"></textarea>
                            </div>

                            <!-- Input Detail Alamat -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300">Kelurahan /
                                        Desa</label>
                                    <input type="text" name="kelurahan" x-model="formData.kelurahan"
                                        class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 focus:ring-brand-500 shadow-sm">
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300">Kecamatan</label>
                                    <input type="text" name="kecamatan" x-model="formData.kecamatan"
                                        class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 focus:ring-brand-500 shadow-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Kota /
                                        Kabupaten</label>
                                    <input type="text" name="kota" x-model="formData.kota"
                                        class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 focus:ring-brand-500 shadow-sm">
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300">Provinsi</label>
                                    <input type="text" name="provinsi" x-model="formData.provinsi"
                                        class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 focus:ring-brand-500 shadow-sm">
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/50 sm:flex-row sm:justify-end sm:px-6">
                            <button type="button" @click="show = false"
                                class="inline-flex min-h-11 items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 dark:text-slate-200 dark:hover:bg-slate-700">Batal</button>
                            <button type="submit"
                                class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-6 py-2 text-sm font-semibold text-white transition-colors hover:bg-brand-800">Simpan
                                Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>