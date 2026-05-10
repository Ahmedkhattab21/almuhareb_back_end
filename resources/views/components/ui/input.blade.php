@props([
    'label' => '',
    'name',
    'type' => 'text',
    'placeholder' => '',
    'icon' => null,
    'value' => '',
])

<div class="w-full">
    @if($label)
        <label for="{{ $name }}" class="block mb-2 text-sm font-semibold text-slate-900">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        @if($icon === 'email')
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M4 6h16v12H4z"/>
                    <path d="m4 7 8 6 8-6"/>
                </svg>
            </span>
        @endif

        @if($icon === 'lock')
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <rect x="5" y="10" width="14" height="10" rx="2"/>
                    <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                </svg>
            </span>
        @endif

        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge([
                'class' => 'w-full h-14 rounded-xl border border-slate-300 bg-slate-50 px-12 text-sm text-slate-900 outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200'
            ]) }}
        >
    </div>

    @error($name)
        <p class="mt-2 text-xs text-red-600 flex items-center gap-1">
            <span>ⓘ</span>
            {{ $message }}
        </p>
    @enderror
</div>
