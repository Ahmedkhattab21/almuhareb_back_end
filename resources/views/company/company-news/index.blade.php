@extends('layouts.company')

@section('title', __('company_news.company.page_title'))

@section('content')
<div class="space-y-6">
    <section class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <div class="text-sm text-slate-500">
                {{ __('company_dashboard.breadcrumb_parent') }} <span class="mx-1">›</span>
                <span class="font-bold text-[#0f1b3d]">{{ __('company_news.title') }}</span>
            </div>
            <h1 class="mt-2 text-3xl font-black text-[#0f1b3d] sm:text-4xl">{{ __('company_news.company.title') }}</h1>
            <p class="mt-2 text-sm leading-7 text-slate-500">{{ __('company_news.company.subtitle') }}</p>
        </div>

        <a href="{{ route('company.company-news.create') }}"
            class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#0f1b3d] px-6 text-sm font-extrabold text-white shadow-md">
            {{ __('company_news.actions.create') }}
        </a>
    </section>

    <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('company.company-news.index') }}" class="border-b border-slate-100 p-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="{{ __('company_news.filters.search') }}"
                    class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold outline-none focus:border-[#5368aa] lg:max-w-xl">
                <div class="flex items-center gap-3">
                    <button class="h-12 rounded-2xl bg-[#0f1b3d] px-6 text-sm font-extrabold text-white">{{ __('company_news.filters.apply') }}</button>
                    <a href="{{ route('company.company-news.index') }}" class="text-sm font-bold text-blue-700">{{ __('company_news.filters.reset') }}</a>
                </div>
            </div>
        </form>

        <div class="grid gap-5 p-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse($news as $item)
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <a href="{{ route('company.company-news.show', $item) }}" class="block">
                        @if($item->image_url)
                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="h-48 w-full object-cover">
                        @else
                            <div class="flex h-48 w-full items-center justify-center bg-[#f8fbff] text-sm font-bold text-slate-400">
                                {{ __('company_news.no_image') }}
                            </div>
                        @endif
                        <div class="space-y-3 p-5">
                            <h2 class="line-clamp-2 text-xl font-black text-[#0f1b3d]">{{ $item->title }}</h2>
                            <p class="line-clamp-3 text-sm font-medium leading-7 text-slate-500">{{ $item->description }}</p>
                            <p class="text-xs font-bold text-slate-400">{{ $item->created_at?->diffForHumans() }}</p>
                        </div>
                    </a>
                </article>
            @empty
                <div class="col-span-full rounded-2xl bg-[#f8fbff] p-8 text-center text-sm font-bold text-slate-500">
                    {{ __('company_news.empty') }}
                </div>
            @endforelse
        </div>

        @if(method_exists($news, 'links'))
            <div class="border-t border-slate-100 p-5">{{ $news->links() }}</div>
        @endif
    </section>
</div>
@endsection
