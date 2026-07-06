@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $content = trans('landing.pages.' . $page);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $content['title'] ?? __('landing.brand') }}</title>
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

          <a href="{{ route('pages.contact') }}" class="text-sm font-medium text-slate-600 hover:text-[#0F172A]">
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
                href="{{ route('landing') }}#contact"
                class="rounded-xl bg-[#0F172A] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1E293B]"
            >
                {{ __('landing.actions.start_now') }}
            </a>
        </div>
    </div>
</header>

<main class="py-20">
    <section class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="mb-10 text-center">
            <span class="inline-flex rounded-full bg-[#DDE8FF] px-4 py-2 text-sm font-bold text-[#5368B2]">
                {{ __('landing.brand') }}
            </span>

            <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-[#0F172A] sm:text-5xl">
                {{ $content['title'] }}
            </h1>

            <p class="mx-auto mt-5 max-w-2xl text-base leading-8 text-slate-500">
                {{ $content['description'] }}
            </p>
        </div>

        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-10">
            <div class="space-y-10">
                @foreach($content['sections'] as $section)
                    <div>
                        <h2 class="text-2xl font-extrabold text-[#0F172A]">
                            {{ $section['title'] }}
                        </h2>

                        <p class="mt-4 text-base leading-9 text-slate-600">
                            {{ $section['body'] }}
                        </p>
                    </div>
                @endforeach
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
        </div>

        <p class="text-sm text-slate-500">
            {{ __('landing.footer.copyright') }}
        </p>
    </div>
</footer>

</body>
</html>
