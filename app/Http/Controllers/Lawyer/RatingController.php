<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\TicketRating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index(Request $request)
    {
        $lawyer = auth('lawyer')->user();

        $query = TicketRating::query()
            ->with([
                'ticket:id,title,status,worker_id,company_id,lawyer_id,category_id,created_at,closed_at',
                'ticket.category:id,name',
                'worker:id,name,email,phone',
                'company:id,company_name,email,phone',
            ])
            ->where('lawyer_id', $lawyer?->id)
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                    ->orWhere('rating', $search)
                    ->orWhereHas('ticket', fn ($ticketQuery) => $ticketQuery
                        ->where('id', $search)
                        ->orWhere('title', 'like', "%{$search}%"))
                    ->orWhereHas('worker', fn ($workerQuery) => $workerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%"))
                    ->orWhereHas('company', fn ($companyQuery) => $companyQuery
                        ->where('company_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('rating') && $request->rating !== 'all') {
            $query->where('rating', (int) $request->rating);
        }

        $ratings = $query->paginate(10)->withQueryString();

        $baseQuery = TicketRating::where('lawyer_id', $lawyer?->id);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'today' => (clone $baseQuery)->whereDate('created_at', now()->toDateString())->count(),
            'average' => round((float) (clone $baseQuery)->avg('rating'), 1),
            'with_message' => (clone $baseQuery)->whereNotNull('message')->where('message', '<>', '')->count(),
        ];

        return view('lawyer.ratings.index', compact('ratings', 'stats'));
    }
}
