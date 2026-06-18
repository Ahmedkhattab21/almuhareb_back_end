@extends('layouts.app')

@section('title', 'تفاصيل الاستشارة')

@section('content')
@php
    $messages = $messages ?? collect();
    $worker = $ticket->worker;
    $company = $ticket->company;
    $lawyer = $ticket->lawyer;
    $category = $ticket->category;
    $lawyerCategories = $lawyerCategories ?? collect();
    $workerName = $worker->name ?? '-';
    $workerInitial = mb_substr($workerName, 0, 1);
    $ticketTitleOriginal = $ticket->title_original ?: $ticket->title;
    $ticketTitleTranslated = $ticket->title_translated;
    $ticketLocationUrl = ($ticket->lat !== null && $ticket->long !== null)
        ? 'https://www.google.com/maps?q=' . $ticket->lat . ',' . $ticket->long
        : null;
    $statusMap = [
        'open' => ['label' => 'مفتوحة', 'class' => 'bg-green-50 text-green-700 border-green-100'],
        'pending' => ['label' => 'بانتظار الرد', 'class' => 'bg-yellow-50 text-yellow-700 border-yellow-100'],
        'in_progress' => ['label' => 'قيد المعالجة', 'class' => 'bg-blue-50 text-blue-700 border-blue-100'],
        'closed' => ['label' => 'مغلقة', 'class' => 'bg-slate-100 text-slate-600 border-slate-200'],
    ];
    $statusData = $statusMap[$ticket->status] ?? ['label' => $ticket->status ?? '-', 'class' => 'bg-slate-100 text-slate-600 border-slate-200'];
@endphp

<div class="space-y-8">
    <section class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.tickets.index') }}" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-[#0f1b3d] transition hover:bg-slate-50">
                    <svg class="h-5 w-5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <p class="text-xs font-black text-slate-400">رقم الاستشارة</p>
                    <h1 class="mt-1 text-2xl font-black text-[#0f1b3d]">{{ $ticket->id }}#</h1>
                    <p class="mt-1 text-xs font-bold text-slate-400">تم التحديث {{ $ticket->updated_at ? $ticket->updated_at->diffForHumans() : '-' }}</p>
                </div>
            </div>
            <span class="inline-flex w-fit items-center rounded-2xl border px-4 py-2 text-sm font-black {{ $statusData['class'] }}">{{ $statusData['label'] }}</span>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-5">
                <div>
                    <p class="text-sm font-black text-slate-400">بيانات العامل</p>
                    <h2 class="mt-2 text-2xl font-black text-[#0f1b3d]">{{ $workerName }}</h2>
                    <p class="mt-1 text-sm font-bold text-slate-500">{{ $worker->phone ?? '-' }}</p>
                </div>
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-[#eef3ff] text-xl font-black text-[#5368aa]">{{ $workerInitial }}</div>
            </div>
            <div class="mt-6 grid grid-cols-1 gap-4">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-black text-slate-400">البريد الإلكتروني</p>
                    <p class="mt-2 truncate text-sm font-bold text-[#0f1b3d]">{{ $worker->email ?? '-' }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-black text-slate-400">رقم الإقامة</p>
                    <p class="mt-2 text-sm font-bold text-[#0f1b3d]">{{ $worker->iqama_number ?? $worker->residency_number ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-black text-slate-400">بيانات الشركة</p>
            <h2 class="mt-2 text-2xl font-black text-[#0f1b3d]">{{ $company->company_name ?? '-' }}</h2>
            <p class="mt-1 text-sm font-bold text-slate-500">{{ $company->email ?? '-' }}</p>
            <div class="mt-6 rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-black text-slate-400">رقم الشركة</p>
                <p class="mt-2 text-sm font-bold text-[#0f1b3d]">{{ $company->phone ?? '-' }}</p>
            </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-black text-slate-400">المستشار المسؤول</p>
            <h2 class="mt-2 text-2xl font-black text-[#0f1b3d]">{{ $lawyer->name ?? '-' }}</h2>
            <p class="mt-1 text-sm font-bold text-slate-500">{{ $lawyer->email ?? '-' }}</p>
            <div class="mt-6 rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-black text-slate-400">هاتف المستشار</p>
                <p class="mt-2 text-sm font-bold text-[#0f1b3d]">{{ $lawyer->phone ?? '-' }}</p>
            </div>
            <div class="mt-4 rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-black text-slate-400">أنواع القضايا التي يعمل عليها</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    @forelse($lawyerCategories as $lawyerCategory)
                        <span class="inline-flex rounded-full bg-[#eef3ff] px-3 py-1 text-xs font-extrabold text-[#5368aa]">
                            {{ $lawyerCategory->name }}
                        </span>
                    @empty
                        <span class="text-sm font-bold text-slate-500">لا توجد أنواع قضايا مرتبطة.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-black text-slate-400">عنوان الاستشارة</p>
                <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">النص الأصلي</p>
                        <h2 class="mt-3 text-2xl font-black leading-tight text-[#0f1b3d]">{{ $ticketTitleOriginal }}</h2>
                    </div>

                    <div class="rounded-2xl bg-blue-50/60 p-4">
                        <p class="text-xs font-black uppercase tracking-widest text-[#5368aa]">الترجمة</p>
                        <h2 class="mt-3 text-2xl font-black leading-tight text-[#5368aa]">{{ $ticketTitleTranslated ?: 'لا توجد ترجمة متاحة.' }}</h2>
                    </div>

                    <div class="rounded-2xl bg-[#eef3ff] p-4 lg:col-span-2">
                        <p class="text-xs font-black uppercase tracking-widest text-[#5368aa]">نوع القضية</p>
                        <div class="mt-3">
                            @if($category)
                                <span class="inline-flex rounded-full bg-white px-4 py-2 text-sm font-extrabold text-[#5368aa]">
                                    {{ $category->name }}
                                </span>
                            @else
                                <span class="text-sm font-bold text-slate-500">غير محدد</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4 text-center sm:min-w-[130px]">
                <p class="text-xs font-black text-slate-400">تاريخ الإنشاء</p>
                <p class="mt-2 text-sm font-black text-[#0f1b3d]">{{ $ticket->created_at ? $ticket->created_at->format('Y-m-d') : '-' }}</p>
            </div>
        </div>

        <div class="mt-5 rounded-2xl bg-slate-50 p-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-slate-400">موقع إنشاء الاستشارة</p>
                    @if($ticketLocationUrl)
                        <p class="mt-2 text-sm font-black text-[#0f1b3d]">{{ $ticket->lat }}, {{ $ticket->long }}</p>
                    @else
                        <p class="mt-2 text-sm font-bold text-slate-500">لم يتم إرسال موقع مع هذه الاستشارة.</p>
                    @endif
                </div>

                @if($ticketLocationUrl)
                    <a href="{{ $ticketLocationUrl }}" target="_blank" rel="noopener" class="inline-flex h-11 items-center justify-center rounded-2xl bg-[#0f1b3d] px-5 text-sm font-extrabold text-white transition hover:bg-[#17264f]">
                        فتح الموقع على الخريطة
                    </a>
                @endif
            </div>
        </div>
    </section>

    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-5">
            <h2 class="text-xl font-black text-[#0f1b3d]">رسائل الاستشارة</h2>
            <p class="mt-1 text-sm font-bold text-slate-500">كل الرسائل والمرفقات والترجمات المرتبطة بهذه الاستشارة.</p>
        </div>
        <div class="space-y-6 p-6">
            @forelse($messages as $message)
                @php
                    $senderLabels = ['worker' => 'رسالة العامل', 'company' => 'رد الشركة', 'lawyer' => 'رد المستشار', 'admin' => 'رد الإدارة', 'ai' => 'اقتراح الذكاء الاصطناعي'];
                    $senderLabel = $senderLabels[$message->sender_type] ?? $message->sender_type;
                    $isWorker = $message->sender_type === 'worker';
                    $isLawyer = $message->sender_type === 'lawyer';
                    $bubbleClass = $isWorker ? 'border-blue-100 bg-blue-50/70' : 'border-slate-200 bg-slate-50';
                    $senderBadgeClass = $isWorker ? 'bg-blue-100 text-blue-700' : 'bg-[#0f1b3d] text-white';
                @endphp
                <div class="rounded-[24px] border p-5 {{ $bubbleClass }}">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full text-sm font-black {{ $senderBadgeClass }}">{{ mb_substr($senderLabel, 0, 1) }}</span>
                            <div>
                                <h3 class="text-sm font-black text-[#0f1b3d]">{{ $senderLabel }}</h3>
                                <p class="mt-1 text-xs font-bold text-slate-400">{{ $message->created_at ? $message->created_at->format('Y-m-d H:i') : '-' }}</p>
                            </div>
                        </div>
                        <span class="rounded-full bg-white px-3 py-1 text-xs font-black text-slate-500">#{{ $message->message_order }}</span>
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
                        <div class="rounded-2xl bg-white p-5">
                            <p class="mb-3 text-xs font-black uppercase tracking-widest text-slate-400">{{ $isLawyer ? 'العربية' : 'النص الأصلي' }}</p>
                            <p class="text-sm font-bold leading-8 text-[#0f1b3d]">{{ $message->message_original }}</p>
                        </div>
                        <div class="rounded-2xl bg-white p-5">
                            <p class="mb-3 text-xs font-black uppercase tracking-widest text-[#5368aa]">{{ $isLawyer ? 'الإنجليزية' : 'الترجمة' }}</p>
                            <p class="text-sm font-bold leading-8 text-[#5368aa]">{{ ($isLawyer ? $message->getAttribute('display_english_message') : $message->message_translated) ?: 'لا توجد ترجمة متاحة.' }}</p>
                        </div>
                    </div>

                    @if($message->attachments && $message->attachments->count())
                        <div class="mt-5 flex flex-wrap gap-2">
                            @foreach($message->attachments as $attachment)
                                <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-black text-[#0f1b3d] transition hover:bg-slate-50">
                                    {{ $attachment->file_name ?? 'مرفق' }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-2xl bg-slate-50 p-10 text-center"><p class="text-sm font-bold text-slate-500">لا توجد رسائل داخل هذه الاستشارة.</p></div>
            @endforelse
        </div>
    </section>

    @if(false)
        <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-black text-[#0f1b3d]">إرسال رد من الإدارة</h2>
            <p class="mt-1 text-sm font-bold text-slate-500">اكتب رد الإدارة، وسيظهر ضمن سجل الرسائل.</p>
            <form id="adminReplyForm" method="POST" action="{{ route('admin.tickets.reply', $ticket) }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                @csrf
                <textarea name="message_original" rows="6" required placeholder="اكتب رد الإدارة هنا..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-5 text-sm font-bold leading-8 text-[#0f1b3d] outline-none transition focus:border-[#5368aa] focus:ring-4 focus:ring-[#5368aa]/10"></textarea>
                <input type="hidden" name="original_language" value="ar">
                <input type="hidden" name="translated_language" value="{{ $messages->first()?->original_language ?? 'en' }}">
                <div>
                    <label class="mb-2 block text-sm font-extrabold text-slate-600">المرفقات</label>
                    <input type="file" name="attachments[]" multiple class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-500">
                </div>
                <button id="adminReplySubmitButton" type="submit" class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-[#0f1b3d] px-8 text-sm font-extrabold text-white transition hover:bg-[#17264f] disabled:cursor-not-allowed disabled:opacity-70">
                    <span id="adminReplySubmitSpinner" class="hidden h-5 w-5 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                    <span id="adminReplySubmitText">إرسال الرد</span>
                </button>
            </form>
        </section>
    @endif
</div>

<script>
    const adminReplyForm = document.getElementById('adminReplyForm');
    if (adminReplyForm) {
        adminReplyForm.addEventListener('submit', function () {
            const button = document.getElementById('adminReplySubmitButton');
            const spinner = document.getElementById('adminReplySubmitSpinner');
            const text = document.getElementById('adminReplySubmitText');
            if (!button || button.disabled) return;
            button.disabled = true;
            spinner?.classList.remove('hidden');
            if (text) text.textContent = 'جاري الإرسال...';
        });
    }
</script>
@endsection
