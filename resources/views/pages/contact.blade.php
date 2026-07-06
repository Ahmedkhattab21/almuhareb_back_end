@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('landing.contact_page.title') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    @include('partials.vite-assets')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>
        body {
            font-family: 'IBM Plex Sans Arabic', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F5F7FB] text-[#0F172A] antialiased">

<header class="sticky top-0 z-50 border-b border-slate-200/70 bg-white/90 backdrop-blur-xl">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('landing') }}" class="inline-flex items-center gap-3 text-xl font-extrabold text-[#0F172A]">
            <img src="{{ asset('brand/myaman-icon-navy.png') }}" alt="myaman" class="h-9 w-9 object-contain">
            {{ __('landing.brand') }}
        </a>

        <nav class="hidden items-center gap-8 lg:flex">
            <a href="{{ route('landing') }}" class="text-sm font-medium text-slate-600 hover:text-[#0F172A]">
                {{ __('landing.nav.home') }}
            </a>

            <a href="{{ route('landing') }}#features" class="text-sm font-medium text-slate-600 hover:text-[#0F172A]">
                {{ __('landing.nav.features') }}
            </a>

            <a href="{{ route('landing') }}#how-it-works" class="text-sm font-medium text-slate-600 hover:text-[#0F172A]">
                {{ __('landing.nav.how_it_works') }}
            </a>

            <a href="{{ route('pages.about') }}" class="text-sm font-medium text-slate-600 hover:text-[#0F172A]">
                {{ __('landing.nav.about') }}
            </a>

            <a href="{{ route('pages.privacy') }}" class="text-sm font-medium text-slate-600 hover:text-[#0F172A]">
                {{ __('landing.nav.privacy') }}
            </a>

            <a href="{{ route('pages.terms') }}" class="text-sm font-medium text-slate-600 hover:text-[#0F172A]">
                {{ __('landing.nav.terms') }}
            </a>

            <a href="{{ route('pages.contact') }}" class="text-sm font-bold text-[#0F172A]">
                {{ __('landing.nav.contact') }}
            </a>
        </nav>

        <div class="flex items-center gap-3">
            <a
                href="{{ route('lang.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}"
                class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-[#0F172A] transition hover:bg-slate-50"
            >
                {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
            </a>

            <a
                href="{{ url('/company/login') }}"
                class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-[#0F172A] transition hover:bg-slate-50"
            >
                {{ __('landing.actions.login') }}
            </a>

            <a
                href="{{ route('pages.contact') }}"
                class="rounded-xl bg-[#0F172A] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1E293B]"
            >
                {{ __('landing.actions.start_now') }}
            </a>
        </div>
    </div>
</header>

<main>
    <section class="relative overflow-hidden bg-[#EEF4FF] py-20">
        <div class="absolute inset-0 opacity-40">
            <div class="absolute -top-24 {{ $isRtl ? '-right-24' : '-left-24' }} h-72 w-72 rounded-full bg-[#5368B2]/20 blur-3xl"></div>
            <div class="absolute -bottom-24 {{ $isRtl ? '-left-24' : '-right-24' }} h-72 w-72 rounded-full bg-[#D4AF37]/20 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <span class="inline-flex rounded-full bg-[#DDE8FF] px-4 py-2 text-sm font-bold text-[#5368B2]">
                    {{ __('landing.contact_page.badge') }}
                </span>

                <h1 class="mt-6 text-4xl font-extrabold leading-tight text-[#0F172A] sm:text-5xl">
                    {{ __('landing.contact_page.title') }}
                </h1>

                <p class="mt-5 text-base leading-8 text-slate-600">
                    {{ __('landing.contact_page.description') }}
                </p>
            </div>
        </div>
    </section>

    <section class="py-20">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">

            <div class="space-y-5">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#E4EEFF] text-[#5368B2]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>

                    <h3 class="text-lg font-extrabold text-[#0F172A]">
                        {{ __('landing.contact_page.info.email_title') }}
                    </h3>

                    <p class="mt-2 text-sm text-slate-500">
                        info@example.com
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#E4EEFF] text-[#5368B2]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3l2 5-2 1a12 12 0 006 6l1-2 5 2v3a2 2 0 01-2 2h-1C9.82 20 4 14.18 4 7V5z"/>
                        </svg>
                    </div>

                    <h3 class="text-lg font-extrabold text-[#0F172A]">
                        {{ __('landing.contact_page.info.phone_title') }}
                    </h3>

                    <p class="mt-2 text-sm text-slate-500">
                        +966 5X XXX XXXX
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#E4EEFF] text-[#5368B2]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11a3 3 0 100-6 3 3 0 000 6z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 7-7.5 11-7.5 11s-7.5-4-7.5-11a7.5 7.5 0 1115 0z"/>
                        </svg>
                    </div>

                    <h3 class="text-lg font-extrabold text-[#0F172A]">
                        {{ __('landing.contact_page.info.location_title') }}
                    </h3>

                    <p class="mt-2 text-sm text-slate-500">
                        {{ __('landing.contact_page.info.location_value') }}
                    </p>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    @if(session('toast_success'))
                        <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-semibold text-green-700">
                            {{ session('toast_success') }}
                        </div>
                    @endif

                    <h2 class="text-2xl font-extrabold text-[#0F172A]">
                        {{ __('landing.contact_page.form.title') }}
                    </h2>

                    <p class="mt-2 text-sm leading-7 text-slate-500">
                        {{ __('landing.contact_page.form.description') }}
                    </p>

                    <form action="{{ route('contact.submit') }}" method="POST" class="mt-8 space-y-6">
                        @csrf

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-bold text-[#0F172A]">
                                    {{ __('landing.contact_page.form.name') }}
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#5368B2] focus:ring-4 focus:ring-[#5368B2]/10"
                                    placeholder="{{ __('landing.contact_page.form.name_placeholder') }}"
                                >

                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-bold text-[#0F172A]">
                                    {{ __('landing.contact_page.form.email') }}
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#5368B2] focus:ring-4 focus:ring-[#5368B2]/10"
                                    placeholder="{{ __('landing.contact_page.form.email_placeholder') }}"
                                >

                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-bold text-[#0F172A]">
                                    {{ __('landing.contact_page.form.phone') }}
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#5368B2] focus:ring-4 focus:ring-[#5368B2]/10"
                                    placeholder="{{ __('landing.contact_page.form.phone_placeholder') }}"
                                >

                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-bold text-[#0F172A]">
                                    {{ __('landing.contact_page.form.company') }}
                                </label>

                                <input
                                    type="text"
                                    name="company"
                                    value="{{ old('company') }}"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#5368B2] focus:ring-4 focus:ring-[#5368B2]/10"
                                    placeholder="{{ __('landing.contact_page.form.company_placeholder') }}"
                                >

                                @error('company')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-bold text-[#0F172A]">
                                {{ __('landing.contact_page.form.message') }}
                            </label>

                            <textarea
                                name="message"
                                rows="6"
                                class="w-full resize-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#5368B2] focus:ring-4 focus:ring-[#5368B2]/10"
                                placeholder="{{ __('landing.contact_page.form.message_placeholder') }}"
                            >{{ old('message') }}</textarea>

                            @error('message')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-[#0F172A] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#1E293B] sm:w-auto"
                        >
                            {{ __('landing.contact_page.form.submit') }}
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </section>
</main>

<footer class="bg-[#0F172A] py-12 text-white">
    <div class="mx-auto flex max-w-7xl flex-col gap-8 px-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
        <a href="{{ route('landing') }}" class="inline-flex items-center gap-3 text-lg font-extrabold">
            <img src="{{ asset('brand/myaman-icon-white.png') }}" alt="myaman" class="h-8 w-8 object-contain">
            {{ __('landing.brand') }}
        </a>

        <div class="flex flex-wrap items-center gap-6 text-sm text-slate-400">
            <a href="{{ route('pages.about') }}" class="hover:text-white">
                {{ __('landing.footer.about') }}
            </a>

            <a href="{{ route('pages.privacy') }}" class="hover:text-white">
                {{ __('landing.footer.privacy') }}
            </a>

            <a href="{{ route('pages.terms') }}" class="hover:text-white">
                {{ __('landing.footer.terms') }}
            </a>

            <a href="{{ route('pages.contact') }}" class="hover:text-white">
                {{ __('landing.nav.contact') }}
            </a>
        </div>

        <p class="text-sm text-slate-500">
            {{ __('landing.footer.copyright') }}
        </p>
    </div>
</footer>

</body>
</html>
