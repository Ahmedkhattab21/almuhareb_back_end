<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Nationality;
use App\Models\Position;
use App\Models\PreferedLanguage;
use App\Models\Worker;
use App\Services\SystemNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class WorkerController extends Controller
{
    private const NPL_TABLE = 'nationalities_prefered_language';

    public function index(Request $request)
    {
        $query = Worker::query()
            ->with([
                'company',
                'position',
                'nationalityPreferredLanguage.nationality',
                'nationalityPreferredLanguage.preferedLanguage',
            ])
            ->withCount('tickets');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('iqama_number', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($companyQuery) use ($search) {
                        $companyQuery->where('company_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('position', function ($positionQuery) use ($search) {
                        $positionQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('nationalityPreferredLanguage.nationality', function ($nationalityQuery) use ($search) {
                        $nationalityQuery->where('nationality', 'like', "%{$search}%");
                    })
                    ->orWhereHas('nationalityPreferredLanguage.preferedLanguage', function ($languageQuery) use ($search) {
                        $languageQuery->where('prefered_language', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('company_id') && $request->company_id !== 'all') {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('position_id') && $request->position_id !== 'all') {
            $query->where('position_id', $request->position_id);
        }

        if ($request->filled('nationality_id') && $request->nationality_id !== 'all') {
            $query->whereHas('nationalityPreferredLanguage', function ($nationalityQuery) use ($request) {
                $nationalityQuery->where('nationality_id', $request->nationality_id);
            });
        }

        if ($request->filled('prefered_language_id') && $request->prefered_language_id !== 'all') {
            $query->whereHas('nationalityPreferredLanguage', function ($languageQuery) use ($request) {
                $languageQuery->where('prefered_language_id', $request->prefered_language_id);
            });
        }

        $sort = $request->get('sort', 'id_asc');

        match ($sort) {
            'latest' => $query->orderByDesc('id'),
            'oldest', 'id_asc' => $query->orderBy('id', 'asc'),
            'name_asc' => $query->orderBy('name', 'asc')->orderBy('id', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc')->orderBy('id', 'asc'),
            default => $query->orderBy('id', 'asc'),
        };

        $workers = $query->paginate(10)->withQueryString();

        $topLanguage = null;

        if (
            Schema::hasTable(self::NPL_TABLE) &&
            Schema::hasTable('prefered_languages')
        ) {
            $topLanguage = DB::table(self::NPL_TABLE . ' as npl')
                ->join('prefered_languages as pl', 'npl.prefered_language_id', '=', 'pl.id')
                ->selectRaw('pl.prefered_language, COUNT(*) as total')
                ->groupBy('pl.id', 'pl.prefered_language')
                ->orderByDesc('total')
                ->first();
        }

        $stats = [
            'total' => Worker::count(),
            'active' => Worker::where('status', 'active')->count(),
            'pending' => Worker::where('status', 'pending')->count(),

            'nationalities' => Schema::hasTable(self::NPL_TABLE)
                ? DB::table(self::NPL_TABLE)->distinct('nationality_id')->count('nationality_id')
                : 0,

            'top_language' => $topLanguage?->prefered_language ?? '-',
        ];

        $companies = Company::query()
            ->where('status', 'active')
            ->orderBy('company_name', 'asc')
            ->get(['id', 'company_name', 'status']);

        $nationalities = Nationality::query()
            ->where('status', 'active')
            ->orderBy('nationality', 'asc')
            ->get();

        $preferedLanguages = PreferedLanguage::query()
            ->where('status', 'active')
            ->orderBy('prefered_language', 'asc')
            ->get();

        $positions = Schema::hasTable('positions')
            ? Position::query()
                ->where('status', 'active')
                ->orderBy('name', 'asc')
                ->get()
            : collect();

        return view('admin.workers.index', compact(
            'workers',
            'stats',
            'companies',
            'nationalities',
            'preferedLanguages',
            'positions'
        ));
    }

    public function create()
    {
        $companies = Company::query()
            ->where('status', 'active')
            ->orderBy('company_name', 'asc')
            ->get(['id', 'company_name', 'status']);

        $nationalities = Nationality::query()
            ->where('status', 'active')
            ->orderBy('nationality', 'asc')
            ->get();

        $preferedLanguages = PreferedLanguage::query()
            ->where('status', 'active')
            ->orderBy('prefered_language', 'asc')
            ->get();

        $positions = Schema::hasTable('positions')
            ? Position::query()
                ->where('status', 'active')
                ->orderBy('name', 'asc')
                ->get()
            : collect();

        return view('admin.workers.create', compact(
            'companies',
            'nationalities',
            'preferedLanguages',
            'positions'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => [
                'required',
                Rule::exists('companies', 'id')->where(function ($query) {
                    return $query->where('status', 'active');
                }),
            ],

            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:workers,email'],
            'phone' => ['required', 'string', 'max:50', 'unique:workers,phone'],
            'iqama_number' => ['nullable', 'string', 'max:100', 'unique:workers,iqama_number'],

            'position_id' => [
                'nullable',
                Rule::exists('positions', 'id')->where(function ($query) {
                    return $query->where('status', 'active');
                }),
            ],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', 'in:active,pending,suspended'],

            'nationality_id' => [
                'required',
                Rule::exists('nationalities', 'id')->where(function ($query) {
                    return $query->where('status', 'active');
                }),
            ],

            'prefered_language_id' => [
                'required',
                Rule::exists('prefered_languages', 'id')->where(function ($query) {
                    return $query->where('status', 'active');
                }),
            ],
        ]);

        try {
            DB::beginTransaction();

            $nationalityId = $data['nationality_id'];
            $preferedLanguageId = $data['prefered_language_id'];

            unset($data['nationality_id'], $data['prefered_language_id']);
            $data['preferred_language'] = PreferedLanguage::whereKey($preferedLanguageId)->value('code');

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('workers', 'public');
            }

            $data['password'] = Hash::make(Str::random(32));

            /*
             * created_by عندك مربوط بجدول companies
             * لذلك بنحفظ فيه company_id.
             */
            $data['created_by'] = $data['company_id'];

            $worker = Worker::create($data);

            DB::table(self::NPL_TABLE)->insert([
                'worker_id' => $worker->id,
                'nationality_id' => $nationalityId,
                'prefered_language_id' => $preferedLanguageId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            $worker->refresh()->load('company.lawyer');

            SystemNotifier::notifyWorkerChange(
                worker: $worker,
                type: 'worker_created',
                title: 'تم إضافة عامل جديد',
                body: "تم إضافة العامل {$worker->name} إلى النظام.",
                actor: auth('admin')->user(),
                data: ['worker_id' => $worker->id, 'action' => 'created']
            );

            if ($request->input('action') === 'save_and_add_another') {
                return redirect()
                    ->route('admin.workers.create')
                    ->with('toast_success', __('workers.messages.created'));
            }

            return redirect()
                ->route('admin.workers.index')
                ->with('toast_success', __('workers.messages.created'));

        } catch (Throwable $e) {
            DB::rollBack();

            report($e);

            return back()
                ->withInput()
                ->with('toast_error', __('workers.messages.create_failed'));
        }
    }

    public function show(Worker $worker)
    {
        $worker->load([
            'company',
            'position',
            'nationalityPreferredLanguage.nationality',
            'nationalityPreferredLanguage.preferedLanguage',
        ]);

        $worker->loadCount('tickets');

        return view('admin.workers.show', compact('worker'));
    }

    public function edit(Worker $worker)
    {
        $worker->load([
            'company',
            'position',
            'nationalityPreferredLanguage.nationality',
            'nationalityPreferredLanguage.preferedLanguage',
        ]);

        $companies = Company::query()
            ->where('status', 'active')
            ->orderBy('company_name', 'asc')
            ->get(['id', 'company_name', 'status']);

        $nationalities = Nationality::query()
            ->where('status', 'active')
            ->orderBy('nationality', 'asc')
            ->get();

        $preferedLanguages = PreferedLanguage::query()
            ->where('status', 'active')
            ->orderBy('prefered_language', 'asc')
            ->get();

        $positions = Schema::hasTable('positions')
            ? Position::query()
                ->where('status', 'active')
                ->orderBy('name', 'asc')
                ->get()
            : collect();

        $selectedNationalityId = $worker->nationalityPreferredLanguage?->nationality_id;
        $selectedPreferedLanguageId = $worker->nationalityPreferredLanguage?->prefered_language_id;

        return view('admin.workers.edit', compact(
            'worker',
            'companies',
            'nationalities',
            'preferedLanguages',
            'positions',
            'selectedNationalityId',
            'selectedPreferedLanguageId'
        ));
    }

    public function update(Request $request, Worker $worker)
    {
        $data = $request->validate([
            'company_id' => [
                'required',
                Rule::exists('companies', 'id')->where(function ($query) {
                    return $query->where('status', 'active');
                }),
            ],

            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('workers', 'email')->ignore($worker->id),
            ],

            'phone' => [
                'required',
                'string',
                'max:50',
                Rule::unique('workers', 'phone')->ignore($worker->id),
            ],

            'iqama_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('workers', 'iqama_number')->ignore($worker->id),
            ],

            'position_id' => [
                'nullable',
                Rule::exists('positions', 'id')->where(function ($query) {
                    return $query->where('status', 'active');
                }),
            ],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', 'in:active,pending,suspended'],

            'nationality_id' => [
                'required',
                Rule::exists('nationalities', 'id')->where(function ($query) {
                    return $query->where('status', 'active');
                }),
            ],

            'prefered_language_id' => [
                'required',
                Rule::exists('prefered_languages', 'id')->where(function ($query) {
                    return $query->where('status', 'active');
                }),
            ],
        ]);

        try {
            DB::beginTransaction();

            $nationalityId = $data['nationality_id'];
            $preferedLanguageId = $data['prefered_language_id'];

            unset($data['nationality_id'], $data['prefered_language_id']);
            $data['preferred_language'] = PreferedLanguage::whereKey($preferedLanguageId)->value('code');

            if ($request->hasFile('image')) {
                if ($worker->image) {
                    Storage::disk('public')->delete($worker->image);
                }

                $data['image'] = $request->file('image')->store('workers', 'public');
            }

            $worker->update($data);

            $oldLink = DB::table(self::NPL_TABLE)
                ->where('worker_id', $worker->id)
                ->first();

            if ($oldLink) {
                DB::table(self::NPL_TABLE)
                    ->where('worker_id', $worker->id)
                    ->update([
                        'nationality_id' => $nationalityId,
                        'prefered_language_id' => $preferedLanguageId,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table(self::NPL_TABLE)->insert([
                    'worker_id' => $worker->id,
                    'nationality_id' => $nationalityId,
                    'prefered_language_id' => $preferedLanguageId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            $worker->refresh()->load('company.lawyer');

            SystemNotifier::notifyWorkerChange(
                worker: $worker,
                type: 'worker_updated',
                title: 'تم تعديل بيانات عامل',
                body: "تم تعديل بيانات العامل {$worker->name}.",
                actor: auth('admin')->user(),
                data: ['worker_id' => $worker->id, 'action' => 'updated']
            );

            return redirect()
                ->route('admin.workers.index')
                ->with('toast_success', __('workers.messages.updated'));

        } catch (Throwable $e) {
            DB::rollBack();

            report($e);

            return back()
                ->withInput()
                ->with('toast_error', __('workers.messages.update_failed'));
        }
    }

    public function destroy(Worker $worker)
    {
        try {
            DB::beginTransaction();

            $worker->load('company.lawyer');
            $workerName = $worker->name;
            $workerId = $worker->id;

            SystemNotifier::notifyWorkerChange(
                worker: $worker,
                type: 'worker_deleted',
                title: 'تم حذف عامل',
                body: "تم حذف العامل {$workerName} من النظام.",
                actor: auth('admin')->user(),
                data: ['worker_id' => $workerId, 'action' => 'deleted']
            );

            if ($worker->image) {
                Storage::disk('public')->delete($worker->image);
            }

            DB::table(self::NPL_TABLE)
                ->where('worker_id', $worker->id)
                ->delete();

            $worker->delete();

            DB::commit();

            return redirect()
                ->route('admin.workers.index')
                ->with('toast_success', __('workers.messages.deleted'));

        } catch (Throwable $e) {
            DB::rollBack();

            report($e);

            return back()
                ->with('toast_error', __('workers.messages.delete_failed'));
        }
    }
}
