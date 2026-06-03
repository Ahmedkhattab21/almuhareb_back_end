@extends('layouts.lawyer')

@section('title', 'تفاصيل التوصية')

@section('content')
@php
    $attachmentUrl = $recommendation->attachment ? asset('storage/' . $recommendation->attachment) : null;
@endphp

<div class="space-y-6">
    <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="text-sm text-slate-500">
                    التوصيات <span class="mx-1">›</span> <span class="font-bold text-[#0f1b3d]">#{{ $recommendation->id }}</span>
                </div>

                <h1 class="mt-3 text-3xl font-black text-[#0f1b3d]">{{ $recommendation->title }}</h1>
                <p class="mt-2 text-sm text-slate-500">تم إرسالها للشركة بخصوص التذكرة رقم #{{ $recommendation->ticket_id }}</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('lawyer.recommendations.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl border border-slate-200 px-6 text-sm font-extrabold text-[#0f1b3d]">رجوع</a>

                <form method="POST" action="{{ route('lawyer.recommendations.destroy', $recommendation) }}" onsubmit="return confirm('هل تريد حذف هذه التوصية؟')">
                    @csrf
                    @method('DELETE')
                    <button class="inline-flex h-12 items-center justify-center rounded-2xl bg-red-600 px-6 text-sm font-extrabold text-white">حذف</button>
                </form>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-5 md:grid-cols-3">
        <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-400">الشركة</p>
            <h3 class="mt-2 text-xl font-black text-[#0f1b3d]">{{ $recommendation->company?->company_name ?? '-' }}</h3>
            <p class="mt-1 text-sm text-slate-500">{{ $recommendation->company?->email ?? '-' }}</p>
        </div>

        <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-400">العامل</p>
            <h3 class="mt-2 text-xl font-black text-[#0f1b3d]">{{ $recommendation->worker?->name ?? '-' }}</h3>
            <p class="mt-1 text-sm text-slate-500">{{ $recommendation->worker?->phone ?? '-' }}</p>
        </div>

        <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-400">نوع القضية</p>
            <h3 class="mt-2 text-xl font-black text-[#0f1b3d]">{{ $recommendation->ticket?->category?->name ?? '-' }}</h3>
            <p class="mt-1 text-sm text-slate-500">{{ $recommendation->created_at?->format('Y-m-d H:i') ?? '-' }}</p>
        </div>
    </section>

    <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-xl font-black text-[#0f1b3d]">نص التوصية</h2>
        <p class="mt-5 whitespace-pre-line text-sm font-bold leading-8 text-slate-700">{{ $recommendation->description }}</p>

        <div class="mt-6 flex flex-wrap gap-3">
            @if(Route::has('lawyer.tickets.show'))
                <a href="{{ route('lawyer.tickets.show', $recommendation->ticket_id) }}" class="inline-flex h-11 items-center rounded-2xl bg-[#0f1b3d] px-5 text-sm font-extrabold text-white">فتح التذكرة</a>
            @endif

            @if($attachmentUrl)
                <a href="{{ $attachmentUrl }}" target="_blank" class="inline-flex h-11 items-center rounded-2xl border border-slate-200 px-5 text-sm font-extrabold text-[#0f1b3d]">
                    {{ $recommendation->attachment_name ?? 'عرض المرفق' }}
                </a>
            @endif
        </div>
    </section>
</div>
@endsection
