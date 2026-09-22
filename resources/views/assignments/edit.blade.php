<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">Edit Tugas</h2></x-slot>
    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div x-data="assignmentEdit({{ Js::from($assignment->loadMissing(['tasks', 'groups.students'])) }})"
            class="bg-white dark:bg-slate-800 p-6 md:p-8 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <form method="POST" action="{{ route('assignments.update', $assignment) }}" class="space-y-6">
                @csrf @method('PUT')
                <div class="grid md:grid-cols-2 gap-5">
                    <div><label class="form-label">Judul tugas</label><input name="title" value="{{ old('title', $assignment->title) }}" class="form-input" required></div>
                    <div><label class="form-label">Mata pelajaran</label><select name="subject_id" class="form-input"><option value="">-- Pilih mapel --</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}" @selected(old('subject_id', $assignment->subject_id) == $subject->id)>{{ $subject->nama_mapel }}</option>@endforeach</select></div>
                    <div><label class="form-label">Batas waktu</label><input type="datetime-local" name="due_at" value="{{ old('due_at', optional($assignment->due_at)->format('Y-m-d\TH:i')) }}" class="form-input"></div>
                </div>
                <div><label class="form-label">Petunjuk umum</label><div class="editor-toolbar"><button type="button" @mousedown.prevent="richTextCommand('bold')"><b>B</b></button><button type="button" @mousedown.prevent="richTextCommand('italic')"><i>I</i></button><button type="button" @mousedown.prevent="richTextCommand('insertUnorderedList')">• List</button><button type="button" @mousedown.prevent="richTextCommand('insertOrderedList')">1. List</button></div><div contenteditable="true" x-init="$el.innerHTML = description" @input="description = $event.target.innerHTML" class="rich-editor" data-placeholder="Tulis petunjuk tugas..."></div><input type="hidden" name="description" :value="description"></div>

                <div class="border-t dark:border-slate-700 pt-5">
                    <div class="flex justify-between items-center mb-3"><div><h3 class="font-bold">Daftar item tugas</h3><p class="text-xs text-slate-500">Item yang sudah dipilih kelompok tidak dapat dihapus.</p></div><button type="button" @click="tasks.push({id: null, title: '', description: ''})" class="btn-secondary">+ Tambah tugas</button></div>
                    <div class="space-y-4">
                        <template x-for="(task, index) in tasks" :key="index">
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border dark:border-slate-700">
                                <input type="hidden" :name="`task_ids[${index}]`" x-model="task.id">
                                <div class="flex justify-between"><span class="text-xs font-bold text-slate-500">TUGAS <span x-text="index + 1"></span></span><button type="button" @click="tasks.splice(index, 1)" class="text-xs text-red-600">Hapus dari daftar</button></div>
                                <input :name="`task_titles[${index}]`" x-model="task.title" class="form-input mt-2" required>
                                <div class="editor-toolbar mt-2"><button type="button" @mousedown.prevent="richTextCommand('bold')"><b>B</b></button><button type="button" @mousedown.prevent="richTextCommand('italic')"><i>I</i></button><button type="button" @mousedown.prevent="richTextCommand('insertUnorderedList')">• List</button><button type="button" @mousedown.prevent="richTextCommand('insertOrderedList')">1. List</button></div><div contenteditable="true" x-init="$el.innerHTML = task.description" @input="task.description = $event.target.innerHTML" class="rich-editor" data-placeholder="Tulis instruksi tugas..." ></div>
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
                                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border dark:border-slate-700">
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

                <div class="flex justify-end gap-3"><a href="{{ route('assignments.show', $assignment) }}" class="btn-secondary">Batal</a><button class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-bold">Simpan perubahan</button></div>
            </form>
        </div>
    </div>
    <style>.form-label{display:block;font-size:.75rem;font-weight:700;color:#64748b;margin-bottom:.4rem}.form-input{width:100%;border-radius:.75rem;border-color:#cbd5e1;background:transparent;padding:.65rem .8rem;font-size:.875rem}.rich-editor{min-height:7rem;width:100%;border:1px solid #cbd5e1;border-radius:.75rem;padding:.65rem .8rem;font-size:.875rem;background:transparent}.rich-editor:focus{outline:2px solid #818cf8;outline-offset:1px}.rich-editor:empty:before{content:attr(data-placeholder);color:#94a3b8}.rich-editor ol{list-style:decimal;padding-left:1.5rem}.rich-editor ul{list-style:disc;padding-left:1.5rem}.btn-secondary{padding:.55rem .8rem;border-radius:.65rem;background:#e2e8f0;color:#334155;font-size:.75rem;font-weight:700}</style>
    <style>.editor-toolbar{display:flex;gap:.25rem;padding:.35rem;border:1px solid #cbd5e1;border-bottom:0;border-radius:.75rem .75rem 0 0;background:#f8fafc}.editor-toolbar button{padding:.15rem .45rem;border-radius:.35rem;color:#475569;font-size:.75rem}.rich-editor{border-radius:0 0 .75rem .75rem}</style>
    <script>
        function assignmentEdit(data) {
            return {
                type: data.type,
                description: @json(old('description', $assignment->description)),
                richTextCommand(command) {
                    document.execCommand(command, false);
                },
                students: (data.classroom && data.classroom.students) || [],
                tasks: (data.tasks || []).map(task => ({id: task.id, title: task.title, description: task.description})),
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
