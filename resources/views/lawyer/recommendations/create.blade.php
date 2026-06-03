@extends('layouts.lawyer')

@section('title', 'إضافة توصية')

@section('content')
@php
    $tickets = collect($tickets ?? []);
    $selectedTicket = $selectedTicket ?? null;
    $selectedTicketId = old('ticket_id', request('ticket_id', $selectedTicket?->id));
@endphp

<div class="space-y-6">
    <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="text-sm text-slate-500">
                    التوصيات <span class="mx-1">›</span> <span class="font-bold text-[#0f1b3d]">إضافة توصية</span>
                </div>

                <h1 class="mt-3 text-3xl font-black text-[#0f1b3d]">إضافة توصية للشركة</h1>
                <p class="mt-2 text-sm leading-7 text-slate-500">اختر التذكرة، وسيتم ربط التوصية تلقائيا بالعامل والشركة الخاصة بهذه الشكوى.</p>
            </div>

            <a href="{{ route('lawyer.recommendations.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl border border-slate-200 px-6 text-sm font-extrabold text-[#0f1b3d]">رجوع</a>
        </div>
    </section>

    @if($errors->any())
        <section class="rounded-2xl border border-red-100 bg-red-50 p-4 text-sm font-bold text-red-700">
            {{ $errors->first() }}
        </section>
    @endif

    <form method="POST" action="{{ route('lawyer.recommendations.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        @csrf

        <section class="space-y-5 rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
            <div>
                <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">التذكرة المرتبطة</label>
                <select
                    name="ticket_id"
                    required
                    class="h-13 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 py-3 text-sm font-bold text-[#0f1b3d] outline-none focus:border-[#5368aa]"
                >
                    <option value="">اختر التذكرة</option>
                    @foreach($tickets as $ticket)
                        <option value="{{ $ticket->id }}" @selected((string) $selectedTicketId === (string) $ticket->id)>
                            #{{ $ticket->id }} - {{ \Illuminate\Support\Str::limit($ticket->title_translated ?? $ticket->title ?? $ticket->title_original ?? 'بدون عنوان', 80) }}
                            - {{ $ticket->worker?->name ?? '-' }}
                            - {{ $ticket->company?->company_name ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">عنوان التوصية</label>
                <input
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    required
                    maxlength="255"
                    placeholder="اكتب عنوان مختصر للتوصية"
                    class="h-13 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 py-3 text-sm font-bold text-[#0f1b3d] outline-none focus:border-[#5368aa]"
                >
            </div>

            <div>
                <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">نص التوصية</label>
                <textarea
                    name="description"
                    rows="9"
                    required
                    placeholder="اكتب التوصية الموجهة للشركة..."
                    class="w-full rounded-2xl border border-slate-200 bg-[#f8fbff] p-4 text-sm font-bold leading-8 text-[#0f1b3d] outline-none focus:border-[#5368aa]"
                >{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">مرفق اختياري</label>
                <input
                    type="file"
                    name="attachment"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-500"
                >
            </div>

            <button class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#0f1b3d] px-8 text-sm font-extrabold text-white">
                إرسال التوصية
            </button>
        </section>

        <aside class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-[#0f1b3d]">معلومات الربط</h2>

            @if($selectedTicket)
                <div class="mt-5 space-y-4">
                    <div class="rounded-2xl bg-[#f8fbff] p-4">
                        <p class="text-xs font-bold text-slate-400">التذكرة</p>
                        <p class="mt-1 font-black text-[#0f1b3d]">#{{ $selectedTicket->id }}</p>
                    </div>

                    <div class="rounded-2xl bg-[#f8fbff] p-4">
                        <p class="text-xs font-bold text-slate-400">العامل</p>
                        <p class="mt-1 font-black text-[#0f1b3d]">{{ $selectedTicket->worker?->name ?? '-' }}</p>
                    </div>

                    <div class="rounded-2xl bg-[#f8fbff] p-4">
                        <p class="text-xs font-bold text-slate-400">الشركة</p>
                        <p class="mt-1 font-black text-[#0f1b3d]">{{ $selectedTicket->company?->company_name ?? '-' }}</p>
                    </div>

                    <div class="rounded-2xl bg-[#f8fbff] p-4">
                        <p class="text-xs font-bold text-slate-400">نوع القضية</p>
                        <p class="mt-1 font-black text-[#0f1b3d]">{{ $selectedTicket->category?->name ?? '-' }}</p>
                    </div>
                </div>
            @else
                <p class="mt-5 text-sm font-bold leading-7 text-slate-500">عند فتح الإضافة من داخل تذكرة معينة ستظهر بيانات العامل والشركة هنا.</p>
            @endif
        </aside>
    </form>
</div>
@endsection
