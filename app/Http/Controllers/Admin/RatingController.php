<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Lawyer;
use App\Models\TicketRating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index(Request $request)
    {
        $query = TicketRating::query()
            ->with([
                'ticket:id,title,status,worker_id,company_id,lawyer_id,category_id,created_at,closed_at',
                'ticket.category:id,name',
                'worker:id,name,email,phone',
                'company:id,company_name,email,phone',
                'lawyer:id,name,email,phone',
            ])
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
                        ->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('lawyer', fn ($lawyerQuery) => $lawyerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('rating') && $request->rating !== 'all') {
            $query->where('rating', (int) $request->rating);
        }

        if ($request->filled('company_id') && $request->company_id !== 'all') {
            $query->where('company_id', (int) $request->company_id);
        }

        if ($request->filled('lawyer_id') && $request->lawyer_id !== 'all') {
            $query->where('lawyer_id', (int) $request->lawyer_id);
        }

        $ratings = $query->paginate(10)->withQueryString();

        $companies = Company::query()->orderBy('company_name')->get(['id', 'company_name']);
        $lawyers = Lawyer::query()->orderBy('name')->get(['id', 'name']);

        $stats = [
            'total' => TicketRating::count(),
            'today' => TicketRating::whereDate('created_at', now()->toDateString())->count(),
            'average' => round((float) TicketRating::avg('rating'), 1),
            'with_message' => TicketRating::whereNotNull('message')->where('message', '<>', '')->count(),
        ];

        return view('admin.ratings.index', compact('ratings', 'companies', 'lawyers', 'stats'));
    }
}
