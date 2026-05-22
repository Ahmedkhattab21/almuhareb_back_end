@extends('layouts.app')

@section('title', __('app_pages.page_title'))

@section('content')
<div class="space-y-6">
    <section class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <div class="text-sm text-slate-500">
                {{ __('dashboard.sidebar.dashboard') }} <span class="mx-1">›</span>
                <span class="font-bold text-[#0f1b3d]">{{ __('app_pages.title') }}</span>
            </div>
            <h1 class="mt-2 text-3xl font-black text-[#0f1b3d] sm:text-4xl">{{ __('app_pages.title') }}</h1>
            <p class="mt-2 text-sm leading-7 text-slate-500">{{ __('app_pages.subtitle') }}</p>
        </div>

        <a href="{{ route('admin.app-pages.create') }}"
            class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#0f1b3d] px-6 text-sm font-extrabold text-white shadow-md">
            {{ __('app_pages.actions.create') }}
        </a>
    </section>

    <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('admin.app-pages.index') }}" class="border-b border-slate-100 p-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="{{ __('app_pages.filters.search') }}"
                    class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold outline-none focus:border-[#5368aa] lg:max-w-xl">
                <div class="flex items-center gap-3">
                    <button class="h-12 rounded-2xl bg-[#0f1b3d] px-6 text-sm font-extrabold text-white">{{ __('app_pages.filters.apply') }}</button>
                    <a href="{{ route('admin.app-pages.index') }}" class="text-sm font-bold text-blue-700">{{ __('app_pages.filters.reset') }}</a>
                </div>
            </div>
        </form>

        <div class="divide-y divide-slate-100">
            @forelse($pages as $page)
                <a href="{{ route('admin.app-pages.show', $page) }}" class="block p-5 transition hover:bg-slate-50">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-xs font-black text-blue-700">{{ __('app_pages.types.' . $page->type) }}</p>
                            <h2 class="mt-2 text-xl font-black text-[#0f1b3d]">{{ $page->title }}</h2>
                            <p class="mt-2 line-clamp-2 text-sm font-medium leading-7 text-slate-500">{{ $page->content }}</p>
                        </div>
                        <p class="shrink-0 text-xs font-bold text-slate-400">{{ $page->updated_at?->diffForHumans() }}</p>
                    </div>
                </a>
            @empty
                <div class="p-8 text-center text-sm font-bold text-slate-500">{{ __('app_pages.empty') }}</div>
            @endforelse
        </div>

        @if(method_exists($pages, 'links'))
            <div class="border-t border-slate-100 p-5">{{ $pages->links() }}</div>
        @endif
    </section>
</div>
@endsection
