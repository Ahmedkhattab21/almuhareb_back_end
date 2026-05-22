@extends('layouts.app')

@section('title', __('app_pages.create.title'))

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <section class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-[#0f1b3d]">{{ __('app_pages.create.title') }}</h1>
            <p class="mt-2 text-sm leading-7 text-slate-500">{{ __('app_pages.create.subtitle') }}</p>
        </div>
        <a href="{{ route('admin.app-pages.index') }}" class="rounded-2xl border border-slate-200 px-5 py-3 text-sm font-bold text-[#0f1b3d]">
            {{ __('app_pages.actions.back') }}
        </a>
    </section>

    @if(empty($types))
        <div class="rounded-[26px] border border-slate-200 bg-white p-8 text-center text-sm font-bold text-slate-500 shadow-sm">
            {{ __('app_pages.create.no_available_types') }}
        </div>
    @else
        <form method="POST" action="{{ route('admin.app-pages.store') }}" class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @include('admin.app-pages._form')
            <div class="mt-6 flex justify-end">
                <button class="h-12 rounded-2xl bg-[#0f1b3d] px-8 text-sm font-extrabold text-white">{{ __('app_pages.actions.save') }}</button>
            </div>
        </form>
    @endif
</div>
@endsection
