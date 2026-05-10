@php
    $currentLocale = app()->getLocale();
    $nextLocale = $currentLocale === 'ar' ? 'en' : 'ar';
    $label = $currentLocale === 'ar' ? 'English' : 'العربية';
@endphp

<a
    href="{{ route('lang.switch', $nextLocale) }}"
    class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white/80 px-4 py-2 text-sm text-slate-700 shadow-sm backdrop-blur hover:bg-white transition"
>
    <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="9"/>
        <path d="M3 12h18"/>
        <path d="M12 3a14 14 0 0 1 0 18"/>
        <path d="M12 3a14 14 0 0 0 0 18"/>
    </svg>

    <span>{{ $label }}</span>
</a>
