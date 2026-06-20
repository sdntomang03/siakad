<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Pengumpulan Rapor</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-slate-800">
                    @if(session('success'))
                    <div class="mb-4 text-sm text-green-600">{{ session('success') }}</div>
                    @endif

                    @if(auth()->user() && ! auth()->user()->hasRole('superadmin') && isset($students) &&
                    $students->isEmpty())
                    <div class="mb-4 text-sm text-yellow-700">Akun Anda belum terkait dengan sekolah atau belum ada
                        siswa di sekolah Anda. Silakan minta administrator untuk menetapkan sekolah pada akun Anda atau
                        hubungkan data pegawai ke sekolah.</div>
                    @endif

                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2"> <button id=\"setLocationSchoolBtn\" type=\"button\"
                                disabled class=\"px-3 py-2 bg-blue-600 text-white rounded-lg text-xs font-bold
                                hover:bg-blue-700 transition opacity-50\">Lokasi Sekolah</button>
                            <button id=\"setLocationHomeBtn\" type=\"button\" disabled class=\"px-3 py-2 bg-purple-600
                                text-white rounded-lg text-xs font-bold hover:bg-purple-700 transition
                                opacity-50\">Lokasi Rumah</button> <button id="bulkReturnBtn" type="button" disabled
                                class="px-3 py-2 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition opacity-50">Kembalikan
                                Terpilih</button>
                        </div>
                    </div>

                    {{-- Panel: Daftar Siswa per Kelas yang Diampu --}}
                    <div class="mb-6 p-4 border rounded">
                        <h3 class="font-semibold mb-2">Daftar Siswa (Kelas yang Anda Ampu)</h3>
                        @if(isset($classrooms) && $classrooms->isNotEmpty())
                        <form id="setReturnedForm" action="{{ route('report-submissions.set-returned-multiple') }}"
                            method="POST">
                            @csrf
                            <div class="flex items-center gap-3 mb-3">
                                <label class="text-sm">Periode (opsional):</label>
                                <input type="text" name="period" class="rounded border-slate-300 px-2 py-1"
                                    placeholder="e.g. 2025/2026" />
                                <button id="setReturnedBtn" type="button" disabled
                                    class="ml-auto px-3 py-2 bg-indigo-600 text-white rounded text-sm opacity-50">Tandai
                                    Dikembalikan</button>
                            </div>

                            @foreach($classrooms as $classroom)
                            <div class="mb-2">
                                <div class="font-medium">{{ $classroom->tingkat }} {{ $classroom->nama_kelas }} <input
                                        type="checkbox" class="class-select-all ml-2"
                                        data-class-id="{{ $classroom->id }}" /></div>
                                <div class="grid grid-cols-3 gap-2 mt-2">
                                    @php
                                    $studentsInClass = $students->filter(fn($st) => $st->classrooms->contains('id',
                                    $classroom->id));
                                    @endphp
                                    @forelse($studentsInClass as $stu)
                                    <label class="p-2 border rounded flex items-center gap-2">
                                        <input type="checkbox" name="student_ids[]" value="{{ $stu->id }}"
                                            class="student-checkbox" data-class-id="{{ $classroom->id }}" />
                                        <span class="text-sm">{{ $stu->nama_lengkap }}</span>
                                    </label>
                                    @empty
                                    <div class="text-sm text-slate-500">Tidak ada siswa di kelas ini.</div>
                                    @endforelse
                                </div>
                            </div>
                            @endforeach
                        </form>
                        @else
                        <div class="text-sm text-slate-500">Tidak ada kelas yang terkait dengan akun Anda.</div>
                        @endif
                    </div>

                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-slate-500">
                                <th class="py-2 w-12 text-center"><input type="checkbox" id="selectAllSubmissions"
                                        class="rounded border-slate-300 h-4 w-4" /></th>
                                <th class="py-2">Siswa</th>
                                <th class="py-2">Kelas</th>
                                <th class="py-2">Periode</th>
                                <th class="py-2">Lokasi</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Dikirim</th>
                                <th class="py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($submissions as $s)
                            <tr class="border-t">
                                <td class="py-2 text-center"><input type="checkbox"
                                        class="submission-checkbox rounded border-slate-300 h-4 w-4"
                                        value="{{ $s->id }}" /></td>
                                <td class="py-2 font-semibold">{{ $s->student->nama_lengkap ?? '—' }}</td>
                                <td class="py-2">{{ $s->classroom->tingkat ?? '—' }} {{ $s->classroom->nama_kelas ?? ''
                                    }}</td>
                                <td class="py-2">{{ $s->period ?? '—' }}</td>
                                <td class="py-2">{{ $s->location === 'school' ? 'Sekolah' : 'Rumah' }}</td>
                                <td class="py-2">
                                    @if($s->is_returned)
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">Dikembalikan</span>
                                    @elseif($s->is_submitted)
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">Terkumpul</span>
                                    @else
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">Belum</span>
                                    @endif
                                </td>
                                <td class="py-2">{{ optional($s->submitted_at)->format('Y-m-d H:i') ?? '—' }}</td>
                                <td class="py-2">
                                    <div class="flex items-center gap-2">
                                        @if($s->is_submitted && ! $s->is_returned)
                                        <form action="{{ route('report-submissions.return', $s) }}" method="POST"
                                            class="inline-block">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1 text-[10px] font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Kembalikan</button>
                                        </form>
                                        @endif

                                        <form action="{{ route('report-submissions.toggle', $s) }}" method="POST"
                                            class="inline-block">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1 text-[10px] font-bold text-white {{ $s->is_submitted ? 'bg-slate-500 hover:bg-slate-600' : 'bg-indigo-600 hover:bg-indigo-700' }} rounded-lg">{{
                                                $s->is_submitted ? 'Batalkan' : 'Tandai Terkumpul' }}</button>
                                        </form>

                                        <form action="{{ route('report-submissions.destroy', $s) }}" method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('Hapus catatan pengumpulan rapor ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1 text-[10px] font-bold text-white bg-rose-600 rounded-lg hover:bg-rose-700">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-4 text-slate-500">Belum ada catatan pengumpulan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <form id="bulkReturnForm" action="{{ route('report-submissions.return-multiple') }}" method="POST"
                        class="hidden">
                        @csrf
                    </form>

                    <div class="mt-4">{{ $submissions->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAllSubmissions');
            const checkboxes = Array.from(document.querySelectorAll('.submission-checkbox'));
            const bulkReturnBtn = document.getElementById('bulkReturnBtn');
            const bulkForm = document.getElementById('bulkReturnForm');

            const setReturnedForm = document.getElementById('setReturnedForm');
            const setReturnedBtn = document.getElementById('setReturnedBtn');

            function updateSetReturned() {
                const any = document.querySelectorAll('#setReturnedForm .student-checkbox:checked').length > 0;
                setReturnedBtn.disabled = !any;
                setReturnedBtn.classList.toggle('opacity-50', !any);
            }

            if (setReturnedBtn) {
                document.querySelectorAll('.student-checkbox').forEach(cb => cb.addEventListener('change', updateSetReturned));
                document.querySelectorAll('.class-select-all').forEach(ch => ch.addEventListener('change', function () {
                    const id = this.dataset.classId;
                    document.querySelectorAll('.student-checkbox[data-class-id="' + id + '"]').forEach(s => s.checked = this.checked);
                    updateSetReturned();
                }));

                setReturnedBtn.addEventListener('click', function () {
                    if (!confirm('Tandai siswa terpilih sebagai dikembalikan ke sekolah?')) return;
                    setReturnedForm.submit();
                });
            }

            function updateBulk() {
                const any = document.querySelectorAll('.submission-checkbox:checked').length > 0;
                bulkReturnBtn.disabled = !any;
                bulkReturnBtn.classList.toggle('opacity-50', !any);
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    updateBulk();
                });
            }

            checkboxes.forEach(cb => cb.addEventListener('change', updateBulk));

            bulkReturnBtn.addEventListener('click', function () {
                const ids = Array.from(document.querySelectorAll('.submission-checkbox:checked')).map(i => i.value);
                if (!ids.length) return;
                if (!confirm('Tandai rapor terpilih sebagai dikembalikan?')) return;
                // attach hidden inputs
                bulkForm.querySelectorAll('input[name="submission_ids[]"]').forEach(n => n.remove());
                ids.forEach(id => {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'submission_ids[]';
                    inp.value = id;
                    bulkForm.appendChild(inp);
                });
                bulkForm.submit();
            });
        });
    </script>

</x-app-layout>