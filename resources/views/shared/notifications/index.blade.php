@extends($layout ?? 'layouts.app')

@section('title', __('notifications.title'))

@section('content')
@php
    $typeLabel = fn ($type) => __('notifications.types.' . $type) === 'notifications.types.' . $type
        ? $type
        : __('notifications.types.' . $type);
@endphp

<div class="space-y-6">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <div class="mb-2 flex items-center gap-2 text-sm text-slate-500">
                    <span>{{ __('notifications.breadcrumb_home') }}</span>
                    <span>/</span>
                    <span class="font-medium text-slate-700">{{ __('notifications.title') }}</span>
                </div>

                <h1 class="text-2xl font-bold text-[#0f1b3d]">{{ __('notifications.title') }}</h1>
                <p class="mt-2 text-sm text-slate-500">{{ __('notifications.subtitle') }}</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-center">
                    <div class="text-xs text-blue-600">{{ __('notifications.unread') }}</div>
                    <div class="mt-1 text-2xl font-bold text-blue-700">{{ $unreadCount }}</div>
                </div>

                @if($unreadCount > 0)
                    <form method="POST" action="{{ route($guard . '.notifications.readAll') }}">
                        @csrf
                        <button type="submit" class="rounded-xl bg-[#0f1b3d] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#17264f]">
                            {{ __('notifications.mark_all_read') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-bold text-[#0f1b3d]">{{ __('notifications.list_title') }}</h2>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($notifications as $notification)
                <div class="group flex flex-col gap-4 px-6 py-5 transition hover:bg-slate-50 md:flex-row md:items-start md:justify-between">
                    <a href="{{ route($guard . '.notifications.open', $notification->id) }}" class="flex flex-1 gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl {{ $notification->read_at ? 'bg-slate-100 text-slate-500' : 'bg-blue-50 text-blue-600' }}">
                            @if(str_contains($notification->type, 'ticket'))
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9a3 3 0 0 0 0 6v3h18v-3a3 3 0 0 0 0-6V6H3v3z" /><path d="M13 6v12" /></svg>
                            @elseif(str_contains($notification->type, 'worker'))
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" /><path d="M4 21a8 8 0 0 1 16 0" /></svg>
                            @elseif(str_contains($notification->type, 'company'))
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 21h16" /><path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16" /></svg>
                            @elseif(str_contains($notification->type, 'lawyer'))
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 13l-7 7" /><path d="M14 4l6 6" /><path d="M8 10l6 6" /></svg>
                            @else
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" /><path d="M13.7 21a2 2 0 0 1-3.4 0" /></svg>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-bold text-[#0f1b3d]">{{ $notification->title }}</h3>
                                @if(!$notification->read_at)
                                    <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">{{ __('notifications.new') }}</span>
                                @endif
                            </div>

                            @if($notification->body)
                                <p class="mt-1 text-sm leading-6 text-slate-600">{{ $notification->body }}</p>
                            @endif

                            <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-slate-400">
                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                <span class="rounded-full bg-slate-100 px-2 py-1 text-slate-500">{{ $typeLabel($notification->type) }}</span>
                            </div>
                        </div>
                    </a>

                    @if(!$notification->read_at)
                        <form method="POST" action="{{ route($guard . '.notifications.read', $notification->id) }}">
                            @csrf
                            <button type="submit" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                                {{ __('notifications.mark_read') }}
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-500">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" /><path d="M13.7 21a2 2 0 0 1-3.4 0" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#0f1b3d]">{{ __('notifications.empty_title') }}</h3>
                    <p class="mt-2 max-w-md text-sm leading-6 text-slate-500">{{ __('notifications.empty_body') }}</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">{{ $notifications->links() }}</div>
        @endif
    </section>
</div>
@endsection
