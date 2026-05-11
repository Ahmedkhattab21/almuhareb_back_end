@props([
    'type' => 'button',
    'loading' => false,
    'full' => true
])

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'w-full h-14 rounded-xl bg-[#0f1b3d] text-white font-semibold shadow-lg shadow-slate-300 hover:bg-[#111f49] transition flex items-center justify-center gap-3'
    ]) }}
>
    @if($loading)
        <span class="w-5 h-5 rounded-full border-2 border-white/30 border-t-white animate-spin"></span>
    @endif

    {{ $slot }}
</button>
