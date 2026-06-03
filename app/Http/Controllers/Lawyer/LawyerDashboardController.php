<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Ticket;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LawyerDashboardController extends Controller
{
    public function index()
    {
        $lawyer = auth('lawyer')->user();

        abort_unless($lawyer, 403);

        /*
        |--------------------------------------------------------------------------
        | الشركات المسؤول عنها المحامي
        |--------------------------------------------------------------------------
        */
        $companyIds = $this->assignedCompanyIds((int) $lawyer->id);

        /*
        |--------------------------------------------------------------------------
        | العمال التابعين للشركات المسؤول عنها المحامي
        |--------------------------------------------------------------------------
        */
        $workersQuery = $this->workersForLawyer($companyIds, (int) $lawyer->id);

        /*
        |--------------------------------------------------------------------------
        | لو الشركات مش طالعة من companies.lawyer_id
        | نحاول نجيب الشركات من العمال التابعين للمحامي
        |--------------------------------------------------------------------------
        */
        if ($companyIds->isEmpty() && Schema::hasColumn('workers', 'company_id')) {
            $companyIds = (clone $workersQuery)
                ->pluck('company_id')
                ->filter()
                ->unique()
                ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | التذاكر التابعة لشركات المحامي أو عماله
        |--------------------------------------------------------------------------
        */
        $ticketsQuery = $this->ticketsForLawyer($companyIds, (int) $lawyer->id);

        /*
        |--------------------------------------------------------------------------
        | الإحصائيات الرئيسية
        |--------------------------------------------------------------------------
        */
        $totalCompanies = $companyIds->count();

        $totalWorkers = (clone $workersQuery)->count();

        $totalTickets = (clone $ticketsQuery)->count();

        $openTickets = $this->countTicketsByStatus(
            query: clone $ticketsQuery,
            statuses: ['open', 'pending', 'waiting_reply', 'in_progress']
        );

        $closedTickets = $this->countTicketsByStatus(
            query: clone $ticketsQuery,
            statuses: ['closed', 'resolved']
        );

        $closedTodayTickets = (clone $ticketsQuery)
            ->whereIn('status', ['closed', 'resolved'])
            ->whereDate('closed_at', now()->toDateString())
            ->count();

        $stats = [
            // المفاتيح الأساسية المستخدمة في صفحة الداشبورد
            'total_workers' => $totalWorkers,
            'total_companies' => $totalCompanies,
            'total_tickets' => $totalTickets,
            'open_tickets' => $openTickets,
            'closed_tickets' => $closedTickets,
            'closed_today_tickets' => $closedTodayTickets,

            // مفاتيح احتياطية لو أي Blade قديم بيستخدمها
            'workers' => $totalWorkers,
            'companies' => $totalCompanies,
            'tickets' => $totalTickets,
            'assigned_workers' => $totalWorkers,
            'assigned_companies' => $totalCompanies,
        ];

        /*
        |--------------------------------------------------------------------------
        | التذاكر خلال آخر 7 أيام
        |--------------------------------------------------------------------------
        */
        $ticketsOverWeek = collect(range(6, 0))->map(function ($daysAgo) use ($ticketsQuery) {
            $date = Carbon::now()->subDays($daysAgo);

            $count = (clone $ticketsQuery)
                ->whereDate('created_at', $date->toDateString())
                ->count();

            return [
                'label' => $date->translatedFormat('D'),
                'short_date' => $date->format('m-d'),
                'count' => $count,
            ];
        });

        $maxWeeklyTickets = max(1, (int) $ticketsOverWeek->max('count'));

        $closedTicketsHistory = collect(range(7, 0))->map(function ($daysAgo) use ($ticketsQuery) {
            $date = Carbon::now()->subDays($daysAgo);

            $count = (clone $ticketsQuery)
                ->whereIn('status', ['closed', 'resolved'])
                ->whereDate('closed_at', $date->toDateString())
                ->count();

            return [
                'label' => $date->translatedFormat('D'),
                'short_date' => $date->format('m-d'),
                'count' => $count,
            ];
        });

        $maxClosedTicketsHistory = max(1, (int) $closedTicketsHistory->max('count'));

        /*
        |--------------------------------------------------------------------------
        | أحدث التذاكر
        |--------------------------------------------------------------------------
        */
        $recentTickets = (clone $ticketsQuery)
            ->with([
                'worker',
                'worker.company',
            ])
            ->latest()
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | أحدث العمال
        |--------------------------------------------------------------------------
        */
        $recentWorkers = (clone $workersQuery)
            ->with([
                'company',
                'position',
                'nationality',
            ])
            ->latest()
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | أحدث الشركات
        |--------------------------------------------------------------------------
        */
        $recentCompanies = Company::query()
            ->whereIn('id', $companyIds)
            ->withCount('workers')
            ->latest()
            ->limit(5)
            ->get();

        $recentCompanies = $recentCompanies->map(function ($company) use ($lawyer) {
            $company->setAttribute(
                'open_tickets_count',
                $this->countCompanyOpenTickets($company, (int) $lawyer->id)
            );

            return $company;
        });

        return view('lawyer.dashboard', compact(
            'lawyer',
            'stats',
            'ticketsOverWeek',
            'maxWeeklyTickets',
            'closedTicketsHistory',
            'maxClosedTicketsHistory',
            'recentTickets',
            'recentWorkers',
            'recentCompanies'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | IDs الشركات المسؤول عنها المحامي
    |--------------------------------------------------------------------------
    */
    private function assignedCompanyIds(int $lawyerId): Collection
    {
        $companyIds = collect();

        if (Schema::hasTable('lawyers_categories')) {
            $companyIds = DB::table('lawyers_categories')
                ->where('lawyer_id', $lawyerId)
                ->whereNotNull('company_id')
                ->distinct()
                ->pluck('company_id');
        }

        return $companyIds
            ->filter()
            ->unique()
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | Query العمال المسؤول عنهم المحامي
    |--------------------------------------------------------------------------
    */
    private function workersForLawyer(Collection $companyIds, int $lawyerId): Builder
    {
        $query = Worker::query();

        return $query->where(function ($q) use ($companyIds, $lawyerId) {
            $hasCondition = false;

            if ($companyIds->isNotEmpty() && Schema::hasColumn('workers', 'company_id')) {
                $q->whereIn('company_id', $companyIds);
                $hasCondition = true;
            }

            /*
             * احتياطي لو جدول workers عندك فيه lawyer_id
             */
            if (Schema::hasColumn('workers', 'lawyer_id')) {
                if ($hasCondition) {
                    $q->orWhere('lawyer_id', $lawyerId);
                } else {
                    $q->where('lawyer_id', $lawyerId);
                }

                $hasCondition = true;
            }

            if (! $hasCondition) {
                $q->whereRaw('1 = 0');
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Query التذاكر المسؤول عنها المحامي
    |--------------------------------------------------------------------------
    */
    private function ticketsForLawyer(Collection $companyIds, int $lawyerId): Builder
    {
        $query = Ticket::query();

        if (! Schema::hasColumn('tickets', 'lawyer_id')) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('lawyer_id', $lawyerId);

        $workerIds = $this->assignedWorkerIds($companyIds, $lawyerId);

        return $query->where(function ($q) use ($companyIds, $lawyerId, $workerIds) {
            $hasCondition = false;

            /*
             * لو جدول tickets فيه lawyer_id
             */
            if (Schema::hasColumn('tickets', 'lawyer_id')) {
                $q->where('lawyer_id', $lawyerId);
                $hasCondition = true;
            }

            /*
             * لو جدول tickets فيه company_id
             */
            if ($companyIds->isNotEmpty() && Schema::hasColumn('tickets', 'company_id')) {
                if ($hasCondition) {
                    $q->orWhereIn('company_id', $companyIds);
                } else {
                    $q->whereIn('company_id', $companyIds);
                }

                $hasCondition = true;
            }

            /*
             * لو التذكرة مربوطة بالعامل فقط
             */
            if ($workerIds->isNotEmpty() && Schema::hasColumn('tickets', 'worker_id')) {
                if ($hasCondition) {
                    $q->orWhereIn('worker_id', $workerIds);
                } else {
                    $q->whereIn('worker_id', $workerIds);
                }

                $hasCondition = true;
            }

            if (! $hasCondition) {
                $q->whereRaw('1 = 0');
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | IDs العمال المسؤول عنهم المحامي
    |--------------------------------------------------------------------------
    */
    private function assignedWorkerIds(Collection $companyIds, int $lawyerId): Collection
    {
        return $this->workersForLawyer($companyIds, $lawyerId)
            ->pluck('id')
            ->filter()
            ->unique()
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | عدّ التذاكر حسب الحالة
    |--------------------------------------------------------------------------
    */
    private function countTicketsByStatus(Builder $query, array $statuses): int
    {
        if (! Schema::hasColumn('tickets', 'status')) {
            return $query->count();
        }

        return $query->whereIn('status', $statuses)->count();
    }

    /*
    |--------------------------------------------------------------------------
    | عدد التذاكر المفتوحة لكل شركة
    |--------------------------------------------------------------------------
    */
    private function countCompanyOpenTickets(Company $company, int $lawyerId): int
    {
        $openStatuses = ['open', 'pending', 'waiting_reply', 'in_progress'];

        /*
         * لو tickets فيها company_id
         */
        if (Schema::hasColumn('tickets', 'company_id')) {
            $query = Ticket::query()
                ->where('company_id', $company->id)
                ->where('lawyer_id', $lawyerId);

            if (Schema::hasColumn('tickets', 'status')) {
                $query->whereIn('status', $openStatuses);
            }

            return $query->count();
        }

        /*
         * لو tickets مربوطة بالعامل فقط
         */
        if (! Schema::hasColumn('workers', 'company_id') || ! Schema::hasColumn('tickets', 'worker_id')) {
            return 0;
        }

        $workerIds = Worker::query()
            ->where('company_id', $company->id)
            ->pluck('id');

        if ($workerIds->isEmpty()) {
            return 0;
        }

        $query = Ticket::query()
            ->whereIn('worker_id', $workerIds)
            ->where('lawyer_id', $lawyerId);

        if (Schema::hasColumn('tickets', 'status')) {
            $query->whereIn('status', $openStatuses);
        }

        return $query->count();
    }
}
