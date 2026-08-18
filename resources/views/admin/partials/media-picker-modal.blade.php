<!-- Reusable WordPress-Style Media Picker Modal -->
<div id="mediaPickerModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full h-[85vh] flex flex-col shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-red-500/10 text-red-400 flex items-center justify-center border border-red-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Media Library Picker</h3>
                    <p class="text-[11px] text-slate-400">Select an asset from your library or upload new files</p>
                </div>
            </div>

            <!-- Tabs: Library / Upload -->
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    onclick="switchMediaTab('library')"
                    id="tabBtnLibrary"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-red-600 text-white transition"
                >
                    Library
                </button>
                <button
                    type="button"
                    onclick="switchMediaTab('upload')"
                    id="tabBtnUpload"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-800 text-slate-400 hover:text-white transition"
                >
                    Upload Files
                </button>

                <button type="button" onclick="closeMediaPicker()" class="ml-2 text-slate-400 hover:text-white p-1">
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
                        class="w-full max-w-xs px-3.5 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-red-500"
                    />
                    <div id="mediaLoadingSpinner" class="text-xs text-slate-400 font-mono hidden flex items-center gap-1.5">
                        <svg class="animate-spin w-3.5 h-3.5 text-red-500" fill="none" viewBox="0 0 24 24">
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
                    class="w-full max-w-md p-10 rounded-3xl border-2 border-dashed border-slate-700 bg-slate-950/60 flex flex-col items-center justify-center space-y-3 cursor-pointer"
                    onclick="document.getElementById('modalFileInput').click();"
                >
                    <div class="w-14 h-14 rounded-2xl bg-red-500/10 text-red-400 flex items-center justify-center border border-red-500/20">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </div>
                    <div class="font-bold text-white text-sm">Select files to upload</div>
                    <p class="text-xs text-slate-400">or drag and drop files directly here</p>
                    <input type="file" id="modalFileInput" multiple accept="image/*" class="hidden" onchange="uploadModalFiles(this.files)" />
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-slate-800 bg-slate-950 flex items-center justify-between">
            <div id="mediaPickerSelectionInfo" class="text-xs text-slate-400 truncate max-w-md">
                No asset selected
            </div>

            <div class="flex items-center gap-3">
                <button
                    type="button"
                    onclick="closeMediaPicker()"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl text-xs transition"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    id="mediaPickerConfirmBtn"
                    onclick="confirmMediaSelection()"
                    disabled
                    class="px-5 py-2 bg-red-600 disabled:opacity-40 disabled:pointer-events-none hover:bg-red-500 text-white font-semibold rounded-xl text-xs shadow-lg shadow-red-600/30 transition"
                >
                    Use Selected Asset
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPickerTarget = null; // 'featured_image' or 'editor'
    let selectedMediaItem = null;
    let mediaSearchTimeout = null;

    function openMediaPicker(target = 'featured_image') {
        currentPickerTarget = target;
        selectedMediaItem = null;
        document.getElementById('mediaPickerConfirmBtn').disabled = true;
        document.getElementById('mediaPickerSelectionInfo').innerText = 'No asset selected';
        document.getElementById('mediaPickerModal').classList.remove('hidden');
        switchMediaTab('library');
        fetchMediaAssets();
    }

    function closeMediaPicker() {
        document.getElementById('mediaPickerModal').classList.add('hidden');
    }

    function switchMediaTab(tab) {
        const tabLib = document.getElementById('mediaTabLibrary');
        const tabUp = document.getElementById('mediaTabUpload');
        const btnLib = document.getElementById('tabBtnLibrary');
        const btnUp = document.getElementById('tabBtnUpload');

        if (tab === 'library') {
            tabLib.classList.remove('hidden');
            tabUp.classList.add('hidden');
            btnLib.className = "px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-red-600 text-white transition";
            btnUp.className = "px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-800 text-slate-400 hover:text-white transition";
        } else {
            tabLib.classList.add('hidden');
            tabUp.classList.remove('hidden');
            btnUp.className = "px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-red-600 text-white transition";
            btnLib.className = "px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-800 text-slate-400 hover:text-white transition";
        }
    }

    function debounceSearchMedia() {
        clearTimeout(mediaSearchTimeout);
        mediaSearchTimeout = setTimeout(() => {
            fetchMediaAssets();
        }, 300);
    }

    async function fetchMediaAssets() {
        const query = document.getElementById('mediaPickerSearch').value;
        const spinner = document.getElementById('mediaLoadingSpinner');
        const grid = document.getElementById('mediaPickerGrid');

        spinner.classList.remove('hidden');

        try {
            const res = await fetch(`/admin/media?json=1&type=images&q=${encodeURIComponent(query)}`);
            const data = await res.json();

            grid.innerHTML = '';

            if (!data.data || data.data.length === 0) {
                grid.innerHTML = '<div class="col-span-full py-8 text-center text-xs text-slate-500">No media assets found. Upload one!</div>';
                return;
            }

            data.data.forEach(item => {
                const el = document.createElement('div');
                el.className = "group relative aspect-square rounded-xl overflow-hidden bg-slate-950 border border-slate-800 hover:border-red-500 cursor-pointer transition";
                el.innerHTML = `
                    <img src="${item.url}" alt="${item.alt_text || item.original_name}" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-red-600/20 opacity-0 transition selection-overlay"></div>
                `;
                el.onclick = () => selectMediaGridItem(item, el);
                grid.appendChild(el);
            });
        } catch (err) {
            console.error('Error loading media:', err);
        } finally {
            spinner.classList.add('hidden');
        }
    }

    function selectMediaGridItem(item, element) {
        selectedMediaItem = item;
        document.querySelectorAll('#mediaPickerGrid > div').forEach(el => {
            el.classList.remove('ring-2', 'ring-red-500', 'border-red-500');
        });
        element.classList.add('ring-2', 'ring-red-500', 'border-red-500');

        document.getElementById('mediaPickerSelectionInfo').innerHTML = `
            Selected: <span class="font-bold text-white">${item.original_name}</span> (${item.formatted_size})
        `;
        document.getElementById('mediaPickerConfirmBtn').disabled = false;
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
            // Insert image into active text editor
            insertImageIntoEditor(selectedMediaItem);
        }

        closeMediaPicker();
    }

    function updateFeaturedPreview(url) {
        const preview = document.getElementById('featuredImagePreview');
        const img = document.getElementById('featuredImagePreviewImg');
        if (preview && img && url) {
            img.src = url;
            preview.classList.remove('hidden');
        }
    }

    async function uploadModalFiles(files) {
        if (!files || files.length === 0) return;

        const formData = new FormData();
        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }

        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const res = await fetch('/admin/media', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();
            if (data.success) {
                switchMediaTab('library');
                fetchMediaAssets();
            } else {
                alert(data.error || 'Upload failed');
            }
        } catch (err) {
            alert('Upload error: ' + err.message);
        }
    }

    function handleModalDrop(e) {
        e.preventDefault();
        uploadModalFiles(e.dataTransfer.files);
    }
</script>
