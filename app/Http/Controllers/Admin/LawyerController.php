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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class LawyerController extends Controller
{
    public function index(Request $request)
    {
        $query = Lawyer::query()
            ->withCount('tickets');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->where('id', $search);
                }

                $q->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $sort = $request->get('sort', 'id_asc');

        match ($sort) {
            'latest' => $query->orderByDesc('id'),
            'rating_desc' => $query->orderByDesc('rating')->orderBy('id', 'asc'),
            'cases_desc' => $query->orderByDesc('tickets_count')->orderBy('id', 'asc'),
            'name_asc' => $query->orderBy('name', 'asc')->orderBy('id', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc')->orderBy('id', 'asc'),
            'id_asc' => $query->orderBy('id', 'asc'),
            default => $query->orderBy('id', 'asc'),
        };

        $lawyers = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => Lawyer::count(),
            'active' => Lawyer::where('status', 'active')->count(),
            'avg_rating' => Lawyer::avg('rating') ?? 0,
        ];

        return view('admin.lawyers.index', compact('lawyers', 'stats'));
    }

    public function create()
    {
        $companies = $this->activeCompaniesQuery()->get();

        return view('admin.lawyers.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:lawyers,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'status' => ['required', 'in:active,pending,suspended'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'company_ids' => ['nullable', 'array'],
            'company_ids.*' => [
                'integer',
                Rule::exists('companies', 'id')->where(function ($query) {
                    return $query->where('status', 'active');
                }),
            ],
        ]);

        try {
            $companyIds = $request->input('company_ids', []);
            unset($data['company_ids']);

            if ($request->hasFile('avatar')) {
                $data['avatar'] = $request->file('avatar')->store('lawyers', 'public');
            }

            $adminId = auth('admin')->id();

            $data['password'] = Hash::make($data['password']);
            $data['admin_id'] = $adminId;
            $data['created_by'] = $adminId;

            $data['rating'] = 0;
            $data['active_cases_count'] = 0;

            $lawyer = Lawyer::create($data);

            $this->attachActiveCompaniesToLawyer($lawyer, $companyIds);

            if ($request->input('action') === 'save_and_add_another') {
                return redirect()
                    ->route('admin.lawyers.create')
                    ->with('toast_success', __('lawyers.messages.created'));
            }

            return redirect()
                ->route('admin.lawyers.index')
                ->with('toast_success', __('lawyers.messages.created'));
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('toast_error', __('lawyers.messages.create_failed'));
        }
    }

    public function show(Lawyer $lawyer)
    {
        $lawyer->load(['admin', 'creator']);

        $companiesCount = $lawyer->companies()->count();

        $companies = $lawyer->companies()
            ->orderBy('id', 'asc')
            ->paginate(5, ['*'], 'companies_page')
            ->withQueryString();

        $companyIds = $lawyer->companies()->pluck('id');

        $workersCount = 0;

        if (
            Schema::hasTable('workers') &&
            Schema::hasColumn('workers', 'company_id')
        ) {
            $workersCount = DB::table('workers')
                ->whereIn('company_id', $companyIds)
                ->count();
        }

        $totalTicketsCount = 0;
        $openTicketsCount = 0;
        $closedTicketsCount = 0;
        $latestTickets = collect();

        if (
            Schema::hasTable('tickets') &&
            Schema::hasColumn('tickets', 'company_id')
        ) {
            $totalTicketsCount = DB::table('tickets')
                ->whereIn('company_id', $companyIds)
                ->count();

            if (Schema::hasColumn('tickets', 'status')) {
                $openTicketsCount = DB::table('tickets')
                    ->whereIn('company_id', $companyIds)
                    ->whereNotIn('status', ['closed', 'resolved'])
                    ->count();

                $closedTicketsCount = DB::table('tickets')
                    ->whereIn('company_id', $companyIds)
                    ->whereIn('status', ['closed', 'resolved'])
                    ->count();
            }

            $latestTickets = DB::table('tickets')
                ->whereIn('company_id', $companyIds)
                ->latest('id')
                ->limit(5)
                ->get();
        }

        $stats = [
            'companies' => $companiesCount,
            'workers' => $workersCount,
            'total_tickets' => $totalTicketsCount,
            'open_tickets' => $openTicketsCount,
            'closed_tickets' => $closedTicketsCount,
            'active_cases_count' => $openTicketsCount,
            'rating' => 0,
        ];

        return view('admin.lawyers.show', compact(
            'lawyer',
            'companies',
            'latestTickets',
            'stats'
        ));
    }

    public function edit(Lawyer $lawyer)
    {
        $companies = $this->activeCompaniesQuery()->get();

        $selectedCompanyIds = Company::query()
            ->where('lawyer_id', $lawyer->id)
            ->where('status', 'active')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        return view('admin.lawyers.edit', compact(
            'lawyer',
            'companies',
            'selectedCompanyIds'
        ));
    }

    public function update(Request $request, Lawyer $lawyer)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('lawyers', 'email')->ignore($lawyer->id),
            ],

            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'status' => ['required', 'in:active,pending,suspended'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'company_ids' => ['nullable', 'array'],
            'company_ids.*' => [
                'integer',
                Rule::exists('companies', 'id')->where(function ($query) {
                    return $query->where('status', 'active');
                }),
            ],
        ]);

        try {
            $companyIds = $request->input('company_ids', []);
            unset($data['company_ids']);

            if ($request->input('action') === 'suspend') {
                $data['status'] = 'suspended';
            }

            if ($request->hasFile('avatar')) {
                if ($lawyer->avatar) {
                    Storage::disk('public')->delete($lawyer->avatar);
                }

                $data['avatar'] = $request->file('avatar')->store('lawyers', 'public');
            }

            if (! empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $lawyer->update($data);

            $this->syncActiveCompaniesForLawyer($lawyer, $companyIds);

            if ($request->input('action') === 'save_and_show' && Route::has('admin.lawyers.show')) {
                return redirect()
                    ->route('admin.lawyers.show', $lawyer->id);
            }

            return redirect()
                ->route('admin.lawyers.index')
                ->with('toast_success', __('lawyers.messages.updated'));
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('toast_error', __('lawyers.messages.update_failed'));
        }
    }

    public function destroy(Lawyer $lawyer)
    {
        try {
            if ($lawyer->avatar) {
                Storage::disk('public')->delete($lawyer->avatar);
            }

            Company::query()
                ->where('lawyer_id', $lawyer->id)
                ->update([
                    'lawyer_id' => null,
                ]);

            $lawyer->delete();

            return redirect()
                ->route('admin.lawyers.index')
                ->with('toast_success', __('lawyers.messages.deleted'));
        } catch (Throwable $e) {
            report($e);

            return back()
                ->with('toast_error', __('lawyers.messages.delete_failed'));
        }
    }

    private function activeCompaniesQuery()
    {
        return Company::query()
            ->select('id', 'company_name', 'email', 'status')
            ->where('status', 'active')
            ->orderBy('company_name', 'asc');
    }

    private function attachActiveCompaniesToLawyer(Lawyer $lawyer, array $companyIds): void
    {
        $companyIds = collect($companyIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        if (empty($companyIds)) {
            return;
        }

        Company::query()
            ->whereIn('id', $companyIds)
            ->where('status', 'active')
            ->update([
                'lawyer_id' => $lawyer->id,
            ]);
    }

    private function syncActiveCompaniesForLawyer(Lawyer $lawyer, array $companyIds): void
    {
        $companyIds = collect($companyIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        if (empty($companyIds)) {
            Company::query()
                ->where('lawyer_id', $lawyer->id)
                ->where('status', 'active')
                ->update([
                    'lawyer_id' => null,
                ]);

            return;
        }

        Company::query()
            ->where('lawyer_id', $lawyer->id)
            ->where('status', 'active')
            ->whereNotIn('id', $companyIds)
            ->update([
                'lawyer_id' => null,
            ]);

        $this->attachActiveCompaniesToLawyer($lawyer, $companyIds);
    }
}
