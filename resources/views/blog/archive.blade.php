@extends('layouts.blog')

@section('title', "{$title} — Sejan · Blog")
@section('meta_description', $description)
@section('canonical_url', url()->current())

@if ($posts->isNotEmpty() && $posts->first()->thumbnail_url)
    @section('preload_headers')
        <link rel="preload" as="image" href="{{ $posts->first()->thumbnail_url }}" fetchpriority="high">
    @endsection
@endif

@section('schema_json')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'CollectionPage',
            '@id' => url()->current() . '#archive',
            'url' => url()->current(),
            'name' => "{$title} — Sejan · Blog",
            'description' => $description,
            'inLanguage' => 'en-US',
            'isPartOf' => [
                '@type' => 'WebSite',
                '@id' => route('home') . '#website',
                'name' => 'Sejan · Blog',
                'url' => route('home'),
            ],
            'about' => [
                '@type' => 'Thing',
                'name' => $title,
            ],
            'publisher' => [
                '@type' => 'Person',
                'name' => 'S. M. Mominul Haque (Sejan)',
                'url' => route('blog.about'),
            ],
        ],
        [
            '@type' => 'BreadcrumbList',
            '@id' => url()->current() . '#breadcrumb',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => route('home'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $title,
                    'item' => url()->current(),
                ],
            ],
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-10">
    <!-- Archive Header -->
    <div class="p-8 sm:p-12 rounded-3xl glass-card text-center space-y-4">
        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
            <span>{{ $type }} Archive</span>
        </div>
        <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight">
            {{ $title }}
        </h1>
        <p class="text-sm sm:text-base text-slate-600 max-w-2xl mx-auto">
            {{ $description }}
        </p>
    </div>

    <!-- Posts Grid -->
    <div>
        @if ($posts->isEmpty())
            <div class="p-12 text-center rounded-3xl glass-card space-y-4">
                <h3 class="text-lg font-bold text-slate-900">No articles found in this archive</h3>
                <p class="text-xs text-slate-500">There are currently no published articles under {{ $title }}.</p>
                <a href="{{ route('home') }}" class="inline-block px-5 py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-xs font-semibold text-white transition">
                    Return to Homepage
                </a>
            </div>
        @else
            <div class="masonry-grid">
                @foreach ($posts as $post)
                    <article class="group glass-card overflow-hidden rounded-3xl flex flex-col hover-lift-enhanced transition-all duration-300">
                        <div class="relative h-52 w-full overflow-hidden bg-slate-100">
                            @if ($post->featured_image)
                                <img
                                    src="{{ $post->thumbnail_url }}"
                                    alt="{{ $post->title }}"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    @if ($loop->first)
                                        loading="eager"
                                        fetchpriority="high"
                                        decoding="async"
                                    @else
                                        loading="lazy"
                                        decoding="async"
                                    @endif
                                />
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-emerald-100/70 via-slate-100 to-teal-100/70 text-xs font-bold uppercase tracking-wider text-emerald-700">
                                    {{ $title }}
                                </div>
                            @endif

                            <div class="absolute top-4 left-4">
                                <span class="rounded-full bg-white/90 backdrop-blur-md px-3 py-1 text-xs font-semibold text-emerald-700 shadow-xs border border-white/60">
                                    {{ $title }}
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-1 flex-col justify-between p-6">
                            <div>
                                <div class="flex items-center gap-4 text-xs font-medium text-slate-500 mb-3">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}
                                    </span>
                                </div>

                                <a href="{{ route('blog.show', $post->slug) }}" class="block group/title">
                                    <h2 class="text-xl font-bold leading-snug text-slate-900 transition-colors group-hover/title:text-emerald-600 line-clamp-2">
                                        {{ $post->title }}
                                    </h2>
                                </a>

                                <p class="mt-3 text-sm text-slate-600 leading-relaxed line-clamp-3">
                                    {{ $post->excerpt }}
                                </p>
                            </div>

                            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                                <a
                                    href="{{ route('blog.show', $post->slug) }}"
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-3.5 py-1.5 rounded-full transition-all hover:bg-emerald-100 hover:text-emerald-800"
                                >
                                    <span>Read More</span>
                                    <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
