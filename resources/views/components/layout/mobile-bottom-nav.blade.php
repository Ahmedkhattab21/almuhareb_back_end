<nav class="fixed bottom-0 left-0 right-0 z-40 border-t border-slate-200 bg-white px-3 py-2 shadow-[0_-8px_30px_rgba(15,27,61,0.08)] lg:hidden">

    <div class="grid grid-cols-5 items-center gap-1 text-center text-[11px]">

        {{-- Dashboard --}}
        <a
            href="{{ route('admin.dashboard') }}"
            class="flex flex-col items-center justify-center gap-1 rounded-2xl px-2 py-2 font-semibold text-[#0f1b3d]"
        >
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#0f1b3d] text-white">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 11.5L12 4l9 7.5"/>
                    <path d="M5 10.5V20h14v-9.5"/>
                    <path d="M9 20v-6h6v6"/>
                </svg>
            </span>

            <span>{{ __('dashboard.sidebar.dashboard') }}</span>
        </a>

        {{-- Users --}}
        <a
            href="#"
            class="flex flex-col items-center justify-center gap-1 rounded-2xl px-2 py-2 font-medium text-slate-500"
        >
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </span>

            <span>{{ __('dashboard.sidebar.users') }}</span>
        </a>

        {{-- Tickets --}}
        <a
            href="#"
            class="flex flex-col items-center justify-center gap-1 rounded-2xl px-2 py-2 font-medium text-slate-500"
        >
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 9a3 3 0 0 0 0 6v3h18v-3a3 3 0 0 0 0-6V6H3v3z"/>
                    <path d="M13 6v12"/>
                </svg>
            </span>

            <span>{{ __('dashboard.sidebar.tickets') }}</span>
        </a>

        {{-- Notifications --}}
        <a
            href="#"
            class="relative flex flex-col items-center justify-center gap-1 rounded-2xl px-2 py-2 font-medium text-slate-500"
        >
            <span class="relative flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                    <path d="M13.7 21a2 2 0 0 1-3.4 0"/>
                </svg>

                <span class="absolute -top-1 -end-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                    5
                </span>
            </span>

            <span>{{ __('dashboard.sidebar.notifications') }}</span>
        </a>

        {{-- More / Open Sidebar --}}
        <button
            type="button"
            onclick="openSidebar()"
            class="flex flex-col items-center justify-center gap-1 rounded-2xl px-2 py-2 font-medium text-slate-500"
        >
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M4 6h16"/>
                    <path d="M4 12h16"/>
                    <path d="M4 18h16"/>
                </svg>
            </span>

            <span>{{ __('dashboard.bottom.more') }}</span>
        </button>

    </div>

</nav>
