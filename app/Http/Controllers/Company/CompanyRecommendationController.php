<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Recommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyRecommendationController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::guard('company')->id();

        $query = Recommendation::query()
            ->with(['ticket.category', 'worker', 'lawyer'])
            ->where('company_id', $companyId)
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('worker', fn ($workerQuery) => $workerQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('lawyer', fn ($lawyerQuery) => $lawyerQuery->where('name', 'like', "%{$search}%"));
            });
        }

        $recommendations = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => Recommendation::where('company_id', $companyId)->count(),
            'today' => Recommendation::where('company_id', $companyId)
                ->whereDate('created_at', now()->toDateString())
                ->count(),
        ];

        return view('company.recommendations.index', compact('recommendations', 'stats'));
    }

    public function show(Recommendation $recommendation)
    {
        abort_if((int) $recommendation->company_id !== (int) Auth::guard('company')->id(), 403);

        $recommendation->load(['ticket.category', 'worker', 'lawyer']);

        return view('company.recommendations.show', compact('recommendation'));
    }
}
