<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">Buat Tugas</h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div x-data="assignmentForm({{ Js::from($classrooms) }})"
            class="bg-white dark:bg-slate-800 p-6 md:p-8 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <form action="{{ route('assignments.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="form-label">Kelas</label>
                        <select name="classroom_id" x-model="classroomId" class="form-input" required>
                            <option value="">-- Pilih kelas --</option>
                            @foreach($classrooms as $classroom)
                                <option value="{{ $classroom->id }}">{{ $classroom->tingkat }} - {{ $classroom->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Mata Pelajaran</label>
                        <select name="subject_id" class="form-input">
                            <option value="">-- Pilih mapel --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Judul tugas</label>
                        <input name="title" class="form-input" required maxlength="255">
                    </div>
                    <div>
                        <label class="form-label">Batas waktu (opsional)</label>
                        <input type="datetime-local" name="due_at" class="form-input">
                    </div>
                </div>
                <div>
                    <label class="form-label">Petunjuk umum</label>
                    <div class="flex justify-end mb-2"><button type="button" @click="toggleDescriptionSource()" class="source-toggle" x-text="descriptionSource ? 'Visual Editor' : 'Edit Source HTML'"></button></div>
                    <div x-show="!descriptionSource" x-init="initDescriptionEditor($el)" class="quill-editor" data-placeholder="Tulis petunjuk tugas..."></div>
                    <textarea x-show="descriptionSource" x-model="description" class="source-editor" rows="7" spellcheck="false" placeholder="<p>Tulis HTML petunjuk tugas...</p>"></textarea>
                    <input type="hidden" name="description" :value="description">
                </div>
                <div>
                    <label class="form-label">Jenis tugas</label>
                    <div class="flex gap-6 mt-2">
                        <label><input type="radio" name="type" value="individual" x-model="type" required> Individu</label>
                        <label><input type="radio" name="type" value="group" x-model="type"> Kelompok</label>
                    </div>
                </div>

                <div class="border-t dark:border-slate-700 pt-5">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-white">Daftar tugas yang tersedia</h3>
                            <p class="text-xs text-slate-500">Untuk tugas kelompok, setiap kelompok akan mengambil satu tugas yang belum pernah dipilih.</p>
                        </div>
                        <button type="button" @click="tasks.push({title: '', description: '', sourceMode: false})" class="btn-secondary">+ Tambah tugas</button>
                    </div>
                    <div class="space-y-4">
                        <template x-for="(task, index) in tasks" :key="index">
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border dark:border-slate-700">
                                <div class="flex justify-between gap-4">
                                    <span class="text-xs font-bold text-slate-500">TUGAS <span x-text="index + 1"></span></span>
                                    <button type="button" x-show="tasks.length > 1" @click="tasks.splice(index, 1)" class="text-xs text-red-600">Hapus</button>
                                </div>
                                <input :name="`task_titles[${index}]`" x-model="task.title" class="form-input mt-2" placeholder="Judul tugas" required>
                                <div class="flex justify-end mt-2 mb-2"><button type="button" @click="toggleTaskSource(task)" class="source-toggle" x-text="task.sourceMode ? 'Visual Editor' : 'Edit Source HTML'"></button></div><div x-show="!task.sourceMode" x-init="initTaskEditor($el, task)" class="quill-editor" data-placeholder="Tulis instruksi tugas..."></div><textarea x-show="task.sourceMode" x-model="task.description" class="source-editor" rows="7" spellcheck="false" placeholder="<p>Tulis HTML instruksi tugas...</p>"></textarea>
                                <input type="hidden" :name="`task_descriptions[${index}]`" :value="task.description">
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="type === 'group'" x-transition class="border-t dark:border-slate-700 pt-5">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-white">Pembagian kelompok</h3>
                            <p class="text-xs text-slate-500">Pilih anggota dan ketua. Anggota akan melihat tugas serta seluruh anggota kelompoknya.</p>
                        </div>
                        <button type="button" @click="addGroup()" class="btn-secondary">+ Tambah kelompok</button>
                    </div>
                    <template x-if="classroomStudents.length === 0">
                        <p class="text-sm text-amber-600">Pilih kelas untuk menampilkan siswa.</p>
                    </template>
                    <div class="space-y-4">
                        <template x-for="(group, groupIndex) in groups" :key="groupIndex">
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border dark:border-slate-700">
                                <div class="flex justify-between gap-4 mb-3">
                                    <input :name="`groups[${groupIndex}][name]`" x-model="group.name" class="form-input" placeholder="Nama kelompok" required>
                                    <button type="button" x-show="groups.length > 1" @click="groups.splice(groupIndex, 1)" class="text-xs text-red-600">Hapus</button>
                                </div>
                                <label class="form-label">Ketua</label>
                                <select :name="`groups[${groupIndex}][leader_student_id]`" x-model="group.leader_student_id" @change="setLeader(groupIndex)" class="form-input mb-3" required>
                                    <option value="">-- Pilih ketua --</option>
                                    <template x-for="student in availableStudents(groupIndex)" :key="student.id">
                                        <option :value="student.id" x-text="student.nama_lengkap"></option>
                                    </template>
                                </select>
                                <label class="form-label">Anggota</label>
                                <select :name="`groups[${groupIndex}][student_ids][]`" x-model="group.student_ids" class="form-input" multiple size="5" required>
                                    <template x-for="student in availableStudents(groupIndex)" :key="student.id">
                                        <option :value="student.id" x-text="student.nama_lengkap"></option>
                                    </template>
                                </select>
                                <p class="text-xs text-slate-500 mt-2">Ketua otomatis ditambahkan sebagai anggota. Siswa yang sudah masuk kelompok lain tidak tersedia.</p>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:bg-indigo-700">Simpan tugas</button>
                </div>
            </form>
        </div>
    </div>
    <style>
        .form-label { display:block; font-size:.75rem; font-weight:700; color:#64748b; margin-bottom:.4rem; text-transform:uppercase; }
        .source-toggle { font-size:.75rem; font-weight:700; color:#4f46e5; }
        .source-editor { width:100%; border:1px solid #cbd5e1; border-radius:.75rem; padding:.65rem .8rem; font: .8rem/1.5 ui-monospace, SFMono-Regular, Consolas, monospace; background:#0f172a; color:#e2e8f0; }
        .quill-editor { min-height:8rem; }
        .form-input { width:100%; border-radius:.75rem; border-color:#cbd5e1; background:transparent; padding:.65rem .8rem; font-size:.875rem; }
        .btn-secondary { padding:.55rem .8rem; border-radius:.65rem; background:#e2e8f0; color:#334155; font-size:.75rem; font-weight:700; }
    </style>
    <script>
        function assignmentForm(classrooms) {
            return {
                classrooms,
                classroomId: '',
                type: 'individual',
                description: '',
                descriptionSource: false,
                initDescriptionEditor(element) {
                    this.descriptionQuill = window.createAssignmentQuill(element, this.description, value => this.description = value);
                },
                initTaskEditor(element, task) {
                    task.quill = window.createAssignmentQuill(element, task.description, value => task.description = value);
                },
                toggleDescriptionSource() {
                    if (!this.descriptionSource) {
                        this.description = this.descriptionQuill.root.innerHTML;
                    } else {
                        this.descriptionQuill.root.innerHTML = this.description || '';
                    }
                    this.descriptionSource = !this.descriptionSource;
                },
                toggleTaskSource(task) {
                    if (!task.sourceMode) {
                        task.description = task.quill.root.innerHTML;
                    } else {
                        task.quill.root.innerHTML = task.description || '';
                    }
                    task.sourceMode = !task.sourceMode;
                },
                tasks: [{title: '', description: '', sourceMode: false}],
                groups: [{name: 'Kelompok 1', leader_student_id: '', student_ids: []}],
                get classroomStudents() {
                    return (this.classrooms.find(item => String(item.id) === String(this.classroomId)) || {}).students || [];
                },
                availableStudents(groupIndex) {
                    const assignedToOtherGroups = this.groups
                        .filter((group, index) => index !== groupIndex)
                        .flatMap(group => group.student_ids || [])
                        .map(id => String(id));

                    return this.classroomStudents.filter(student => !assignedToOtherGroups.includes(String(student.id)));
                },
                setLeader(groupIndex) {
                    const group = this.groups[groupIndex];
                    const leaderId = String(group.leader_student_id || '');

                    if (!leaderId) {
                        return;
                    }

                    group.student_ids = [...new Set([
                        ...(group.student_ids || []).map(id => String(id)),
                        leaderId,
                    ])];
                },
                addGroup() {
                    this.groups.push({name: `Kelompok ${this.groups.length + 1}`, leader_student_id: '', student_ids: []});
                }
            }
        }
    </script>
</x-app-layout>
