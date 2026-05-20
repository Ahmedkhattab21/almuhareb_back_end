<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $lawyerId = auth('lawyer')->id();

        $query = Company::query()
            ->assignedToLawyer($lawyerId)
            ->with(['lawyer'])
            ->withCount('workers');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('tax_number', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $sort = $request->get('sort', 'id_asc');

        match ($sort) {
            'latest' => $query->orderByDesc('id'),
            'oldest', 'id_asc' => $query->orderBy('id', 'asc'),
            'name_asc' => $query->orderBy('company_name', 'asc')->orderBy('id', 'asc'),
            'name_desc' => $query->orderBy('company_name', 'desc')->orderBy('id', 'asc'),
            default => $query->orderBy('id', 'asc'),
        };

        $companies = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => Company::assignedToLawyer($lawyerId)->count(),
            'active' => Company::assignedToLawyer($lawyerId)->where('status', 'active')->count(),
            'open_disputes' => Company::assignedToLawyer($lawyerId)
                ->where('status', '!=', 'active')
                ->count(),
        ];

        return view('lawyer.company.index', compact('companies', 'stats'));
    }

public function show(Company $company)
{
    $lawyerId = auth('lawyer')->id();

    abort_unless((int) $company->lawyer_id === (int) $lawyerId, 404);

    $company->load([
        'lawyer',
        'creator',
        'workers.nationality',
    ]);

    $workersCount = $company->workers()->count();

    $activeWorkersCount = $company->workers()
        ->where('status', 'active')
        ->count();

    /*
    |--------------------------------------------------------------------------
    | Tickets Stats
    |--------------------------------------------------------------------------
    | open_tickets هنا معناها كل التذاكر، عشان الـ Blade عندك مستخدم نفس المفتاح.
    |--------------------------------------------------------------------------
    */
    $allTicketsCount = 0;
    $closedTicketsCount = 0;
    $latestTickets = collect();

    if (
        Schema::hasTable('tickets') &&
        Schema::hasColumn('tickets', 'company_id')
    ) {
        // كل التذاكر الخاصة بالشركة بدون فلترة الحالة
        $allTicketsCount = DB::table('tickets')
            ->where('company_id', $company->id)
            ->count();

        // التذاكر المغلقة فقط لو عمود status موجود
        if (Schema::hasColumn('tickets', 'status')) {
            $closedTicketsCount = DB::table('tickets')
                ->where('company_id', $company->id)
                ->whereIn('status', ['closed', 'resolved'])
                ->count();
        }

        $latestTickets = DB::table('tickets')
            ->where('company_id', $company->id)
            ->latest('id')
            ->limit(5)
            ->get();
    }

    $workers = $company->workers()
        ->with('nationality')
        ->orderBy('id', 'asc')
        ->paginate(5, ['*'], 'workers_page')
        ->withQueryString();

    $stats = [
        'workers' => $workersCount,
        'active_workers' => $activeWorkersCount,

        // نفس المفتاح القديم لكن بقى بيعرض كل التذاكر
        'open_tickets' => $allTicketsCount,

        'closed_tickets' => $closedTicketsCount,
        'assigned_lawyer' => $company->lawyer ? 1 : 0,
    ];

    return view('lawyer.company.show', compact(
        'company',
        'workers',
        'latestTickets',
        'stats'
    ));
}
}
