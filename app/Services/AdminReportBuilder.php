<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Company;
use App\Models\ContactTicket;
use App\Models\Lawyer;
use App\Models\Position;
use App\Models\Recommendation;
use App\Models\Ticket;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminReportBuilder
{
    public function build(string $report, Request $request): array
    {
        return match ($report) {
            'dashboard' => $this->dashboardReport(),
            'companies' => $this->companiesReport($request),
            'categories' => $this->categoriesReport($request),
            'lawyers' => $this->lawyersReport($request),
            'workers' => $this->workersReport($request),
            'positions' => $this->positionsReport($request),
            'tickets' => $this->ticketsReport($request),
            'recommendations' => $this->recommendationsReport($request),
            'contact-tickets' => $this->contactTicketsReport($request),
            default => abort(404),
        };
    }

    private function dashboardReport(): array
    {
        $stats = [
            ['إجمالي الشركات', Company::count()],
            ['إجمالي المستشارين', Lawyer::count()],
            ['إجمالي العمال', Worker::count()],
            ['إجمالي الاستشارات', Ticket::count()],
            ['الاستشارات المفتوحة', Ticket::whereIn('status', ['open', 'pending', 'in_progress'])->count()],
            ['الاستشارات المغلقة', Ticket::where('status', 'closed')->count()],
        ];

        $weekStart = now()->startOfDay()->subDays(6);
        $ticketCountsByDate = Ticket::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->whereDate('created_at', '>=', $weekStart->toDateString())
            ->groupBy('date')
            ->pluck('total', 'date');

        $days = collect(range(0, 6))->map(function ($offset) use ($ticketCountsByDate) {
            $date = now()->startOfDay()->subDays(6 - $offset);

            return [
                $date->translatedFormat('l'),
                $date->toDateString(),
                (int) ($ticketCountsByDate[$date->toDateString()] ?? 0),
            ];
        })->all();

        $recentTickets = Ticket::with(['worker', 'company', 'lawyer', 'category'])
            ->latest('created_at')
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(fn (Ticket $ticket) => [
                $ticket->id,
                $ticket->title ?: $ticket->title_original ?: '-',
                $ticket->worker?->name ?? '-',
                $ticket->company?->company_name ?? '-',
                $ticket->lawyer?->name ?? '-',
                $ticket->category?->name ?? '-',
                $this->statusLabel($ticket->status),
                $this->date($ticket->created_at),
            ])
            ->all();

        $recentCompanies = Company::latest('created_at')
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(fn (Company $company) => [
                $company->id,
                $company->company_name,
                $company->email,
                $company->phone,
                $this->statusLabel($company->status),
                $this->date($company->created_at),
            ])
            ->all();

        $recentWorkers = Worker::with('company')
            ->latest('created_at')
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(fn (Worker $worker) => [
                $worker->id,
                $worker->name,
                $worker->company?->company_name ?? '-',
                $worker->phone,
                $worker->iqama_number,
                $this->statusLabel($worker->status),
                $this->date($worker->created_at),
            ])
            ->all();

        return $this->payload('dashboard', 'تقرير لوحة التحكم', [
            $this->section('الإحصائيات العامة', ['البند', 'القيمة'], $stats),
            $this->section('الاستشارات بمرور الوقت', ['اليوم', 'التاريخ', 'العدد'], $days),
            $this->section('أحدث الاستشارات', ['ID', 'العنوان', 'العامل', 'الشركة', 'المستشار', 'نوع القضية', 'الحالة', 'تاريخ الإنشاء'], $recentTickets),
            $this->section('أحدث الشركات', ['ID', 'الشركة', 'البريد الإلكتروني', 'الهاتف', 'الحالة', 'تاريخ الإنشاء'], $recentCompanies),
            $this->section('أحدث العمال', ['ID', 'العامل', 'الشركة', 'الهاتف', 'رقم الإقامة', 'الحالة', 'تاريخ الإنشاء'], $recentWorkers),
        ]);
    }

    private function companiesReport(Request $request): array
    {
        $query = Company::with(['lawyer', 'creator'])->withCount(['tickets', 'workers']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(fn ($q) => $q
                ->where('company_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('tax_number', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%"));
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('lawyer_id') && $request->lawyer_id !== 'all') {
            $query->whereHas('lawyers', fn ($q) => $q->where('lawyers.id', $request->lawyer_id));
        }

        $this->applySort($query, $request, 'company_name');

        $rows = $query->get()->map(fn (Company $company) => [
            $company->id,
            $company->company_name,
            $company->email,
            $company->phone,
            $company->tax_number,
            $company->address,
            $this->statusLabel($company->status),
            $company->workers_count,
            $company->tickets_count,
            $company->creator?->name ?? '-',
            $this->date($company->created_at),
        ])->all();

        return $this->payload('companies', 'تقرير الشركات', [
            $this->section('قائمة الشركات', ['ID', 'الشركة', 'البريد الإلكتروني', 'الهاتف', 'الرقم الضريبي', 'العنوان', 'الحالة', 'عدد العمال', 'عدد الاستشارات', 'أضيفت بواسطة', 'تاريخ الإنشاء'], $rows),
        ]);
    }

    private function categoriesReport(Request $request): array
    {
        $query = Category::with('admin:id,name,email')
            ->withCount([
                'lawyers as consultants_count' => fn ($q) => $q->select(DB::raw('count(distinct lawyers.id)')),
                'tickets',
            ]);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('status', 'like', "%{$search}%"));
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $this->applySort($query, $request, 'name');

        $rows = $query->get()->map(fn (Category $category) => [
            $category->id,
            $category->name,
            $this->statusLabel($category->status),
            $category->consultants_count,
            $category->tickets_count,
            $category->admin?->name ?? '-',
            $this->date($category->created_at),
        ])->all();

        return $this->payload('categories', 'تقرير أنواع القضايا', [
            $this->section('قائمة أنواع القضايا', ['ID', 'نوع القضية', 'الحالة', 'عدد المستشارين', 'عدد الاستشارات', 'أضيفت بواسطة', 'تاريخ الإنشاء'], $rows),
        ]);
    }

    private function lawyersReport(Request $request): array
    {
        $query = Lawyer::with(['creator', 'categories'])
            ->withCount(['tickets', 'companies'])
            ->withCount([
                'tickets as closed_today_tickets_count' => fn ($q) => $q
                    ->whereIn('status', ['closed', 'resolved'])
                    ->whereDate('closed_at', now()->toDateString()),
            ]);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(fn ($q) => $q
                ->when(is_numeric($search), fn ($sub) => $sub->where('id', $search))
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $this->applySort($query, $request, 'name');

        $rows = $query->get()->map(fn (Lawyer $lawyer) => [
            $lawyer->id,
            $lawyer->name,
            $lawyer->email,
            $lawyer->phone,
            $this->statusLabel($lawyer->status),
            $lawyer->companies_count,
            $lawyer->tickets_count,
            $lawyer->closed_today_tickets_count,
            $lawyer->categories->pluck('name')->unique()->implode('، ') ?: '-',
            $lawyer->creator?->name ?? '-',
            $this->date($lawyer->created_at),
        ])->all();

        return $this->payload('lawyers', 'تقرير المستشارين', [
            $this->section('قائمة المستشارين', ['ID', 'الاسم', 'البريد الإلكتروني', 'الهاتف', 'الحالة', 'عدد الشركات', 'عدد الاستشارات', 'إنجاز اليوم', 'أنواع القضايا', 'أضيف بواسطة', 'تاريخ الإنشاء'], $rows),
        ]);
    }

    private function workersReport(Request $request): array
    {
        $query = Worker::with([
            'company',
            'position',
            'city',
            'nationalityPreferredLanguage.nationality',
            'nationalityPreferredLanguage.preferedLanguage',
        ])->withCount('tickets');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('iqama_number', 'like', "%{$search}%")
                ->orWhereHas('company', fn ($sub) => $sub->where('company_name', 'like', "%{$search}%"))
                ->orWhereHas('position', fn ($sub) => $sub->where('name', 'like', "%{$search}%")));
        }

        foreach (['status', 'company_id', 'position_id'] as $field) {
            if ($request->filled($field) && $request->{$field} !== 'all') {
                $query->where($field, $request->{$field});
            }
        }

        if ($request->filled('nationality_id') && $request->nationality_id !== 'all') {
            $query->whereHas('nationalityPreferredLanguage', fn ($q) => $q->where('nationality_id', $request->nationality_id));
        }

        if ($request->filled('prefered_language_id') && $request->prefered_language_id !== 'all') {
            $query->whereHas('nationalityPreferredLanguage', fn ($q) => $q->where('prefered_language_id', $request->prefered_language_id));
        }

        $this->applySort($query, $request, 'name');

        $rows = $query->get()->map(fn (Worker $worker) => [
            $worker->id,
            $worker->name,
            $worker->email,
            $worker->phone,
            $worker->iqama_number,
            $worker->company?->company_name ?? '-',
            $worker->position?->name ?? '-',
            $worker->city?->name ?? '-',
            $worker->nationalityPreferredLanguage?->nationality?->nationality ?? '-',
            $worker->nationalityPreferredLanguage?->preferedLanguage?->prefered_language ?? $worker->preferred_language ?? '-',
            $this->statusLabel($worker->status),
            $worker->tickets_count,
            $this->date($worker->created_at),
        ])->all();

        return $this->payload('workers', 'تقرير العمال', [
            $this->section('قائمة العمال', ['ID', 'الاسم', 'البريد الإلكتروني', 'الهاتف', 'رقم الإقامة', 'الشركة', 'الوظيفة', 'المدينة', 'الجنسية', 'اللغة المفضلة', 'الحالة', 'عدد الاستشارات', 'تاريخ الإنشاء'], $rows),
        ]);
    }

    private function positionsReport(Request $request): array
    {
        $query = Position::withCount('workers');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('status', 'like', "%{$search}%"));
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $this->applySort($query, $request, 'name');

        $rows = $query->get()->map(fn (Position $position) => [
            $position->id,
            $position->name,
            $this->statusLabel($position->status),
            $position->workers_count,
            $this->date($position->created_at),
        ])->all();

        return $this->payload('positions', 'تقرير الوظائف', [
            $this->section('قائمة الوظائف', ['ID', 'الوظيفة', 'الحالة', 'عدد العمال', 'تاريخ الإنشاء'], $rows),
        ]);
    }

    private function ticketsReport(Request $request): array
    {
        $query = Ticket::with(['worker', 'company', 'lawyer', 'category', 'latestMessage']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $ticketNumber = ltrim(preg_replace('/\D+/', '', $search) ?? '', '0');
            $query->where(function ($q) use ($search, $ticketNumber) {
                if ($ticketNumber !== '') {
                    $q->where('id', $ticketNumber);
                }

                $q->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('title_original', 'like', "%{$search}%")
                    ->orWhere('title_translated', 'like', "%{$search}%")
                    ->orWhere('last_message_preview', 'like', "%{$search}%")
                    ->orWhereHas('worker', fn ($sub) => $sub->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"))
                    ->orWhereHas('company', fn ($sub) => $sub->where('company_name', 'like', "%{$search}%"))
                    ->orWhereHas('lawyer', fn ($sub) => $sub->where('name', 'like', "%{$search}%"));
            });
        }

        foreach (['status', 'company_id', 'lawyer_id', 'category_id', 'priority'] as $field) {
            if ($request->filled($field) && $request->{$field} !== 'all') {
                $query->where($field, $request->{$field});
            }
        }

        $rows = $query->latest('last_message_at')->latest('id')->get()->map(fn (Ticket $ticket) => [
            $ticket->id,
            $ticket->title ?: $ticket->title_original ?: '-',
            $ticket->worker?->name ?? '-',
            $ticket->company?->company_name ?? '-',
            $ticket->lawyer?->name ?? '-',
            $ticket->category?->name ?? '-',
            $this->statusLabel($ticket->status),
            $this->statusLabel($ticket->priority),
            $ticket->last_message_preview,
            $this->date($ticket->last_message_at),
            $ticket->lat,
            $ticket->long,
            $this->date($ticket->created_at),
        ])->all();

        return $this->payload('tickets', 'تقرير الاستشارات', [
            $this->section('قائمة الاستشارات', ['ID', 'العنوان', 'العامل', 'الشركة', 'المستشار', 'نوع القضية', 'الحالة', 'الأولوية', 'آخر رسالة', 'وقت آخر رسالة', 'Lat', 'Long', 'تاريخ الإنشاء'], $rows),
        ]);
    }

    private function recommendationsReport(Request $request): array
    {
        $query = Recommendation::with(['ticket.category', 'worker', 'company', 'lawyer'])->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(fn ($q) => $q
                ->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhereHas('worker', fn ($sub) => $sub->where('name', 'like', "%{$search}%"))
                ->orWhereHas('company', fn ($sub) => $sub->where('company_name', 'like', "%{$search}%"))
                ->orWhereHas('lawyer', fn ($sub) => $sub->where('name', 'like', "%{$search}%")));
        }

        foreach (['company_id', 'lawyer_id'] as $field) {
            if ($request->filled($field) && $request->{$field} !== 'all') {
                $query->where($field, $request->{$field});
            }
        }

        $rows = $query->get()->map(fn (Recommendation $recommendation) => [
            $recommendation->id,
            $recommendation->title,
            $recommendation->ticket_id,
            $recommendation->ticket?->category?->name ?? '-',
            $recommendation->worker?->name ?? '-',
            $recommendation->company?->company_name ?? '-',
            $recommendation->lawyer?->name ?? '-',
            $recommendation->attachment_name ?? '-',
            $this->date($recommendation->created_at),
            $recommendation->description,
        ])->all();

        return $this->payload('recommendations', 'تقرير التوصيات', [
            $this->section('قائمة التوصيات', ['ID', 'العنوان', 'رقم الاستشارة', 'نوع القضية', 'العامل', 'الشركة', 'المستشار', 'المرفق', 'التاريخ', 'الوصف'], $rows),
        ]);
    }

    private function contactTicketsReport(Request $request): array
    {
        $query = ContactTicket::query()->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('company', 'like', "%{$search}%")
                ->orWhere('message', 'like', "%{$search}%"));
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $rows = $query->get()->map(fn (ContactTicket $ticket) => [
            $ticket->id,
            $ticket->name,
            $ticket->email,
            $ticket->phone,
            $ticket->company,
            $this->statusLabel($ticket->status),
            $this->date($ticket->read_at),
            $this->date($ticket->created_at),
            $ticket->message,
        ])->all();

        return $this->payload('contact-tickets', 'تقرير رسائل التواصل', [
            $this->section('قائمة رسائل التواصل', ['ID', 'الاسم', 'البريد الإلكتروني', 'الهاتف', 'الشركة', 'الحالة', 'تاريخ القراءة', 'تاريخ الإرسال', 'الرسالة'], $rows),
        ]);
    }

    private function applySort(Builder $query, Request $request, string $nameColumn): void
    {
        match ($request->get('sort', 'id_asc')) {
            'latest' => $query->orderByDesc('id'),
            'name_asc' => $query->orderBy($nameColumn, 'asc')->orderBy('id', 'asc'),
            'name_desc' => $query->orderBy($nameColumn, 'desc')->orderBy('id', 'asc'),
            default => $query->orderBy('id', 'asc'),
        };
    }

    private function payload(string $slug, string $title, array $sections): array
    {
        return [
            'slug' => $slug,
            'title' => $title,
            'generatedAt' => now(),
            'sections' => $sections,
        ];
    }

    private function section(string $title, array $headers, array $rows): array
    {
        return compact('title', 'headers', 'rows');
    }

    private function date(mixed $value): string
    {
        if (blank($value)) {
            return '-';
        }

        return $value instanceof Carbon
            ? $value->format('Y-m-d H:i')
            : Carbon::parse($value)->format('Y-m-d H:i');
    }

    private function statusLabel(?string $status): string
    {
        return [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'pending' => 'معلق',
            'suspended' => 'موقوف',
            'open' => 'مفتوحة',
            'in_progress' => 'قيد المعالجة',
            'closed' => 'مغلقة',
            'resolved' => 'مغلقة',
            'new' => 'جديد',
            'read' => 'مقروء',
            'low' => 'منخفضة',
            'medium' => 'متوسطة',
            'high' => 'عالية',
            'urgent' => 'عاجلة',
        ][$status] ?? ($status ?: '-');
    }
}
