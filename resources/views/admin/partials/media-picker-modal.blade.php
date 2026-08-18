<!-- Reusable WordPress-Style Media Picker Modal -->
<div id="mediaPickerModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden flex items-center justify-center p-4 text-slate-900">
    <div class="bg-white border border-slate-200 rounded-3xl max-w-4xl w-full h-[85vh] flex flex-col shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Media Library Picker</h3>
                    <p class="text-[11px] text-slate-500">Select an asset from your library or upload new files</p>
                </div>
            </div>

            <!-- Tabs: Library / Upload -->
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    onclick="switchMediaTab('library')"
                    id="tabBtnLibrary"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-emerald-600 text-white transition shadow-2xs"
                >
                    Library
                </button>
                <button
                    type="button"
                    onclick="switchMediaTab('upload')"
                    id="tabBtnUpload"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:text-slate-900 transition"
                >
                    Upload Files
                </button>

                <button type="button" onclick="closeMediaPicker()" class="ml-2 text-slate-400 hover:text-slate-700 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body Content -->
        <div class="flex-1 overflow-hidden flex flex-col">
            <!-- TAB 1: Library Grid -->
            <div id="mediaTabLibrary" class="flex-1 p-5 overflow-y-auto space-y-4">
                <!-- Search & Filters in Modal -->
                <div class="flex items-center justify-between gap-4">
                    <input
                        type="text"
                        id="mediaPickerSearch"
                        oninput="debounceSearchMedia()"
                        placeholder="Search media files..."
                        class="w-full max-w-xs px-3.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500"
                    />
                    <div id="mediaLoadingSpinner" class="text-xs text-slate-500 font-mono hidden flex items-center gap-1.5">
                        <svg class="animate-spin w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Loading assets...</span>
                    </div>
                </div>

                <!-- Grid items container -->
                <div id="mediaPickerGrid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>

            <!-- TAB 2: Drag & Drop Upload in Modal -->
            <div id="mediaTabUpload" class="flex-1 p-8 hidden flex flex-col items-center justify-center text-center">
                <div
                    ondrop="handleModalDrop(event)"
                    ondragover="event.preventDefault();"
                    class="w-full max-w-md p-10 rounded-3xl border-2 border-dashed border-slate-300 bg-slate-50 flex flex-col items-center justify-center space-y-3 cursor-pointer hover:border-emerald-500 transition"
                    onclick="document.getElementById('modalFileInput').click();"
                >
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </div>
                    <div class="font-bold text-slate-900 text-sm">Select files to upload</div>
                    <p class="text-xs text-slate-500">or drag and drop files directly here</p>
                    <input type="file" id="modalFileInput" multiple accept="image/*" class="hidden" onchange="uploadModalFiles(this.files)" />
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
            <div id="mediaPickerSelectionInfo" class="text-xs text-slate-500 truncate max-w-md">
                No asset selected
            </div>

            <div class="flex items-center gap-3">
                <button
                    type="button"
                    onclick="closeMediaPicker()"
                    class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 transition"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    id="btnConfirmMediaSelect"
                    disabled
                    onclick="confirmMediaSelection()"
                    class="px-5 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed text-white shadow-xs transition"
                >
                    Select Asset
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPickerTarget = null;
    let selectedMediaItem = null;
    let mediaSearchTimeout = null;

    function openMediaPicker(target) {
        currentPickerTarget = target;
        selectedMediaItem = null;
        updateSelectionUi();
        switchMediaTab('library');
        loadMediaLibrary();
        document.getElementById('mediaPickerModal').classList.remove('hidden');
    }

    function closeMediaPicker() {
        document.getElementById('mediaPickerModal').classList.add('hidden');
        currentPickerTarget = null;
        selectedMediaItem = null;
    }

    function switchMediaTab(tab) {
        const tabLib = document.getElementById('mediaTabLibrary');
        const tabUpload = document.getElementById('mediaTabUpload');
        const btnLib = document.getElementById('tabBtnLibrary');
        const btnUpload = document.getElementById('tabBtnUpload');

        if (tab === 'library') {
            tabLib.classList.remove('hidden');
            tabUpload.classList.add('hidden');
            btnLib.className = 'px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-emerald-600 text-white transition shadow-2xs';
            btnUpload.className = 'px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:text-slate-900 transition';
        } else {
            tabLib.classList.add('hidden');
            tabUpload.classList.remove('hidden');
            btnLib.className = 'px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:text-slate-900 transition';
            btnUpload.className = 'px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-emerald-600 text-white transition shadow-2xs';
        }
    }

    function debounceSearchMedia() {
        clearTimeout(mediaSearchTimeout);
        mediaSearchTimeout = setTimeout(() => {
            const q = document.getElementById('mediaPickerSearch').value;
            loadMediaLibrary(q);
        }, 300);
    }

    async function loadMediaLibrary(query = '') {
        const spinner = document.getElementById('mediaLoadingSpinner');
        const grid = document.getElementById('mediaPickerGrid');
        spinner.classList.remove('hidden');

        try {
            const url = new URL('/admin/media/picker-list', window.location.origin);
            if (query) url.searchParams.set('q', query);

            const res = await fetch(url.toString(), {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            grid.innerHTML = '';
            if (data.data.length === 0) {
                grid.innerHTML = '<div class="col-span-full py-8 text-center text-slate-400 text-xs">No media files found</div>';
                return;
            }

            data.data.forEach(item => {
                const el = document.createElement('div');
                el.className = `group relative rounded-xl overflow-hidden aspect-square bg-slate-100 border ${selectedMediaItem?.id === item.id ? 'border-emerald-500 ring-2 ring-emerald-500' : 'border-slate-200'} hover:border-emerald-400 cursor-pointer transition`;
                el.onclick = () => selectMediaItem(item, el);

                if (item.is_image) {
                    el.innerHTML = `<img src="${item.url}" alt="${item.alt_text || ''}" class="w-full h-full object-cover" />`;
                } else {
                    el.innerHTML = `<div class="w-full h-full flex flex-col items-center justify-center p-2 text-center text-slate-500 font-mono text-[10px]"><span class="truncate w-full">${item.original_name}</span></div>`;
                }

                grid.appendChild(el);
            });
        } catch (err) {
            console.error('Failed to load media list', err);
        } finally {
            spinner.classList.add('hidden');
        }
    }

    function selectMediaItem(item, element) {
        selectedMediaItem = item;

        document.querySelectorAll('#mediaPickerGrid > div').forEach(div => {
            div.classList.remove('border-emerald-500', 'ring-2', 'ring-emerald-500');
            div.classList.add('border-slate-200');
        });

        element.classList.remove('border-slate-200');
        element.classList.add('border-emerald-500', 'ring-2', 'ring-emerald-500');

        updateSelectionUi();
    }

    function updateSelectionUi() {
        const info = document.getElementById('mediaPickerSelectionInfo');
        const btn = document.getElementById('btnConfirmMediaSelect');

        if (selectedMediaItem) {
            info.innerHTML = `<span class="font-bold text-slate-900">${selectedMediaItem.original_name}</span> &bull; <span class="font-mono text-slate-500">${selectedMediaItem.formatted_size}</span>`;
            btn.disabled = false;
        } else {
            info.innerText = 'No asset selected';
            btn.disabled = true;
        }
    }

    function confirmMediaSelection() {
        if (!selectedMediaItem) return;

        if (currentPickerTarget === 'featured_image') {
            const input = document.getElementById('featured_image');
            if (input) {
                input.value = selectedMediaItem.url;
                updateFeaturedPreview(selectedMediaItem.url);
            }
        } else if (currentPickerTarget === 'editor') {
            if (window.editorInstance) {
                window.editorInstance.model.change(writer => {
                    const imageElement = writer.createElement('imageBlock', {
                        src: selectedMediaItem.url,
                        alt: selectedMediaItem.alt_text || selectedMediaItem.original_name
                    });
                    window.editorInstance.model.insertContent(imageElement, window.editorInstance.model.document.selection);
                });
            }
        }

        closeMediaPicker();
    }

    function updateFeaturedPreview(url) {
        const container = document.getElementById('featuredImagePreview');
        const img = document.getElementById('featuredImagePreviewImg');
        if (!container || !img) return;

        if (url && url.trim().length > 0) {
            img.src = url;
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    function handleModalDrop(e) {
        e.preventDefault();
        uploadModalFiles(e.dataTransfer.files);
    }

    async function uploadModalFiles(files) {
        if (!files || files.length === 0) return;

        const formData = new FormData();
        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const res = await fetch('/admin/media', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (res.ok) {
                switchMediaTab('library');
                loadMediaLibrary();
            } else {
                alert('File upload failed. Check format and size limits.');
            }
        } catch (err) {
            console.error(err);
            alert('Upload request failed.');
        }
    }
</script>
