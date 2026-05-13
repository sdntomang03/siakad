<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-slate-800 dark:text-slate-200">Tahun Pelajaran</h2>
            @can('create-academic-years')
            <button @click="$dispatch('open-at-modal')"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold uppercase hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/20">
                Tambah Tahun
            </button>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <table class="min-w-full w-full text-left whitespace-nowrap">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Tahun Ajaran</th>
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Semester Aktif</th>
                            @role('superadmin')
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Sekolah</th>
                            @endrole
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($academicYears->groupBy('tahun_ajaran') as $tahun => $items)
                        @php
                        $activeSemester = $items->firstWhere('is_active', true);
                        $ganjil = $items->firstWhere('semester', 'Ganjil');
                        $genap = $items->firstWhere('semester', 'Genap');
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">{{ $tahun }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                {{ $activeSemester ? $activeSemester->semester : 'Belum ditentukan' }}
                            </td>
                            @role('superadmin')
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                {{ $items->first()->school->nama_sekolah ?? '-' }}
                            </td>
                            @endrole
                            <td class="px-6 py-4">
                                @if($activeSemester)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                                    {{ $activeSemester->semester }} Aktif
                                </span>
                                @else
                                <span class="text-xs text-slate-400 italic">Non-aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-3">
                                {{-- Tombol Ganjil/Genap --}}
                                <div class="inline-flex rounded-lg shadow-sm align-middle">
                                    <form id="form-active-{{ $ganjil->id }}"
                                        action="{{ route('academic-years.aktifkan', $ganjil) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="button"
                                            @click="confirmAction('form-active-{{ $ganjil->id }}', 'Aktifkan Ganjil?', 'Aktifkan semester Ganjil {{ $tahun }}?')"
                                            class="px-3 py-1.5 text-xs font-bold rounded-l-lg border border-slate-200 dark:border-slate-700 {{ $ganjil->is_active ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                                            Ganjil
                                        </button>
                                    </form>
                                    <form id="form-active-{{ $genap->id }}"
                                        action="{{ route('academic-years.aktifkan', $genap) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="button"
                                            @click="confirmAction('form-active-{{ $genap->id }}', 'Aktifkan Genap?', 'Aktifkan semester Genap {{ $tahun }}?')"
                                            class="px-3 py-1.5 text-xs font-bold rounded-r-lg border-t border-b border-r border-slate-200 dark:border-slate-700 {{ $genap->is_active ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                                            Genap
                                        </button>
                                    </form>
                                </div>

                                {{-- Edit --}}
                                @can('edit-academic-years')
                                <button
                                    @click="$dispatch('open-at-modal', { id: {{ $ganjil->id }}, tahun: '{{ $tahun }}' })"
                                    class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg transition-colors align-middle">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </button>
                                @endcan

                                {{-- Hapus --}}
                                @can('delete-academic-years')
                                <form id="form-del-{{ $ganjil->id }}"
                                    action="{{ route('academic-years.destroy', $ganjil) }}" method="POST"
                                    class="inline align-middle">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                        @click="confirmAction('form-del-{{ $ganjil->id }}', 'Hapus Tahun?', 'Hapus paket tahun ajaran {{ $tahun }}?')"
                                        class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors {{ ($ganjil->is_active || $genap->is_active) ? 'opacity-20 cursor-not-allowed' : '' }}"
                                        {{ ($ganjil->is_active || $genap->is_active) ? 'disabled' : '' }}>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500 italic">Belum ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
                    {{ $academicYears->links() }}
                </div>
            </div>
        </div>

        {{-- MODAL DINAMIS --}}
        <div x-data="{
            show: false,
            isEdit: false,
            actionUrl: '',
            method: 'POST',
            tahun: ''
        }" @open-at-modal.window="
            show = true;
            if($event.detail && $event.detail.id) {
                isEdit = true;
                actionUrl = '/academic-years/' + $event.detail.id;
                method = 'PUT';
                tahun = $event.detail.tahun;
            } else {
                isEdit = false;
                actionUrl = '{{ route('academic-years.store') }}';
                method = 'POST';
                tahun = '';
            }
        " x-show="show"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            style="display: none;">
            <div @click.away="show = false"
                class="bg-white dark:bg-slate-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">
                <form :action="actionUrl" method="POST">
                    @csrf
                    <template x-if="method === 'PUT'"><input type="hidden" name="_method" value="PUT"></template>

                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white"
                            x-text="isEdit ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran'"></h3>
                    </div>

                    <div class="p-6 space-y-4">
                        @role('superadmin')
                        <template x-if="!isEdit">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Pilih
                                    Sekolah</label>
                                <select name="school_id" required
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                                    @foreach($schools as $school)
                                    <option value="{{ $school->id }}">{{ $school->nama_sekolah }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </template>
                        @endrole

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tahun Ajaran</label>
                            <input type="text" name="tahun_ajaran" x-model="tahun" required pattern="\d{4}/\d{4}"
                                placeholder="Contoh: 2025/2026"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                            <p class="mt-1 text-[10px] text-slate-500 italic">* Format: YYYY/YYYY (contoh: 2025/2026)
                            </p>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 flex justify-end gap-3">
                        <button type="button" @click="show = false"
                            class="text-xs font-bold text-slate-500 uppercase">Batal</button>
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold uppercase hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>