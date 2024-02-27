import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

ClassicEditor
    .create(document.querySelector('#editor'), {
        readOnly: true
    }).then(editor => {
        editor.isReadOnly = true; // make the editor read-only right after initialization
    }).catch(error => {
        console.error(error);
    });
