@extends('layouts.auth')

@section('title', __('auth.login'))

@section('content')




<div class="relative min-h-screen px-4 py-8 flex flex-col">

    {{-- Language --}}
    <div class="flex justify-end max-w-7xl w-full mx-auto">
        <x-ui.language-switch />
    </div>

    {{-- Login Area --}}
    <div class="flex-1 flex items-center justify-center py-10">

        <div class="w-full max-w-[480px] bg-white rounded-2xl shadow-xl shadow-slate-300/40 px-8 sm:px-10 py-10 sm:py-12">

            {{-- Logo --}}
            <div class="flex flex-col items-center text-center">

                <div class="w-16 h-16 rounded-xl bg-[#0f1b3d] flex items-center justify-center mb-5">
                    <x-brand-logo variant="white" class="h-11 w-11" />
                </div>

                <h1 class="text-2xl font-bold text-black">
                    {{ __('auth.brand') }}
                </h1>

                <h2 class="mt-8 text-3xl font-bold text-slate-900">
                    {{ __('auth.welcome') }}
                </h2>

                <p class="mt-3 text-sm sm:text-base text-slate-600">
                    {{ __('auth.subtitle') }}
                </p>
            </div>

            {{-- Form --}}
                <form
            id="adminLoginForm"
            method="POST"
            action="{{ route('admin.login.submit') }}"
            class="mt-10 space-y-6"
            autocomplete="on"
        >
            @csrf

            <x-ui.input
                label="{{ __('auth.email') }}"
                name="email"
                type="email"
                icon="email"
                placeholder="admin@myaman.com"
                autocomplete="username"
                required
                autofocus
            />

            <div>
                <label for="password" class="block mb-2 text-sm font-semibold text-slate-900">
                    {{ __('auth.password') }}
                </label>

                <div class="relative">
                    <span class="absolute start-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <rect x="5" y="10" width="14" height="10" rx="2"/>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                        </svg>
                    </span>

                    <input
                        id="password"
                        name="password"
                        type="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                        class="w-full h-14 rounded-xl border border-slate-300 bg-slate-50 ps-12 pe-12 text-sm text-slate-900 outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                    >

                    <button
                        type="button"
                        onclick="togglePassword()"
                        class="absolute end-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700"
                        aria-label="Toggle password visibility"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>

                {{-- @error('password')
                    <p class="mt-2 text-xs text-red-600 flex items-center gap-1">
                        <span>ⓘ</span>
                        {{ $message }}
                    </p>
                @enderror --}}
            </div>

            <button
                id="loginSubmitBtn"
                type="submit"
                class="w-full h-14 rounded-xl bg-[#0f1b3d] text-white font-semibold shadow-lg shadow-slate-300 hover:bg-[#111f49] transition flex items-center justify-center gap-3 disabled:opacity-90 disabled:cursor-not-allowed"
            >
                <span class="btn-text">{{ __('auth.login') }}</span>

                <span class="btn-loading hidden items-center justify-center gap-3">
                    <span class="w-5 h-5 rounded-full border-2 border-white/30 border-t-white animate-spin"></span>
                    <span>{{ __('auth.login') }}</span>
                </span>
            </button>
                </form>
            {{-- Secure Text --}}
            <div class="mt-12 flex items-center justify-center gap-2 text-xs font-semibold tracking-wide text-slate-500 uppercase">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <rect x="5" y="10" width="14" height="10" rx="2"/>
                    <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                </svg>
                <span>{{ __('auth.secure') }}</span>
            </div>

        </div>

    </div>

    {{-- Support --}}
    <div class="text-center text-sm text-slate-600 pb-6">
        {{ __('auth.support_text') }}
        <a href="#" class="font-semibold text-blue-700 hover:underline">
            {{ __('auth.support_link') }}
        </a>
    </div>

</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');

        input.type = input.type === 'password' ? 'text' : 'password';
    }
        function togglePassword() {
        const input = document.getElementById('password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    document.getElementById('adminLoginForm').addEventListener('submit', function () {
        const button = document.getElementById('loginSubmitBtn');
        const btnText = button.querySelector('.btn-text');
        const btnLoading = button.querySelector('.btn-loading');

        button.disabled = true;
        btnText.classList.add('hidden');
        btnLoading.classList.remove('hidden');
        btnLoading.classList.add('flex');
    });
</script>
@if(session('redirect_url'))
    <script>
        setTimeout(function () {
            window.location.href = "{{ session('redirect_url') }}";
        }, 600);
    </script>
@endif

@endsection
