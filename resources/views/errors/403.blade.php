@extends('layouts.app')

@section('title', 'Access Forbidden')

@section('layout')
<div class="min-h-screen flex flex-col items-center justify-center p-6 bg-slate-950 text-center relative overflow-hidden">
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b15_1px,transparent_1px),linear-gradient(to_bottom,#1e293b15_1px,transparent_1px)] bg-[size:4rem_4rem] pointer-events-none"></div>

    <div class="relative max-w-md w-full glass-panel rounded-3xl p-8 border border-slate-800 shadow-2xl">
        <div class="w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center mx-auto mb-5 shadow-lg shadow-amber-500/5">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>

        <h1 class="text-2xl font-extrabold text-white mb-2">Registration Disabled</h1>
        <p class="text-sm text-slate-400 mb-6 leading-relaxed">
            {{ $exception->getMessage() ?: 'Public registration is disabled on this system. Only pre-configured seeder administrators are permitted.' }}
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a
                href="{{ route('login') }}"
                class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white text-xs font-semibold shadow-lg shadow-red-600/30 transition"
            >
                Go to Admin Sign In
            </a>
            @auth
                <a
                    href="{{ route('dashboard') }}"
                    class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition"
                >
                    Return to Dashboard
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
