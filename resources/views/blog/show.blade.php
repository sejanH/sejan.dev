@extends('layouts.blog')

@section('title', ($post->meta_title ?: $post->title) . ' — Sejan · Blog')
@section('meta_description', $post->seo_description)
@section('canonical_url', $post->canonical_url ?: route('blog.show', $post->slug))
@section('og_type', 'article')
@section('og_title', $post->seo_title)
@section('og_description', $post->seo_description)

@if ($post->featured_image)
    @section('og_image', $post->featured_image)
    @section('preload_headers')
        <link rel="preload" as="image" href="{{ $post->featured_image }}" fetchpriority="high">
    @endsection
@endif

@section('og_article_tags')
    <meta property="article:published_time" content="{{ $post->published_at ? $post->published_at->toIso8601String() : $post->created_at->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">
    <meta property="article:author" content="{{ route('blog.about') }}">
    @if ($post->categories->first())
        <meta property="article:section" content="{{ $post->categories->first()->name }}">
    @endif
    @foreach ($post->tags as $tag)
        <meta property="article:tag" content="{{ $tag->name }}">
    @endforeach
@endsection

@section('schema_json')
@php
    $breadcrumbItems = [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => route('home'),
        ],
    ];

    if ($post->categories->first()) {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => $post->categories->first()->name,
            'item' => route('blog.category', $post->categories->first()->slug),
        ];
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $post->title,
            'item' => route('blog.show', $post->slug),
        ];
    } else {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => $post->title,
            'item' => route('blog.show', $post->slug),
        ];
    }

    $wordCount = str_word_count(strip_tags($post->content));
    $readingMinutes = max(1, (int) ceil($wordCount / 200));

    $blogPosting = [
        '@type' => 'BlogPosting',
        '@id' => route('blog.show', $post->slug) . '#article',
        'isPartOf' => [
            '@type' => 'Blog',
            '@id' => route('home') . '#blog',
            'name' => 'Sejan · Blog',
            'publisher' => [
                '@type' => 'Person',
                'name' => 'S. M. Mominul Haque (Sejan)',
            ],
        ],
        'headline' => $post->title,
        'description' => $post->seo_description,
        'url' => route('blog.show', $post->slug),
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => route('blog.show', $post->slug),
        ],
        'datePublished' => $post->published_at ? $post->published_at->toIso8601String() : $post->created_at->toIso8601String(),
        'dateModified' => $post->updated_at->toIso8601String(),
        'author' => [
            '@type' => 'Person',
            '@id' => route('blog.about') . '#author',
            'name' => 'S. M. Mominul Haque (Sejan)',
            'url' => route('blog.about'),
            'jobTitle' => 'Senior Software Engineer & Architect',
            'sameAs' => [
                'https://www.linkedin.com/in/s-m-mominul-haque-sejan-79b77b83/',
                'https://twitter.com/sejanH',
                'https://github.com/sejanH',
            ],
        ],
        'publisher' => [
            '@type' => 'Person',
            'name' => 'S. M. Mominul Haque (Sejan)',
            'url' => route('home'),
        ],
        'wordCount' => $wordCount,
        'timeRequired' => "PT{$readingMinutes}M",
        'inLanguage' => 'en-US',
    ];

    if ($post->featured_image) {
        $blogPosting['image'] = [
            '@type' => 'ImageObject',
            'url' => $post->featured_image,
        ];
    }

    if ($post->categories->isNotEmpty()) {
        $blogPosting['articleSection'] = $post->categories->pluck('name')->toArray();
    }

    if ($post->tags->isNotEmpty()) {
        $blogPosting['keywords'] = $post->tags->pluck('name')->implode(', ');
    }

    $schemaData = [
        '@context' => 'https://schema.org',
        '@graph' => [
            $blogPosting,
            [
                '@type' => 'BreadcrumbList',
                '@id' => route('blog.show', $post->slug) . '#breadcrumb',
                'itemListElement' => $breadcrumbItems,
            ],
        ],
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

        {{-- Top Breadcrumb & Header Banner (full width above columns) --}}
        <section class="glass-card rounded-3xl p-6 sm:p-8 text-center shadow-xs mb-8">
            <h1 class="text-2xl sm:text-4xl font-extrabold leading-tight text-slate-900 mb-4">
                {{ $post->title }}
            </h1>

            <div class="flex flex-wrap items-center justify-center gap-2 text-xs sm:text-sm font-medium text-emerald-700">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1 hover:text-emerald-800 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m3 12 9-9 9 9M9 21V9h6v12" />
                    </svg>
                    <span>Home</span>
                </a>
                <span class="text-slate-400">›</span>
                @if ($post->categories->first())
                    <a href="{{ route('blog.category', $post->categories->first()->slug) }}" class="hover:text-emerald-800 transition">
                        {{ $post->categories->first()->name }}
                    </a>
                    <span class="text-slate-400">›</span>
                @endif
                <span class="text-slate-600 truncate max-w-xs">{{ $post->title }}</span>
            </div>
        </section>

        {{-- Two-column layout: article left, sidebar right --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

            {{-- LEFT: Main article content + comments (2/3 width) --}}
            <div class="min-w-0 space-y-8 lg:col-span-2">

                {{-- Main Article Container --}}
                <section class="glass-card rounded-3xl p-6 sm:p-10 shadow-xs">
                    {{-- Metadata & Social Share Bar --}}
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 pb-6 mb-8">
                        <div class="flex flex-wrap items-center gap-3 text-xs uppercase tracking-wider text-slate-500 font-medium">
                            <span>{{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}</span>
                            @foreach ($post->categories as $category)
                                <a href="{{ route('blog.category', $category->slug) }}" class="rounded-full bg-emerald-50 text-emerald-700 px-3 py-1 text-xs font-semibold hover:bg-emerald-100 transition">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>

                        @php
                            $shareUrl = urlencode(url()->current());
                            $shareTitle = urlencode($post->title);
                        @endphp
                        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <span>Share</span>
                            <div class="flex items-center gap-1.5 ml-2">
                                <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener noreferrer" class="h-8 w-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center transition-all hover:bg-emerald-600 hover:text-white" title="Share on Twitter">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 5.8c-.7.3-1.4.5-2.1.6a3.5 3.5 0 0 0-6.1 2.4c0 .3 0 .6.1.8-3-.1-5.7-1.6-7.5-3.9a3.5 3.5 0 0 0 .5 4.6c-.6 0-1.1-.2-1.6-.4v.1c0 1.7 1.2 3.1 2.8 3.4-.3.1-.6.1-.9.1-.2 0-.4 0-.6-.1.4 1.3 1.7 2.3 3.2 2.3A7 7 0 0 1 2 18.1a9.8 9.8 0 0 0 5.3 1.5c6.3 0 9.8-5.2 9.8-9.8v-.4c.7-.5 1.3-1.1 1.8-1.8Z" /></svg>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener noreferrer" class="h-8 w-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center transition-all hover:bg-emerald-600 hover:text-white" title="Share on Facebook">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M13.5 9H15V7h-1.5C11.6 7 11 8.1 11 9.7V11H9v2h2v6h2v-6h1.7l.3-2H13v-1c0-.6.2-1 .5-1Z" /></svg>
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" rel="noopener noreferrer" class="h-8 w-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center transition-all hover:bg-emerald-600 hover:text-white" title="Share on LinkedIn">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M6.94 6.5a1.44 1.44 0 1 1-2.88 0 1.44 1.44 0 0 1 2.88 0ZM4 8.75h3.06V20H4V8.75Zm6.05 0H13v1.61h.05c.41-.77 1.41-1.58 2.9-1.58 3.1 0 3.68 2.02 3.68 4.64V20h-3.06v-5.62c0-1.34-.03-3.07-1.87-3.07-1.87 0-2.16 1.46-2.16 2.97V20H10V8.75Z" /></svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Featured Image --}}
                    @if ($post->featured_image)
                        <div class="mb-8 overflow-hidden rounded-2xl shadow-sm border border-slate-200">
                            <img
                                src="{{ $post->featured_image }}"
                                alt="{{ $post->title }}"
                                class="w-full max-h-[480px] object-cover"
                                loading="eager"
                                fetchpriority="high"
                                decoding="async"
                            />
                        </div>
                    @endif

                    {{-- Article Body --}}
                    <article class="article-prose">
                        {!! $post->content !!}
                    </article>

                    {{-- Tags --}}
                    @if ($post->tags->count() > 0)
                        <div class="mt-10 pt-6 border-t border-slate-200 flex flex-wrap items-center gap-2">
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 mr-2">Tags:</span>
                            @foreach ($post->tags as $tag)
                                <a href="{{ route('blog.tag', $tag->slug) }}" class="rounded-full bg-slate-100 text-slate-700 px-3 py-1 text-xs font-medium hover:bg-emerald-50 hover:text-emerald-700 transition">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </section>

                {{-- Comments & Discussion Section --}}
                <section class="glass-card rounded-3xl p-6 sm:p-10 shadow-xs space-y-8">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900">
                            Comments ({{ $post->comments()->where('status', 'approved')->count() }})
                        </h3>
                        <a href="#comment_form_container" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                            Leave a Reply ↓
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    {{-- Threaded Comments List --}}
                    @php $approvedComments = $post->approvedRootComments; @endphp

                    @if ($approvedComments->count() > 0)
                        <div class="space-y-4">
                            @foreach ($approvedComments as $comment)
                                <div class="rounded-2xl border border-slate-200/80 bg-slate-50/50 p-5 text-sm transition-all hover:bg-slate-50">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $comment->gravatar_url }}" alt="{{ $comment->author_name }}" class="w-8 h-8 rounded-full border border-slate-200" />
                                            <div>
                                                <span class="font-bold text-slate-900">{{ $comment->author_name }}</span>
                                                <span class="text-xs text-slate-400 ml-2">{{ $comment->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                        <button type="button" onclick="replyToComment({{ $comment->id }}, '{{ addslashes($comment->author_name) }}')" class="text-xs font-semibold text-emerald-600 hover:text-emerald-800 transition">Reply</button>
                                    </div>

                                    <div class="text-slate-700 leading-relaxed pl-11">
                                        {!! nl2br(e($comment->content)) !!}
                                    </div>

                                    {{-- Nested Replies --}}
                                    @if ($comment->approvedReplies->count() > 0)
                                        <div class="mt-4 pl-8 border-l-2 border-emerald-200 space-y-3">
                                            @foreach ($comment->approvedReplies as $reply)
                                                <div class="rounded-xl bg-white p-3.5 border border-slate-200/60">
                                                    <div class="flex items-center justify-between mb-1.5">
                                                        <div class="flex items-center gap-2.5">
                                                            <img src="{{ $reply->gravatar_url }}" alt="{{ $reply->author_name }}" class="w-6 h-6 rounded-full border border-slate-200" />
                                                            <span class="font-bold text-xs text-slate-900">{{ $reply->author_name }}</span>
                                                            <span class="text-[10px] text-slate-400">{{ $reply->created_at->format('M d, Y') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-xs text-slate-700 pl-8">{!! nl2br(e($reply->content)) !!}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500 text-center py-6">No comments yet. Be the first to start the discussion!</p>
                    @endif

                    {{-- Comment Submission Form --}}
                    <div id="comment_form_container" class="pt-6 border-t border-slate-200">
                        <h4 class="text-base font-bold text-slate-900 mb-4">Leave a Comment</h4>

                        <div id="replying_to_banner" class="hidden mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-center justify-between">
                            <span>Replying to <strong id="reply_author_name"></strong></span>
                            <button type="button" onclick="cancelReply()" class="text-emerald-700 hover:text-emerald-900 font-bold underline ml-2">Cancel Reply</button>
                        </div>

                        <form action="{{ route('comments.store', $post->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="parent_id" id="parent_id_input" value="" />
                            <!-- Honeypot anti-spam (spatie/laravel-honeypot) -->
                            @honeypot

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="author_name" class="block text-xs font-semibold text-slate-700 mb-1">Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="author_name" id="author_name" required value="{{ old('author_name') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm text-slate-900 focus:border-emerald-500 focus:bg-white focus:outline-none transition" placeholder="Your Name" />
                                </div>
                                <div>
                                    <label for="author_email" class="block text-xs font-semibold text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="author_email" id="author_email" required value="{{ old('author_email') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm text-slate-900 focus:border-emerald-500 focus:bg-white focus:outline-none transition" placeholder="you@example.com" />
                                </div>
                            </div>

                            <div>
                                <label for="comment_content" class="block text-xs font-semibold text-slate-700 mb-1">Comment <span class="text-red-500">*</span></label>
                                <textarea name="content" id="comment_content" rows="4" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-900 focus:border-emerald-500 focus:bg-white focus:outline-none transition" placeholder="Share your thoughts...">{{ old('content') }}</textarea>
                            </div>

                            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-semibold text-sm rounded-full shadow-sm hover:opacity-95 hover:scale-105 transition">
                                Submit Comment
                            </button>
                        </form>
                    </div>
                </section>

            </div>{{-- end LEFT --}}

            {{-- RIGHT: Sticky Related Posts Sidebar --}}
            <aside class="hidden lg:block sticky top-24 space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Related Articles</h3>
                    </div>
                    @if ($relatedPosts->count() > 0)
                        <ul class="divide-y divide-slate-100">
                            @foreach ($relatedPosts as $rel)
                                <li>
                                    <a href="{{ route('blog.show', $rel->slug) }}" class="flex gap-3 p-4 hover:bg-slate-50 transition group">
                                        @if ($rel->featured_image)
                                            <img src="{{ $rel->thumbnail_url }}" alt="{{ $rel->title }}" class="w-14 h-14 rounded-lg object-cover shrink-0 border border-slate-100" />
                                        @else
                                            <div class="w-14 h-14 rounded-lg bg-emerald-50 border border-slate-100 shrink-0 flex items-center justify-center text-emerald-400">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            @if ($rel->categories->first())
                                                <span class="text-[10px] font-semibold text-emerald-600 uppercase tracking-wide">{{ $rel->categories->first()->name }}</span>
                                            @endif
                                            <p class="text-xs font-semibold text-slate-800 group-hover:text-emerald-700 transition leading-snug line-clamp-2 mt-0.5">{{ $rel->title }}</p>
                                            <span class="text-[10px] text-slate-400 mt-1 block">{{ $rel->published_at?->format('M d, Y') }}</span>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="p-5 text-xs text-slate-400">No related articles found.</p>
                    @endif
                </div>

                {{-- Categories Widget (cached 1hr, hierarchical) --}}
                @if ($sidebarCategories->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Categories</h3>
                        </div>
                        <ul class="divide-y divide-slate-100">
                            @foreach ($sidebarCategories as $parent)
                                @if ($parent->posts_count > 0 || $parent->children->isNotEmpty())
                                    <li>
                                        {{-- Parent category --}}
                                        <a href="{{ route('blog.category', $parent->slug) }}"
                                           class="flex items-center justify-between px-5 py-3 hover:bg-slate-50 transition group">
                                            <span class="text-sm font-semibold text-slate-800 group-hover:text-emerald-700 transition">
                                                {{ $parent->name }}
                                            </span>
                                            <span class="text-[11px] font-semibold text-slate-400 bg-slate-100 rounded-full px-2 py-0.5 group-hover:bg-emerald-50 group-hover:text-emerald-600 transition">
                                                {{ $parent->posts_count }}
                                            </span>
                                        </a>

                                        {{-- Child subcategories --}}
                                        @if ($parent->children->isNotEmpty())
                                            <ul class="border-t border-slate-100 bg-slate-50/60">
                                                @foreach ($parent->children as $child)
                                                    @if ($child->posts_count > 0)
                                                        <li>
                                                            <a href="{{ route('blog.category', $child->slug) }}"
                                                               class="flex items-center justify-between pl-8 pr-5 py-2.5 hover:bg-white transition group">
                                                                <span class="flex items-center gap-1.5 text-xs text-slate-600 group-hover:text-emerald-700 transition">
                                                                    <span class="text-slate-300">↳</span>
                                                                    {{ $child->name }}
                                                                </span>
                                                                <span class="text-[10px] font-semibold text-slate-400 bg-white border border-slate-200 rounded-full px-1.5 py-0.5 group-hover:border-emerald-200 group-hover:text-emerald-600 transition">
                                                                    {{ $child->posts_count }}
                                                                </span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif

            </aside>{{-- end RIGHT --}}

        </div>{{-- end grid --}}
    </div>
@endsection

