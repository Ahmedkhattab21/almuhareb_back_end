<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Lawyer;
use App\Services\SystemNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
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
            $activeLawyerExists = Lawyer::query()
                ->where('id', $request->lawyer_id)
                ->where('status', 'active')
                ->exists();

            if ($activeLawyerExists) {
                $query->where('lawyer_id', $request->lawyer_id);
            }
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
            'total' => Company::count(),
            'active' => Company::where('status', 'active')->count(),
            'open_disputes' => Company::where('status', '!=', 'active')->count(),
        ];

        $lawyers = Lawyer::query()
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'status']);

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

        $totalTicketsCount = 0;
        $closedTicketsCount = 0;
        $latestTickets = collect();

        if (
            Schema::hasTable('tickets') &&
            Schema::hasColumn('tickets', 'company_id')
        ) {
            $totalTicketsCount = DB::table('tickets')
                ->where('company_id', $company->id)
                ->count();

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
            'total_tickets' => $totalTicketsCount,
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
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.companies.create', compact('lawyers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lawyer_id' => [
                'nullable',
                Rule::exists('lawyers', 'id')->where(function ($query) {
                    return $query->where('status', 'active');
                }),
            ],
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
            $company->load('lawyer');

            $this->notifyCompanyChange(
                company: $company,
                type: 'company_created',
                title: 'تم إضافة شركة جديدة',
                body: "تم إضافة شركة {$company->company_name} إلى النظام.",
                adminUrl: $this->routeOrNull('admin.companies.show', $company),
                companyUrl: $this->companyUrlForCompany(),
                data: [
                    'action' => 'created',
                    'company_id' => $company->id,
                    'company_name' => $company->company_name,
                ]
            );

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
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.companies.edit', compact('company', 'lawyers'));
    }

    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'lawyer_id' => [
                'nullable',
                Rule::exists('lawyers', 'id')->where(function ($query) {
                    return $query->where('status', 'active');
                }),
            ],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:companies,email,' . $company->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:50'],
            'tax_number' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,pending,suspended'],
        ]);

        try {
            $company->load('lawyer');
            $previousLawyer = $company->lawyer;

            if ($request->input('action') === 'suspend') {
                $data['status'] = 'suspended';
            }

            if (! empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $company->update($data);

            $changedFields = collect(array_keys($company->getChanges()))
                ->reject(fn ($field) => in_array($field, ['updated_at', 'password'], true))
                ->values()
                ->all();

            $company->refresh()->load('lawyer');

            $isSuspendedAction = $request->input('action') === 'suspend';

            $this->notifyCompanyChange(
                company: $company,
                type: $isSuspendedAction ? 'company_suspended' : 'company_updated',
                title: $isSuspendedAction ? 'تم إيقاف شركة' : 'تم تعديل بيانات شركة',
                body: $this->buildCompanyUpdateBody($company, $changedFields, $isSuspendedAction),
                adminUrl: $this->routeOrNull('admin.companies.show', $company),
                companyUrl: $this->companyUrlForCompany(),
                previousLawyer: $previousLawyer,
                data: [
                    'action' => $isSuspendedAction ? 'suspended' : 'updated',
                    'company_id' => $company->id,
                    'company_name' => $company->company_name,
                    'changed_fields' => $changedFields,
                ]
            );

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
            $company->load('lawyer');

            $this->notifyCompanyChange(
                company: $company,
                type: 'company_deleted',
                title: 'تم حذف شركة',
                body: "تم حذف شركة {$company->company_name} من النظام.",
                adminUrl: $this->routeOrNull('admin.companies.index'),
                companyUrl: $this->companyUrlForCompany(),
                forceLawyerIndexUrl: true,
                data: [
                    'action' => 'deleted',
                    'company_id' => $company->id,
                    'company_name' => $company->company_name,
                ]
            );

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

    private function notifyCompanyChange(
        Company $company,
        string $type,
        string $title,
        string $body,
        ?string $adminUrl = null,
        ?string $companyUrl = null,
        ?Lawyer $previousLawyer = null,
        ?array $data = null,
        bool $forceLawyerIndexUrl = false
    ): void {
        $actor = auth('admin')->user();

        foreach (SystemNotifier::admins() as $admin) {
            SystemNotifier::sendTo(
                recipient: $admin,
                type: $type,
                title: $title,
                body: $body,
                url: $adminUrl,
                actor: $actor,
                entity: $company,
                data: $data
            );
        }

        SystemNotifier::sendTo(
            recipient: $company,
            type: $type,
            title: $title,
            body: $body,
            url: $companyUrl,
            actor: $actor,
            entity: $company,
            data: $data
        );

        $lawyers = collect([$company->lawyer, $previousLawyer])
            ->filter()
            ->unique(fn ($lawyer) => get_class($lawyer) . ':' . $lawyer->getKey())
            ->values();

        foreach ($lawyers as $lawyer) {
            SystemNotifier::sendTo(
                recipient: $lawyer,
                type: $type,
                title: $title,
                body: $body,
                url: $forceLawyerIndexUrl ? $this->lawyerCompaniesIndexUrl() : $this->companyUrlForLawyer($company, $lawyer),
                actor: $actor,
                entity: $company,
                data: $data
            );
        }
    }

    private function buildCompanyUpdateBody(Company $company, array $changedFields, bool $isSuspendedAction = false): string
    {
        if ($isSuspendedAction) {
            return "تم إيقاف شركة {$company->company_name} داخل النظام.";
        }

        if (empty($changedFields)) {
            return "تم حفظ بيانات شركة {$company->company_name} بنجاح.";
        }

        return "تم تعديل بيانات شركة {$company->company_name}. الحقول المعدلة: " . $this->formatCompanyChangedFields($changedFields) . '.';
    }

    private function formatCompanyChangedFields(array $fields): string
    {
        $labels = [
            'lawyer_id' => 'المحامي المسؤول',
            'company_name' => 'اسم الشركة',
            'email' => 'البريد الإلكتروني',
            'phone' => 'رقم الهاتف',
            'tax_number' => 'الرقم الضريبي',
            'address' => 'العنوان',
            'status' => 'الحالة',
        ];

        return collect($fields)
            ->map(fn ($field) => $labels[$field] ?? $field)
            ->implode('، ');
    }

    private function routeOrNull(string $name, mixed $parameters = null): ?string
    {
        if (! Route::has($name)) {
            return null;
        }

        return $parameters === null
            ? route($name)
            : route($name, $parameters);
    }

    private function companyUrlForLawyer(Company $company, Lawyer $lawyer): ?string
    {
        if ((int) $company->lawyer_id !== (int) $lawyer->id) {
            return $this->lawyerCompaniesIndexUrl();
        }

        if (Route::has('lawyer.companies.show')) {
            return route('lawyer.companies.show', $company->id);
        }

        return $this->lawyerCompaniesIndexUrl();
    }

    private function lawyerCompaniesIndexUrl(): ?string
    {
        if (Route::has('lawyer.companies.index')) {
            return route('lawyer.companies.index');
        }

        if (Route::has('lawyer.dashboard')) {
            return route('lawyer.dashboard');
        }

        return null;
    }

    private function companyUrlForCompany(): ?string
    {
        if (Route::has('company.dashboard')) {
            return route('company.dashboard');
        }

        if (Route::has('company.profile.show')) {
            return route('company.profile.show');
        }

        return null;
    }
}
