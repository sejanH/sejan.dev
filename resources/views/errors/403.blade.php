@extends('layouts.app')

@section('title', 'Access Forbidden')

@section('layout')
<div class="min-h-screen flex flex-col items-center justify-center p-6 bg-gradient-to-br from-emerald-50/80 via-white to-teal-50/40 text-center relative overflow-hidden text-slate-900">
    <div class="relative max-w-md w-full glass-panel rounded-3xl p-8 border border-slate-200 bg-white shadow-xl">
        <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center mx-auto mb-5 shadow-xs">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>

        <h1 class="text-2xl font-extrabold text-slate-900 mb-2">Registration Disabled</h1>
        <p class="text-sm text-slate-500 mb-6 leading-relaxed">
            {{ $exception->getMessage() ?: 'Public registration is disabled on this system. Only pre-configured seeder administrators are permitted.' }}
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a
                href="{{ route('login') }}"
                class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-95 text-white text-xs font-semibold shadow-md shadow-emerald-500/20 transition"
            >
                Go to Admin Sign In
            </a>
            @auth
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-200 transition"
                >
                    Return to Dashboard
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
