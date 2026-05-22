<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Services\SystemNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;

class CompanyPositionController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | مهم:
        | هنا مش بنعمل filter بـ company_id
        | عشان الشركة تشوف كل الوظائف سواء تخص شركتها أو شركات أخرى
        |--------------------------------------------------------------------------
        */

        $query = Position::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                if (Schema::hasColumn('positions', 'name')) {
                    $q->where('name', 'like', "%{$search}%");
                }

                if (Schema::hasColumn('positions', 'name_ar')) {
                    $q->orWhere('name_ar', 'like', "%{$search}%");
                }

                if (Schema::hasColumn('positions', 'name_en')) {
                    $q->orWhere('name_en', 'like', "%{$search}%");
                }
            });
        }

        if (
            $request->filled('status')
            && $request->status !== 'all'
            && Schema::hasColumn('positions', 'status')
        ) {
            $query->where('status', $request->status);
        }

        match ($request->get('sort', 'id_asc')) {
            'latest' => $query->latest(),
            'oldest' => $query->oldest(),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            default => $query->orderBy('id', 'asc'),
        };

        $positions = $query->paginate(10)->withQueryString();

        $stats = $this->getStats();

        return view('company.positions.index', compact('positions', 'stats'));
    }

    public function create()
    {
        if (View::exists('company.positions.create')) {
            return view('company.positions.create');
        }

        return redirect()
            ->route('company.positions.index')
            ->with('toast_error', __('company_positions.messages.page_not_ready'));
    }

 public function store(Request $request)
 {
     $companyId = auth('company')->id();

     $validated = $request->validate(
         $this->validationRules(),
         $this->validationMessages()
     );

     $data = [
         'name' => $validated['name'],
     ];

     if (Schema::hasColumn('positions', 'status')) {
         $data['status'] = $validated['status'] ?? 'active';
     }

     /*
     |--------------------------------------------------------------------------
     | لو جدول positions فيه company_id
     | هنحفظ الشركة اللي أضافت الوظيفة
     | لكن في العرض هنفضل نعرض كل الوظائف لكل الشركات
     |--------------------------------------------------------------------------
     */
     if (Schema::hasColumn('positions', 'company_id')) {
         $data['company_id'] = $companyId;
     }

     if (Schema::hasColumn('positions', 'created_by')) {
         $data['created_by'] = $companyId;
     }

     $position = Position::create($data);

     SystemNotifier::notifyPositionChange(
         position: $position,
         type: 'position_created',
         title: 'تم إضافة وظيفة جديدة',
         body: "تم إضافة وظيفة {$position->name}.",
         actor: auth('company')->user(),
         company: auth('company')->user(),
         data: ['position_id' => $position->id, 'action' => 'created']
     );

     if ($request->input('action') === 'save_and_show' && Route::has('company.positions.show')) {
         return redirect()
             ->route('company.positions.show', $position->id)
             ->with('toast_success', __('company_positions.messages.created'));
     }

     return redirect()
         ->route('company.positions.index')
         ->with('toast_success', __('company_positions.messages.created'));
 }

    public function show(Position $position)
    {
        /*
        |--------------------------------------------------------------------------
        | مفيش منع بـ company_id
        | لأن المطلوب الشركة تشوف كل الوظائف
        |--------------------------------------------------------------------------
        */

        if (View::exists('company.positions.show')) {
            return view('company.positions.show', compact('position'));
        }

        return redirect()
            ->route('company.positions.index');
    }

    public function edit(Position $position)
    {
        if (View::exists('company.positions.edit')) {
            return view('company.positions.edit', compact('position'));
        }

        return redirect()
            ->route('company.positions.index')
            ->with('toast_error', __('company_positions.messages.page_not_ready'));
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate(
            $this->validationRules($position->id),
            $this->validationMessages()
        );

        $data = [
            'name' => $validated['name'],
        ];

        if (Schema::hasColumn('positions', 'status')) {
            $data['status'] = $validated['status'] ?? 'active';
        }

        $position->update($data);

        SystemNotifier::notifyPositionChange(
            position: $position,
            type: 'position_updated',
            title: 'تم تعديل وظيفة',
            body: "تم تعديل وظيفة {$position->name}.",
            actor: auth('company')->user(),
            company: auth('company')->user(),
            data: ['position_id' => $position->id, 'action' => 'updated']
        );

        if ($request->input('action') === 'save_and_show' && Route::has('company.positions.show')) {
            return redirect()
                ->route('company.positions.show', $position->id)
                ->with('toast_success', __('company_positions.messages.updated'));
        }

        return redirect()
            ->route('company.positions.index')
            ->with('toast_success', __('company_positions.messages.updated'));
    }

    public function destroy(Position $position)
    {
        $positionName = $position->name;

        SystemNotifier::notifyPositionChange(
            position: $position,
            type: 'position_deleted',
            title: 'تم حذف وظيفة',
            body: "تم حذف وظيفة {$positionName}.",
            actor: auth('company')->user(),
            company: auth('company')->user(),
            data: ['position_id' => $position->id, 'action' => 'deleted']
        );

        $position->delete();

        return redirect()
            ->route('company.positions.index')
            ->with('toast_success', __('company_positions.messages.deleted'));
    }

    private function getStats(): array
    {
        $baseQuery = Position::query();

        if (! Schema::hasColumn('positions', 'status')) {
            return [
                'total' => (clone $baseQuery)->count(),
                'active' => 0,
                'inactive' => 0,
            ];
        }

        return [
            'total' => (clone $baseQuery)->count(),

            'active' => (clone $baseQuery)
                ->where('status', 'active')
                ->count(),

            'inactive' => (clone $baseQuery)
                ->where('status', 'inactive')
                ->count(),
        ];
    }

    private function validationRules(?int $positionId = null): array
    {
        $uniqueRule = Rule::unique('positions', 'name');

        if ($positionId) {
            $uniqueRule->ignore($positionId);
        }

        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                $uniqueRule,
            ],
        ];

        if (Schema::hasColumn('positions', 'status')) {
            $rules['status'] = [
                'required',
                Rule::in(['active', 'inactive']),
            ];
        }

        return $rules;
    }

    private function validationMessages(): array
    {
        return [
            'name.required' => __('company_positions.validation.name_required'),
            'name.string' => __('company_positions.validation.name_invalid'),
            'name.max' => __('company_positions.validation.name_max'),
            'name.unique' => __('company_positions.validation.name_unique'),

            'status.required' => __('company_positions.validation.status_required'),
            'status.in' => __('company_positions.validation.status_invalid'),
        ];
    }
}
