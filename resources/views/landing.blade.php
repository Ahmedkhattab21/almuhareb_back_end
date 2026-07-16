@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';

    $challenges = trans('landing.challenges.items');
    $solutions = trans('landing.solution.items');
    $features = trans('landing.features.items');
    $steps = trans('landing.steps.items');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('landing.meta.title') }}</title>
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

{{-- Header --}}
<header class="sticky top-0 z-50 border-b border-slate-200/70 bg-white/90 backdrop-blur-xl">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <a href="#" class="inline-flex items-center gap-3 text-xl font-extrabold text-[#0F172A]">
            <img src="{{ asset('brand/myaman-icon-navy.png') }}" alt="AMAN" class="h-9 w-9 object-contain">
            {{ __('landing.brand') }}
        </a>

      <nav class="hidden items-center gap-8 lg:flex">
    <a href="{{ route('landing') }}" class="text-sm font-semibold text-[#0F172A]">
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

    <a href="{{ route('landing') }}#contact" class="text-sm font-medium text-slate-600 hover:text-[#0F172A]">
        {{ __('landing.nav.contact') }}
    </a>
</nav>

        <div class="flex items-center gap-3">
            <a
                href="{{ url('/lang/' . ($isRtl ? 'en' : 'ar')) }}"
                class="hidden rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-[#0F172A] transition hover:bg-slate-50 sm:inline-flex"
            >
                {{ $isRtl ? 'English' : 'العربية' }}
            </a>

            <a
                href="{{ url('/company/login') }}"
                class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-[#0F172A] transition hover:bg-slate-50"
            >
                {{ __('landing.actions.login') }}
            </a>

            <a
                href="#contact"
                class="rounded-xl bg-[#0F172A] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1E293B]"
            >
                {{ __('landing.actions.start_now') }}
            </a>
        </div>
    </div>
</header>

<main>

    {{-- Hero --}}
    <section id="home" class="relative overflow-hidden bg-[#EEF4FF]">
        <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-24">

            <div class="{{ $isRtl ? 'text-right' : 'text-left' }}">
                <div class="mb-5 inline-flex items-center gap-2 rounded-full bg-[#DDE8FF] px-4 py-2 text-sm font-semibold text-[#5368B2]">
                    <span class="h-2 w-2 rounded-full bg-[#5368B2]"></span>
                    {{ __('landing.hero.badge') }}
                </div>

                <h1 class="max-w-2xl text-4xl font-extrabold leading-tight tracking-tight text-[#314375] sm:text-5xl lg:text-6xl">
                    {{ __('landing.hero.title') }}
                </h1>

                <p class="mt-6 max-w-xl text-base leading-8 text-slate-600">
                    {{ __('landing.hero.description') }}
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <a
                        href="#contact"
                        class="rounded-xl bg-[#0F172A] px-6 py-3 text-sm font-bold text-white shadow-lg shadow-slate-900/10 transition hover:bg-[#1E293B]"
                    >
                        {{ __('landing.actions.request_demo') }}
                    </a>

                    <a
                        href="#how-it-works"
                        class="rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-[#0F172A] shadow-sm transition hover:bg-slate-50"
                    >
                        {{ __('landing.actions.watch_explain') }}
                    </a>
                </div>
            </div>

            {{-- Dashboard Mockup --}}
            <div class="relative">
                <div class="absolute -inset-6 rounded-[2rem] bg-[#2563EB]/10 blur-3xl"></div>

                <div class="relative overflow-hidden rounded-2xl bg-[#07151A] p-5 shadow-2xl shadow-slate-900/30">
                    <div class="rounded-xl border border-cyan-400/10 bg-[#0C1D23] p-4">
                        <div class="mb-5 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-3 w-3 rounded-full bg-red-400"></span>
                                <span class="h-3 w-3 rounded-full bg-yellow-400"></span>
                                <span class="h-3 w-3 rounded-full bg-green-400"></span>
                            </div>
                            <div class="h-2 w-28 rounded-full bg-cyan-400/40"></div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="md:col-span-2">
                                <div class="mb-4 h-3 w-40 rounded-full bg-slate-500/40"></div>

                                <div class="space-y-3">
                                    <div class="rounded-xl border border-slate-700/70 bg-slate-900/60 p-4">
                                        <div class="mb-3 h-2 w-32 rounded-full bg-slate-500/50"></div>
                                        <div class="grid grid-cols-3 gap-3">
                                            <div class="h-16 rounded-lg bg-[#132F38]"></div>
                                            <div class="h-16 rounded-lg bg-[#132F38]"></div>
                                            <div class="h-16 rounded-lg bg-[#132F38]"></div>
                                        </div>
                                    </div>

                                    <div class="rounded-xl border border-slate-700/70 bg-slate-900/60 p-4">
                                        <div class="mb-4 flex items-center justify-between">
                                            <div class="h-2 w-24 rounded-full bg-slate-500/50"></div>
                                            <div class="h-2 w-16 rounded-full bg-cyan-400/50"></div>
                                        </div>

                                        <div class="flex h-24 items-end gap-1">
                                            @foreach(range(1, 42) as $i)
                                                <span
                                                    class="flex-1 rounded-t bg-cyan-400/40"
                                                    style="height: {{ rand(20, 95) }}%;"
                                                ></span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                @foreach(range(1, 4) as $i)
                                    <div class="rounded-xl border border-slate-700/70 bg-slate-900/60 p-4">
                                        <div class="mb-3 h-2 w-16 rounded-full bg-slate-500/50"></div>
                                        <div class="h-8 rounded-lg bg-[#132F38]"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

  {{-- Challenges --}}
<section class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Section Header --}}
        <div class="mb-12 grid gap-8 lg:grid-cols-2 lg:items-center">
            <div class="text-center lg:text-right">
                <h2 class="text-3xl font-extrabold text-[#0F172A] sm:text-4xl">
                    {{ __('landing.challenges.title') }}
                </h2>

                <div class="mx-auto mt-4 h-1 w-28 rounded-full bg-[#D4AF37] lg:mx-0"></div>
            </div>

            <p class="mx-auto max-w-xl text-center text-sm leading-8 text-slate-500 lg:mx-0 lg:text-right">
                {{ __('landing.challenges.description') }}
            </p>
        </div>

        {{-- Cards --}}
        <div class="grid gap-5 lg:grid-cols-2">

            {{-- Dark Card --}}
            <div class="rounded-2xl bg-[#0F172A] p-6 text-white shadow-sm">
                <div class="mb-5 flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 text-white">
                    {!! $challenges[0]['icon'] !!}
                </div>

                <h3 class="text-lg font-extrabold text-white">
                    {{ $challenges[0]['title'] }}
                </h3>

                <p class="mt-3 text-sm leading-7 text-slate-300">
                    {{ $challenges[0]['description'] }}
                </p>
            </div>

            {{-- Language Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-red-500">
                    {!! $challenges[1]['icon'] !!}
                </div>

                <h3 class="text-lg font-extrabold text-[#0F172A]">
                    {{ $challenges[1]['title'] }}
                </h3>

                <p class="mt-3 text-sm leading-7 text-slate-500">
                    {{ $challenges[1]['description'] }}
                </p>
            </div>

            {{-- Analytics Card --}}
            <div class="rounded-2xl border border-[#C9DAF8] bg-[#EAF2FF] p-6 shadow-sm">
                <div class="grid gap-6 md:grid-cols-2 md:items-center">
                    <div>
                        <div class="mb-5 flex h-11 w-11 items-center justify-center rounded-xl bg-white text-[#5368B2]">
                            {!! $challenges[2]['icon'] !!}
                        </div>

                        <h3 class="text-lg font-extrabold text-[#0F172A]">
                            {{ $challenges[2]['title'] }}
                        </h3>

                        <p class="mt-3 text-sm leading-7 text-slate-600">
                            {{ $challenges[2]['description'] }}
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="relative overflow-hidden rounded-xl bg-white px-4 py-3 shadow-sm">
                            <div class="absolute right-0 top-0 h-full w-1 bg-red-400"></div>
                            <div class="h-3 w-28 rounded-full bg-slate-200"></div>
                            <div class="mt-3 h-2 w-40 rounded-full bg-slate-100"></div>
                        </div>

                        <div class="relative overflow-hidden rounded-xl bg-white px-4 py-3 shadow-sm">
                            <div class="absolute right-0 top-0 h-full w-1 bg-red-400"></div>
                            <div class="h-3 w-24 rounded-full bg-slate-200"></div>
                            <div class="mt-3 h-2 w-36 rounded-full bg-slate-100"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Legal Procedures Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex h-11 w-11 items-center justify-center rounded-xl bg-[#EEF4FF] text-[#5368B2]">
                    {!! $challenges[3]['icon'] !!}
                </div>

                <h3 class="text-lg font-extrabold text-[#0F172A]">
                    {{ $challenges[3]['title'] }}
                </h3>

                <p class="mt-3 text-sm leading-7 text-slate-500">
                    {{ $challenges[3]['description'] }}
                </p>
            </div>

        </div>
    </div>
</section>

    {{-- Solution --}}
    <section class="bg-[#0F172A] py-20 text-white">
        <div class="mx-auto grid max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:px-8 lg:items-center">
            <div>
                <h2 class="text-3xl font-extrabold">
                    {{ __('landing.solution.title') }}
                </h2>

                <p class="mt-5 max-w-xl text-sm leading-8 text-slate-300">
                    {{ __('landing.solution.description') }}
                </p>

                <ul class="mt-8 space-y-4">
                    @foreach(trans('landing.solution.points') as $point)
                        <li class="flex items-start gap-3 text-sm text-slate-200">
                            <span class="mt-1 flex h-5 w-5 items-center justify-center rounded-full border border-[#D4AF37] text-xs text-[#D4AF37]">
                                ✓
                            </span>
                            <span>{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>

                <a
                    href="#contact"
                    class="mt-8 inline-flex rounded-xl bg-white px-6 py-3 text-sm font-bold text-[#0F172A] transition hover:bg-slate-100"
                >
                    {{ __('landing.actions.get_electronic_office') }}
                </a>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                @foreach($solutions as $index => $item)
                    <div class="rounded-2xl bg-white/10 p-6 backdrop-blur transition hover:bg-white/15 {{ $index === 0 ? 'bg-[#5368B2]' : '' }}">
                        <div class="mb-5 flex h-11 w-11 items-center justify-center rounded-xl bg-white/10">
                            {!! $item['icon'] !!}
                        </div>

                        <h3 class="text-base font-extrabold">
                            {{ $item['title'] }}
                        </h3>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="bg-[#F5F7FB] py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-[#0F172A]">
                    {{ __('landing.features.title') }}
                </h2>
                <p class="mt-4 text-sm text-slate-500">
                    {{ __('landing.features.description') }}
                </p>
            </div>

            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($features as $item)
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="mb-5 flex h-11 w-11 items-center justify-center rounded-xl bg-[#E4EEFF] text-[#5368B2]">
                            {!! $item['icon'] !!}
                        </div>

                        <h3 class="text-base font-extrabold text-[#0F172A]">
                            {{ $item['title'] }}
                        </h3>

                        <p class="mt-3 text-sm leading-7 text-slate-500">
                            {{ $item['description'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Steps --}}
    <section id="how-it-works" class="bg-white py-20">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-[#0F172A]">
                    {{ __('landing.steps.title') }}
                </h2>
            </div>

            <div class="relative mt-14">
                <div class="absolute bottom-0 top-0 {{ $isRtl ? 'right-5' : 'left-5' }} w-px bg-slate-200"></div>

                <div class="space-y-10">
                    @foreach($steps as $index => $step)
                        <div class="relative flex gap-6">
                            <div class="relative z-10 flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $index === 0 ? 'bg-black text-white' : 'bg-[#DDE8FF] text-[#5368B2]' }} text-sm font-extrabold">
                                {{ $index + 1 }}
                            </div>

                            <div>
                                <h3 class="text-base font-extrabold text-[#0F172A]">
                                    {{ $step['title'] }}
                                </h3>

                                <p class="mt-2 text-sm leading-7 text-slate-500">
                                    {{ $step['description'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

 {{-- CTA --}}
<section id="contact" class="bg-white pb-24">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-[2rem] bg-gradient-to-br from-[#5368B2] to-[#0F172A] px-6 py-12 text-center text-white shadow-2xl shadow-slate-900/20 sm:px-12">
            <h2 class="text-3xl font-extrabold leading-tight sm:text-4xl">
                {{ __('landing.cta.title') }}
            </h2>

            <p class="mx-auto mt-5 max-w-2xl text-sm leading-7 text-slate-200">
                {{ __('landing.cta.description') }}
            </p>

            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a
                    href="{{ url('/company/login') }}"
                    class="rounded-xl bg-white px-6 py-3 text-sm font-bold text-[#0F172A] transition hover:bg-slate-100"
                >
                    {{ __('landing.actions.free_consultation') }}
                </a>

                <a
                    href="{{ route('pages.contact') }}"
                    class="rounded-xl bg-white/10 px-6 py-3 text-sm font-bold text-white ring-1 ring-white/20 transition hover:bg-white/15"
                >
                    {{ __('landing.actions.contact_us') }}
                </a>
            </div>
        </div>
    </div>
</section>

</main>

{{-- Footer --}}
<footer class="bg-[#0F172A] py-12 text-white">
    <div class="mx-auto flex max-w-7xl flex-col gap-8 px-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">

        <a href="{{ route('landing') }}" class="inline-flex items-center gap-3 text-lg font-extrabold">
            <img src="{{ asset('brand/myaman-icon-white.png') }}" alt="AMAN" class="h-8 w-8 object-contain">
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

            <a href="{{ url('/company/login') }}" class="hover:text-white">
                {{ __('landing.footer.company_login') }}
            </a>

            <a href="{{ url('/lawyer/login') }}" class="hover:text-white">
                {{ __('landing.footer.lawyer_login') }}
            </a>

            <a
                href="https://apps.apple.com/sa/app/aman-%D8%A2%D9%85%D8%A7%D9%86/id6788716349"
                target="_blank"
                rel="noopener noreferrer"
                class="hover:text-white"
            >
                {{ __('landing.footer.ios_app') }}
            </a>

            <a
                href="https://play.google.com/store/apps/details?id=almuhareb.com.app&pcampaignid=web_share"
                target="_blank"
                rel="noopener noreferrer"
                class="hover:text-white"
            >
                {{ __('landing.footer.android_app') }}
            </a>
        </div>

        <p class="text-sm text-slate-500">
            {{ __('landing.footer.copyright') }}
        </p>
    </div>
</footer>

</body>
</html>
