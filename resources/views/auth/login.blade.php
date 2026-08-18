@extends('layouts.app')

@section('title', 'Admin Sign In')

@section('layout')
<div class="relative min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 lg:p-8 bg-gradient-to-br from-emerald-50/80 via-white to-teal-50/40 overflow-hidden text-slate-900">
    <!-- Ambient Background Glows -->
    <div class="absolute top-1/4 -left-20 w-96 h-96 bg-emerald-300/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-teal-300/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-emerald-200/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative w-full max-w-md">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/20 mb-4 ring-8 ring-emerald-500/10 text-white font-bold text-xl">
                sz
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                sejan<span class="text-emerald-600">.dev</span>
            </h1>
            <p class="text-sm text-slate-500 mt-1 font-medium">
                Admin Control Center
            </p>
        </div>

        <!-- Main Card -->
        <div class="glass-panel rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200 bg-white relative">
            <!-- Registration Disabled Notice Badge -->
            <div class="mb-6 rounded-2xl bg-amber-50 border border-amber-200 p-3.5 flex items-start gap-3">
                <div class="shrink-0 w-5 h-5 text-amber-600 mt-0.5">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="text-xs text-amber-800 leading-relaxed">
                    <span class="font-bold text-amber-900">Registration Disabled:</span> Only pre-configured seeder administrator accounts can log in.
                </div>
            </div>

            <!-- Flash Session Status -->
            @if (session('status'))
                <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs text-emerald-800 flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Validation Errors Alert -->
            @if ($errors->any())
                <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-3 text-xs text-rose-800">
                    <div class="font-semibold flex items-center gap-1.5 mb-1 text-rose-900">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Authentication Failed</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-rose-700">
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
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                        Admin Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            placeholder="Email address"
                            class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border @error('email') border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:border-emerald-500 focus:bg-white @enderror rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition shadow-2xs"
                        />
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Password
                        </label>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
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
                            class="w-full pl-11 pr-11 py-2.5 bg-slate-50 border @error('password') border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:border-emerald-500 focus:bg-white @enderror rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition shadow-2xs"
                        />
                        <button
                            type="button"
                            onclick="togglePasswordVisibility()"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 transition"
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
                            class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-0 transition"
                            checked
                        />
                        <span class="text-xs text-slate-600 group-hover:text-slate-900 select-none">Remember this session</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full py-3 px-4 bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-95 text-white font-semibold rounded-xl text-sm shadow-md shadow-emerald-500/20 hover:scale-[1.01] active:scale-[0.99] transition duration-150 flex items-center justify-center gap-2"
                >
                    <span>Sign In to Admin Dashboard</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-xs text-slate-500">
            <span>PHP 8.2+</span> &bull; <span>sejan.dev</span>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.add('text-emerald-600');
        } else {
            input.type = 'password';
            icon.classList.remove('text-emerald-600');
        }
    }
</script>
@endsection
