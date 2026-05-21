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
            'message' => 'تم جلب إحصائيات تذاكر العامل بنجاح.',
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
}
