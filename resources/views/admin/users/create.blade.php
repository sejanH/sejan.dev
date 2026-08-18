@extends('layouts.app')

@section('title', 'Provision New User & Role')

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
                    <a href="{{ route('admin.users.index') }}" class="hover:text-slate-800 transition">Users & Roles</a>
                    <span>/</span>
                    <span class="text-slate-800 font-medium">New User</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                    Provision System User
                </h1>
                <p class="text-xs text-slate-500">
                    Create a new system user account and assign role-based capabilities.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('admin.users.index') }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-200 transition flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Back to Users</span>
                </a>
            </div>
        </header>

        <!-- Body -->
        <div class="p-4 sm:p-6 lg:p-8 max-w-4xl w-full mx-auto">
            @if ($errors->any())
                <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-xs text-rose-800 shadow-xs">
                    <div class="flex items-center gap-2 font-bold mb-2">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Please correct the errors below:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Account Credentials Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-5">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-sm font-bold text-slate-900">User Identity & Authentication</h2>
                        <p class="text-xs text-slate-500">Essential account details and credentials for system sign-in.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Full Name -->
                        <div>
                            <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Full Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                placeholder="e.g. Jane Doe"
                                class="w-full px-3.5 py-2 text-xs bg-slate-50 border {{ $errors->has('name') ? 'border-rose-300 ring-1 ring-rose-500/20' : 'border-slate-200' }} rounded-xl focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                            />
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Email Address <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                placeholder="e.g. jane@example.com"
                                class="w-full px-3.5 py-2 text-xs bg-slate-50 border {{ $errors->has('email') ? 'border-rose-300 ring-1 ring-rose-500/20' : 'border-slate-200' }} rounded-xl focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Password <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                placeholder="Minimum 8 characters"
                                class="w-full px-3.5 py-2 text-xs bg-slate-50 border {{ $errors->has('password') ? 'border-rose-300 ring-1 ring-rose-500/20' : 'border-slate-200' }} rounded-xl focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                            />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Confirm Password <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                                placeholder="Re-enter password"
                                class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                            />
                        </div>
                    </div>
                </div>

                <!-- Role Selection Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-sm font-bold text-slate-900">Role & Access Level <span class="text-rose-500">*</span></h2>
                        <p class="text-xs text-slate-500">Select the permission profile that will govern this user's administrative access.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        @php $selectedRole = old('role', 'editor'); @endphp

                        <!-- Admin Role -->
                        <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition {{ $selectedRole === 'admin' ? 'border-emerald-500 bg-emerald-50/40 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    <span class="text-xs font-bold text-slate-900">Administrator</span>
                                </div>
                                <input
                                    type="radio"
                                    name="role"
                                    value="admin"
                                    class="text-emerald-600 focus:ring-emerald-500 w-4 h-4"
                                    {{ $selectedRole === 'admin' ? 'checked' : '' }}
                                />
                            </div>
                            <p class="text-[11px] text-slate-500">
                                Full control of the system: user management, role assignments, system settings, WP migration, and all content.
                            </p>
                            <div class="mt-3 pt-2 border-t border-slate-200/60 text-[10px] font-mono text-emerald-700">
                                All permissions (manage users, settings, posts, media, comments)
                            </div>
                        </label>

                        <!-- Editor Role -->
                        <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition {{ $selectedRole === 'editor' ? 'border-blue-500 bg-blue-50/40 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                    <span class="text-xs font-bold text-slate-900">Editor</span>
                                </div>
                                <input
                                    type="radio"
                                    name="role"
                                    value="editor"
                                    class="text-blue-600 focus:ring-blue-500 w-4 h-4"
                                    {{ $selectedRole === 'editor' ? 'checked' : '' }}
                                />
                            </div>
                            <p class="text-[11px] text-slate-500">
                                Manage and publish all blog posts, moderate user comments, and organize the media library.
                            </p>
                            <div class="mt-3 pt-2 border-t border-slate-200/60 text-[10px] font-mono text-blue-700">
                                manage posts, manage comments, manage media
                            </div>
                        </label>

                        <!-- Author Role -->
                        <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition {{ $selectedRole === 'author' ? 'border-amber-500 bg-amber-50/40 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                    <span class="text-xs font-bold text-slate-900">Author</span>
                                </div>
                                <input
                                    type="radio"
                                    name="role"
                                    value="author"
                                    class="text-amber-600 focus:ring-amber-500 w-4 h-4"
                                    {{ $selectedRole === 'author' ? 'checked' : '' }}
                                />
                            </div>
                            <p class="text-[11px] text-slate-500">
                                Draft and publish their own technical articles and upload supporting diagrams/media.
                            </p>
                            <div class="mt-3 pt-2 border-t border-slate-200/60 text-[10px] font-mono text-amber-700">
                                create posts, edit own posts, manage media
                            </div>
                        </label>

                        <!-- Standard User Role -->
                        <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition {{ $selectedRole === 'user' ? 'border-slate-500 bg-slate-100/60 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                                    <span class="text-xs font-bold text-slate-900">Standard User</span>
                                </div>
                                <input
                                    type="radio"
                                    name="role"
                                    value="user"
                                    class="text-slate-600 focus:ring-slate-500 w-4 h-4"
                                    {{ $selectedRole === 'user' ? 'checked' : '' }}
                                />
                            </div>
                            <p class="text-[11px] text-slate-500">
                                Read public articles and participate in discussions. No administrative panel access.
                            </p>
                            <div class="mt-3 pt-2 border-t border-slate-200/60 text-[10px] font-mono text-slate-500">
                                Public reader access
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Form Submit Actions -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="px-4 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold border border-slate-200 transition"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-95 text-white text-xs font-semibold shadow-md shadow-emerald-500/20 transition flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span>Create User Account</span>
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection
