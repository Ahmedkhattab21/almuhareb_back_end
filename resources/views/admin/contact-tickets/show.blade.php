@extends('layouts.app')

@section('title', __('contact_tickets.show_title'))

@section('content')
@php
    $statusData = $contactTicket->status === \App\Models\ContactTicket::STATUS_READ
        ? ['label' => __('contact_tickets.statuses.read'), 'class' => 'bg-slate-100 text-slate-600']
        : ['label' => __('contact_tickets.statuses.new'), 'class' => 'bg-blue-50 text-blue-700'];
@endphp

<div class="space-y-6">
    <section class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <div class="text-sm text-slate-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#0f1b3d]">{{ __('dashboard.sidebar.dashboard') }}</a>
                <span class="mx-1">&rsaquo;</span>
                <a href="{{ route('admin.contact-tickets.index') }}" class="hover:text-[#0f1b3d]">{{ __('contact_tickets.title') }}</a>
                <span class="mx-1">&rsaquo;</span>
                <span class="font-bold text-[#0f1b3d]">{{ $contactTicket->name }}</span>
            </div>
            <h1 class="mt-2 text-3xl font-black text-[#0f1b3d] sm:text-4xl">{{ __('contact_tickets.show_title') }}</h1>
        </div>

        <a href="{{ route('admin.contact-tickets.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl border border-slate-200 px-5 text-sm font-bold text-[#0f1b3d]">
            {{ __('contact_tickets.back') }}
        </a>
    </section>

    <section class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm lg:col-span-1">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-xl font-black text-[#0f1b3d]">{{ __('contact_tickets.table.sender') }}</h2>
                <span class="rounded-full px-3 py-1 text-xs font-black {{ $statusData['class'] }}">{{ $statusData['label'] }}</span>
            </div>

            <div class="mt-6 space-y-5 text-sm">
                <div>
                    <p class="font-bold text-slate-400">{{ __('contact_tickets.fields.name') }}</p>
                    <p class="mt-1 font-black text-[#0f1b3d]">{{ $contactTicket->name }}</p>
                </div>
                <div>
                    <p class="font-bold text-slate-400">{{ __('contact_tickets.fields.email') }}</p>
                    <p class="mt-1 font-black text-[#0f1b3d]">{{ $contactTicket->email }}</p>
                </div>
                <div>
                    <p class="font-bold text-slate-400">{{ __('contact_tickets.fields.phone') }}</p>
                    <p class="mt-1 font-black text-[#0f1b3d]">{{ $contactTicket->phone ?: '-' }}</p>
                </div>
                <div>
                    <p class="font-bold text-slate-400">{{ __('contact_tickets.fields.company') }}</p>
                    <p class="mt-1 font-black text-[#0f1b3d]">{{ $contactTicket->company ?: '-' }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="text-xl font-black text-[#0f1b3d]">{{ __('contact_tickets.fields.message') }}</h2>
            <p class="mt-5 whitespace-pre-line rounded-2xl bg-[#f8fbff] p-5 text-sm font-bold leading-8 text-slate-700">{{ $contactTicket->message }}</p>

            <div class="mt-6 grid gap-4 text-sm md:grid-cols-2">
                <div class="rounded-2xl border border-slate-100 p-4">
                    <p class="font-bold text-slate-400">{{ __('contact_tickets.fields.created_at') }}</p>
                    <p class="mt-1 font-black text-[#0f1b3d]">{{ $contactTicket->created_at?->format('Y-m-d H:i') ?? '-' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-100 p-4">
                    <p class="font-bold text-slate-400">{{ __('contact_tickets.fields.read_at') }}</p>
                    <p class="mt-1 font-black text-[#0f1b3d]">{{ $contactTicket->read_at?->format('Y-m-d H:i') ?? '-' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-100 p-4">
                    <p class="font-bold text-slate-400">{{ __('contact_tickets.fields.ip_address') }}</p>
                    <p class="mt-1 font-black text-[#0f1b3d]">{{ $contactTicket->ip_address ?: '-' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-100 p-4">
                    <p class="font-bold text-slate-400">{{ __('contact_tickets.fields.user_agent') }}</p>
                    <p class="mt-1 break-words font-black text-[#0f1b3d]">{{ $contactTicket->user_agent ?: '-' }}</p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
