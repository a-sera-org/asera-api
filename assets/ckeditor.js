import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

ClassicEditor.create(document.querySelector('#jobDescription'))
    .then(editor => {
        editor.isReadOnly = true
    })
    .catch(error => {
        console.error(error);
    });

ClassicEditor.create(document.querySelector('#entrepriseDescription'), {
    readOnly: true,
}).catch(error => {
    console.error(error);
});
