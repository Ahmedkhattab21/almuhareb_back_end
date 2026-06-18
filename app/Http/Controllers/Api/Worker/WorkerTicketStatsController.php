<?php

namespace App\Http\Controllers\Api\Worker;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class WorkerTicketStatsController extends Controller
{
    public function index(Request $request)
    {
        $worker = $request->user();

        $baseQuery = Ticket::query()
            ->where('worker_id', $worker->id);

        $totalTickets = (clone $baseQuery)->count();

        $openTickets = (clone $baseQuery)
            ->where('status', 'open')
            ->count();

        $pendingTickets = (clone $baseQuery)
            ->where('status', 'pending')
            ->count();

        $closedTickets = (clone $baseQuery)
            ->where('status', 'closed')
            ->count();

        return response()->json([
            'status' => true,
            'message' => 'تم جلب إحصائيات استشارات العامل بنجاح.',
            'data' => [
                'worker' => [
                    'id' => $worker->id,
                    'name' => $worker->name,
                    'email' => $worker->email,
                ],
                'tickets' => [
                    'total' => $totalTickets,
                    'open' => $openTickets,
                    'pending' => $pendingTickets,
                    'closed' => $closedTickets,
                ],
            ],
        ]);
    }

    public function stats(Request $request)
{
    $worker = $request->user();

    $baseQuery = Ticket::query()
        ->where('worker_id', $worker->id);

    return response()->json([
        'status' => true,
        'message' => 'تم جلب إحصائيات استشارات العامل بنجاح.',
        'data' => [
            'worker' => [
                'id' => $worker->id,
                'name' => $worker->name,
                'email' => $worker->email,
                'phone' => $worker->phone,
            ],
            'tickets' => [
                'total' => (clone $baseQuery)->count(),
                'open' => (clone $baseQuery)->where('status', 'open')->count(),
                'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
                'closed' => (clone $baseQuery)->where('status', 'closed')->count(),
            ],
        ],
    ]);
}
}
