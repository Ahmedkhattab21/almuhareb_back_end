<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Lawyer;
use App\Models\Ticket;
use App\Models\Worker;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'companies' => Company::count(),
            'lawyers' => Lawyer::count(),
            'workers' => Worker::count(),
            'tickets' => Ticket::count(),
        ];

        $recentTickets = Ticket::with(['worker', 'company', 'lawyer', 'messages'])
            ->latest('created_at')
            ->latest('id')
            ->limit(5)
            ->get();

        $recentCompanies = Company::with('lawyer')
            ->latest('created_at')
            ->latest('id')
            ->limit(5)
            ->get();

        $recentWorkers = Worker::with('company')
            ->latest('created_at')
            ->latest('id')
            ->limit(5)
            ->get();

        $weekStart = now()->startOfDay()->subDays(6);

        $ticketCountsByDate = Ticket::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->whereDate('created_at', '>=', $weekStart->toDateString())
            ->groupBy('date')
            ->pluck('total', 'date');

        $arabicDays = [
            'Saturday' => 'السبت',
            'Sunday' => 'الأحد',
            'Monday' => 'الإثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
        ];

        $ticketsOverTime = collect(range(0, 6))->map(function ($offset) use ($ticketCountsByDate, $arabicDays) {
            $date = now()->startOfDay()->subDays(6 - $offset);
            $dateKey = $date->toDateString();

            return [
                'date' => $date,
                'label' => $arabicDays[$date->format('l')] ?? $date->format('D'),
                'short_date' => $date->format('m-d'),
                'count' => (int) ($ticketCountsByDate[$dateKey] ?? 0),
            ];
        });

        $maxWeeklyTickets = max(1, (int) $ticketsOverTime->max('count'));

        $openTicketsCount = Ticket::whereIn('status', ['open', 'pending', 'in_progress'])->count();
        $closedTicketsCount = Ticket::where('status', 'closed')->count();
        $totalTicketsCount = $stats['tickets'];

        $ticketStatusDistribution = [
            'total' => $totalTicketsCount,
            'open' => $openTicketsCount,
            'closed' => $closedTicketsCount,
            'total_percent' => $totalTicketsCount > 0 ? 100 : 0,
            'open_percent' => $totalTicketsCount > 0 ? round(($openTicketsCount / $totalTicketsCount) * 100) : 0,
            'closed_percent' => $totalTicketsCount > 0 ? round(($closedTicketsCount / $totalTicketsCount) * 100) : 0,
        ];

        return view('admin.dashboard', compact(
            'stats',
            'recentTickets',
            'recentCompanies',
            'recentWorkers',
            'ticketsOverTime',
            'maxWeeklyTickets',
            'ticketStatusDistribution',
        ));
    }
}
