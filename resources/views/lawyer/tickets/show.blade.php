@extends('layouts.lawyer')

@section('title', __('lawyer_tickets.show.page_title'))

@section('content')
@php
    $messages = $messages ?? collect();

    $worker = $ticket->worker;
    $company = $ticket->company;
    $category = $ticket->category;

    $workerName = $worker->name ?? '-';
    $workerInitial = mb_substr($workerName, 0, 1);

    $companyName = $company->company_name ?? $company->name ?? '-';
    $categoryName = $category->name ?? '-';
    $ticketTitleOriginal = $ticket->title_original ?: $ticket->title;
    $ticketTitleTranslated = $ticket->title_translated;
    $ticketLocationUrl = ($ticket->lat !== null && $ticket->long !== null)
        ? 'https://www.google.com/maps?q=' . $ticket->lat . ',' . $ticket->long
        : null;

    $statusMap = [
        'open' => [
            'label' => __('lawyer_tickets.status.open'),
            'class' => 'bg-green-50 text-green-700 border-green-100',
        ],
        'pending' => [
            'label' => __('lawyer_tickets.status.pending'),
            'class' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
        ],
        'in_progress' => [
            'label' => __('lawyer_tickets.status.in_progress'),
            'class' => 'bg-blue-50 text-blue-700 border-blue-100',
        ],
        'closed' => [
            'label' => __('lawyer_tickets.status.closed'),
            'class' => 'bg-slate-100 text-slate-600 border-slate-200',
        ],
    ];

    $statusData = $statusMap[$ticket->status] ?? [
        'label' => $ticket->status ?? '-',
        'class' => 'bg-slate-100 text-slate-600 border-slate-200',
    ];
@endphp

<div class="space-y-8">

    {{-- Header --}}
    <section class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            <div class="flex items-center gap-4">
                <a
                    href="{{ route('lawyer.tickets.index') }}"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-[#0f1b3d] transition hover:bg-slate-50"
                >
                    <svg class="h-5 w-5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 12H5"/>
                        <path d="M12 19l-7-7 7-7"/>
                    </svg>
                </a>

                <div>
                    <p class="text-xs font-black text-slate-400">
                        {{ __('lawyer_tickets.show.ticket_number') }}
                    </p>

                    <h1 class="mt-1 text-2xl font-black text-[#0f1b3d]">
                        {{ $ticket->id }}#
                    </h1>

                    <p class="mt-1 text-xs font-bold text-slate-400">
                        {{ __('lawyer_tickets.show.last_update') }}
                        {{ $ticket->updated_at ? $ticket->updated_at->diffForHumans() : '-' }}
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center rounded-2xl border px-4 py-2 text-sm font-black {{ $statusData['class'] }}">
                    {{ $statusData['label'] }}
                </span>

                @if(Route::has('lawyer.recommendations.create'))
                    <a
                        href="{{ route('lawyer.recommendations.create', ['ticket_id' => $ticket->id]) }}"
                        class="inline-flex h-11 items-center justify-center gap-2 rounded-2xl bg-[#0f1b3d] px-5 text-sm font-extrabold text-white transition hover:bg-[#17264f]"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                            <path d="M4 5h16v14H4z" />
                            <path d="m8 11 2.5 2.5L16 8" />
                        </svg>
                        إضافة توصية
                    </a>
                @endif

                @if($ticket->status !== 'closed' && Route::has('lawyer.tickets.close'))
                    <form method="POST" action="{{ route('lawyer.tickets.close', $ticket) }}">
                        @csrf

                        <button
                            type="submit"
                            onclick="return confirm('{{ __('lawyer_tickets.confirm.close') }}')"
                            class="inline-flex h-11 items-center justify-center gap-2 rounded-2xl bg-red-600 px-5 text-sm font-extrabold text-white transition hover:bg-red-700"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                <path d="M18 6 6 18"/>
                                <path d="m6 6 12 12"/>
                            </svg>
                            إغلاق التذكرة
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </section>

    {{-- Company + Worker Data --}}
    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">

        {{-- Worker Data --}}
        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-5">
                <div>
                    <p class="text-sm font-black text-slate-400">
                        {{ __('lawyer_tickets.show.worker_data') }}
                    </p>

                    <h2 class="mt-2 text-2xl font-black text-[#0f1b3d]">
                        {{ $workerName }}
                    </h2>

                    <p class="mt-1 text-sm font-bold text-slate-500">
                        {{ $worker->phone ?? '-' }}
                    </p>
                </div>

                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-[#eef3ff] text-xl font-black text-[#5368aa]">
                    {{ $workerInitial }}
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-black text-slate-400">
                        {{ __('lawyer_tickets.show.worker_email') }}
                    </p>

                    <p class="mt-2 truncate text-sm font-bold text-[#0f1b3d]">
                        {{ $worker->email ?? '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-black text-slate-400">
                        {{ __('lawyer_tickets.show.iqama_number') }}
                    </p>

                    <p class="mt-2 text-sm font-bold text-[#0f1b3d]">
                        {{ $worker->iqama_number ?? $worker->residency_number ?? '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-black text-slate-400">
                        {{ __('lawyer_tickets.show.position') }}
                    </p>

                    <p class="mt-2 text-sm font-bold text-[#0f1b3d]">
                        {{ $worker->position?->name ?? $worker->position ?? $worker->job_title ?? '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-black text-slate-400">
                        {{ __('lawyer_tickets.show.language') }}
                    </p>

                    <p class="mt-2 text-sm font-bold text-[#0f1b3d]">
                        {{ $worker->preferredLanguage?->name ?? $worker->preferedLanguage?->name ?? $worker->preferred_language ?? $worker->language ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Company Data --}}
        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-5">
                <div>
                    <p class="text-sm font-black text-slate-400">
                        {{ __('lawyer_tickets.show.company_data') }}
                    </p>

                    <h2 class="mt-2 text-2xl font-black text-[#0f1b3d]">
                        {{ $companyName }}
                    </h2>

                    <p class="mt-1 text-sm font-bold text-slate-500">
                        {{ $company->email ?? '-' }}
                    </p>
                </div>

                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 21h16"/>
                        <path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16"/>
                        <path d="M9 8h1"/>
                        <path d="M14 8h1"/>
                        <path d="M9 12h1"/>
                        <path d="M14 12h1"/>
                    </svg>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-black text-slate-400">
                        {{ __('lawyer_tickets.show.company_phone') }}
                    </p>

                    <p class="mt-2 text-sm font-bold text-[#0f1b3d]">
                        {{ $company->phone ?? '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-black text-slate-400">
                        {{ __('lawyer_tickets.show.company_status') }}
                    </p>

                    <p class="mt-2 text-sm font-bold text-[#0f1b3d]">
                        {{ $company->status ?? '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 sm:col-span-2">
                    <p class="text-xs font-black text-slate-400">
                        {{ __('lawyer_tickets.show.company_address') }}
                    </p>

                    <p class="mt-2 text-sm font-bold text-[#0f1b3d]">
                        {{ $company->address ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

    </section>

    {{-- Complaint Title --}}
    <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            <div>
                <p class="text-sm font-black text-slate-400">
                    {{ __('lawyer_tickets.show.complaint_title') }}
                </p>

                <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">
                            النص الأصلي
                        </p>

                        <h2 class="mt-3 text-2xl font-black leading-tight text-[#0f1b3d]">
                            {{ $ticketTitleOriginal }}
                        </h2>
                    </div>

                    <div class="rounded-2xl bg-blue-50/60 p-4">
                        <p class="text-xs font-black uppercase tracking-widest text-[#5368aa]">
                            الترجمة
                        </p>

                        <h2 class="mt-3 text-2xl font-black leading-tight text-[#5368aa]">
                            {{ $ticketTitleTranslated ?: 'لا توجد ترجمة متاحة.' }}
                        </h2>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 text-center sm:min-w-[130px]">
                <div class="rounded-2xl bg-[#eef3ff] p-4">
                    <p class="text-xs font-black text-[#5368aa]">
                        نوع القضية
                    </p>

                    <p class="mt-2 text-sm font-black text-[#0f1b3d]">
                        {{ $categoryName }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-black text-slate-400">
                        {{ __('lawyer_tickets.show.created_at') }}
                    </p>

                    <p class="mt-2 text-sm font-black text-[#0f1b3d]">
                        {{ $ticket->created_at ? $ticket->created_at->format('Y-m-d') : '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-black text-slate-400">
                        موقع إنشاء التذكرة
                    </p>

                    @if($ticketLocationUrl)
                        <p class="mt-2 text-sm font-black text-[#0f1b3d]">
                            {{ $ticket->lat }}, {{ $ticket->long }}
                        </p>

                        <a href="{{ $ticketLocationUrl }}" target="_blank" rel="noopener" class="mt-3 inline-flex h-9 items-center justify-center rounded-xl bg-[#0f1b3d] px-4 text-xs font-extrabold text-white transition hover:bg-[#17264f]">
                            فتح الخريطة
                        </a>
                    @else
                        <p class="mt-2 text-sm font-bold text-slate-500">
                            غير متاح
                        </p>
                    @endif
                </div>
            </div>

        </div>
    </section>

    {{-- Messages --}}
    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-6 py-5">
            <h2 class="text-xl font-black text-[#0f1b3d]">
                {{ __('lawyer_tickets.show.complaint_messages') }}
            </h2>

            <p class="mt-1 text-sm font-bold text-slate-500">
                {{ __('lawyer_tickets.show.complaint_messages_subtitle') }}
            </p>
        </div>

        <div class="space-y-6 p-6">
            @forelse($messages as $message)
                @php
                    $isWorker = $message->sender_type === 'worker';
                    $isLawyer = $message->sender_type === 'lawyer';
                    $isCompany = $message->sender_type === 'company';
                    $isAdmin = $message->sender_type === 'admin';

                    $senderLabel = __('lawyer_tickets.senders.' . $message->sender_type);

                    $suggestion = $message->aiSuggestions && $message->aiSuggestions->count()
                        ? $message->aiSuggestions->last()
                        : null;

                    $bubbleClass = $isWorker
                        ? 'border-blue-100 bg-blue-50/70'
                        : 'border-slate-200 bg-slate-50';

                    $senderBadgeClass = $isWorker
                        ? 'bg-blue-100 text-blue-700'
                        : ($isLawyer ? 'bg-[#0f1b3d] text-white' : 'bg-slate-200 text-slate-700');
                @endphp

                <div class="rounded-[24px] border p-5 {{ $bubbleClass }}">

                    {{-- Message Header --}}
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full text-sm font-black {{ $senderBadgeClass }}">
                                {{ mb_substr($senderLabel, 0, 1) }}
                            </span>

                            <div>
                                <h3 class="text-sm font-black text-[#0f1b3d]">
                                    {{ $senderLabel }}
                                </h3>

                                <p class="mt-1 text-xs font-bold text-slate-400">
                                    {{ $message->created_at ? $message->created_at->format('Y-m-d H:i') : '-' }}
                                </p>
                            </div>
                        </div>

                        <span class="rounded-full bg-white px-3 py-1 text-xs font-black text-slate-500">
                            #{{ $message->message_order }}
                        </span>
                    </div>

                    {{-- Worker Message: Original + Translation + AI --}}
                    @if($isWorker)
                        <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">

                            <div class="rounded-2xl bg-white p-5">
                                <p class="mb-3 text-xs font-black uppercase tracking-widest text-slate-400">
                                    {{ __('lawyer_tickets.show.original_message') }}
                                </p>

                                <p class="whitespace-pre-line text-sm font-bold leading-8 text-[#0f1b3d]">
                                    {{ $message->message_original }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-white p-5">
                                <p class="mb-3 text-xs font-black uppercase tracking-widest text-[#5368aa]">
                                    {{ __('lawyer_tickets.show.translated_message') }}
                                </p>

                                <p class="whitespace-pre-line text-sm font-bold leading-8 text-[#5368aa]">
                                    {{ $message->message_translated ?: __('lawyer_tickets.show.no_translation') }}
                                </p>
                            </div>

                        </div>

                        <div class="mt-5 rounded-2xl border border-indigo-100 bg-white p-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-black text-[#0f1b3d]">
                                        {{ __('lawyer_tickets.show.ai_suggestion') }}
                                    </p>

                                    <p class="mt-1 text-xs font-bold text-slate-400">
                                        {{ __('lawyer_tickets.show.ai_suggestion_subtitle') }}
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    onclick="useAiSuggestion(this)"
                                    data-suggestion-id="{{ $suggestion?->id }}"
                                    data-reply="{{ e($suggestion->suggested_reply ?? '') }}"
                                    @disabled(! $suggestion)
                                    class="inline-flex h-10 items-center justify-center rounded-xl bg-[#0f1b3d] px-4 text-xs font-black text-white transition hover:bg-[#17264f]"
                                >
                                    {{ __('lawyer_tickets.show.use_suggestion') }}
                                </button>
                            </div>

                            <div class="mt-4 rounded-2xl bg-[#eef3ff] p-5">
                                <p class="text-sm font-bold leading-8 text-[#0f1b3d]">
                                    {{ $suggestion->suggested_reply ?? __('lawyer_tickets.show.no_ai_suggestion') }}
                                </p>
                            </div>
                        </div>
                    @else
                        {{-- Other Sender Message --}}
                        <div class="mt-5 rounded-2xl bg-white p-5">
                            <p class="text-sm font-bold leading-8 text-[#0f1b3d]">
                                {{ $message->message_original }}
                            </p>

                            @if($message->message_translated && ! $isLawyer)
                                <div class="mt-4 rounded-2xl bg-[#eef3ff] p-4 text-sm font-bold leading-7 text-[#5368aa]">
                                    {{ $message->message_translated }}
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Attachments --}}
                    @if($message->attachments && $message->attachments->count())
                        <div class="mt-5 flex flex-wrap gap-2">
                            @foreach($message->attachments as $attachment)
                                @php
                                    $attachmentUrl = asset('storage/' . $attachment->file_path);
                                    $isAudioAttachment = ($attachment->file_type ?? null) === 'audio';
                                @endphp

                                @if($isAudioAttachment)
                                    <div class="w-full rounded-2xl border border-slate-200 bg-white p-4">
                                        <div class="mb-3 flex items-center justify-between gap-3">
                                            <p class="text-xs font-black text-[#0f1b3d]">
                                                {{ $attachment->file_name ?? __('lawyer_tickets.show.attachment') }}
                                            </p>

                                            <a href="{{ $attachmentUrl }}" target="_blank" class="text-xs font-black text-[#5368aa] hover:underline">
                                                فتح المقطع الأصلي
                                            </a>
                                        </div>

                                        <audio controls class="w-full">
                                            <source src="{{ $attachmentUrl }}">
                                        </audio>
                                    </div>
                                @else
                                    <a
                                        href="{{ $attachmentUrl }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-black text-[#0f1b3d] transition hover:bg-slate-50"
                                    >
                                        <svg class="h-4 w-4 text-[#5368aa]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <path d="M14 2v6h6"/>
                                        </svg>

                                        {{ $attachment->file_name ?? __('lawyer_tickets.show.attachment') }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif

                </div>
            @empty
                <div class="rounded-2xl bg-slate-50 p-10 text-center">
                    <p class="text-sm font-bold text-slate-500">
                        {{ __('lawyer_tickets.show.no_messages') }}
                    </p>
                </div>
            @endforelse
        </div>

    </section>

    {{-- Reply Form --}}
    @if($ticket->status !== 'closed' && Route::has('lawyer.tickets.reply'))
        <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-black text-[#0f1b3d]">
                {{ __('lawyer_tickets.show.send_reply') }}
            </h2>

            <p class="mt-1 text-sm font-bold text-slate-500">
                {{ __('lawyer_tickets.show.send_reply_subtitle') }}
            </p>

            <form
                id="lawyerReplyForm"
                method="POST"
                action="{{ route('lawyer.tickets.reply', $ticket) }}"
                enctype="multipart/form-data"
                class="mt-6 space-y-4"
            >
                @csrf

                <textarea
                    id="lawyerReplyTextarea"
                    name="message_original"
                    rows="6"
                    required
                    placeholder="{{ __('lawyer_tickets.show.reply_placeholder') }}"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-5 text-sm font-bold leading-8 text-[#0f1b3d] outline-none transition focus:border-[#5368aa] focus:ring-4 focus:ring-[#5368aa]/10"
                ></textarea>

              <input type="hidden" name="original_language" value="ar">
              <input type="hidden" id="isAiGeneratedInput" name="is_ai_generated" value="0">
              <input type="hidden" id="aiSuggestionIdInput" name="ai_suggestion_id" value="">

<input
    type="hidden"
    name="translated_language"
    value="{{ $messages->first()?->original_language ?? 'en' }}"
>

<div>
    <label class="mb-2 block text-sm font-extrabold text-slate-600">
        {{ __('lawyer_tickets.show.attachments') }}
    </label>

    <input
        type="file"
        name="attachments[]"
        multiple
        class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-500"
    >
</div>

                <button
                    id="lawyerReplySubmitButton"
                    type="submit"
                    class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-[#0f1b3d] px-8 text-sm font-extrabold text-white transition hover:bg-[#17264f] disabled:cursor-not-allowed disabled:opacity-70"
                >
                    <span
                        id="lawyerReplySubmitSpinner"
                        class="hidden h-5 w-5 animate-spin rounded-full border-2 border-white/30 border-t-white"
                    ></span>
                    <span id="lawyerReplySubmitText">{{ __('lawyer_tickets.show.send') }}</span>
                </button>
            </form>
        </section>
    @endif

</div>

<script>
    function useAiSuggestion(button) {
        const reply = button.getAttribute('data-reply') || '';
        const textarea = document.getElementById('lawyerReplyTextarea');

        if (!textarea) {
            return;
        }

        textarea.value = reply;
        const isAiGeneratedInput = document.getElementById('isAiGeneratedInput');
        const aiSuggestionIdInput = document.getElementById('aiSuggestionIdInput');

        if (isAiGeneratedInput) {
            isAiGeneratedInput.value = '1';
        }

        if (aiSuggestionIdInput) {
            aiSuggestionIdInput.value = button.getAttribute('data-suggestion-id') || '';
        }

        textarea.focus();

        window.scrollTo({
            top: textarea.getBoundingClientRect().top + window.scrollY - 180,
            behavior: 'smooth'
        });
    }

    const lawyerReplyForm = document.getElementById('lawyerReplyForm');

    if (lawyerReplyForm) {
        lawyerReplyForm.addEventListener('submit', function () {
            const button = document.getElementById('lawyerReplySubmitButton');
            const spinner = document.getElementById('lawyerReplySubmitSpinner');
            const text = document.getElementById('lawyerReplySubmitText');

            if (!button || button.disabled) {
                return;
            }

            button.disabled = true;

            if (spinner) {
                spinner.classList.remove('hidden');
            }

            if (text) {
                text.textContent = '{{ __('lawyer_tickets.show.sending') }}';
            }
        });
    }
</script>
@endsection
