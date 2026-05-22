@extends('layouts.app')

@section('title', $appPage->title)

@section('content')
<div class="space-y-6">
    <section class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-4xl">
            <p class="text-sm font-black text-blue-700">{{ __('app_pages.types.' . $appPage->type) }}</p>
            <h1 class="mt-2 text-3xl font-black text-[#0f1b3d] sm:text-4xl">{{ $appPage->title }}</h1>
            <p class="mt-2 text-sm font-bold text-slate-400">{{ $appPage->updated_at?->format('Y-m-d H:i') }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.app-pages.edit', $appPage) }}" class="rounded-2xl bg-[#0f1b3d] px-5 py-3 text-sm font-extrabold text-white">
                {{ __('app_pages.actions.edit') }}
            </a>
            <form method="POST" action="{{ route('admin.app-pages.destroy', $appPage) }}" onsubmit="return confirm('{{ __('app_pages.actions.confirm_delete') }}')">
                @csrf
                @method('DELETE')
                <button class="rounded-2xl bg-red-600 px-5 py-3 text-sm font-extrabold text-white">{{ __('app_pages.actions.delete') }}</button>
            </form>
            <a href="{{ route('admin.app-pages.index') }}" class="rounded-2xl border border-slate-200 px-5 py-3 text-sm font-bold text-[#0f1b3d]">
                {{ __('app_pages.actions.back') }}
            </a>
        </div>
    </section>

    <article class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
        <p class="whitespace-pre-line text-base font-medium leading-9 text-slate-700">{{ $appPage->content }}</p>
    </article>
</div>
@endsection
