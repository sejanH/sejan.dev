@extends('layouts.app')

@section('title', 'Edit Tag — ' . $tag->name)

@section('layout')
<div class="min-h-screen bg-slate-50 flex flex-col lg:flex-row text-slate-900">
    <!-- Sidebar -->
    @include('admin.partials.sidebar')

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Header -->
        <header class="p-4 sm:p-6 border-b border-slate-200 bg-white/90 backdrop-blur-md sticky top-0 z-20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ route('admin.tags.index') }}" class="hover:text-emerald-600 transition">Tags</a>
                    <span>/</span>
                    <span class="text-slate-900 font-semibold">#{{ $tag->name }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                    Edit Tag: #{{ $tag->name }}
                </h1>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('blog.tag', $tag->slug) }}"
                    target="_blank"
                    class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition flex items-center gap-1.5 shadow-2xs"
                >
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span>View Public Page</span>
                </a>
                <a
                    href="{{ route('admin.tags.index') }}"
                    class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition flex items-center gap-1.5 shadow-2xs"
                >
                    <span>← Back to Tags</span>
                </a>
            </div>
        </header>

        <!-- Main Content Body -->
        <div class="p-4 sm:p-6 lg:p-8 max-w-2xl w-full">
            @if ($errors->any())
                <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-xs text-rose-800 space-y-1 shadow-2xs">
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

            <div class="glass-panel rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-6">
                <form action="{{ route('admin.tags.update', $tag) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Tag Name -->
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                            Tag Name <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $tag->name) }}"
                            required
                            class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-emerald-500 shadow-2xs"
                        />
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="slug" class="block text-xs font-semibold text-slate-700 mb-1">
                            Slug URL <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center">
                            <span class="px-3 py-2.5 bg-slate-100 border border-r-0 border-slate-200 rounded-l-xl text-xs text-slate-500 font-mono">
                                /tag/
                            </span>
                            <input
                                type="text"
                                id="slug"
                                name="slug"
                                value="{{ old('slug', $tag->slug) }}"
                                required
                                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-r-xl text-xs text-slate-900 focus:outline-none focus:border-emerald-500 shadow-2xs font-mono"
                            />
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">
                            Description
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-emerald-500 shadow-2xs"
                        >{{ old('description', $tag->description) }}</textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <button
                            type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-95 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-500/20 transition flex items-center gap-2 cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Save Changes</span>
                        </button>

                        <a
                            href="{{ route('admin.tags.index') }}"
                            class="px-4 py-2 text-xs font-medium text-slate-500 hover:text-slate-800 transition"
                        >
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
@endsection
