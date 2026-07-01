@php
    $isRtl = app()->getLocale() === 'ar';

    $isShowPage = request()->routeIs(
        'company.workers.show',
        'company.tickets.show',
        'company.positions.show',
        'company.lawyer.show'
    );

    $hasToast = ! $isShowPage && (session('toast_success') || session('toast_error'));
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('company_dashboard.page_title'))</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-white text-[#0f1b3d]">

    <div class="min-h-screen">

        {{-- Sidebar --}}
        <x-layout.company-sidebar :is-rtl="$isRtl" />

        {{-- Mobile / Tablet Overlay --}}
        <div
            id="companySidebarOverlay"
            class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden"
            onclick="closeCompanySidebar()"
        ></div>

        {{-- Main Content --}}
        <div class="min-h-screen transition-all duration-300 {{ $isRtl ? 'lg:mr-72' : 'lg:ml-72' }}">

            {{-- Top Navbar --}}
            <x-layout.company-navbar />

            <main class="px-4 py-6 sm:px-6 lg:px-8 pb-24 lg:pb-8">
                <div class="mx-auto max-w-[1500px]">
                    @yield('content')
                </div>
            </main>

            {{-- Mobile Bottom Nav --}}
            <x-layout.company-mobile-bottom-nav />

        </div>

    </div>

    {{-- Toast --}}
    @if ($hasToast)
        @php
            $toastType = session('toast_success') ? 'success' : 'error';

            $toastMessage = session('toast_success') ?? session('toast_error');

            $toastClasses = $toastType === 'success'
                ? 'border-green-200 bg-green-50 text-green-800'
                : 'border-red-200 bg-red-50 text-red-800';

            $toastIconClasses = $toastType === 'success'
                ? 'bg-green-100 text-green-700'
                : 'bg-red-100 text-red-700';
        @endphp

        <div
            id="app-toast"
            class="fixed top-6 z-[9999] w-[calc(100%-2rem)] max-w-md rounded-2xl border p-4 shadow-xl transition-all duration-300 start-1/2 -translate-x-1/2 sm:start-auto sm:end-6 sm:translate-x-0 {{ $toastClasses }}"
        >
            <div class="flex items-start gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $toastIconClasses }}">
                    @if ($toastType === 'success')
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M20 6L9 17l-5-5" />
                        </svg>
                    @else
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M12 9v4" />
                            <path d="M12 17h.01" />
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                        </svg>
                    @endif
                </div>

                <div class="flex-1">
                    <p class="text-sm font-black">
                        {{ $toastType === 'success' ? __('messages.success') : __('messages.error') }}
                    </p>

                    <p class="mt-1 text-sm font-bold leading-6">
                        {{ $toastMessage }}
                    </p>
                </div>

                <button
                    type="button"
                    onclick="document.getElementById('app-toast')?.remove()"
                    class="rounded-lg p-1 opacity-70 transition hover:opacity-100"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path d="M18 6L6 18" />
                        <path d="M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <script>
            setTimeout(function () {
                const toast = document.getElementById('app-toast');

                if (toast) {
                    toast.style.opacity = '0';

                    setTimeout(function () {
                        toast.remove();
                    }, 300);
                }
            }, 3500);
        </script>
    @endif

    <script>
        function companySidebarHiddenClass() {
            return document.documentElement.dir === 'rtl'
                ? 'translate-x-full'
                : '-translate-x-full';
        }

        function openCompanySidebar() {
            const sidebar = document.getElementById('companySidebar');
            const overlay = document.getElementById('companySidebarOverlay');

            sidebar.classList.remove(companySidebarHiddenClass());
            overlay.classList.remove('hidden');
        }

        function closeCompanySidebar() {
            const sidebar = document.getElementById('companySidebar');
            const overlay = document.getElementById('companySidebarOverlay');

            sidebar.classList.add(companySidebarHiddenClass());
            overlay.classList.add('hidden');
        }
    </script>

    <script>
        document.addEventListener('submit', function (event) {
            const form = event.target;

            if (!form.matches('[data-loading-form]')) {
                return;
            }

            const clickedButton = event.submitter;

            if (!clickedButton || !clickedButton.matches('[data-loading-button]')) {
                return;
            }

            if (form.dataset.submitting === 'true') {
                event.preventDefault();
                return;
            }

            form.dataset.submitting = 'true';

            if (clickedButton.name && clickedButton.value) {
                const hiddenAction = document.createElement('input');
                hiddenAction.type = 'hidden';
                hiddenAction.name = clickedButton.name;
                hiddenAction.value = clickedButton.value;
                form.appendChild(hiddenAction);
            }

            const loadingText = clickedButton.dataset.loadingText || '{{ __("messages.loading") }}';

            clickedButton.innerHTML = `
                <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span>${loadingText}</span>
            `;

            form.querySelectorAll('[data-loading-button]').forEach(function (button) {
                button.disabled = true;
                button.classList.add('cursor-not-allowed', 'opacity-70');
            });
        });
    </script>




</body>
</html>
