@props([
    'variant' => 'navy',
    'class' => 'h-8 w-8',
])

@php
    $source = $variant === 'white'
        ? 'brand/myaman-icon-white.png'
        : 'brand/myaman-icon-navy.png';
@endphp

<img
    src="{{ asset($source) }}"
    alt="AMAN"
    {{ $attributes->merge(['class' => $class . ' object-contain']) }}
>
