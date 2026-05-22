<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Worker;

class CompanyDashboardController extends Controller
{
    public function index()
    {
        $company = auth('company')->user();

        $ticketsQuery = Ticket::query()
            ->where('company_id', $company->id);

        $workersQuery = Worker::query()
            ->where('company_id', $company->id);

        $totalTickets = (clone $ticketsQuery)->count();
        $closedTickets = (clone $ticketsQuery)
            ->whereIn('status', ['closed', 'resolved'])
            ->count();
        $openTickets = max(0, $totalTickets - $closedTickets);

        $stats = [
            'workers' => (clone $workersQuery)->count(),
            'tickets' => $totalTickets,
            'open_tickets' => $openTickets,
            'closed_tickets' => $closedTickets,
            'lawyer_name' => $company->lawyer?->name ?? __('company_dashboard.common.not_assigned'),
        ];

        $weekStart = now()->startOfDay()->subDays(6);

        $ticketCountsByDate = (clone $ticketsQuery)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->whereDate('created_at', '>=', $weekStart->toDateString())
            ->groupBy('date')
            ->pluck('total', 'date');

        $ticketsOverWeek = collect(range(0, 6))->map(function ($offset) use ($ticketCountsByDate) {
            $date = now()->startOfDay()->subDays(6 - $offset);
            $dateKey = $date->toDateString();

            return [
                'date' => $dateKey,
                'label' => __('company_dashboard.days.' . strtolower($date->format('D'))),
                'short_date' => $date->format('m-d'),
                'count' => (int) ($ticketCountsByDate[$dateKey] ?? 0),
            ];
        });

        $maxWeeklyTickets = max(1, (int) $ticketsOverWeek->max('count'));

        $ticketStatusDistribution = [
            'total' => $totalTickets,
            'open' => $openTickets,
            'closed' => $closedTickets,
            'total_percent' => $totalTickets > 0 ? 100 : 0,
            'open_percent' => $totalTickets > 0 ? round(($openTickets / $totalTickets) * 100) : 0,
            'closed_percent' => $totalTickets > 0 ? round(($closedTickets / $totalTickets) * 100) : 0,
        ];

        $recentTickets = (clone $ticketsQuery)
            ->with('worker')
            ->latest('created_at')
            ->latest('id')
            ->limit(5)
            ->get();

        $recentWorkers = (clone $workersQuery)
            ->with(['position', 'nationality'])
            ->latest('created_at')
            ->latest('id')
            ->limit(5)
            ->get();

        $lawyer = $company->lawyer;

        return view('company.dashboard', compact(
            'company',
            'stats',
            'ticketsOverWeek',
            'maxWeeklyTickets',
            'ticketStatusDistribution',
            'recentTickets',
            'recentWorkers',
            'lawyer'
        ));
    }
}
