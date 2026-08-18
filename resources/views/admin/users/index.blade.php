@extends('layouts.app')

@section('title', 'Manage System Users & Roles')

@section('layout')
<div class="min-h-screen bg-slate-50 flex flex-col lg:flex-row text-slate-900">
    <!-- Sidebar -->
    @include('admin.partials.sidebar')

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Header -->
        <header class="p-4 sm:p-6 border-b border-slate-200 bg-white/90 backdrop-blur-md sticky top-0 z-20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                        Users & Roles Management
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        RBAC Engine
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Configure granular role-based permissions, provision new administrators, editors, and authors.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('admin.users.create') }}"
                    class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-95 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-500/20 transition flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>Create New User</span>
                </a>
            </div>
        </header>

        <!-- Main Body -->
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 w-full">
            @if (session('status'))
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-xs text-emerald-800 flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4 text-xs text-rose-800 flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Role Metrics Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                    <div class="flex items-center justify-between text-slate-500 mb-1">
                        <span class="text-xs font-semibold">Total Accounts</span>
                        <div class="p-1.5 rounded-lg bg-slate-100 text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-slate-900">{{ $totalUsers }}</div>
                    <div class="text-[11px] text-slate-400 mt-1">System registered</div>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                    <div class="flex items-center justify-between text-slate-500 mb-1">
                        <span class="text-xs font-semibold">Administrators</span>
                        <div class="p-1.5 rounded-lg bg-emerald-100 text-emerald-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-emerald-700">{{ $adminCount }}</div>
                    <div class="text-[11px] text-slate-400 mt-1">Full system access</div>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                    <div class="flex items-center justify-between text-slate-500 mb-1">
                        <span class="text-xs font-semibold">Editors</span>
                        <div class="p-1.5 rounded-lg bg-blue-100 text-blue-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-blue-700">{{ $editorCount }}</div>
                    <div class="text-[11px] text-slate-400 mt-1">Content & moderation</div>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                    <div class="flex items-center justify-between text-slate-500 mb-1">
                        <span class="text-xs font-semibold">Authors</span>
                        <div class="p-1.5 rounded-lg bg-amber-100 text-amber-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-amber-700">{{ $authorCount }}</div>
                    <div class="text-[11px] text-slate-400 mt-1">Article creators</div>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.users.index') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ empty($currentRole) ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }} transition">
                        All Users ({{ $totalUsers }})
                    </a>
                    @foreach ($roles as $role)
                        <a href="{{ route('admin.users.index', ['role' => $role->name, 'search' => $currentSearch]) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ $currentRole === $role->name ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }} transition capitalize">
                            {{ $role->name }}
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2">
                    @if ($currentRole)
                        <input type="hidden" name="role" value="{{ $currentRole }}">
                    @endif
                    <div class="relative w-full sm:w-64">
                        <input
                            type="text"
                            name="search"
                            value="{{ $currentSearch }}"
                            placeholder="Search name or email..."
                            class="w-full pl-9 pr-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                        />
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    @if ($currentSearch || $currentRole)
                        <a href="{{ route('admin.users.index') }}" class="p-1.5 rounded-xl bg-slate-200 text-slate-600 hover:bg-slate-300 transition" title="Clear Filters">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Users Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/75 text-slate-500 font-semibold uppercase tracking-wider text-[10px]">
                                <th class="py-3 px-4">User</th>
                                <th class="py-3 px-4">Role & Access</th>
                                <th class="py-3 px-4">Direct Permissions</th>
                                <th class="py-3 px-4">Joined Date</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-normal">
                            @forelse ($users as $user)
                                @php
                                    $primaryRole = $user->roles->first()?->name ?? $user->role ?? 'user';
                                    $isCurrentUser = auth()->id() === $user->id;
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition">
                                    <!-- User Info -->
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl {{ $primaryRole === 'admin' ? 'bg-gradient-to-br from-emerald-500 to-teal-600 text-white' : ($primaryRole === 'editor' ? 'bg-gradient-to-br from-blue-500 to-indigo-600 text-white' : ($primaryRole === 'author' ? 'bg-gradient-to-br from-amber-500 to-orange-600 text-white' : 'bg-slate-200 text-slate-700')) }} flex items-center justify-center font-bold text-xs shadow-2xs shrink-0">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-slate-900 flex items-center gap-2">
                                                    <span>{{ $user->name }}</span>
                                                    @if ($isCurrentUser)
                                                        <span class="px-1.5 py-0.2 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                            You
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                                    {{ $user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Role Badge -->
                                    <td class="py-3.5 px-4">
                                        @if ($primaryRole === 'admin')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                </svg>
                                                Administrator
                                            </span>
                                        @elseif ($primaryRole === 'editor')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-800 border border-blue-200">
                                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Editor
                                            </span>
                                        @elseif ($primaryRole === 'author')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                                Author
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                Standard User
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Permissions Summary -->
                                    <td class="py-3.5 px-4 text-slate-600">
                                        @if ($primaryRole === 'admin')
                                            <span class="text-[11px] text-emerald-700 font-medium">All Capabilities & User Management</span>
                                        @elseif ($primaryRole === 'editor')
                                            <span class="text-[11px] text-blue-700 font-medium">Posts, Media, Comment Moderation</span>
                                        @elseif ($primaryRole === 'author')
                                            <span class="text-[11px] text-amber-700 font-medium">Create Articles, Media Uploads</span>
                                        @else
                                            <span class="text-[11px] text-slate-400">Public Reading & Comments</span>
                                        @endif
                                    </td>

                                    <!-- Joined Date -->
                                    <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                                        {{ $user->created_at ? $user->created_at->format('M d, Y') : 'Pre-seeded' }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a
                                                href="{{ route('admin.users.edit', $user) }}"
                                                class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition flex items-center gap-1"
                                                title="Edit User and Roles"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span>Edit</span>
                                            </a>

                                            @if (!$isCurrentUser)
                                                <form
                                                    action="{{ route('admin.users.destroy', $user) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Are you sure you want to permanently delete user \'{{ addslashes($user->name) }}\'? This action cannot be undone.');"
                                                    class="inline"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold border border-rose-200/60 transition flex items-center gap-1"
                                                        title="Delete User"
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        <span>Delete</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400 text-xs">
                                        No users found matching your search or filter criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection
