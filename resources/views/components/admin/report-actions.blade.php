@props(['report'])

@php
    $query = request()->query();
    $pdfUrl = route('admin.reports.show', array_merge(['report' => $report, 'format' => 'pdf'], $query));
    $excelUrl = route('admin.reports.show', array_merge(['report' => $report, 'format' => 'excel'], $query));
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-3']) }}>
    <a href="{{ $pdfUrl }}" target="_blank"
        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 text-sm font-extrabold text-[#0f1b3d] shadow-sm transition hover:bg-slate-50">
        <span>PDF</span>
        <span>استخراج</span>
    </a>
    <a href="{{ $excelUrl }}"
        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-[#0f1b3d] px-5 text-sm font-extrabold text-white shadow-sm transition hover:bg-[#16264f]">
        <span>Excel</span>
        <span>استخراج</span>
    </a>
</div>
