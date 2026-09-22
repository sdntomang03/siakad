import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const toolbar = [
    [{ header: [2, 3, 4, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    [{ list: 'ordered' }, { list: 'bullet' }],
    ['blockquote', 'link'],
    ['clean'],
];

window.createAssignmentQuill = (element, initialValue, onChange) => {
    const editor = new Quill(element, {
        theme: 'snow',
        modules: { toolbar },
        placeholder: element.dataset.placeholder || 'Tulis isi tugas...',
    });

    editor.root.innerHTML = initialValue || '';
    editor.on('text-change', () => onChange(editor.root.innerHTML));

    return editor;
};
