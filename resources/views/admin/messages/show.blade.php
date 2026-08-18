@extends('layouts.app')

@section('title', 'Message from ' . $message->name)

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
                    <a href="{{ route('admin.messages.index') }}" class="hover:text-slate-800 transition">Contact Inbox</a>
                    <span>/</span>
                    <span class="text-slate-800 font-medium">Message Details</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span>{{ $message->subject ?: 'Correspondence' }}</span>
                </h1>
                <p class="text-xs text-slate-500">
                    Received {{ $message->created_at->format('F d, Y \a\t g:i A') }} ({{ $message->created_at->diffForHumans() }})
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <a
                    href="{{ route('admin.messages.index') }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-200 transition flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Back to Inbox</span>
                </a>

                <a
                    href="mailto:{{ $message->email }}?subject={{ urlencode('Re: ' . ($message->subject ?: 'Your inquiry')) }}"
                    class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-95 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-500/20 transition flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Reply via Email</span>
                </a>
            </div>
        </header>

        <!-- Body -->
        <div class="p-4 sm:p-6 lg:p-8 max-w-4xl w-full mx-auto space-y-6">
            @if (session('status'))
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-xs text-emerald-800 flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
            @endif

            <!-- Sender Information Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4 mb-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center font-bold text-sm shadow-2xs shrink-0">
                            {{ strtoupper(substr($message->name, 0, 2)) }}
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">{{ $message->name }}</h2>
                            <a href="mailto:{{ $message->email }}" class="text-xs text-emerald-700 hover:underline font-mono">
                                {{ $message->email }}
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <form action="{{ route('admin.messages.toggle', $message) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button
                                type="submit"
                                class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $message->is_read ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-emerald-100 hover:bg-emerald-200 text-emerald-800' }} transition flex items-center gap-1.5"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
                                </svg>
                                <span>{{ $message->is_read ? 'Mark as Unread' : 'Mark as Read' }}</span>
                            </button>
                        </form>

                        <form
                            action="{{ route('admin.messages.destroy', $message) }}"
                            method="POST"
                            onsubmit="return confirm('Are you sure you want to permanently delete this contact message?');"
                        >
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200/60 text-xs font-semibold transition flex items-center gap-1.5"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Delete</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Message Metadata Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Subject</span>
                        <span class="font-medium text-slate-800">{{ $message->subject ?: '(None provided)' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">IP Address</span>
                        <span class="font-mono text-slate-600">{{ $message->ip_address ?: 'Unknown' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Status</span>
                        <span class="font-medium {{ $message->is_read ? 'text-slate-600' : 'text-emerald-700' }}">
                            {{ $message->is_read ? 'Read (' . $message->read_at?->format('M d, H:i') . ')' : 'Unread' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Message Body Card -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                    Message Content
                </h3>
                <div class="text-sm text-slate-800 leading-relaxed whitespace-pre-line bg-slate-50/60 p-5 rounded-xl border border-slate-100">
                    {{ $message->message }}
                </div>
            </div>

            <!-- Quick Reply Card -->
            <div class="bg-emerald-50/50 border border-emerald-200/70 p-5 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h4 class="text-xs font-bold text-emerald-900">Direct Communication</h4>
                    <p class="text-[11px] text-emerald-700 mt-0.5">Click below to open your preferred mail client with a prefilled recipient and subject line.</p>
                </div>
                <a
                    href="mailto:{{ $message->email }}?subject={{ urlencode('Re: ' . ($message->subject ?: 'Your inquiry')) }}"
                    class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-95 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-500/20 transition flex items-center gap-2 shrink-0"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Compose Reply to {{ $message->name }}</span>
                </a>
            </div>
        </div>
    </main>
</div>
@endsection
