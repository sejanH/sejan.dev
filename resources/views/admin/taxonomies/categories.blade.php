@extends('layouts.app')

@section('title', 'Manage Categories — Taxonomies')

@section('layout')
<div class="min-h-screen bg-slate-50 flex flex-col lg:flex-row text-slate-900">
    <!-- Sidebar -->
    @include('admin.partials.sidebar')

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Header -->
        <header class="p-4 sm:p-6 border-b border-slate-200 bg-white/90 backdrop-blur-md sticky top-0 z-20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                    Taxonomies Management
                </h1>
                <p class="text-xs text-slate-500">
                    Organize your articles into hierarchical categories and searchable tags.
                </p>
            </div>

            <!-- Taxonomy Switcher Tabs -->
            <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl border border-slate-200/80">
                <a
                    href="{{ route('admin.categories.index') }}"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ request()->routeIs('admin.categories.*') ? 'bg-white text-emerald-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"
                >
                    Categories ({{ $totalCount }})
                </a>
                <a
                    href="{{ route('admin.tags.index') }}"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ request()->routeIs('admin.tags.*') ? 'bg-white text-emerald-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"
                >
                    Tags ({{ \App\Models\Tag::count() }})
                </a>
            </div>
        </header>

        <!-- Main Content Body -->
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 w-full">
            @if (session('status'))
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-xs text-emerald-800 flex items-center gap-3 shadow-2xs">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4 text-xs text-rose-800 space-y-1 shadow-2xs">
                    <div class="font-bold flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Please correct the errors below:</span>
                    </div>
                    <ul class="list-disc list-inside pl-2 space-y-0.5 text-rose-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Left: Add New Category Card -->
                <div class="lg:col-span-4 glass-panel rounded-3xl border border-slate-200 bg-white p-6 shadow-xs space-y-5">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Add New Category</span>
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-1">Create a new category for grouping published posts.</p>
                    </div>

                    <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Category Name -->
                        <div>
                            <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                                Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                placeholder="e.g. Architecture"
                                class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 shadow-2xs"
                            />
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block text-xs font-semibold text-slate-700 mb-1">
                                Slug <span class="text-[10px] text-slate-400 font-normal">(Optional, auto-generated)</span>
                            </label>
                            <input
                                type="text"
                                id="slug"
                                name="slug"
                                value="{{ old('slug') }}"
                                placeholder="e.g. architecture"
                                class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 shadow-2xs font-mono"
                            />
                        </div>

                        <!-- Parent Category -->
                        <div>
                            <label for="parent_id" class="block text-xs font-semibold text-slate-700 mb-1">
                                Parent Category <span class="text-[10px] text-slate-400 font-normal">(Hierarchical)</span>
                            </label>
                            <select
                                id="parent_id"
                                name="parent_id"
                                class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-emerald-500 shadow-2xs"
                            >
                                <option value="">None (Top Level)</option>
                                @foreach ($parentCategories as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">
                                Description
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="3"
                                placeholder="Brief summary of articles in this topic..."
                                class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 shadow-2xs"
                            >{{ old('description') }}</textarea>
                        </div>

                        <button
                            type="submit"
                            class="w-full py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-95 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-500/20 transition flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Add Category</span>
                        </button>
                    </form>
                </div>

                <!-- Right: Categories Table -->
                <div class="lg:col-span-8 space-y-4">
                    <!-- Search Bar -->
                    <div class="flex items-center justify-between gap-4">
                        <form action="{{ route('admin.categories.index') }}" method="GET" class="relative max-w-sm w-full">
                            <input
                                type="text"
                                name="q"
                                value="{{ $search }}"
                                placeholder="Search categories..."
                                class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition shadow-2xs"
                            />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </form>

                        @if (!empty($search))
                            <a href="{{ route('admin.categories.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium underline shrink-0">
                                Clear search
                            </a>
                        @endif
                    </div>

                    <!-- Categories Table -->
                    <div class="glass-panel rounded-3xl border border-slate-200 bg-white shadow-xs overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-slate-700">
                                <thead class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-500">
                                    <tr>
                                        <th class="py-3.5 px-5">Name & Slug</th>
                                        <th class="py-3.5 px-4">Parent</th>
                                        <th class="py-3.5 px-4">Description</th>
                                        <th class="py-3.5 px-4 text-center">Articles</th>
                                        <th class="py-3.5 px-5 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse ($categories as $category)
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-3.5 px-5">
                                                <div class="font-bold text-slate-900 text-xs">
                                                    <a href="{{ route('admin.categories.edit', $category) }}" class="hover:text-emerald-600 transition">
                                                        {{ $category->name }}
                                                    </a>
                                                </div>
                                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                                    /category/{{ $category->slug }}
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                @if ($category->parent)
                                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-medium border border-slate-200">
                                                        {{ $category->parent->name }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 text-[11px]">—</span>
                                                @endif
                                            </td>
                                            <td class="py-3.5 px-4 max-w-xs truncate text-slate-500 text-[11px]">
                                                {{ $category->description ?: '—' }}
                                            </td>
                                            <td class="py-3.5 px-4 text-center">
                                                <a
                                                    href="{{ route('blog.category', $category->slug) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition"
                                                >
                                                    {{ $category->posts_count }}
                                                </a>
                                            </td>
                                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                                <div class="inline-flex items-center gap-1.5">
                                                    <a
                                                        href="{{ route('admin.categories.edit', $category) }}"
                                                        class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 transition shadow-2xs"
                                                        title="Edit Category"
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>

                                                    <form
                                                        action="{{ route('admin.categories.destroy', $category) }}"
                                                        method="POST"
                                                        class="inline"
                                                        onsubmit="return confirm('Are you sure you want to delete category \'{{ addslashes($category->name) }}\'? (Articles will not be deleted)');"
                                                    >
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            type="submit"
                                                            class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition shadow-2xs cursor-pointer"
                                                            title="Delete Category"
                                                        >
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-8 text-center text-slate-400">
                                                No categories found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($categories->hasPages())
                            <div class="p-4 border-t border-slate-200">
                                {{ $categories->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
