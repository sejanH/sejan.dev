@extends('layouts.app')

@section('title', 'Admin Sign In')

@section('layout')
<div class="relative min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 lg:p-8 bg-slate-950 overflow-hidden">
    <!-- Ambient Background Glows -->
    <div class="absolute top-1/4 -left-20 w-96 h-96 bg-red-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Background Grid Pattern -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b15_1px,transparent_1px),linear-gradient(to_bottom,#1e293b15_1px,transparent_1px)] bg-[size:4rem_4rem] pointer-events-none"></div>

    <div class="relative w-full max-w-md">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-red-500 to-rose-600 shadow-xl shadow-red-500/20 border border-red-400/30 mb-4 ring-8 ring-red-500/10">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                sejan<span class="text-red-500">.dev</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1 font-medium">
                WordPress to Laravel 12 Migration Portal
            </p>
        </div>

        <!-- Main Card -->
        <div class="glass-panel rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-800/80 relative">
            <!-- Registration Disabled Notice Badge -->
            <div class="mb-6 rounded-2xl bg-amber-500/10 border border-amber-500/20 p-3.5 flex items-start gap-3">
                <div class="flex-shrink-0 w-5 h-5 text-amber-400 mt-0.5">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="text-xs text-amber-200/90 leading-relaxed">
                    <span class="font-bold text-amber-300">Registration Disabled:</span> Only pre-configured seeder administrator accounts can log in.
                </div>
            </div>

            <!-- Flash Session Status -->
            @if (session('status'))
                <div class="mb-6 rounded-xl bg-emerald-500/10 border border-emerald-500/20 p-3 text-xs text-emerald-300 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Validation Errors Alert -->
            @if ($errors->any())
                <div class="mb-6 rounded-xl bg-red-500/10 border border-red-500/20 p-3 text-xs text-red-300">
                    <div class="font-semibold flex items-center gap-1.5 mb-1 text-red-200">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Authentication Failed</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-red-300/80">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login.attempt') }}" method="POST" class="space-y-5" id="loginForm">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Admin Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email', 'admin@sejan.dev') }}"
                            required
                            autofocus
                            placeholder="admin@sejan.dev"
                            class="w-full pl-11 pr-4 py-2.5 bg-slate-900/90 border @error('email') border-red-500/80 focus:ring-red-500 @else border-slate-700/80 focus:border-red-500 focus:ring-red-500/30 @enderror rounded-xl text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 transition shadow-inner"
                        />
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Password
                        </label>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            placeholder="••••••••"
                            value="password"
                            class="w-full pl-11 pr-11 py-2.5 bg-slate-900/90 border @error('password') border-red-500/80 focus:ring-red-500 @else border-slate-700/80 focus:border-red-500 focus:ring-red-500/30 @enderror rounded-xl text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 transition shadow-inner"
                        />
                        <button
                            type="button"
                            onclick="togglePasswordVisibility()"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition"
                            tabindex="-1"
                        >
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-red-600 focus:ring-red-500/30 focus:ring-offset-0 transition"
                            checked
                        />
                        <span class="text-xs text-slate-400 group-hover:text-slate-300 select-none">Remember this session</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full py-3 px-4 bg-gradient-to-r from-red-600 via-rose-600 to-red-700 hover:from-red-500 hover:to-rose-600 text-white font-semibold rounded-xl text-sm shadow-lg shadow-red-600/30 hover:shadow-red-600/50 hover:scale-[1.01] active:scale-[0.99] transition duration-150 flex items-center justify-center gap-2"
                >
                    <span>Sign In to Admin Dashboard</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>

            <!-- Seeded Admin Hint Card -->
            <div class="mt-6 pt-5 border-t border-slate-800/80">
                <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                    <span class="font-medium flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Seeded Admin Account
                    </span>
                    <button
                        type="button"
                        onclick="fillAdminCredentials()"
                        class="text-red-400 hover:text-red-300 font-semibold underline text-[11px]"
                    >
                        Auto-Fill
                    </button>
                </div>
                <div class="bg-slate-900/90 rounded-xl p-2.5 text-[11px] font-mono text-slate-400 flex justify-between border border-slate-800">
                    <div><span class="text-slate-500">Email:</span> <span class="text-slate-200">admin@sejan.dev</span></div>
                    <div><span class="text-slate-500">Pass:</span> <span class="text-slate-200">password</span></div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-xs text-slate-500">
            <span>Laravel v12.x</span> &bull; <span>PHP 8.2+</span> &bull; <span>sejan.dev</span>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.add('text-red-400');
        } else {
            input.type = 'password';
            icon.classList.remove('text-red-400');
        }
    }

    function fillAdminCredentials() {
        document.getElementById('email').value = 'admin@sejan.dev';
        document.getElementById('password').value = 'password';
    }
</script>
@endsection
