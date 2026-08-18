let globalEditorInstance = null;

document.addEventListener('DOMContentLoaded', () => {
    const editorEl = document.querySelector('#editorContent');
    if (!editorEl) return;

    ClassicEditor
        .create(editorEl, {
            toolbar: {
                items: [
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    'underline',
                    'strikethrough',
                    'code',
                    '|',
                    'bulletedList',
                    'numberedList',
                    'blockQuote',
                    'codeBlock',
                    'insertTable',
                    '|',
                    'link',
                    'horizontalLine',
                    '|',
                    'undo',
                    'redo'
                ],
                shouldNotGroupWhenFull: true
            },
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                ]
            }
        })
        .then(editor => {
            globalEditorInstance = editor;

            // Sync on change to hidden content input
            editor.model.document.on('change:data', () => {
                const contentInput = document.getElementById('content');
                if (contentInput) {
                    contentInput.value = editor.getData();
                }
            });

            // Set initial data
            const contentInput = document.getElementById('content');
            if (contentInput && contentInput.value) {
                editor.setData(contentInput.value);
            }
        })
        .catch(error => {
            console.error('CKEditor initialization error:', error);
        });
});

/**
 * Global function called by the Media Picker modal to insert an image into CKEditor.
 */
function insertImageIntoEditor(mediaItem) {
    if (!globalEditorInstance || !mediaItem) return;

    const imgAlt = mediaItem.alt_text ? ` alt="${mediaItem.alt_text}"` : '';
    const figCaption = mediaItem.caption ? `<figcaption class="text-xs text-center text-slate-400 mt-2">${mediaItem.caption}</figcaption>` : '';
    const imageHtml = `
        <figure class="my-6 rounded-2xl overflow-hidden">
            <img src="${mediaItem.url}"${imgAlt} class="w-full rounded-2xl shadow-lg" />
            ${figCaption}
        </figure>
    `;

    const viewFragment = globalEditorInstance.data.processor.toView(imageHtml);
    const modelFragment = globalEditorInstance.data.toModel(viewFragment);
    globalEditorInstance.model.insertContent(modelFragment);

    // Also update hidden form content input
    const contentInput = document.getElementById('content');
    if (contentInput) {
        contentInput.value = globalEditorInstance.getData();
    }
}
