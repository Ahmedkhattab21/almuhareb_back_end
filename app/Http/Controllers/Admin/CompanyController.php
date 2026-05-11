<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Lawyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Throwable;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::query()
            ->with(['lawyer', 'creator']);

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

        if ($request->filled('lawyer_id') && $request->lawyer_id !== 'all') {
            $query->where('lawyer_id', $request->lawyer_id);
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

        $openDisputes = 0;

        if (
            Schema::hasTable('tickets') &&
            Schema::hasColumn('tickets', 'company_id') &&
            Schema::hasColumn('tickets', 'status')
        ) {
            $openDisputes = DB::table('tickets')
                ->whereNotIn('status', ['closed', 'resolved'])
                ->count();
        }

        $stats = [
            'total' => Company::count(),
            'active' => Company::where('status', 'active')->count(),
            'open_disputes' => $openDisputes,
        ];

        $lawyers = Lawyer::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('admin.companies.index', compact(
            'companies',
            'stats',
            'lawyers'
        ));
    }

    public function show(Company $company)
    {
        $company->load([
            'lawyer',
            'creator',
            'workers.nationality',
        ]);

        $workersCount = $company->workers()->count();

        $activeWorkersCount = $company->workers()
            ->where('status', 'active')
            ->count();

        $openTicketsCount = 0;
        $closedTicketsCount = 0;
        $latestTickets = collect();

        if (
            Schema::hasTable('tickets') &&
            Schema::hasColumn('tickets', 'company_id') &&
            Schema::hasColumn('tickets', 'status')
        ) {
            $openTicketsCount = DB::table('tickets')
                ->where('company_id', $company->id)
                ->whereNotIn('status', ['closed', 'resolved'])
                ->count();

            $closedTicketsCount = DB::table('tickets')
                ->where('company_id', $company->id)
                ->whereIn('status', ['closed', 'resolved'])
                ->count();

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
            'open_tickets' => $openTicketsCount,
            'closed_tickets' => $closedTicketsCount,
            'assigned_lawyer' => $company->lawyer ? 1 : 0,
        ];

        return view('admin.companies.show', compact(
            'company',
            'workers',
            'latestTickets',
            'stats'
        ));
    }

    public function create()
    {
        $lawyers = Lawyer::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('admin.companies.create', compact('lawyers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lawyer_id' => ['nullable', 'exists:lawyers,id'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:companies,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:50'],
            'tax_number' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,pending,suspended'],
        ]);

        try {
            $data['password'] = Hash::make($data['password']);
            $data['created_by'] = auth('admin')->id();

            $company = Company::create($data);

            if ($request->input('action') === 'save_and_add_worker' && Route::has('admin.workers.create')) {
                return redirect()
                    ->route('admin.workers.create', ['company_id' => $company->id])
                    ->with('toast_success', __('companies.messages.created'));
            }

            return redirect()
                ->route('admin.companies.index')
                ->with('toast_success', __('companies.messages.created'));

        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('toast_error', __('companies.messages.create_failed'));
        }
    }

    public function edit(Company $company)
    {
        $lawyers = Lawyer::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('admin.companies.edit', compact('company', 'lawyers'));
    }

    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'lawyer_id' => ['nullable', 'exists:lawyers,id'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:companies,email,' . $company->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:50'],
            'tax_number' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,pending,suspended'],
        ]);

        try {
            if ($request->input('action') === 'suspend') {
                $data['status'] = 'suspended';
            }

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $company->update($data);

            if ($request->input('action') === 'save_and_show') {
                return redirect()
                    ->route('admin.companies.show', $company->id)
                    ->with('toast_success', __('companies.messages.updated'));
            }

            return redirect()
                ->route('admin.companies.index')
                ->with('toast_success', __('companies.messages.updated'));

        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('toast_error', __('companies.messages.update_failed'));
        }
    }

    public function destroy(Company $company)
    {
        try {
            $company->delete();

            return redirect()
                ->route('admin.companies.index')
                ->with('toast_success', __('companies.messages.deleted'));

        } catch (Throwable $e) {
            report($e);

            return back()
                ->with('toast_error', __('companies.messages.delete_failed'));
        }
    }
}
