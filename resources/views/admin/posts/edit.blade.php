@extends('layouts.app')

@section('title', 'Edit Article: ' . $post->title)

@section('layout')
<!-- CKEditor 5 Self-Hosted Styles -->
<link rel="stylesheet" href="/vendor/ckeditor5/editor-dark.css">

<div class="min-h-screen bg-slate-950 flex flex-col lg:flex-row">
    @include('admin.partials.sidebar')

    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <header class="p-4 sm:p-6 border-b border-slate-800 bg-slate-900/40 backdrop-blur-md sticky top-0 z-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.posts.index') }}" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl font-extrabold text-white tracking-tight">Edit Article</h1>
                    <p class="text-xs text-slate-400">Updating &ldquo;{{ Str::limit($post->title, 40) }}&rdquo;</p>
                </div>
            </div>

            <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 flex items-center gap-1.5 transition">
                <span>View Live</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>
        </header>

        <div class="p-4 sm:p-6 lg:p-8 max-w-5xl w-full mx-auto">
            @if ($errors->any())
                <div class="mb-6 rounded-2xl bg-red-500/10 border border-red-500/20 p-4 text-xs text-red-300">
                    <div class="font-bold mb-1">Please fix the following errors:</div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.posts.update', $post) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left: Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="glass-panel rounded-3xl p-6 border border-slate-800 space-y-4">
                            <div>
                                <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                                    Article Title *
                                </label>
                                <input
                                    type="text"
                                    name="title"
                                    id="title"
                                    value="{{ old('title', $post->title) }}"
                                    required
                                    class="w-full px-4 py-3 bg-slate-900 border border-slate-800 focus:border-red-500 focus:ring-1 focus:ring-red-500 rounded-xl text-base text-white focus:outline-none"
                                />
                            </div>

                            <div>
                                <label for="slug" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                    URL Slug *
                                </label>
                                <input
                                    type="text"
                                    name="slug"
                                    id="slug"
                                    value="{{ old('slug', $post->slug) }}"
                                    required
                                    class="w-full px-4 py-2 bg-slate-900/80 border border-slate-800 font-mono text-xs text-slate-300 rounded-xl focus:outline-none focus:border-red-500"
                                />
                            </div>

                            <div>
                                <label for="excerpt" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                                    Summary / Excerpt
                                </label>
                                <textarea
                                    name="excerpt"
                                    id="excerpt"
                                    rows="2"
                                    class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-red-500"
                                >{{ old('excerpt', $post->excerpt) }}</textarea>
                            </div>
                        </div>

                        <!-- CKEditor 5 Rich Content Editor -->
                        <div class="glass-panel rounded-3xl p-6 border border-slate-800 space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                                    Article Content (Rich Text & Media) *
                                </label>

                                <button
                                    type="button"
                                    onclick="openMediaPicker('editor')"
                                    class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-1.5 transition border border-slate-700"
                                >
                                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Insert Media from Library</span>
                                </button>
                            </div>

                            <textarea name="content" id="content" class="hidden">{{ old('content', $post->content) }}</textarea>
                            <div id="editorContent">{!! old('content', $post->content) !!}</div>
                        </div>
                    </div>

                    <!-- Right: Settings -->
                    <div class="space-y-6">
                        <div class="glass-panel rounded-3xl p-6 border border-slate-800 space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300">Publishing Options</h3>

                            <div>
                                <label class="block text-xs text-slate-400 mb-1.5">Status</label>
                                <select name="status" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-red-500">
                                    <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Published (Live)</option>
                                    <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Draft (Private)</option>
                                </select>
                            </div>

                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $post->is_featured) ? 'checked' : '' }} class="rounded bg-slate-900 border-slate-700 text-red-600 focus:ring-0">
                                <label for="is_featured" class="text-xs text-slate-300 font-medium">Pin as Featured Article</label>
                            </div>

                            <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-semibold rounded-xl text-xs shadow-lg shadow-red-600/30 transition">
                                Update Article
                            </button>
                        </div>

                        <!-- Featured Image with Media Manager Selector -->
                        <div class="glass-panel rounded-3xl p-6 border border-slate-800 space-y-3">
                            <div class="flex items-center justify-between">
                                <label for="featured_image" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                                    Featured Image
                                </label>
                                <button
                                    type="button"
                                    onclick="openMediaPicker('featured_image')"
                                    class="text-[11px] text-red-400 hover:text-red-300 font-semibold"
                                >
                                    Browse Library
                                </button>
                            </div>

                            <input
                                type="url"
                                name="featured_image"
                                id="featured_image"
                                value="{{ old('featured_image', $post->featured_image) }}"
                                placeholder="Select or enter image URL..."
                                oninput="updateFeaturedPreview(this.value)"
                                class="w-full px-3.5 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-slate-200 font-mono focus:outline-none focus:border-red-500"
                            />

                            <div id="featuredImagePreview" class="{{ $post->featured_image ? '' : 'hidden' }} rounded-2xl overflow-hidden aspect-[16/10] bg-slate-950 border border-slate-800">
                                <img id="featuredImagePreviewImg" src="{{ $post->featured_image }}" alt="" class="w-full h-full object-cover" />
                            </div>
                        </div>

                        <div class="glass-panel rounded-3xl p-6 border border-slate-800 space-y-3">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                                Categories
                            </label>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                @foreach ($categories as $cat)
                                    <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            name="categories[]"
                                            value="{{ $cat->id }}"
                                            {{ in_array($cat->id, old('categories', $selectedCategoryIds)) ? 'checked' : '' }}
                                            class="rounded bg-slate-900 border-slate-700 text-red-600 focus:ring-0"
                                        />
                                        <span>{{ $cat->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="glass-panel rounded-3xl p-6 border border-slate-800 space-y-3">
                            <label for="tags_input" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                                Tags
                            </label>
                            <input
                                type="text"
                                name="tags_input"
                                id="tags_input"
                                value="{{ old('tags_input', $tagsString) }}"
                                class="w-full px-3.5 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-red-500"
                            />
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
