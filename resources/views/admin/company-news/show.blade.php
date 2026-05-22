@extends('layouts.app')

@section('title', $newsItem->title)

@section('content')
<div class="space-y-6">
    <section class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-4xl">
            <p class="text-sm font-bold text-slate-500">{{ $newsItem->company?->company_name }}</p>
            <h1 class="mt-2 text-3xl font-black text-[#0f1b3d] sm:text-4xl">{{ $newsItem->title }}</h1>
            <p class="mt-2 text-sm font-bold text-slate-400">{{ $newsItem->created_at?->format('Y-m-d H:i') }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.company-news.edit', $newsItem) }}" class="rounded-2xl bg-[#0f1b3d] px-5 py-3 text-sm font-extrabold text-white">
                {{ __('company_news.actions.edit') }}
            </a>
            <form method="POST" action="{{ route('admin.company-news.destroy', $newsItem) }}" onsubmit="return confirm('{{ __('company_news.actions.confirm_delete') }}')">
                @csrf
                @method('DELETE')
                <button class="rounded-2xl bg-red-600 px-5 py-3 text-sm font-extrabold text-white">{{ __('company_news.actions.delete') }}</button>
            </form>
            <a href="{{ route('admin.company-news.index') }}" class="rounded-2xl border border-slate-200 px-5 py-3 text-sm font-bold text-[#0f1b3d]">
                {{ __('company_news.actions.back') }}
            </a>
        </div>
    </section>

    <article class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
        @if($newsItem->image_url)
            <img src="{{ $newsItem->image_url }}" alt="{{ $newsItem->title }}" class="max-h-[460px] w-full object-cover">
        @endif

        <div class="space-y-6 p-6">
            <p class="whitespace-pre-line text-base font-medium leading-9 text-slate-700">{{ $newsItem->description }}</p>

            <div class="grid gap-4 border-t border-slate-100 pt-5 text-sm sm:grid-cols-3">
                <div>
                    <p class="font-bold text-slate-500">{{ __('company_news.show.company') }}</p>
                    <p class="mt-1 font-black text-[#0f1b3d]">{{ $newsItem->company?->company_name }}</p>
                </div>
                <div>
                    <p class="font-bold text-slate-500">{{ __('company_news.show.created_by') }}</p>
                    <p class="mt-1 font-black text-[#0f1b3d]">{{ $newsItem->adminCreator?->name ?? $newsItem->companyCreator?->company_name ?? '-' }}</p>
                </div>
                <div>
                    <p class="font-bold text-slate-500">{{ __('company_news.show.updated_at') }}</p>
                    <p class="mt-1 font-black text-[#0f1b3d]">{{ $newsItem->updated_at?->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>
    </article>
</div>
@endsection
