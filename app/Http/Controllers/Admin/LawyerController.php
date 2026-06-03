<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\Lawyer;
use App\Services\SystemNotifier;
use Carbon\Carbon;
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
            ->withCount('tickets')
            ->withCount([
                'tickets as closed_today_tickets_count' => function ($query) {
                    $query->whereIn('status', ['closed', 'resolved'])
                        ->whereDate('closed_at', now()->toDateString());
                },
            ]);

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
        ];

        return view('admin.lawyers.index', compact('lawyers', 'stats'));
    }

    public function create()
    {
        $companies = $this->activeCompaniesQuery()->get();
        $categories = $this->activeCategoriesQuery()->get();

        return view('admin.lawyers.create', compact('companies', 'categories'));
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

            'company_ids' => ['required', 'array', 'min:1'],
            'company_ids.*' => [
                'integer',
                Rule::exists('companies', 'id')->where(function ($query) {
                    return $query->where('status', 'active');
                }),
            ],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => [
                'integer',
                Rule::exists('categories', 'id')->where(function ($query) {
                    return $query->where('status', Category::STATUS_ACTIVE);
                }),
            ],
        ]);

        try {
            $companyIds = $request->input('company_ids', []);
            $categoryIds = $request->input('category_ids', []);
            unset($data['company_ids'], $data['category_ids']);

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

            $this->syncLawyerAssignments($lawyer, $companyIds, $categoryIds);
            $lawyer->refresh()->load(['companies', 'categories']);

            SystemNotifier::notifyLawyerChange(
                lawyer: $lawyer,
                type: 'lawyer_created',
                title: 'تم إضافة محامي جديد',
                body: "تم إضافة المحامي {$lawyer->name} إلى النظام.",
                actor: auth('admin')->user(),
                data: ['lawyer_id' => $lawyer->id, 'action' => 'created']
            );

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
        $lawyer->load(['admin', 'creator', 'categories']);

        $companyIds = DB::table('lawyers_categories')
            ->where('lawyer_id', $lawyer->id)
            ->whereNotNull('company_id')
            ->distinct()
            ->pluck('company_id');

        $companiesCount = $companyIds->count();

        $companies = Company::query()
            ->whereIn('id', $companyIds)
            ->orderBy('id', 'asc')
            ->paginate(5, ['*'], 'companies_page')
            ->through(function (Company $company) use ($lawyer) {
                $company->case_categories = Category::query()
                    ->join('lawyers_categories', 'categories.id', '=', 'lawyers_categories.category_id')
                    ->where('lawyers_categories.lawyer_id', $lawyer->id)
                    ->where('lawyers_categories.company_id', $company->id)
                    ->orderBy('categories.name')
                    ->get(['categories.id', 'categories.name']);

                return $company;
            })
            ->withQueryString();
        $caseCategories = $lawyer->categories
            ->unique('id')
            ->sortBy('name')
            ->values();

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
        $closedTodayTicketsCount = 0;
        $closedTicketsHistory = collect();
        $latestTickets = collect();

        if (
            Schema::hasTable('tickets') &&
            Schema::hasColumn('tickets', 'company_id')
        ) {
            $totalTicketsCount = DB::table('tickets')
                ->where('lawyer_id', $lawyer->id)
                ->count();

            if (Schema::hasColumn('tickets', 'status')) {
                $openTicketsCount = DB::table('tickets')
                    ->where('lawyer_id', $lawyer->id)
                    ->whereNotIn('status', ['closed', 'resolved'])
                    ->count();

                $closedTicketsCount = DB::table('tickets')
                    ->where('lawyer_id', $lawyer->id)
                    ->whereIn('status', ['closed', 'resolved'])
                    ->count();

                $closedTodayTicketsCount = DB::table('tickets')
                    ->where('lawyer_id', $lawyer->id)
                    ->whereIn('status', ['closed', 'resolved'])
                    ->whereDate('closed_at', now()->toDateString())
                    ->count();
            }

            $latestTickets = DB::table('tickets')
                ->where('lawyer_id', $lawyer->id)
                ->latest('id')
                ->limit(5)
                ->get();
        }

        $closedTicketsHistory = collect(range(7, 1))->map(function ($daysAgo) use ($lawyer) {
            $date = Carbon::now()->subDays($daysAgo);

            $count = DB::table('tickets')
                ->where('lawyer_id', $lawyer->id)
                ->whereIn('status', ['closed', 'resolved'])
                ->whereDate('closed_at', $date->toDateString())
                ->count();

            return [
                'date' => $date->toDateString(),
                'label' => $date->translatedFormat('D'),
                'short_date' => $date->format('m-d'),
                'count' => $count,
            ];
        });

        $stats = [
            'companies' => $companiesCount,
            'workers' => $workersCount,
            'total_tickets' => $totalTicketsCount,
            'open_tickets' => $openTicketsCount,
            'closed_tickets' => $closedTicketsCount,
            'closed_today_tickets' => $closedTodayTicketsCount,
            'active_cases_count' => $openTicketsCount,
        ];

        return view('admin.lawyers.show', compact(
            'lawyer',
            'companies',
            'caseCategories',
            'latestTickets',
            'closedTicketsHistory',
            'stats'
        ));
    }

    public function edit(Lawyer $lawyer)
    {
        $companies = $this->activeCompaniesQuery()->get();
        $categories = $this->activeCategoriesQuery()->get();

        $selectedCompanyIds = $lawyer->companies()
            ->where('companies.status', 'active')
            ->pluck('companies.id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        $selectedCategoryIds = $lawyer->categories()
            ->pluck('categories.id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        return view('admin.lawyers.edit', compact(
            'lawyer',
            'companies',
            'categories',
            'selectedCompanyIds',
            'selectedCategoryIds'
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

            'company_ids' => ['required', 'array', 'min:1'],
            'company_ids.*' => [
                'integer',
                Rule::exists('companies', 'id')->where(function ($query) {
                    return $query->where('status', 'active');
                }),
            ],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => [
                'integer',
                Rule::exists('categories', 'id')->where(function ($query) {
                    return $query->where('status', Category::STATUS_ACTIVE);
                }),
            ],
        ]);

        try {
            $companyIds = $request->input('company_ids', []);
            $categoryIds = $request->input('category_ids', []);
            unset($data['company_ids'], $data['category_ids']);

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

            $this->syncLawyerAssignments($lawyer, $companyIds, $categoryIds);
            $lawyer->refresh()->load(['companies', 'categories']);

            SystemNotifier::notifyLawyerChange(
                lawyer: $lawyer,
                type: 'lawyer_updated',
                title: 'تم تعديل بيانات محامي',
                body: "تم تعديل بيانات المحامي {$lawyer->name}.",
                actor: auth('admin')->user(),
                data: ['lawyer_id' => $lawyer->id, 'action' => 'updated']
            );

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
            $lawyer->load('companies');

            SystemNotifier::notifyLawyerChange(
                lawyer: $lawyer,
                type: 'lawyer_deleted',
                title: 'تم حذف محامي',
                body: "تم حذف المحامي {$lawyer->name} من النظام.",
                actor: auth('admin')->user(),
                data: ['lawyer_id' => $lawyer->id, 'action' => 'deleted']
            );

            if ($lawyer->avatar) {
                Storage::disk('public')->delete($lawyer->avatar);
            }

            DB::table('lawyers_categories')
                ->where('lawyer_id', $lawyer->id)
                ->delete();

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

    private function activeCategoriesQuery()
    {
        return Category::query()
            ->select('id', 'name', 'status')
            ->where('status', Category::STATUS_ACTIVE)
            ->orderBy('name', 'asc');
    }

    private function syncLawyerAssignments(Lawyer $lawyer, array $companyIds, array $categoryIds): void
    {
        $companyIds = collect($companyIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $categoryIds = collect($categoryIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $activeCompanyIds = Company::query()
            ->whereIn('id', $companyIds)
            ->where('status', 'active')
            ->pluck('id')
            ->toArray();

        $activeCategoryIds = Category::query()
            ->whereIn('id', $categoryIds)
            ->where('status', Category::STATUS_ACTIVE)
            ->pluck('id')
            ->toArray();

        DB::transaction(function () use ($lawyer, $activeCompanyIds, $activeCategoryIds) {
            DB::table('lawyers_categories')
                ->where('lawyer_id', $lawyer->id)
                ->delete();

            if (empty($activeCompanyIds) || empty($activeCategoryIds)) {
                return;
            }

            DB::table('lawyers_categories')
                ->whereIn('company_id', $activeCompanyIds)
                ->whereIn('category_id', $activeCategoryIds)
                ->delete();

            $now = now();
            $rows = [];

            foreach ($activeCompanyIds as $companyId) {
                foreach ($activeCategoryIds as $categoryId) {
                    $rows[] = [
                        'company_id' => $companyId,
                        'lawyer_id' => $lawyer->id,
                        'category_id' => $categoryId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            DB::table('lawyers_categories')->insert($rows);
        });
    }
}
