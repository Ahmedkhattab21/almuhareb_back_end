@extends('layouts.app')

@section('title', __('admin_recommendations.details_title'))

@section('content')
@php
    $attachmentUrl = $recommendation->attachment ? asset('storage/' . $recommendation->attachment) : null;
@endphp

<div class="space-y-6">
    <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="text-sm text-slate-500">
                    {{ __('admin_recommendations.title') }}
                    <span class="mx-1">›</span>
                    <span class="font-bold text-[#0f1b3d]">#{{ $recommendation->id }}</span>
                </div>

                <h1 class="mt-3 text-3xl font-black text-[#0f1b3d]">{{ $recommendation->title }}</h1>
                <p class="mt-2 text-sm text-slate-500">{{ __('admin_recommendations.ticket_hint', ['id' => $recommendation->ticket_id]) }}</p>
            </div>

            <a href="{{ route('admin.recommendations.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl border border-slate-200 px-6 text-sm font-extrabold text-[#0f1b3d]">
                {{ __('admin_recommendations.back') }}
            </a>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-400">{{ __('admin_recommendations.cards.company') }}</p>
            <h3 class="mt-2 text-xl font-black text-[#0f1b3d]">{{ $recommendation->company?->company_name ?? '-' }}</h3>
            <p class="mt-1 text-sm text-slate-500">{{ $recommendation->company?->email ?? '-' }}</p>
        </div>

        <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-400">{{ __('admin_recommendations.cards.lawyer') }}</p>
            <h3 class="mt-2 text-xl font-black text-[#0f1b3d]">{{ $recommendation->lawyer?->name ?? '-' }}</h3>
            <p class="mt-1 text-sm text-slate-500">{{ $recommendation->lawyer?->email ?? '-' }}</p>
        </div>

        <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-400">{{ __('admin_recommendations.cards.worker') }}</p>
            <h3 class="mt-2 text-xl font-black text-[#0f1b3d]">{{ $recommendation->worker?->name ?? '-' }}</h3>
            <p class="mt-1 text-sm text-slate-500">{{ $recommendation->worker?->phone ?? '-' }}</p>
        </div>
    </section>

    <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-xl font-black text-[#0f1b3d]">{{ __('admin_recommendations.description_title') }}</h2>
        <p class="mt-5 whitespace-pre-line text-sm font-bold leading-8 text-slate-700">{{ $recommendation->description }}</p>

        <div class="mt-6 flex flex-wrap gap-3">
            @if(Route::has('admin.tickets.show'))
                <a href="{{ route('admin.tickets.show', $recommendation->ticket_id) }}" class="inline-flex h-11 items-center rounded-2xl bg-[#0f1b3d] px-5 text-sm font-extrabold text-white">
                    {{ __('admin_recommendations.open_ticket') }}
                </a>
            @endif

            @if($attachmentUrl)
                <a href="{{ $attachmentUrl }}" target="_blank" class="inline-flex h-11 items-center rounded-2xl border border-slate-200 px-5 text-sm font-extrabold text-[#0f1b3d]">
                    {{ $recommendation->attachment_name ?? __('admin_recommendations.view_attachment') }}
                </a>
            @endif
        </div>
    </section>
</div>
@endsection
