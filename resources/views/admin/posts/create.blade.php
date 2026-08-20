@extends('layouts.app')

@section('title', 'Write New Blog Article')

@section('layout')
<div class="min-h-screen bg-slate-50 flex flex-col lg:flex-row text-slate-900">
    @include('admin.partials.sidebar')

    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <header class="p-4 sm:p-6 border-b border-slate-200 bg-white/90 backdrop-blur-md sticky top-0 z-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.posts.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Create Article</h1>
                    <p class="text-xs text-slate-500">Write and publish an engineering article</p>
                </div>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8 w-full">
            @if ($errors->any())
                <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-xs text-rose-800">
                    <div class="font-bold mb-1">Please fix the following validation errors:</div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.posts.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left 2 Cols: Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Title -->
                        <div class="glass-panel rounded-3xl p-6 border border-slate-200 bg-white shadow-xs space-y-4">
                            <div>
                                <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                                    Article Title *
                                </label>
                                <input
                                    type="text"
                                    name="title"
                                    id="title"
                                    value="{{ old('title') }}"
                                    required
                                    placeholder="e.g. Scaling Laravel 12 on Modern Cloud Infrastructure"
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:bg-white rounded-xl text-base text-slate-900 placeholder-slate-400 focus:outline-none transition"
                                />
                            </div>

                            <div>
                                <label for="slug" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
                                    Custom URL Slug (Optional)
                                </label>
                                <input
                                    type="text"
                                    name="slug"
                                    id="slug"
                                    value="{{ old('slug') }}"
                                    placeholder="scaling-laravel-12-modern-cloud"
                                    class="w-full px-4 py-2 bg-slate-50 border border-slate-200 font-mono text-xs text-slate-800 rounded-xl focus:outline-none focus:border-emerald-500 transition"
                                />
                            </div>

                            <div>
                                <label for="excerpt" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                                    Short Summary / Excerpt
                                </label>
                                <textarea
                                    name="excerpt"
                                    id="excerpt"
                                    rows="2"
                                    placeholder="Brief introduction displayed on cards and search results..."
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition"
                                >{{ old('excerpt') }}</textarea>
                            </div>
                        </div>

                        <!-- CKEditor 5 Rich Text Editor -->
                        <div class="glass-panel rounded-3xl p-6 border border-slate-200 bg-white shadow-xs space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                                    Article Content (Rich Text & Media) *
                                </label>

                                <button
                                    type="button"
                                    onclick="openMediaPicker('editor')"
                                    class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold flex items-center gap-1.5 transition border border-slate-200"
                                >
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Insert Media from Library</span>
                                </button>
                            </div>

                            <!-- Hidden Textarea synced with CKEditor -->
                            <textarea name="content" id="content" class="hidden">{{ old('content') }}</textarea>
                            <div id="editorContent">{!! old('content') !!}</div>
                        </div>

                        <!-- SEO Meta & Google SERP Snippet Preview (Wide Layout Below Editor) -->
                        <div class="glass-panel rounded-3xl p-6 border border-slate-200 bg-white shadow-xs space-y-5">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <div>
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        <span>Search Engine Optimization & Google Search Preview</span>
                                    </h3>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Customize meta titles, search snippets, and canonical links to maximize Google search ranking.</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                    SEO Ready
                                </span>
                            </div>

                            <!-- Live Google SERP Snippet Card -->
                            <div class="rounded-2xl p-4 bg-slate-50 border border-slate-200 text-left space-y-1.5 shadow-2xs">
                                <div class="text-xs text-slate-500 flex items-center gap-1.5 truncate font-sans">
                                    <span class="w-4 h-4 rounded-full bg-slate-200 flex items-center justify-center text-[9px] font-bold text-slate-600">G</span>
                                    <span>https://sejan.dev › posts › <span id="serpPreviewSlug" class="font-medium text-slate-700">article-slug</span></span>
                                </div>
                                <div id="serpPreviewTitle" class="text-base font-semibold text-blue-700 hover:underline cursor-pointer truncate font-sans">
                                    Article Title — sejan.dev
                                </div>
                                <div id="serpPreviewDesc" class="text-xs text-slate-600 leading-relaxed line-clamp-2 font-sans">
                                    Detailed engineering breakdown, architectural overview, and technical implementation guide.
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <label for="meta_title" class="text-xs font-semibold text-slate-700">Custom Meta Title</label>
                                        <span class="text-[10px] text-slate-400 font-mono"><span id="metaTitleCount">{{ strlen(old('meta_title', '')) }}</span>/60</span>
                                    </div>
                                    <input
                                        type="text"
                                        name="meta_title"
                                        id="meta_title"
                                        value="{{ old('meta_title') }}"
                                        placeholder="Defaults to article title if left empty"
                                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-emerald-500"
                                    />
                                </div>

                                <div>
                                    <label for="canonical_url" class="block text-xs font-semibold text-slate-700 mb-1">Canonical URL</label>
                                    <input
                                        type="url"
                                        name="canonical_url"
                                        id="canonical_url"
                                        value="{{ old('canonical_url') }}"
                                        placeholder="https://sejan.dev/posts/..."
                                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 font-mono focus:outline-none focus:border-emerald-500"
                                    />
                                </div>

                                <div class="md:col-span-2">
                                    <div class="flex justify-between items-center mb-1">
                                        <label for="meta_description" class="text-xs font-semibold text-slate-700">Meta Description</label>
                                        <span class="text-[10px] text-slate-400 font-mono"><span id="metaDescCount">{{ strlen(old('meta_description', '')) }}</span>/160</span>
                                    </div>
                                    <textarea
                                        name="meta_description"
                                        id="meta_description"
                                        rows="2"
                                        placeholder="Brief summary optimized for search results (defaults to excerpt or article content)"
                                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-emerald-500"
                                    >{{ old('meta_description') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Col: Sidebar Settings -->
                    <div class="space-y-6">
                        <!-- Widget 1: Publishing & Scheduling -->
                        <div class="glass-panel rounded-3xl p-6 border border-slate-200 bg-white shadow-xs space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-1.5 border-b border-slate-100 pb-3">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Publishing & Status</span>
                            </h3>

                            <div class="space-y-3 text-xs">
                                <div>
                                    <label class="block text-slate-600 font-medium mb-1">Status</label>
                                    <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-emerald-500">
                                        <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Published (Live on site)</option>
                                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Private)</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="published_at" class="block text-slate-600 font-medium mb-1">Publication Timestamp</label>
                                    <input
                                        type="datetime-local"
                                        name="published_at"
                                        id="published_at"
                                        value="{{ old('published_at') }}"
                                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-emerald-500 font-mono"
                                    />
                                    <p class="text-[10px] text-slate-400 mt-1">Leave empty to set current time upon publishing.</p>
                                </div>

                                <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                                    <label for="is_featured" class="flex items-center gap-2 cursor-pointer text-xs text-slate-700 font-medium">
                                        <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="rounded border-slate-300 text-emerald-600 focus:ring-0">
                                        <span>Pin as Featured Article</span>
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-2.5 px-4 bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-95 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-500/20 transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Save & Create Article</span>
                            </button>
                        </div>

                        <!-- Widget 2: Live Article Metrics & Stats -->
                        <div class="glass-panel rounded-3xl p-6 border border-slate-200 bg-white shadow-xs space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-1.5 border-b border-slate-100 pb-3">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span>Live Content Metrics</span>
                            </h3>

                            <div class="grid grid-cols-2 gap-2 text-center">
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <div id="metricWordCount" class="text-base font-extrabold text-slate-900 font-mono">0</div>
                                    <div class="text-[10px] text-slate-500 uppercase">Words</div>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <div id="metricReadingTime" class="text-xs font-extrabold text-emerald-700 font-mono mt-0.5">1 min</div>
                                    <div class="text-[10px] text-slate-500 uppercase mt-0.5">Estimated Read</div>
                                </div>
                            </div>
                        </div>

                        <!-- Widget 4: Featured Image -->
                        <div class="glass-panel rounded-3xl p-6 border border-slate-200 bg-white shadow-xs space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <label for="featured_image" class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Featured Cover</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        onclick="document.getElementById('directFeaturedFileInput').click()"
                                        class="text-xs text-slate-600 hover:text-emerald-700 font-semibold flex items-center gap-1 transition cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        <span>Upload File</span>
                                    </button>
                                    <span class="text-slate-300">|</span>
                                    <button
                                        type="button"
                                        onclick="openMediaPicker('featured_image')"
                                        class="text-xs text-emerald-700 hover:text-emerald-900 font-semibold flex items-center gap-1 transition"
                                    >
                                        Browse Library
                                    </button>
                                </div>
                            </div>

                            <input type="file" id="directFeaturedFileInput" accept="image/*" class="hidden" onchange="handleDirectFeaturedUpload(this.files)" />

                            <div class="flex gap-2">
                                <input
                                    type="url"
                                    name="featured_image"
                                    id="featured_image"
                                    value="{{ old('featured_image') }}"
                                    placeholder="Image URL or upload file..."
                                    oninput="updateFeaturedPreview(this.value)"
                                    class="flex-1 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 font-mono focus:outline-none focus:border-emerald-500"
                                />
                                <button
                                    type="button"
                                    onclick="clearFeaturedImage()"
                                    title="Remove Image"
                                    class="p-1.5 rounded-xl bg-slate-100 hover:bg-red-50 text-slate-500 hover:text-red-600 transition"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>

                            <div id="featuredImagePreview" class="{{ old('featured_image') ? '' : 'hidden' }} rounded-2xl overflow-hidden aspect-[16/9] bg-slate-100 border border-slate-200 shadow-2xs">
                                <img id="featuredImagePreviewImg" src="{{ old('featured_image') }}" alt="" class="w-full h-full object-cover" />
                            </div>
                        </div>

                        <!-- Widget 5: Categories -->
                        <div class="glass-panel rounded-3xl p-6 border border-slate-200 bg-white shadow-xs space-y-3">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-1.5 border-b border-slate-100 pb-3">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <span>Categories</span>
                            </h3>
                            <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                                @foreach ($categories as $cat)
                                    <label class="flex items-center gap-2 text-xs text-slate-700 hover:text-slate-900 cursor-pointer p-1 rounded-lg hover:bg-slate-50">
                                        <input
                                            type="checkbox"
                                            name="categories[]"
                                            value="{{ $cat->id }}"
                                            {{ in_array($cat->id, old('categories', [])) ? 'checked' : '' }}
                                            class="rounded border-slate-300 text-emerald-600 focus:ring-0"
                                        />
                                        <span>{{ $cat->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Widget 6: Tags -->
                        <div class="glass-panel rounded-3xl p-6 border border-slate-200 bg-white shadow-xs space-y-3">
                            <label for="tags_input" class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-1.5 border-b border-slate-100 pb-3">
                                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                                <span>Tags (Comma Separated)</span>
                            </label>
                            <input
                                type="text"
                                name="tags_input"
                                id="tags_input"
                                value="{{ old('tags_input') }}"
                                placeholder="laravel, php, devops, docker..."
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-emerald-500"
                            />
                        </div>

                        <!-- Widget 7: Quick Formatting Helpers -->
                        <div class="glass-panel rounded-3xl p-5 border border-slate-200 bg-white shadow-xs space-y-3">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span>Quick Content Snippets</span>
                            </h3>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <button type="button" onclick="insertSnippetIntoEditor('note')" class="p-2 rounded-xl bg-slate-50 hover:bg-emerald-50 border border-slate-200 text-slate-700 hover:text-emerald-800 transition text-[11px] font-medium text-left">
                                    + Note Callout
                                </button>
                                <button type="button" onclick="insertSnippetIntoEditor('tip')" class="p-2 rounded-xl bg-slate-50 hover:bg-emerald-50 border border-slate-200 text-slate-700 hover:text-emerald-800 transition text-[11px] font-medium text-left">
                                    + Tip Callout
                                </button>
                                <button type="button" onclick="insertSnippetIntoEditor('warning')" class="p-2 rounded-xl bg-slate-50 hover:bg-rose-50 border border-slate-200 text-slate-700 hover:text-rose-800 transition text-[11px] font-medium text-left">
                                    + Warning Box
                                </button>
                                <button type="button" onclick="insertSnippetIntoEditor('code')" class="p-2 rounded-xl bg-slate-50 hover:bg-purple-50 border border-slate-200 text-slate-700 hover:text-purple-800 transition text-[11px] font-medium text-left">
                                    + Code Block
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>

<!-- Include Reusable Media Picker Modal -->
@include('admin.partials.media-picker-modal')

<!-- Self-Hosted CKEditor 5 Script -->
<script src="/vendor/ckeditor5/ckeditor.js"></script>
<script src="/vendor/ckeditor5/editor-init.js"></script>
@endsection
