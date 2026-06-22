<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            Manajemen Distribusi Rapor
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-medium text-sm">
                {{ session('success') }}
            </div>
            @endif

            @if(auth()->user() && ! auth()->user()->hasRole('superadmin') && isset($students) && $students->isEmpty())
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 font-medium text-sm">
                Akun Anda belum terkait dengan kelas perwalian mana pun atau belum ada siswa di kelas Anda. Silakan
                hubungi admin sekolah.
            </div>
            @endif

            {{-- PANEL ATAS: FORM AKSI MASSAL --}}
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                    <h3 class="text-base font-bold text-slate-700 dark:text-slate-300">Catat Pergerakan Rapor</h3>
                    <p class="text-xs text-slate-500">Pilih siswa di masing-masing kelas untuk menandai apakah rapor
                        sedang dibagikan ke rumah atau disimpan di sekolah.</p>
                </div>

                <div class="p-6">
                    @if(isset($classrooms) && $classrooms->isNotEmpty())
                    <div class="space-y-6">
                        @foreach($classrooms as $classroom)

                        <form action="{{ route('report-submissions.bulk-update') }}" method="POST"
                            class="bulkFormClass">
                            @csrf
                            <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
                            <input type="hidden" name="posisi" class="posisiInput">

                            <div
                                class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden shadow-sm">
                                <div
                                    class="bg-slate-100 dark:bg-slate-700 px-4 py-3 flex flex-col md:flex-row md:items-center justify-between gap-3">
                                    <div>
                                        <span class="font-bold text-sm text-slate-700 dark:text-slate-200">
                                            Kelas {{ $classroom->tingkat }} {{ $classroom->nama_kelas }}
                                        </span>
                                        <p class="text-[11px] text-slate-500 mt-0.5">
                                            Tahun Ajaran: <span class="font-semibold">{{
                                                $classroom->academicYear->tahun_ajaran ?? '-' }} ({{
                                                $classroom->academicYear->semester ?? '-' }})</span>
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <label
                                            class="flex items-center gap-2 text-xs font-semibold cursor-pointer mr-2 text-slate-700 dark:text-slate-300">
                                            <input type="checkbox"
                                                class="class-select-all rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                                data-class-id="{{ $classroom->id }}" />
                                            Pilih Semua
                                        </label>

                                        <button type="button" onclick="submitBulk(this, 'Dibawa Siswa')"
                                            class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg shadow transition text-xs">
                                            ↑ Bagikan
                                        </button>
                                        <button type="button" onclick="submitBulk(this, 'Di Sekolah')"
                                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow transition text-xs">
                                            ↓ Terima
                                        </button>
                                    </div>
                                </div>

                                <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @php
                                    $studentsInClass = $students->filter(fn($st) => $st->classrooms->contains('id',
                                    $classroom->id));
                                    @endphp

                                    @forelse($studentsInClass as $stu)
                                    <label
                                        class="flex items-center gap-3 p-3 border border-slate-200 dark:border-slate-600 rounded-lg cursor-pointer hover:bg-indigo-50 dark:hover:bg-slate-700 transition">
                                        <input type="checkbox" name="student_ids[]" value="{{ $stu->id }}"
                                            class="student-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                            data-class-id="{{ $classroom->id }}" />
                                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{
                                            $stu->nama_lengkap }}</span>
                                    </label>
                                    @empty
                                    <div class="text-xs text-slate-500 col-span-full">Tidak ada siswa di kelas ini.
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </form>
                        @endforeach
                    </div>
                    @else
                    <div class="text-sm text-slate-500 text-center py-4">Belum ada kelas yang ditugaskan kepada Anda.
                    </div>
                    @endif
                </div>
            </div>

            {{-- PANEL BAWAH: TABEL RIWAYAT --}}
            {{-- PANEL BAWAH: TABEL RIWAYAT (DILENGKAPI MULTI-SELECT ALPINE.JS) --}}
            <div x-data="{
                    selectedHistory: [],
                    toggleAllHistory(e) {
                        let checkboxes = document.querySelectorAll('.history-checkbox');
                        this.selectedHistory = [];
                        if(e.target.checked) {
                            checkboxes.forEach(cb => { cb.checked = true; this.selectedHistory.push(cb.value); });
                        } else {
                            checkboxes.forEach(cb => cb.checked = false);
                        }
                    }
                 }"
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">

                <form method="POST">
                    @csrf
                    <div
                        class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <h3 class="text-base font-bold text-slate-700 dark:text-slate-300">Status Rapor Terkini</h3>

                        {{-- Tombol Aksi Massal (Muncul Jika Ada yang Dicentang) --}}
                        <div class="flex flex-wrap items-center gap-2">
                            <button x-show="selectedHistory.length > 0" style="display: none;" type="submit"
                                name="posisi" value="Dibawa Siswa"
                                formaction="{{ route('report-submissions.bulk-update-history') }}"
                                onclick="return confirm('Tandai rapor terpilih sedang dibawa siswa?')"
                                class="px-3 py-1.5 bg-amber-100 text-amber-700 border border-amber-200 hover:bg-amber-600 hover:text-white rounded-lg text-xs font-bold transition">
                                ↑ Bagikan (<span x-text="selectedHistory.length"></span>)
                            </button>
                            <button x-show="selectedHistory.length > 0" style="display: none;" type="submit"
                                name="posisi" value="Di Sekolah"
                                formaction="{{ route('report-submissions.bulk-update-history') }}"
                                onclick="return confirm('Tandai rapor terpilih sudah kembali di sekolah?')"
                                class="px-3 py-1.5 bg-emerald-100 text-emerald-700 border border-emerald-200 hover:bg-emerald-600 hover:text-white rounded-lg text-xs font-bold transition">
                                ↓ Terima (<span x-text="selectedHistory.length"></span>)
                            </button>
                            <button x-show="selectedHistory.length > 0" style="display: none;" type="submit"
                                formaction="{{ route('report-submissions.bulk-destroy-history') }}"
                                onclick="return confirm('Hapus permanen riwayat rapor terpilih?')"
                                class="px-3 py-1.5 bg-rose-100 text-rose-700 border border-rose-200 hover:bg-rose-600 hover:text-white rounded-lg text-xs font-bold transition">
                                🗑️ Hapus (<span x-text="selectedHistory.length"></span>)
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-slate-600 dark:text-slate-400">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-100 dark:bg-slate-700">
                                <tr>
                                    <th class="px-6 py-4 w-10">
                                        <input type="checkbox" @change="toggleAllHistory"
                                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 cursor-pointer">
                                    </th>
                                    <th class="px-6 py-4">Nama Siswa</th>
                                    <th class="px-6 py-4">Kelas</th>
                                    <th class="px-6 py-4">Tahun Ajaran</th>
                                    <th class="px-6 py-4">Posisi Rapor</th>
                                    <th class="px-6 py-4">Update Terakhir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($submissions as $s)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                    <td class="px-6 py-4">
                                        {{-- Checkbox per baris --}}
                                        <input type="checkbox" name="submission_ids[]" value="{{ $s->id }}"
                                            x-model="selectedHistory"
                                            class="history-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 cursor-pointer">
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">{{
                                        $s->student->nama_lengkap ?? '—' }}</td>
                                    <td class="px-6 py-4">{{ $s->classroom->tingkat ?? '' }} {{
                                        $s->classroom->nama_kelas ?? '—' }}</td>
                                    <td class="px-6 py-4">
                                        @if($s->academicYear)
                                        {{ $s->academicYear->tahun_ajaran }} | {{ $s->academicYear->semester }}
                                        @else
                                        —
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($s->posisi === 'Di Sekolah')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Di Sekolah
                                        </span>
                                        @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            Dibawa Siswa
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        {{ $s->updated_at->format('d M Y, H:i') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500 italic">Belum ada
                                        riwayat pergerakan rapor.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>

                @if($submissions->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                    {{ $submissions->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.class-select-all').forEach(selectAllCheckbox => {
            selectAllCheckbox.addEventListener('change', function() {
                const classId = this.dataset.classId;
                document.querySelectorAll(`.student-checkbox[data-class-id="${classId}"]`).forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        });

        function submitBulk(btnElement, posisi) {
            const form = btnElement.closest('.bulkFormClass');
            const checkedStudents = form.querySelectorAll('.student-checkbox:checked').length;

            if (checkedStudents === 0) {
                alert('Silakan centang minimal satu siswa di kelas ini terlebih dahulu.');
                return;
            }

            form.querySelector('.posisiInput').value = posisi;
            const actionText = posisi === 'Dibawa Siswa' ? 'dibagikan kepada siswa' : 'diterima kembali di sekolah';

            if (confirm(`Tandai ${checkedStudents} rapor di kelas ini telah ${actionText}?`)) {
                form.submit();
            }
        }
    </script>
</x-app-layout>