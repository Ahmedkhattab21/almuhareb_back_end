@php
    $isRtl = app()->getLocale() === 'ar';
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('dashboard.page_title'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-white text-[#0f1b3d]">

    <x-ui.toast />

    <div class="min-h-screen">

        {{-- Sidebar --}}
        <x-layout.admin-sidebar :is-rtl="$isRtl" />

        {{-- Mobile / Tablet Overlay --}}
        <div
            id="sidebarOverlay"
            class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden"
            onclick="closeSidebar()"
        ></div>

        {{-- Main Content --}}
        <div class="min-h-screen transition-all duration-300 {{ $isRtl ? 'lg:mr-72' : 'lg:ml-72' }}">

            <x-layout.admin-navbar />

            <main class="px-4 py-6 sm:px-6 lg:px-8 pb-24 lg:pb-8">
                <div class="mx-auto max-w-[1500px]">
                    @yield('content')
                </div>
            </main>

            <x-layout.mobile-bottom-nav />

        </div>

    </div>

    <script>
        function sidebarHiddenClass() {
            return document.documentElement.dir === 'rtl'
                ? 'translate-x-full'
                : '-translate-x-full';
        }

        function openSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');

            sidebar.classList.remove(sidebarHiddenClass());
            overlay.classList.remove('hidden');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');

            sidebar.classList.add(sidebarHiddenClass());
            overlay.classList.add('hidden');
        }
    </script>

</body>
</html>
