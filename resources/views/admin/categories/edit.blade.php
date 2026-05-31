@extends('layouts.app')

@section('title', __('categories.edit.page_title'))

@section('content')
<div class="space-y-6 lg:space-y-8">
    <section class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div class="text-start">
            <div class="text-sm text-slate-500">
                {{ __('categories.breadcrumb_parent') }}
                <span class="mx-1">&rsaquo;</span>
                <a href="{{ route('admin.categories.index') }}" class="font-bold text-slate-500 transition hover:text-[#0f1b3d]">{{ __('categories.breadcrumb_current') }}</a>
                <span class="mx-1">&rsaquo;</span>
                <span class="font-bold text-[#0f1b3d]">{{ __('categories.edit.breadcrumb_current') }}</span>
            </div>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">{{ __('categories.edit.title') }}</h1>
            <p class="mt-2 text-sm leading-7 text-slate-500">{{ __('categories.edit.subtitle') }}</p>
        </div>

        <a href="{{ route('admin.categories.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] shadow-sm transition hover:bg-slate-50">{{ __('categories.edit.back') }}</a>
    </section>

    <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-medium text-slate-500">{{ __('categories.edit.lawyers_count') }}</p>
        <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">{{ number_format($category->lawyers_count ?? 0) }}</h3>
    </div>

    <form method="POST" action="{{ route('admin.categories.update', $category) }}" autocomplete="off" data-loading-form class="space-y-5">
        @csrf
        @method('PUT')
        @include('admin.categories.form', ['category' => $category])
    </form>
</div>
@endsection
