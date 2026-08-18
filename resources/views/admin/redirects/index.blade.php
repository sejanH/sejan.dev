@extends('layouts.app')

@section('title', '301 SEO Redirects Manager')

@section('layout')
<div class="min-h-screen bg-slate-950 flex flex-col lg:flex-row">
    @include('admin.partials.sidebar')

    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <header class="p-4 sm:p-6 border-b border-slate-800 bg-slate-900/40 backdrop-blur-md sticky top-0 z-20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">
                    301 SEO Permanent Redirects
                </h1>
                <p class="text-xs text-slate-400">
                    Preserve historical WordPress URLs, maintain Google SEO rankings, and prevent broken links.
                </p>
            </div>

            <div class="flex items-center gap-3 font-mono text-xs text-slate-300">
                <span class="px-3 py-1 rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/20">
                    Total Hits: {{ number_format($totalHits) }}
                </span>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8 space-y-8 max-w-7xl w-full mx-auto">
            @if (session('status'))
                <div class="rounded-2xl bg-emerald-500/10 border border-emerald-500/20 p-4 text-xs text-emerald-300 flex items-center gap-3">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Manual Redirect Creator Form -->
            <div class="glass-panel rounded-3xl p-6 border border-slate-800 shadow-xl space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-300">
                    Add Manual Redirect Rule
                </h2>
                <form action="{{ route('admin.redirects.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-[11px] text-slate-400 font-mono mb-1">Old Source URL (/2023/post/)</label>
                        <input type="text" name="source_url" required placeholder="/2023/old-post-url" class="w-full px-3.5 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-slate-200 font-mono focus:outline-none focus:border-red-500" />
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 font-mono mb-1">New Target URL (/posts/slug)</label>
                        <input type="text" name="target_url" required placeholder="/posts/new-post-slug" class="w-full px-3.5 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-slate-200 font-mono focus:outline-none focus:border-red-500" />
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 font-mono mb-1">HTTP Status</label>
                        <select name="status_code" class="w-full px-3.5 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-slate-200 font-mono focus:outline-none focus:border-red-500">
                            <option value="301">301 (Permanent Redirect)</option>
                            <option value="302">302 (Temporary Redirect)</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full py-2.5 px-4 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-red-600/20 transition">
                            Create Redirect
                        </button>
                    </div>
                </form>
            </div>

            <!-- Redirects Table -->
            <div class="glass-panel rounded-3xl border border-slate-800 shadow-xl overflow-hidden">
                <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Recorded Redirect Rules ({{ $totalRedirects }})</span>
                    <form action="{{ route('admin.redirects.index') }}" method="GET" class="relative max-w-xs w-full">
                        <input type="text" name="q" value="{{ $search }}" placeholder="Search redirects..." class="w-full pl-8 pr-3 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-red-500" />
                        <svg class="w-3.5 h-3.5 absolute left-2.5 top-2.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-900/80 border-b border-slate-800 text-[11px] uppercase tracking-wider text-slate-400">
                            <tr>
                                <th class="py-3 px-6">Legacy Source URL</th>
                                <th class="py-3 px-4">New Target URL</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Redirect Hits</th>
                                <th class="py-3 px-4">Last Hit</th>
                                <th class="py-3 px-6 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 font-mono">
                            @forelse ($redirects as $r)
                                <tr class="hover:bg-slate-900/40 transition">
                                    <td class="py-3.5 px-6 text-red-400">{{ $r->source_url }}</td>
                                    <td class="py-3.5 px-4 text-emerald-400">{{ $r->target_url }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-400 border border-purple-500/20 text-[10px]">
                                            HTTP {{ $r->status_code }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-300">{{ number_format($r->hits) }}</td>
                                    <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                                        {{ $r->last_hit_at ? $r->last_hit_at->diffForHumans() : 'Never' }}
                                    </td>
                                    <td class="py-3.5 px-6 text-right">
                                        <form action="{{ route('admin.redirects.destroy', $r) }}" method="POST" class="inline" onsubmit="return confirm('Delete this redirect rule?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-400 hover:text-rose-300 transition text-xs font-sans">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-500 font-sans">
                                        No redirect rules recorded. Run WordPress migration to automatically generate 301 mappings.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($redirects->hasPages())
                    <div class="p-4 border-t border-slate-800 font-sans">
                        {{ $redirects->links() }}
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection
