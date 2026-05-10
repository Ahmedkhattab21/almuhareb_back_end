<header class="sticky top-0 z-30 border-b border-slate-200 bg-white">

    <div class="flex h-20 w-full items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        {{-- Search --}}
        <div class="flex w-full max-w-md items-center gap-3 rounded-2xl bg-slate-100 px-4 py-3">
            <span class="text-slate-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="M21 21l-4.35-4.35"/>
                </svg>
            </span>

            <input
                type="text"
                placeholder="{{ __('dashboard.search_placeholder') }}"
                class="w-full bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400"
            >
        </div>

        {{-- Actions: Admin + Language + Notifications --}}
        <div class="flex shrink-0 items-center gap-3">

            {{-- Mobile Sidebar Button --}}
            <button
                onclick="openSidebar()"
                type="button"
                class="flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 bg-white text-xl lg:hidden"
            >
                ☰
            </button>

            {{-- Profile Dropdown --}}
            <div class="relative">

                <button
                    type="button"
                    onclick="toggleProfileMenu()"
                    class="flex items-center gap-3 rounded-2xl px-2 py-2 transition hover:bg-slate-50"
                >
                    {{-- Avatar --}}
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-slate-100 text-sm font-bold text-[#0f1b3d]">
                        {{ mb_substr(auth('admin')->user()->name ?? 'A', 0, 1) }}
                    </div>

                    {{-- Name --}}
                    <div class="hidden text-start sm:block">
                        <p class="text-sm font-bold text-slate-900">
                            {{ auth('admin')->user()->name ?? __('dashboard.admin_name') }}
                        </p>
                        <p class="text-xs text-slate-500">
                            {{ __('dashboard.system_admin') }}
                        </p>
                    </div>
                </button>

                {{-- Dropdown --}}
                <div
                    id="profileDropdown"
                    class="absolute start-0 top-[58px] z-50 hidden w-56 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-200/70"
                >
                    <div class="border-b border-slate-100 px-3 py-3">
                        <p class="text-sm font-bold text-slate-900">
                            {{ auth('admin')->user()->name ?? __('dashboard.admin_name') }}
                        </p>

                        <p class="mt-1 truncate text-xs text-slate-500">
                            {{ auth('admin')->user()->email ?? 'admin@almuharib.com' }}
                        </p>
                    </div>

                    <a
                        href="#"
                        class="mt-2 flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                    >
                        <svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 21a8 8 0 0 1 16 0"/>
                        </svg>

                        <span>{{ __('dashboard.profile') }}</span>
                    </a>

                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold text-red-600 transition hover:bg-red-50"
                        >
                            <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <path d="M16 17l5-5-5-5"/>
                                <path d="M21 12H9"/>
                            </svg>

                            <span>{{ __('dashboard.logout') }}</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Language --}}
            <x-ui.language-switch />

            {{-- Notifications --}}
            <button
                type="button"
                class="relative flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 bg-white"
            >
                <svg class="h-5 w-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                    <path d="M13.7 21a2 2 0 0 1-3.4 0"/>
                </svg>

                <span class="absolute -top-2 -end-2 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                    3
                </span>
            </button>

        </div>

    </div>

</header>

<script>
    function toggleProfileMenu() {
        const dropdown = document.getElementById('profileDropdown');

        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }
    }

    document.addEventListener('click', function (event) {
        const dropdown = document.getElementById('profileDropdown');
        const profileButton = event.target.closest('button[onclick="toggleProfileMenu()"]');

        if (!dropdown) return;

        if (!profileButton && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>
