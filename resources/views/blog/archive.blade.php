@extends('layouts.blog')

@section('title', "{$title} — sejan.dev")
@section('meta_description', $description)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 space-y-10">
    <!-- Archive Header -->
    <div class="p-8 sm:p-12 rounded-3xl bg-slate-900/40 border border-slate-800 space-y-4">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-red-500/10 text-red-400 border border-red-500/20">
            <span>{{ $type }} Archive</span>
        </div>
        <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight">
            {{ $title }}
        </h1>
        <p class="text-sm sm:text-base text-slate-400 max-w-2xl">
            {{ $description }}
        </p>
    </div>

    <!-- Posts Grid -->
    <div>
        @if ($posts->isEmpty())
            <div class="p-12 text-center rounded-3xl bg-slate-900/40 border border-slate-800 space-y-4">
                <h3 class="text-lg font-bold text-white">No articles found in this archive</h3>
                <p class="text-xs text-slate-400">There are currently no published articles under {{ $title }}.</p>
                <a href="{{ route('home') }}" class="inline-block px-4 py-2 rounded-xl bg-red-600 hover:bg-red-500 text-xs font-semibold text-white transition">
                    Return to Homepage
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach ($posts as $post)
                    <article class="flex flex-col rounded-3xl bg-slate-900/40 border border-slate-800/80 overflow-hidden hover:border-slate-700 hover:bg-slate-900/70 transition duration-200 group">
                        @if ($post->featured_image)
                            <a href="{{ route('blog.show', $post->slug) }}" class="aspect-[16/10] overflow-hidden bg-slate-800">
                                <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" />
                            </a>
                        @endif

                        <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-red-400 font-semibold">{{ $type }}: {{ $title }}</span>
                                    <span class="text-slate-400 font-mono text-[11px]">{{ $post->reading_time }} min read</span>
                                </div>

                                <h3 class="font-bold text-lg text-white group-hover:text-red-400 transition leading-snug line-clamp-2">
                                    <a href="{{ route('blog.show', $post->slug) }}">
                                        {{ $post->title }}
                                    </a>
                                </h3>

                                <p class="text-xs text-slate-400 leading-relaxed line-clamp-3">
                                    {{ $post->excerpt }}
                                </p>
                            </div>

                            <div class="pt-4 border-t border-slate-800/60 flex items-center justify-between text-xs text-slate-400">
                                <div class="font-mono text-[11px]">
                                    {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Draft' }}
                                </div>
                                <a href="{{ route('blog.show', $post->slug) }}" class="text-red-400 hover:text-red-300 font-semibold group-hover:translate-x-0.5 transition">
                                    &rarr;
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="pt-8">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
