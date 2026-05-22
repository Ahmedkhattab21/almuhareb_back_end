@extends('layouts.app')

@section('title', __('company_news.edit.title'))

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <section class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-[#0f1b3d]">{{ __('company_news.edit.title') }}</h1>
            <p class="mt-2 text-sm leading-7 text-slate-500">{{ __('company_news.edit.subtitle') }}</p>
        </div>
        <a href="{{ route('admin.company-news.show', $newsItem) }}" class="rounded-2xl border border-slate-200 px-5 py-3 text-sm font-bold text-[#0f1b3d]">
            {{ __('company_news.actions.back') }}
        </a>
    </section>

    <form method="POST" action="{{ route('admin.company-news.update', $newsItem) }}" enctype="multipart/form-data"
        class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')
        @include('admin.company-news._form')
        <div class="mt-6 flex justify-end">
            <button class="h-12 rounded-2xl bg-[#0f1b3d] px-8 text-sm font-extrabold text-white">{{ __('company_news.actions.update') }}</button>
        </div>
    </form>
</div>
@endsection
