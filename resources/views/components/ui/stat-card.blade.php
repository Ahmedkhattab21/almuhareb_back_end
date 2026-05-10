@props([
    'title',
    'value',
    'change' => null,
    'type' => 'success',
    'note' => null,
    'icon' => '📌',
])

@php
    $typeClasses = [
        'success' => 'bg-green-50 text-green-700',
        'danger' => 'bg-red-50 text-red-700',
        'info' => 'bg-blue-50 text-blue-700',
        'dark' => 'bg-slate-100 text-slate-700',
    ];

    $badgeClass = $typeClasses[$type] ?? $typeClasses['dark'];
@endphp

<div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-sm text-slate-500">{{ $title }}</p>

            <h3 class="mt-3 text-2xl font-bold text-[#0f1b3d]">
                {{ $value }}
            </h3>
        </div>

        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-lg">
            {{ $icon }}
        </div>
    </div>

    @if($change || $note)
        <div class="mt-4 flex items-center justify-between gap-2">
            @if($change)
                <span class="rounded-lg px-2 py-1 text-xs font-semibold {{ $badgeClass }}">
                    {{ $change }}
                </span>
            @endif

            @if($note)
                <span class="text-xs text-slate-500">
                    {{ $note }}
                </span>
            @endif
        </div>
    @endif
</div>
