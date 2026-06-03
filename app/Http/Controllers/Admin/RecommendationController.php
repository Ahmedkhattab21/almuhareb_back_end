<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Lawyer;
use App\Models\Recommendation;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function index(Request $request)
    {
        $query = Recommendation::query()
            ->with(['ticket.category', 'worker', 'company', 'lawyer'])
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('worker', fn ($workerQuery) => $workerQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('company', fn ($companyQuery) => $companyQuery->where('company_name', 'like', "%{$search}%"))
                    ->orWhereHas('lawyer', fn ($lawyerQuery) => $lawyerQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('company_id') && $request->company_id !== 'all') {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('lawyer_id') && $request->lawyer_id !== 'all') {
            $query->where('lawyer_id', $request->lawyer_id);
        }

        $recommendations = $query->paginate(10)->withQueryString();
        $companies = Company::query()->orderBy('company_name')->get(['id', 'company_name']);
        $lawyers = Lawyer::query()->orderBy('name')->get(['id', 'name']);

        $stats = [
            'total' => Recommendation::count(),
            'today' => Recommendation::whereDate('created_at', now()->toDateString())->count(),
        ];

        return view('admin.recommendations.index', compact('recommendations', 'companies', 'lawyers', 'stats'));
    }

    public function show(Recommendation $recommendation)
    {
        $recommendation->load(['ticket.category', 'worker', 'company', 'lawyer']);

        return view('admin.recommendations.show', compact('recommendation'));
    }
}
