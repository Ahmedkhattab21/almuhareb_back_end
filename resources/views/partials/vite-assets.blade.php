@php
    $viteAssets = ['resources/css/app.css', 'resources/js/app.js'];
    $hasViteManifest = file_exists(public_path('build/manifest.json'));
    $hasViteHot = app()->environment('local') && file_exists(public_path('hot'));
@endphp

@if ($hasViteHot || $hasViteManifest)
    @vite($viteAssets)
@endif
