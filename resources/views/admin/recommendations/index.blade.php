@extends('layouts.app')

@section('title', 'التوصيات')

@section('content')
@php
    $recommendations = $recommendations ?? collect();
    $stats = $stats ?? ['total' => 0, 'today' => 0];
@endphp

<div class="space-y-6">
    <section class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <div class="text-sm text-slate-500">
                لوحة التحكم <span class="mx-1">›</span> <span class="font-bold text-[#0f1b3d]">التوصيات</span>
            </div>

            <h1 class="mt-2 text-3xl font-black text-[#0f1b3d]">التوصيات</h1>
            <p class="mt-2 text-sm leading-7 text-slate-500">كل التوصيات التي أرسلها المحامون للشركات بخصوص شكاوى العمال.</p>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-bold text-slate-500">إجمالي التوصيات</p>
            <h3 class="mt-4 text-5xl font-black text-[#0f1b3d]">{{ number_format($stats['total'] ?? 0) }}</h3>
        </div>

        <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-bold text-slate-500">توصيات اليوم</p>
            <h3 class="mt-4 text-5xl font-black text-[#0f1b3d]">{{ number_format($stats['today'] ?? 0) }}</h3>
        </div>
    </section>

    <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('admin.recommendations.index') }}" class="border-b border-slate-100 p-5">
            <div class="flex flex-col gap-3 xl:flex-row xl:items-center">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="ابحث بالعنوان، الشركة، المحامي، العامل..."
                    class="h-12 flex-1 rounded-2xl border border-slate-200 bg-[#f8fbff] px-5 text-sm font-bold outline-none focus:border-[#5368aa]"
                >

                <select name="company_id" class="h-12 rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600">
                    <option value="all">كل الشركات</option>
                    @foreach($companies ?? [] as $company)
                        <option value="{{ $company->id }}" @selected((string) request('company_id') === (string) $company->id)>
                            {{ $company->company_name }}
                        </option>
                    @endforeach
                </select>

                <select name="lawyer_id" class="h-12 rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600">
                    <option value="all">كل المحامين</option>
                    @foreach($lawyers ?? [] as $lawyer)
                        <option value="{{ $lawyer->id }}" @selected((string) request('lawyer_id') === (string) $lawyer->id)>
                            {{ $lawyer->name }}
                        </option>
                    @endforeach
                </select>

                <button class="h-12 rounded-2xl bg-[#0f1b3d] px-7 text-sm font-extrabold text-white">تطبيق</button>
                <a href="{{ route('admin.recommendations.index') }}" class="inline-flex h-12 items-center rounded-2xl px-4 text-sm font-bold text-blue-700">إعادة ضبط</a>
            </div>
        </form>

        <div class="border-b border-slate-100 px-5 py-5">
            <h2 class="text-2xl font-black text-[#0f1b3d]">قائمة التوصيات</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px] text-sm">
                <thead class="bg-[#f8fbff] text-slate-500">
                    <tr>
                        <th class="px-5 py-4 text-start">ID</th>
                        <th class="px-5 py-4 text-start">العنوان</th>
                        <th class="px-5 py-4 text-start">التذكرة</th>
                        <th class="px-5 py-4 text-start">العامل</th>
                        <th class="px-5 py-4 text-start">الشركة</th>
                        <th class="px-5 py-4 text-start">المحامي</th>
                        <th class="px-5 py-4 text-start">المرفق</th>
                        <th class="px-5 py-4 text-start">التاريخ</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($recommendations as $recommendation)
                        <tr onclick="window.location.href='{{ route('admin.recommendations.show', $recommendation) }}'" class="cursor-pointer hover:bg-slate-50">
                            <td class="px-5 py-5 font-black text-[#0f1b3d]">#{{ $recommendation->id }}</td>
                            <td class="px-5 py-5 font-black text-[#0f1b3d]">{{ \Illuminate\Support\Str::limit($recommendation->title, 45) }}</td>
                            <td class="px-5 py-5">#{{ $recommendation->ticket_id }}</td>
                            <td class="px-5 py-5">{{ $recommendation->worker?->name ?? '-' }}</td>
                            <td class="px-5 py-5">{{ $recommendation->company?->company_name ?? '-' }}</td>
                            <td class="px-5 py-5">{{ $recommendation->lawyer?->name ?? '-' }}</td>
                            <td class="px-5 py-5">
                                @if($recommendation->attachment)
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">موجود</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-5 text-slate-500">{{ $recommendation->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-14 text-center text-slate-500">لا توجد توصيات حتى الآن.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($recommendations, 'links') && $recommendations->hasPages())
            <div class="border-t border-slate-100 bg-[#f8fbff] px-5 py-4">
                {{ $recommendations->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
