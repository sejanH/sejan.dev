@extends('layouts.app')

@section('title', 'Contact Messages Inbox')

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
                        Contact Messages Inbox
                    </h1>
                    @if ($unreadCount > 0)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ $unreadCount }} Unread
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    View, moderate, and respond to incoming correspondence sent through the public contact form.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('blog.contact') }}"
                    target="_blank"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-200 transition flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span>View Public Form</span>
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 w-full">
            @if (session('status'))
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-xs text-emerald-800 flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
            @endif

            <!-- Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                    <div class="flex items-center justify-between text-slate-500 mb-1">
                        <span class="text-xs font-semibold">Total Submissions</span>
                        <div class="p-1.5 rounded-lg bg-slate-100 text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-slate-900">{{ $totalCount }}</div>
                    <div class="text-[11px] text-slate-400 mt-1">Lifetime messages</div>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                    <div class="flex items-center justify-between text-slate-500 mb-1">
                        <span class="text-xs font-semibold">Unread Messages</span>
                        <div class="p-1.5 rounded-lg bg-emerald-100 text-emerald-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-emerald-700">{{ $unreadCount }}</div>
                    <div class="text-[11px] text-slate-400 mt-1">Pending review</div>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                    <div class="flex items-center justify-between text-slate-500 mb-1">
                        <span class="text-xs font-semibold">Archived / Read</span>
                        <div class="p-1.5 rounded-lg bg-slate-100 text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-slate-900">{{ $readCount }}</div>
                    <div class="text-[11px] text-slate-400 mt-1">Processed inquiries</div>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.messages.index') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ empty($currentStatus) ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }} transition">
                        All ({{ $totalCount }})
                    </a>
                    <a href="{{ route('admin.messages.index', ['status' => 'unread', 'search' => $currentSearch]) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ $currentStatus === 'unread' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }} transition">
                        Unread ({{ $unreadCount }})
                    </a>
                    <a href="{{ route('admin.messages.index', ['status' => 'read', 'search' => $currentSearch]) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ $currentStatus === 'read' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }} transition">
                        Read ({{ $readCount }})
                    </a>
                </div>

                <form method="GET" action="{{ route('admin.messages.index') }}" class="flex items-center gap-2">
                    @if ($currentStatus)
                        <input type="hidden" name="status" value="{{ $currentStatus }}">
                    @endif
                    <div class="relative w-full sm:w-64">
                        <input
                            type="text"
                            name="search"
                            value="{{ $currentSearch }}"
                            placeholder="Search messages..."
                            class="w-full pl-9 pr-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                        />
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    @if ($currentSearch || $currentStatus)
                        <a href="{{ route('admin.messages.index') }}" class="p-1.5 rounded-xl bg-slate-200 text-slate-600 hover:bg-slate-300 transition" title="Clear Filters">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Messages Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/75 text-slate-500 font-semibold uppercase tracking-wider text-[10px]">
                                <th class="py-3 px-4 w-10 text-center">Status</th>
                                <th class="py-3 px-4">Sender</th>
                                <th class="py-3 px-4">Subject & Message Preview</th>
                                <th class="py-3 px-4">Received</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-normal">
                            @forelse ($messages as $message)
                                <tr class="hover:bg-slate-50/70 transition {{ !$message->is_read ? 'bg-emerald-50/20 font-medium' : '' }}">
                                    <!-- Status Indicator -->
                                    <td class="py-3.5 px-4 text-center">
                                        @if (!$message->is_read)
                                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-xs animate-pulse" title="Unread Message"></span>
                                        @else
                                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-slate-300" title="Read Message"></span>
                                        @endif
                                    </td>

                                    <!-- Sender Info -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-slate-900">
                                            {{ $message->name }}
                                        </div>
                                        <a href="mailto:{{ $message->email }}" class="text-[11px] text-slate-500 hover:text-emerald-600 font-mono transition inline-block">
                                            {{ $message->email }}
                                        </a>
                                    </td>

                                    <!-- Subject & Preview -->
                                    <td class="py-3.5 px-4 max-w-xs sm:max-w-md">
                                        <a href="{{ route('admin.messages.show', $message) }}" class="group block">
                                            <div class="font-semibold text-slate-900 group-hover:text-emerald-700 transition flex items-center gap-1.5">
                                                <span>{{ $message->subject ?: '(No Subject)' }}</span>
                                                @if (!$message->is_read)
                                                    <span class="px-1.5 py-0.2 rounded-md text-[9px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        New
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-[11px] text-slate-500 truncate group-hover:text-slate-700 mt-0.5">
                                                {{ Str::limit($message->message, 85) }}
                                            </p>
                                        </a>
                                    </td>

                                    <!-- Received Date -->
                                    <td class="py-3.5 px-4 text-slate-500 text-[11px] whitespace-nowrap">
                                        <div title="{{ $message->created_at->format('Y-m-d H:i:s') }}">
                                            {{ $message->created_at->diffForHumans() }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-mono">
                                            {{ $message->created_at->format('M d, Y') }}
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a
                                                href="{{ route('admin.messages.show', $message) }}"
                                                class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold border border-emerald-200/60 transition flex items-center gap-1"
                                                title="View Message"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span>Read</span>
                                            </a>

                                            <form action="{{ route('admin.messages.toggle', $message) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button
                                                    type="submit"
                                                    class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition"
                                                    title="{{ $message->is_read ? 'Mark as Unread' : 'Mark as Read' }}"
                                                >
                                                    @if ($message->is_read)
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    @endif
                                                </button>
                                            </form>

                                            <form
                                                action="{{ route('admin.messages.destroy', $message) }}"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this message?');"
                                                class="inline"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 transition"
                                                    title="Delete Message"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400 text-xs">
                                        No contact messages found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($messages->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $messages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection
