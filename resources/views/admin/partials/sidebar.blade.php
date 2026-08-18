<aside class="w-full lg:w-72 bg-slate-900/80 border-b lg:border-b-0 lg:border-r border-slate-800 flex flex-col flex-shrink-0">
    <!-- Brand Header -->
    <div class="p-5 sm:p-6 border-b border-slate-800 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-500 to-rose-600 flex items-center justify-center shadow-lg shadow-red-500/20 border border-red-400/30">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-base text-white tracking-tight leading-tight">
                    sejan<span class="text-red-500">.dev</span>
                </h2>
                <p class="text-[11px] text-slate-400 font-medium">Admin & Migration Portal</p>
            </div>
        </div>
        <a href="{{ route('home') }}" target="_blank" title="View Public Blog" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
        </a>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
        <div class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
            Control Center
        </div>

        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-red-600/20 to-rose-600/10 text-white border border-red-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-red-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span>Dashboard Overview</span>
        </a>

        <a href="{{ route('admin.posts.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.posts.*') ? 'bg-gradient-to-r from-red-600/20 to-rose-600/10 text-white border border-red-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('admin.posts.*') ? 'text-red-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            <span>Blog Articles</span>
        </a>

        <a href="{{ route('admin.media.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.media.*') ? 'bg-gradient-to-r from-red-600/20 to-rose-600/10 text-white border border-red-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('admin.media.*') ? 'text-red-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Media Library</span>
        </a>

        <a href="{{ route('admin.comments.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.comments.*') ? 'bg-gradient-to-r from-red-600/20 to-rose-600/10 text-white border border-red-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.comments.*') ? 'text-red-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span>Comments</span>
            </div>
            @php $pendingCount = \App\Models\Comment::where('status', 'pending')->count(); @endphp
            @if ($pendingCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    {{ $pendingCount }}
                </span>
            @endif
        </a>

        <div class="pt-4 px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
            WordPress Ingestion
        </div>

        <a href="{{ route('admin.wordpress.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.wordpress.*') ? 'bg-gradient-to-r from-red-600/20 to-rose-600/10 text-white border border-red-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('admin.wordpress.*') ? 'text-red-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            <span>WP Migration Engine</span>
        </a>

        <a href="{{ route('admin.redirects.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.redirects.*') ? 'bg-gradient-to-r from-red-600/20 to-rose-600/10 text-white border border-red-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('admin.redirects.*') ? 'text-red-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            <span>301 SEO Redirects</span>
        </a>

        <div class="pt-4 px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
            Security Status
        </div>

        <div class="px-3.5 py-2 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-300 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
            </svg>
            <span>Registration Disabled</span>
        </div>
    </nav>

    <!-- User Profile & Logout -->
    <div class="p-4 border-t border-slate-800 bg-slate-900/90">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-sm font-bold text-red-400 flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'Admin', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-semibold text-white truncate flex items-center gap-1.5">
                        <span>{{ auth()->user()->name ?? 'Admin' }}</span>
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    </div>
                    <div class="text-[11px] text-slate-400 truncate font-mono">{{ auth()->user()->email ?? 'admin@sejan.dev' }}</div>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="ml-2">
                @csrf
                <button
                    type="submit"
                    title="Sign Out"
                    class="p-2 rounded-xl text-slate-400 hover:text-red-400 hover:bg-slate-800/80 transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
