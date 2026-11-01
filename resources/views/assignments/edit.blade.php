<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">Edit Tugas</h2></x-slot>
    <div class="mx-auto max-w-6xl space-y-5 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div x-data="assignmentEdit({{ Js::from($assignment->loadMissing(['tasks', 'groups.students'])) }})"
            class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-8">
            <form method="POST" action="{{ route('assignments.update', $assignment) }}" class="space-y-6">
                @csrf @method('PUT')
                <div class="grid md:grid-cols-2 gap-5">
                    <div><label class="form-label">Judul tugas</label><input name="title" value="{{ old('title', $assignment->title) }}" class="form-input" required></div>
                    <div><label class="form-label">Mata pelajaran</label><select name="subject_id" class="form-input"><option value="">-- Pilih mapel --</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}" @selected(old('subject_id', $assignment->subject_id) == $subject->id)>{{ $subject->nama_mapel }}</option>@endforeach</select></div>
                    <div><label class="form-label">Batas waktu</label><input type="datetime-local" name="due_at" value="{{ old('due_at', optional($assignment->due_at)->format('Y-m-d\TH:i')) }}" class="form-input"></div>
                </div>
                <div><label class="form-label">Petunjuk umum</label><div class="flex justify-end mb-2"><button type="button" @click="toggleDescriptionSource()" class="source-toggle" x-text="descriptionSource ? 'Visual Editor' : 'Edit Source HTML'"></button></div><div x-show="!descriptionSource" x-init="initDescriptionEditor($el)" class="quill-editor" data-placeholder="Tulis petunjuk tugas..."></div><textarea x-show="descriptionSource" x-model="description" class="source-editor" rows="7" spellcheck="false"></textarea><input type="hidden" name="description" :value="description"></div>

                <div class="border-t dark:border-slate-700 pt-5">
                    <div class="flex justify-between items-center mb-3"><div><h3 class="font-bold">Daftar item tugas</h3><p class="text-xs text-slate-500">Item yang sudah dipilih kelompok tidak dapat dihapus.</p></div><button type="button" @click="tasks.push({id: null, title: '', description: ''})" class="btn-secondary">+ Tambah tugas</button></div>
                    <div class="space-y-4">
                        <template x-for="(task, index) in tasks" :key="index">
                            <div class="p-4 rounded-md bg-slate-50 dark:bg-slate-900/40 border dark:border-slate-700">
                                <input type="hidden" :name="`task_ids[${index}]`" x-model="task.id">
                                <div class="flex justify-between"><span class="text-xs font-bold text-slate-500">TUGAS <span x-text="index + 1"></span></span><button type="button" @click="tasks.splice(index, 1)" class="text-xs text-red-600">Hapus dari daftar</button></div>
                                <input :name="`task_titles[${index}]`" x-model="task.title" class="form-input mt-2" required>
                                <div class="flex justify-end mt-2 mb-2"><button type="button" @click="toggleTaskSource(task)" class="source-toggle" x-text="task.sourceMode ? 'Visual Editor' : 'Edit Source HTML'"></button></div><div x-show="!task.sourceMode" x-init="initTaskEditor($el, task)" class="quill-editor" data-placeholder="Tulis instruksi tugas..." ></div><textarea x-show="task.sourceMode" x-model="task.description" class="source-editor" rows="7" spellcheck="false"></textarea>
                                <input type="hidden" :name="`task_descriptions[${index}]`" :value="task.description">
                            </div>
                        </template>
                    </div>
                </div>

                <template x-if="type === 'group'">
                    <div class="border-t dark:border-slate-700 pt-5">
                        <div class="flex justify-between items-center mb-3"><div><h3 class="font-bold">Kelompok</h3><p class="text-xs text-slate-500">Kelompok yang sudah memilih tugas tidak dapat dihapus.</p></div><button type="button" @click="addGroup()" class="btn-secondary">+ Tambah kelompok</button></div>
                        <div class="space-y-4">
                            <template x-for="(group, groupIndex) in groups" :key="groupIndex">
                                <div class="p-4 rounded-md bg-slate-50 dark:bg-slate-900/40 border dark:border-slate-700">
                                    <input type="hidden" :name="`groups[${groupIndex}][id]`" x-model="group.id">
                                    <div class="flex justify-between"><input :name="`groups[${groupIndex}][name]`" x-model="group.name" class="form-input" required><button type="button" @click="groups.splice(groupIndex, 1)" class="text-xs text-red-600 ml-3">Hapus</button></div>
                                    <label class="form-label mt-3">Ketua</label>
                                    <select :name="`groups[${groupIndex}][leader_student_id]`" x-model="group.leader_student_id" @change="setLeader(groupIndex)" class="form-input" required><option value="">-- Pilih ketua --</option><template x-for="student in availableStudents(groupIndex)" :key="student.id"><option :value="student.id" x-text="student.nama_lengkap"></option></template></select>
                                    <label class="form-label mt-3">Anggota</label>
                                    <select :name="`groups[${groupIndex}][student_ids][]`" x-model="group.student_ids" class="form-input" multiple size="5" required><template x-for="student in availableStudents(groupIndex)" :key="student.id"><option :value="student.id" x-text="student.nama_lengkap"></option></template></select>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 dark:border-slate-700 sm:flex-row sm:justify-end"><a href="{{ route('assignments.show', $assignment) }}" class="btn-secondary">Batal</a><button class="min-h-11 rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">Simpan perubahan</button></div>
            </form>
        </div>
    </div>
    <style>.form-label{display:block;font-size:.875rem;font-weight:600;color:#334155;margin-bottom:.4rem}.dark .form-label{color:#e2e8f0}.form-input{width:100%;border:1px solid #cbd5e1;border-radius:.375rem;background:transparent;padding:.65rem .8rem;font-size:.875rem}.form-input:focus,.source-editor:focus{outline:2px solid #2f725d;outline-offset:2px}.rich-editor{min-height:7rem;width:100%;border:1px solid #cbd5e1;border-radius:.375rem;padding:.65rem .8rem;font-size:.875rem;background:transparent}.rich-editor:focus{outline:2px solid #2f725d;outline-offset:2px}.rich-editor:empty:before{content:attr(data-placeholder);color:#94a3b8}.rich-editor ol{list-style:decimal;padding-left:1.5rem}.rich-editor ul{list-style:disc;padding-left:1.5rem}.btn-secondary{display:inline-flex;min-height:2.75rem;align-items:center;justify-content:center;padding:.55rem .8rem;border:1px solid #cbd5e1;border-radius:.375rem;background:#fff;color:#334155;font-size:.875rem;font-weight:600}.dark .btn-secondary{border-color:#475569;background:#1e293b;color:#e2e8f0}</style>
    <style>.editor-toolbar{display:flex;flex-wrap:wrap;gap:.25rem;padding:.35rem;border:1px solid #cbd5e1;border-bottom:0;border-radius:.375rem .375rem 0 0;background:#f8fafc}.editor-toolbar button{min-height:2rem;padding:.15rem .45rem;border-radius:.25rem;color:#475569;font-size:.75rem}.source-toggle{margin-left:auto;font-weight:600;color:#255b4b!important}.rich-editor{border-radius:0 0 .375rem .375rem}.source-editor{width:100%;border:1px solid #cbd5e1;border-radius:0 0 .375rem .375rem;padding:.65rem .8rem;font:.8rem/1.5 ui-monospace,SFMono-Regular,Consolas,monospace;background:#0f172a;color:#e2e8f0}</style>
    <script>
        function assignmentEdit(data) {
            return {
                type: data.type,
                description: @json(old('description', $assignment->description)),
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
                students: (data.classroom && data.classroom.students) || [],
                tasks: (data.tasks || []).map(task => ({id: task.id, title: task.title, description: task.description, sourceMode: false})),
                groups: (data.groups || []).map(group => ({id: group.id, name: group.name, leader_student_id: String(group.leader_student_id), student_ids: group.students.map(student => String(student.id))})),
                availableStudents(groupIndex) {
                    const used = this.groups.filter((group, index) => index !== groupIndex).flatMap(group => group.student_ids || []).map(id => String(id));
                    return this.students.filter(student => !used.includes(String(student.id)));
                },
                setLeader(groupIndex) {
                    const group = this.groups[groupIndex];
                    group.student_ids = [...new Set([...(group.student_ids || []), String(group.leader_student_id)])];
                },
                addGroup() {
                    this.groups.push({id: null, name: `Kelompok ${this.groups.length + 1}`, leader_student_id: '', student_ids: []});
                }
            };
        }
    </script>
</x-app-layout>
