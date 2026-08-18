@extends('layouts.blog')

@section('title', $post->seo_title)
@section('meta_description', $post->seo_description)
@section('og_type', 'article')
@if($post->featured_image)
    @section('og_image', $post->featured_image)
@endif

@section('content')
<article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 space-y-10">
    <!-- Back to Articles Breadcrumb -->
    <div>
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-red-400 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Back to All Articles</span>
        </a>
    </div>

    <!-- Article Header -->
    <header class="space-y-6">
        <div class="flex flex-wrap items-center gap-2">
            @foreach($post->categories as $cat)
                <a href="{{ route('blog.category', $cat->slug) }}" class="px-3 py-1 rounded-full text-xs font-semibold bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20 transition">
                    {{ $cat->name }}
                </a>
            @endforeach
            <span class="text-xs text-slate-400 font-mono flex items-center gap-1.5 ml-1">
                <span>&bull;</span>
                <span>{{ $post->reading_time }} min read</span>
                <span>&bull;</span>
                <span>{{ number_format($post->views_count) }} views</span>
            </span>
        </div>

        <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight leading-tight">
            {{ $post->title }}
        </h1>

        @if($post->excerpt)
            <p class="text-base sm:text-lg text-slate-300 leading-relaxed font-normal">
                {{ $post->excerpt }}
            </p>
        @endif

        <!-- Author Card & Date -->
        <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-red-600 to-rose-700 text-white font-extrabold text-sm flex items-center justify-center shadow-lg shadow-red-600/20">
                    {{ strtoupper(substr($post->user->name ?? 'Admin', 0, 2)) }}
                </div>
                <div>
                    <div class="font-bold text-sm text-white">{{ $post->user->name ?? 'Sejan' }}</div>
                    <div class="text-xs text-slate-400 font-mono">
                        Published on {{ $post->published_at ? $post->published_at->format('F d, Y') : 'Draft' }}
                    </div>
                </div>
            </div>

            <!-- Share Button -->
            <div class="flex items-center gap-2">
                <button
                    onclick="navigator.clipboard.writeText(window.location.href); alert('Link copied to clipboard!');"
                    title="Copy Article Link"
                    class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-800 text-xs font-semibold flex items-center gap-1.5 transition"
                >
                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span>Copy Link</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Featured Image -->
    @if ($post->featured_image)
        <div class="rounded-3xl overflow-hidden bg-slate-900 border border-slate-800 shadow-2xl aspect-[16/9]">
            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-full object-cover" />
        </div>
    @endif

    <!-- Main Content Body -->
    <div class="article-prose py-4">
        {!! $post->content !!}
    </div>

    <!-- Tags Footer -->
    @if ($post->tags->isNotEmpty())
        <div class="pt-8 border-t border-slate-800/80 space-y-3">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tagged Topics</h3>
            <div class="flex flex-wrap gap-2">
                @foreach ($post->tags as $tag)
                    <a href="{{ route('blog.tag', $tag->slug) }}" class="px-3 py-1 rounded-xl bg-slate-900 hover:bg-red-500/10 hover:text-red-400 text-slate-300 border border-slate-800 text-xs font-mono transition">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Author Profile Box -->
    <div class="p-6 sm:p-8 rounded-3xl bg-slate-900/60 border border-slate-800 flex flex-col sm:flex-row items-start sm:items-center gap-5">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-red-600 to-rose-700 text-white font-extrabold text-lg flex items-center justify-center flex-shrink-0 shadow-xl shadow-red-600/20">
            {{ strtoupper(substr($post->user->name ?? 'Admin', 0, 2)) }}
        </div>
        <div class="space-y-1.5 flex-1">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-base text-white">Written by {{ $post->user->name ?? 'Sejan' }}</h3>
                <span class="text-[11px] font-mono px-2 py-0.5 rounded-full bg-slate-800 text-slate-400 border border-slate-700">Author</span>
            </div>
            <p class="text-xs text-slate-400 leading-relaxed">
                Full-stack software engineer & architect crafting high-performance web applications with Laravel, PHP, modern cloud systems, and scalable data pipelines.
            </p>
        </div>
    </div>

    <!-- COMMENTS SECTION -->
    <section id="comments" class="pt-12 border-t border-slate-800 space-y-8">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span>Discussion ({{ $post->approvedRootComments->count() }})</span>
            </h2>
            <span class="text-xs text-slate-400 font-mono">Manual Moderation Enabled</span>
        </div>

        <!-- Flash Notice -->
        @if (session('status'))
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-300 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-xs text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <!-- Comments Tree -->
        @if ($post->approvedRootComments->isEmpty())
            <div class="p-8 text-center rounded-3xl bg-slate-900/30 border border-slate-800 space-y-2">
                <div class="text-sm font-semibold text-slate-300">No comments yet</div>
                <p class="text-xs text-slate-400">Be the first to share your thoughts on this article!</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach ($post->approvedRootComments as $comment)
                    <div class="p-6 rounded-3xl bg-slate-900/40 border border-slate-800 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img src="{{ $comment->gravatar_url }}" alt="{{ $comment->author_name }}" class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700" />
                                <div>
                                    <div class="font-bold text-xs text-white flex items-center gap-2">
                                        <span>{{ $comment->author_name }}</span>
                                        @if($comment->user_id)
                                            <span class="px-2 py-0.2 rounded-full bg-red-500/10 text-red-400 border border-red-500/20 text-[9px]">Author</span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $comment->created_at->diffForHumans() }}</div>
                                </div>
                            </div>

                            <button
                                type="button"
                                onclick="setReplyParent({{ $comment->id }}, '{{ addslashes($comment->author_name) }}')"
                                class="text-xs text-red-400 hover:text-red-300 font-semibold"
                            >
                                Reply
                            </button>
                        </div>

                        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed pl-11">
                            {{ $comment->content }}
                        </p>

                        <!-- Nested Replies -->
                        @if ($comment->approvedReplies->isNotEmpty())
                            <div class="pl-8 pt-3 space-y-3 border-l-2 border-slate-800 ml-4">
                                @foreach ($comment->approvedReplies as $reply)
                                    <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-2">
                                        <div class="flex items-center gap-2.5">
                                            <img src="{{ $reply->gravatar_url }}" alt="{{ $reply->author_name }}" class="w-6 h-6 rounded-full bg-slate-800 border border-slate-700" />
                                            <div>
                                                <div class="font-bold text-xs text-white flex items-center gap-1.5">
                                                    <span>{{ $reply->author_name }}</span>
                                                    @if($reply->user_id)
                                                        <span class="px-1.5 py-0.2 rounded-full bg-red-500/10 text-red-400 border border-red-500/20 text-[9px]">Author</span>
                                                    @endif
                                                </div>
                                                <div class="text-[10px] text-slate-400 font-mono">{{ $reply->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                        <p class="text-xs text-slate-300 pl-8 leading-relaxed">
                                            {{ $reply->content }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Leave a Comment Form -->
        <div class="p-6 sm:p-8 rounded-3xl bg-slate-900/60 border border-slate-800 space-y-5" id="commentFormContainer">
            <div>
                <h3 class="font-bold text-base text-white">Leave a Comment</h3>
                <p class="text-xs text-slate-400 mt-0.5">
                    Your email address will not be published. Comments are held for moderation before appearing.
                </p>
            </div>

            <!-- Reply Notice Banner -->
            <div id="replyNotice" class="hidden p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-xs text-red-300 flex items-center justify-between">
                <span>Replying to <strong id="replyAuthorName"></strong></span>
                <button type="button" onclick="cancelReply()" class="text-red-400 hover:text-white font-bold">&times; Cancel Reply</button>
            </div>

            <form action="{{ route('comments.store', $post) }}" method="POST" class="space-y-4">
                @csrf
                <!-- Honeypot anti-spam field -->
                <input type="text" name="website_hp" class="hidden" tabindex="-1" autocomplete="off" />
                <input type="hidden" name="parent_id" id="parent_id_input" value="" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="author_name" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Your Name *</label>
                        <input
                            type="text"
                            name="author_name"
                            id="author_name"
                            required
                            placeholder="Alex Smith"
                            value="{{ auth()->check() ? auth()->user()->name : old('author_name') }}"
                            class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-red-500"
                        />
                    </div>
                    <div>
                        <label for="author_email" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Your Email (for Gravatar) *</label>
                        <input
                            type="email"
                            name="author_email"
                            id="author_email"
                            required
                            placeholder="alex@example.com"
                            value="{{ auth()->check() ? auth()->user()->email : old('author_email') }}"
                            class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-red-500"
                        />
                    </div>
                </div>

                <div>
                    <label for="comment_content" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Comment *</label>
                    <textarea
                        name="content"
                        id="comment_content"
                        rows="4"
                        required
                        placeholder="Write your constructive response or question..."
                        class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-red-500 leading-relaxed"
                    >{{ old('content') }}</textarea>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <span class="text-[11px] text-slate-400">🛡️ Manual admin moderation active</span>
                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-red-600/30 transition"
                    >
                        Submit for Moderation
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Related Articles -->
    @if ($relatedPosts->isNotEmpty())
        <div class="pt-12 border-t border-slate-800 space-y-6">
            <h2 class="text-xl font-bold text-white tracking-tight">
                Recommended Articles
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($relatedPosts as $related)
                    <a href="{{ route('blog.show', $related->slug) }}" class="p-5 rounded-2xl bg-slate-900/40 border border-slate-800/80 hover:border-slate-700 hover:bg-slate-900/70 flex flex-col justify-between space-y-3 group transition">
                        <div class="space-y-2">
                            <span class="text-[10px] font-mono text-red-400 uppercase tracking-wider">
                                {{ $related->categories->first()->name ?? 'Article' }}
                            </span>
                            <h3 class="font-bold text-sm text-white group-hover:text-red-400 transition line-clamp-2">
                                {{ $related->title }}
                            </h3>
                        </div>
                        <div class="text-[11px] text-slate-400 font-mono">
                            {{ $related->reading_time }} min read
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</article>

<script>
    function setReplyParent(parentId, authorName) {
        document.getElementById('parent_id_input').value = parentId;
        document.getElementById('replyAuthorName').innerText = authorName;
        document.getElementById('replyNotice').classList.remove('hidden');
        document.getElementById('commentFormContainer').scrollIntoView({ behavior: 'smooth' });
    }

    function cancelReply() {
        document.getElementById('parent_id_input').value = '';
        document.getElementById('replyNotice').classList.add('hidden');
    }
</script>
@endsection
