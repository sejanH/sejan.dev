<aside class="w-full lg:w-56 bg-white border-b lg:border-b-0 lg:border-r border-slate-200 flex flex-col flex-shrink-0">
    <!-- Brand Header -->
    <div class="py-3 px-3.5 border-b border-slate-200 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-xs text-white font-bold text-xs">
                sz
            </div>
            <div>
                <h2 class="font-bold text-xs text-slate-900 tracking-tight leading-tight">
                    sejan<span class="text-emerald-600">.dev</span>
                </h2>
                <p class="text-[10px] text-slate-400 font-medium">Control Center</p>
            </div>
        </div>
        <a href="{{ route('home') }}" target="_blank" title="View Public Blog" class="p-1 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
        </a>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 p-2 space-y-0.5 overflow-y-auto">
        <div class="px-2 pt-1.5 pb-0.5 text-[9px] font-bold uppercase tracking-wider text-slate-400">
            Control Center
        </div>

        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/80 font-semibold shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span class="truncate">Dashboard</span>
        </a>

        <a href="{{ route('admin.posts.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition {{ request()->routeIs('admin.posts.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/80 font-semibold shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.posts.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            <span class="truncate">Articles</span>
        </a>

        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.tags.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/80 font-semibold shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.tags.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            <span class="truncate">Taxonomies</span>
        </a>

        <a href="{{ route('admin.media.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition {{ request()->routeIs('admin.media.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/80 font-semibold shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.media.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="truncate">Media</span>
        </a>

        <a href="{{ route('admin.comments.index') }}" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-medium transition {{ request()->routeIs('admin.comments.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/80 font-semibold shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <div class="flex items-center gap-2 min-w-0">
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.comments.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span class="truncate">Comments</span>
            </div>
            @php $pendingCount = \App\Models\Comment::where('status', 'pending')->count(); @endphp
            @if ($pendingCount > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-200 shrink-0">
                    {{ $pendingCount }}
                </span>
            @endif
        </a>

        <a href="{{ route('admin.messages.index') }}" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-medium transition {{ request()->routeIs('admin.messages.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/80 font-semibold shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <div class="flex items-center gap-2 min-w-0">
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.messages.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span class="truncate">Messages</span>
            </div>
            @php $unreadMessages = \App\Models\ContactMessage::unread()->count(); @endphp
            @if ($unreadMessages > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shrink-0">
                    {{ $unreadMessages }}
                </span>
            @endif
        </a>

        <div class="px-2 pt-2 pb-0.5 text-[9px] font-bold uppercase tracking-wider text-slate-400">
            Ingestion Pipeline
        </div>

        <a href="{{ route('admin.wordpress.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition {{ request()->routeIs('admin.wordpress.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/80 font-semibold shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.wordpress.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            <span class="truncate">WP Pipeline</span>
        </a>

        <a href="{{ route('admin.redirects.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition {{ request()->routeIs('admin.redirects.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/80 font-semibold shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.redirects.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            <span class="truncate">301 Redirects</span>
        </a>

        @if (auth()->user()?->hasRole('admin') || auth()->user()?->can('manage users') || auth()->user()?->isAdmin())
            <div class="px-2 pt-2 pb-0.5 text-[9px] font-bold uppercase tracking-wider text-slate-400">
                Administration & Access
            </div>

            <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-medium transition {{ request()->routeIs('admin.users.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/80 font-semibold shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <div class="flex items-center gap-2 min-w-0">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="truncate">Users & Roles</span>
                </div>
                @php $userCount = \App\Models\User::count(); @endphp
                <span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-slate-100 text-slate-600 border border-slate-200 shrink-0">
                    {{ $userCount }}
                </span>
            </a>
        @endif
    </nav>

    <!-- User Profile & Logout -->
    <div class="py-2.5 px-3 border-t border-slate-200 bg-slate-50">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 min-w-0">
                <div class="w-7 h-7 rounded-lg bg-white border border-slate-200 shadow-2xs flex items-center justify-center text-xs font-bold text-emerald-700 shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'Admin', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-semibold text-slate-900 truncate flex items-center gap-1">
                        <span class="truncate">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                    </div>
                    <div class="text-[10px] text-slate-400 truncate font-mono">{{ auth()->user()->email ?? 'admin@sejan.dev' }}</div>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="ml-1">
                @csrf
                <button
                    type="submit"
                    title="Sign Out"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-slate-200/70 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
