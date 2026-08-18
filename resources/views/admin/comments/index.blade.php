@extends('layouts.app')

@section('title', 'Comments Moderation')

@section('layout')
<div class="min-h-screen bg-slate-50 flex flex-col lg:flex-row text-slate-900">
    @include('admin.partials.sidebar')

    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <header class="p-4 sm:p-6 border-b border-slate-200 bg-white/90 backdrop-blur-md sticky top-0 z-20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                        Comments Moderation
                    </h1>
                    @if($counts['pending'] > 0)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                            {{ $counts['pending'] }} Pending Review
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Review and manually approve reader comments before they appear on your public blog articles.
                </p>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8 space-y-6 w-full">
            @if (session('status'))
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-xs text-emerald-800 flex items-center gap-3">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Tabs and Search -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                    <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ $currentStatus === 'pending' ? 'bg-amber-600 text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }} transition flex items-center gap-1.5">
                        <span>Pending</span>
                        <span class="px-1.5 py-0.2 rounded-full bg-black/10 text-[10px]">{{ $counts['pending'] }}</span>
                    </a>

                    <a href="{{ route('admin.comments.index', ['status' => 'approved']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ $currentStatus === 'approved' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }} transition flex items-center gap-1.5">
                        <span>Approved</span>
                        <span class="px-1.5 py-0.2 rounded-full bg-black/10 text-[10px]">{{ $counts['approved'] }}</span>
                    </a>

                    <a href="{{ route('admin.comments.index', ['status' => 'spam']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ $currentStatus === 'spam' ? 'bg-rose-600 text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }} transition flex items-center gap-1.5">
                        <span>Spam</span>
                        <span class="px-1.5 py-0.2 rounded-full bg-black/10 text-[10px]">{{ $counts['spam'] }}</span>
                    </a>

                    <a href="{{ route('admin.comments.index', ['status' => 'trash']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ $currentStatus === 'trash' ? 'bg-slate-700 text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }} transition flex items-center gap-1.5">
                        <span>Trash</span>
                        <span class="px-1.5 py-0.2 rounded-full bg-black/10 text-[10px]">{{ $counts['trash'] }}</span>
                    </a>
                </div>

                <form action="{{ route('admin.comments.index') }}" method="GET" class="relative max-w-xs w-full">
                    <input type="hidden" name="status" value="{{ $currentStatus }}" />
                    <input type="text" name="q" value="{{ $search }}" placeholder="Search comments..." class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 shadow-2xs" />
                    <svg class="w-3.5 h-3.5 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </form>
            </div>

            <!-- Comments List -->
            @if ($comments->isEmpty())
                <div class="p-12 text-center rounded-3xl bg-white border border-slate-200 space-y-3 shadow-xs">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div class="font-bold text-slate-900 text-sm">No {{ $currentStatus }} comments</div>
                    <p class="text-xs text-slate-500">All caught up with comment moderation.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($comments as $comment)
                        <div class="glass-panel rounded-3xl p-5 sm:p-6 border border-slate-200 bg-white space-y-4 shadow-xs">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $comment->gravatar_url }}" alt="{{ $comment->author_name }}" class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200" />
                                    <div>
                                        <div class="font-bold text-sm text-slate-900 flex items-center gap-2">
                                            <span>{{ $comment->author_name }}</span>
                                            @if($comment->user_id)
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px]">Admin</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-slate-500 font-mono">{{ $comment->author_email }}</div>
                                    </div>
                                </div>

                                <div class="text-right text-xs">
                                    <div class="text-slate-500">
                                        On <a href="{{ route('blog.show', $comment->post->slug) }}" target="_blank" class="text-emerald-700 hover:underline font-medium">{{ Str::limit($comment->post->title, 40) }}</a>
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $comment->created_at->diffForHumans() }}</div>
                                </div>
                            </div>

                            <!-- Comment Body -->
                            <div class="text-xs sm:text-sm text-slate-700 leading-relaxed bg-slate-50 rounded-2xl p-4 border border-slate-200 font-normal">
                                {{ $comment->content }}
                            </div>

                            <!-- Actions Row -->
                            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                                <div class="flex items-center gap-2">
                                    @if ($comment->status !== 'approved')
                                        <form action="{{ route('admin.comments.status', $comment) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 text-xs font-semibold transition">
                                                &check; Approve
                                            </button>
                                        </form>
                                    @endif

                                    @if ($comment->status !== 'pending')
                                        <form action="{{ route('admin.comments.status', $comment) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="pending">
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-600 text-amber-700 hover:text-white border border-amber-200 text-xs font-semibold transition">
                                                Hold / Pending
                                            </button>
                                        </form>
                                    @endif

                                    @if ($comment->status !== 'spam')
                                        <form action="{{ route('admin.comments.status', $comment) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="spam">
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 text-xs font-semibold transition">
                                                Mark Spam
                                            </button>
                                        </form>
                                    @endif

                                    <button
                                        type="button"
                                        onclick="toggleReplyBox({{ $comment->id }})"
                                        class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition border border-slate-200"
                                    >
                                        Reply
                                    </button>
                                </div>

                                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete this comment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 transition">
                                        Delete Permanently
                                    </button>
                                </form>
                            </div>

                            <!-- Admin Inline Reply Form -->
                            <div id="replyBox-{{ $comment->id }}" class="hidden pt-3 border-t border-slate-100">
                                <form action="{{ route('admin.comments.reply', $comment) }}" method="POST" class="space-y-3">
                                    @csrf
                                    <textarea
                                        name="content"
                                        rows="2"
                                        required
                                        placeholder="Write an administrative reply to {{ $comment->author_name }}..."
                                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-emerald-500"
                                    ></textarea>
                                    <div class="flex justify-end gap-2">
                                        <button type="button" onclick="toggleReplyBox({{ $comment->id }})" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 text-xs border border-slate-200">
                                            Cancel
                                        </button>
                                        <button type="submit" class="px-4 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition">
                                            Post Reply
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4">
                    {{ $comments->links() }}
                </div>
            @endif
        </div>
    </main>
</div>

<script>
    function toggleReplyBox(id) {
        const el = document.getElementById(`replyBox-${id}`);
        if (el) {
            el.classList.toggle('hidden');
        }
    }
</script>
@endsection
