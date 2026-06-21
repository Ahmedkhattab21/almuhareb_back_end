<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Position;
use App\Models\Worker;
use App\Services\SystemNotifier;
use App\Services\WorkerBulkImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CompanyWorkerController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth('company')->id();

        $columns = $this->detectWorkerColumns();

        $query = Worker::query()
            ->when(! empty($this->workerRelations()), function ($query) {
                $query->with($this->workerRelations());
            })
            ->withCount('tickets')
            ->where('company_id', $companyId);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search, $columns) {
                if (Schema::hasColumn('workers', 'name')) {
                    $q->where('name', 'like', "%{$search}%");
                }

                if (Schema::hasColumn('workers', 'email')) {
                    $q->orWhere('email', 'like', "%{$search}%");
                }

                if (Schema::hasColumn('workers', 'phone')) {
                    $q->orWhere('phone', 'like', "%{$search}%");
                }

                if ($columns['iqama']) {
                    $q->orWhere($columns['iqama'], 'like', "%{$search}%");
                }

                if (($columns['operating_company'] ?? null) === 'operating_company') {
                    $q->orWhere('operating_company', 'like', "%{$search}%");
                }

                if ($columns['position'] && ! str_ends_with($columns['position'], '_id')) {
                    $q->orWhere($columns['position'], 'like', "%{$search}%");
                }

                if ($columns['nationality'] && ! str_ends_with($columns['nationality'], '_id')) {
                    $q->orWhere($columns['nationality'], 'like', "%{$search}%");
                }

                if ($columns['language'] && ! str_ends_with($columns['language'], '_id')) {
                    $q->orWhere($columns['language'], 'like', "%{$search}%");
                }

                if (method_exists(Worker::class, 'position')) {
                    $q->orWhereHas('position', function ($positionQuery) use ($search) {
                        $positionQuery->where('name', 'like', "%{$search}%");
                    });
                }
            });
        }

        if ($request->filled('status') && $request->status !== 'all' && Schema::hasColumn('workers', 'status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('nationality') && $request->nationality !== 'all') {
            $this->applyNationalityFilter($query, $request->nationality, $columns);
        }

        if ($request->filled('language') && $request->language !== 'all') {
            $this->applyLanguageFilter($query, $request->language, $columns);
        }

        match ($request->get('sort', 'id_asc')) {
            'latest' => $query->latest(),
            'oldest' => $query->oldest(),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            default => $query->orderBy('id', 'asc'),
        };

        $workers = $query->paginate(10)->withQueryString();

        $workers->getCollection()->transform(function ($worker) use ($columns) {
            return $this->normalizeWorkerForView($worker, $columns);
        });

        $stats = $this->getStats($companyId);

        $nationalities = $this->getNationalityFilterOptions();
        $languages = $this->getLanguageFilterOptions();

        return view('company.workers.index', compact(
            'workers',
            'stats',
            'nationalities',
            'languages'
        ));
    }

    public function create()
    {
        $positions = Position::query()
            ->when(Schema::hasColumn('positions', 'status'), function ($query) {
                $query->where('status', 'active');
            })
            ->orderBy('name')
            ->get();

        $nationalities = class_exists(\App\Models\Nationality::class)
            ? \App\Models\Nationality::query()->orderBy('nationality')->get()
            : collect();

        $preferedLanguages = class_exists(\App\Models\PreferedLanguage::class)
            ? \App\Models\PreferedLanguage::query()->orderBy('prefered_language')->get()
            : collect();

        $cities = City::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('company.workers.create', compact(
            'positions',
            'nationalities',
            'preferedLanguages',
            'cities'
        ));
    }

    public function importForm()
    {
        $positions = Position::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $nationalities = class_exists(\App\Models\Nationality::class)
            ? \App\Models\Nationality::query()
                ->where('status', 'active')
                ->orderBy('nationality')
                ->get(['id', 'nationality'])
            : collect();

        $preferedLanguages = class_exists(\App\Models\PreferedLanguage::class)
            ? \App\Models\PreferedLanguage::query()
                ->where('status', 'active')
                ->orderBy('id')
                ->get(['id', 'prefered_language', 'code'])
            : collect();

        $cities = City::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('company.workers.import', compact('positions', 'nationalities', 'preferedLanguages', 'cities'));
    }

    public function import(Request $request, WorkerBulkImportService $importer)
    {
        $data = $request->validate([
            'position_id' => ['nullable', 'integer', Rule::exists('positions', 'id')],
            'city_id' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')->where(fn ($query) => $query->where('status', 'active')),
            ],
            'nationality_id' => [
                'required',
                'integer',
                Rule::exists('nationalities', 'id')->where(fn ($query) => $query->where('status', 'active')),
            ],
            'preferred_language_id' => [
                'required',
                'integer',
                Rule::exists('prefered_languages', 'id')->where(fn ($query) => $query->where('status', 'active')),
            ],
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx', 'max:20480'],
        ]);

        $result = $importer->import($request->file('file'), auth('company')->user(), [
            'position_id' => $data['position_id'] ?? null,
            'city_id' => $data['city_id'],
            'nationality_id' => $data['nationality_id'],
            'preferred_language_id' => $data['preferred_language_id'],
        ], auth('company')->user());

        return redirect()
            ->route('company.workers.import')
            ->with('toast_success', "تم استيراد {$result['created']} عامل، وتخطي {$result['skipped']} صف.")
            ->with('import_result', $result);
    }

    public function importTemplate(WorkerBulkImportService $importer)
    {
        return $importer->templateResponse('company-workers-import-template.csv');
    }

    public function store(Request $request)
    {
        $companyId = auth('company')->id();

        $columns = $this->detectWorkerColumns();

        $request->validate(
            $this->validationRules($columns),
            $this->validationMessages()
        );

        $data = $this->prepareWorkerData($request, $columns);

        $data['company_id'] = $companyId;

        if (Schema::hasColumn('workers', 'created_by')) {
            $data['created_by'] = $companyId;
        }

        $worker = Worker::create($data);

        $this->syncWorkerNationalityLanguage($worker, $request);
        $worker->refresh()->load('company.lawyer');

        SystemNotifier::notifyWorkerChange(
            worker: $worker,
            type: 'worker_created',
            title: 'تم إضافة عامل جديد',
            body: "تم إضافة العامل {$worker->name}.",
            actor: auth('company')->user(),
            data: ['worker_id' => $worker->id, 'action' => 'created']
        );

        if ($request->input('action') === 'save_and_add_another') {
            return redirect()
                ->route('company.workers.create')
                ->with('toast_success', __('company_workers.messages.created'));
        }

        return redirect()
            ->route('company.workers.index')
            ->with('toast_success', __('company_workers.messages.created'));
    }

    public function show(Worker $worker)
    {
        $this->authorizeCompanyWorker($worker);

        $columns = $this->detectWorkerColumns();

        $worker->loadMissing($this->workerRelations());
        $worker->loadCount('tickets');

        $worker = $this->normalizeWorkerForView($worker, $columns);

        if (View::exists('company.workers.show')) {
            return view('company.workers.show', compact('worker'));
        }

        return redirect()
            ->route('company.workers.index');
    }

    public function edit(Worker $worker)
    {
        $this->authorizeCompanyWorker($worker);

        $worker->loadMissing($this->workerRelations());

        $positions = Position::query()
            ->when(Schema::hasColumn('positions', 'status'), function ($query) {
                $query->where('status', 'active');
            })
            ->orderBy('name')
            ->get();

        $nationalities = class_exists(\App\Models\Nationality::class)
            ? \App\Models\Nationality::query()->orderBy('nationality')->get()
            : collect();

        $preferedLanguages = class_exists(\App\Models\PreferedLanguage::class)
            ? \App\Models\PreferedLanguage::query()->orderBy('prefered_language')->get()
            : collect();

        $cities = City::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('company.workers.edit', compact(
            'worker',
            'positions',
            'nationalities',
            'preferedLanguages',
            'cities'
        ));
    }

    public function update(Request $request, Worker $worker)
    {
        $this->authorizeCompanyWorker($worker);

        $columns = $this->detectWorkerColumns();

        $request->validate(
            $this->validationRules($columns, $worker->id),
            $this->validationMessages()
        );

        $data = $this->prepareWorkerData($request, $columns, $worker);

        $worker->update($data);

        $this->syncWorkerNationalityLanguage($worker, $request);
        $worker->refresh()->load('company.lawyer');

        SystemNotifier::notifyWorkerChange(
            worker: $worker,
            type: 'worker_updated',
            title: 'تم تعديل بيانات عامل',
            body: "تم تعديل بيانات العامل {$worker->name}.",
            actor: auth('company')->user(),
            data: ['worker_id' => $worker->id, 'action' => 'updated']
        );

        return redirect()
            ->route('company.workers.index')
            ->with('toast_success', __('company_workers.messages.updated'));
    }

    public function destroy(Worker $worker)
    {
        $this->authorizeCompanyWorker($worker);

        $worker->load('company.lawyer');
        $workerName = $worker->name;
        $workerId = $worker->id;

        SystemNotifier::notifyWorkerChange(
            worker: $worker,
            type: 'worker_deleted',
            title: 'تم حذف عامل',
            body: "تم حذف العامل {$workerName}.",
            actor: auth('company')->user(),
            data: ['worker_id' => $workerId, 'action' => 'deleted']
        );

        $worker->delete();

        return redirect()
            ->route('company.workers.index')
            ->with('toast_success', __('company_workers.messages.deleted'));
    }

    private function authorizeCompanyWorker(Worker $worker): void
    {
        abort_if(
            (int) $worker->company_id !== (int) auth('company')->id(),
            403
        );
    }

    private function workerRelations(): array
    {
        $relations = [];

        if (method_exists(Worker::class, 'position')) {
            $relations[] = 'position';
        }

        if (method_exists(Worker::class, 'city')) {
            $relations[] = 'city';
        }

        if (method_exists(Worker::class, 'company')) {
            $relations[] = 'company';
        }

        if (method_exists(Worker::class, 'nationalityPreferredLanguage')) {
            $relations[] = 'nationalityPreferredLanguage';
        }

        if (method_exists(Worker::class, 'nationality')) {
            $relations[] = 'nationality';
        }

        if (method_exists(Worker::class, 'preferredLanguage')) {
            $relations[] = 'preferredLanguage';
        }

        if (method_exists(Worker::class, 'preferedLanguage')) {
            $relations[] = 'preferedLanguage';
        }

        return $relations;
    }

    private function detectWorkerColumns(): array
    {
        return [
            'iqama' => $this->getExistingColumn('workers', [
                'iqama_number',
                'residency_number',
                'national_id',
            ]),

            'position' => $this->getExistingColumn('workers', [
                'position_id',
                'position',
                'job_title',
            ]),

            'city' => $this->getExistingColumn('workers', [
                'city_id',
            ]),

            'operating_company' => $this->getExistingColumn('workers', [
                'operating_company',
            ]),

            'nationality_relation' => $this->getExistingColumn('workers', [
                'nationality_preferred_language_id',
                'nationality_prefered_language_id',
            ]),

            'nationality' => $this->getExistingColumn('workers', [
                'nationality_id',
                'nationality',
            ]),

            'language' => $this->getExistingColumn('workers', [
                'prefered_language_id',
                'preferred_language_id',
                'language_id',
                'prefered_language',
                'preferred_language',
                'language',
            ]),

            'open_tickets' => $this->getExistingColumn('workers', [
                'open_tickets_count',
                'tickets_count',
            ]),

            'image' => $this->getExistingColumn('workers', [
                'image',
                'avatar',
            ]),
        ];
    }

    private function getExistingColumn(string $table, array $columns): ?string
    {
        if (! Schema::hasTable($table)) {
            return null;
        }

        foreach ($columns as $column) {
            if (Schema::hasColumn($table, $column)) {
                return $column;
            }
        }

        return null;
    }

    private function getExistingTable(array $tables): ?string
    {
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                return $table;
            }
        }

        return null;
    }

    private function normalizeWorkerForView(Worker $worker, array $columns): Worker
    {
        if (! Schema::hasColumn('workers', 'iqama_number') && $columns['iqama']) {
            $worker->setAttribute('iqama_number', $worker->{$columns['iqama']});
        }

        $positionLabel = data_get($worker, 'position.name')
            ?? data_get($worker, 'position.name_ar')
            ?? data_get($worker, 'position.name_en');

        if (! $positionLabel && $columns['position']) {
            if ($columns['position'] === 'position_id') {
                $positionLabel = $this->getPositionLabel($worker->{$columns['position']});
            } else {
                $positionLabel = $worker->{$columns['position']};
            }
        }

        $worker->setAttribute('position_label', $positionLabel ?: '-');

        $nationalityLabel = data_get($worker, 'nationalityPreferredLanguage.nationality.nationality')
            ?? data_get($worker, 'nationalityPreferredLanguage.nationality.name')
            ?? data_get($worker, 'nationality.nationality')
            ?? data_get($worker, 'nationality.name');

        if (! $nationalityLabel && $columns['nationality']) {
            if ($columns['nationality'] === 'nationality_id') {
                $nationalityLabel = $this->getNationalityLabel($worker->{$columns['nationality']});
            } else {
                $nationalityLabel = $worker->{$columns['nationality']};
            }
        }

        if (! $nationalityLabel && $columns['nationality_relation']) {
            $nationalityLabel = $this->getNationalityLabelFromRelation($worker->{$columns['nationality_relation']});
        }

        $worker->setAttribute('nationality_label', $nationalityLabel ?: '-');

        $languageLabel = data_get($worker, 'nationalityPreferredLanguage.preferedLanguage.prefered_language')
            ?? data_get($worker, 'nationalityPreferredLanguage.preferedLanguage.name')
            ?? data_get($worker, 'nationalityPreferredLanguage.preferredLanguage.preferred_language')
            ?? data_get($worker, 'nationalityPreferredLanguage.preferredLanguage.name')
            ?? data_get($worker, 'preferredLanguage.preferred_language')
            ?? data_get($worker, 'preferredLanguage.name')
            ?? data_get($worker, 'preferedLanguage.prefered_language')
            ?? data_get($worker, 'preferedLanguage.name');

        if (! $languageLabel && $columns['language']) {
            if (str_ends_with($columns['language'], '_id')) {
                $languageLabel = $this->getLanguageLabel($worker->{$columns['language']});
            } else {
                $languageLabel = $worker->{$columns['language']};
            }
        }

        if (! $languageLabel && $columns['nationality_relation']) {
            $languageLabel = $this->getLanguageLabelFromRelation($worker->{$columns['nationality_relation']});
        }

        $worker->setAttribute('preferred_language_label', $languageLabel ?: '-');

        if (! Schema::hasColumn('workers', 'open_tickets_count') && $columns['open_tickets']) {
            $worker->setAttribute('open_tickets_count', $worker->{$columns['open_tickets']});
        }

        return $worker;
    }

    private function getStats(int $companyId): array
    {
        $baseQuery = Worker::where('company_id', $companyId);

        if (! Schema::hasColumn('workers', 'status')) {
            return [
                'total' => (clone $baseQuery)->count(),
                'active' => 0,
                'pending' => 0,
                'suspended' => 0,
            ];
        }

        return [
            'total' => (clone $baseQuery)->count(),

            'active' => (clone $baseQuery)
                ->where('status', 'active')
                ->count(),

            'pending' => (clone $baseQuery)
                ->where('status', 'pending')
                ->count(),

            'suspended' => (clone $baseQuery)
                ->where('status', 'suspended')
                ->count(),
        ];
    }

    private function prepareWorkerData(Request $request, array $columns, ?Worker $worker = null): array
    {
        $data = [];

        if (Schema::hasColumn('workers', 'name')) {
            $data['name'] = $request->input('name');
        }

        if (Schema::hasColumn('workers', 'email')) {
            $data['email'] = $request->input('email');
        }

        if (Schema::hasColumn('workers', 'phone')) {
            $data['phone'] = $request->input('phone');
        }

        if ($columns['iqama']) {
            $data[$columns['iqama']] = $request->input('iqama_number');
        }

        if ($columns['position']) {
            if ($columns['position'] === 'position_id') {
                $data['position_id'] = $request->input('position_id');
            } else {
                $data[$columns['position']] = $this->getPositionLabel($request->input('position_id'));
            }
        }

        if (($columns['city'] ?? null) === 'city_id') {
            $data['city_id'] = $request->input('city_id');
        }

        if (($columns['operating_company'] ?? null) === 'operating_company') {
            $data['operating_company'] = $request->input('operating_company');
        }

        $nationalityId = $request->input('nationality_id');
        $languageId = $request->input('prefered_language_id');

        if ($columns['nationality_relation']) {
            $relationId = $this->getNationalityPreferredLanguageId($nationalityId, $languageId);

            if ($relationId) {
                $data[$columns['nationality_relation']] = $relationId;
            }
        }

        if ($columns['nationality']) {
            if ($columns['nationality'] === 'nationality_id') {
                $data['nationality_id'] = $nationalityId;
            } else {
                $data[$columns['nationality']] = $this->getNationalityLabel($nationalityId);
            }
        }

        if ($columns['language']) {
            if (in_array($columns['language'], ['prefered_language_id', 'preferred_language_id', 'language_id'])) {
                $data[$columns['language']] = $languageId;
            } elseif ($columns['language'] === 'preferred_language') {
                $data[$columns['language']] = $this->getLanguageCode($languageId);
            } else {
                $data[$columns['language']] = $this->getLanguageLabel($languageId);
            }
        }

        if (Schema::hasColumn('workers', 'status')) {
            $data['status'] = $request->input('status', 'active');
        }

        if ($columns['open_tickets']) {
            $data[$columns['open_tickets']] = $request->input('open_tickets_count', 0);
        }

        if (Schema::hasColumn('workers', 'password') && ! $worker && $request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        if (Schema::hasColumn('workers', 'password') && ! $worker && empty($data['password'])) {
            $data['password'] = Hash::make(Str::random(32));
        }

        if ($columns['image'] && $request->hasFile('image')) {
            $data[$columns['image']] = $request->file('image')->store('workers', 'public');
        }

        return $data;
    }

    private function getNationalityPreferredLanguageId($nationalityId, $languageId): ?int
    {
        if (! $nationalityId || ! $languageId) {
            return null;
        }

        $table = $this->getExistingTable([
            'nationality_prefered_languages',
            'nationality_preferred_languages',
            'nationality_prefered_language',
            'nationality_preferred_language',
        ]);

        if (! $table || ! Schema::hasColumn($table, 'nationality_id')) {
            return null;
        }

        $languageColumn = $this->getExistingColumn($table, [
            'prefered_language_id',
            'preferred_language_id',
            'language_id',
        ]);

        if (! $languageColumn) {
            return null;
        }

        $relationId = DB::table($table)
            ->where('nationality_id', $nationalityId)
            ->where($languageColumn, $languageId)
            ->value('id');

        if ($relationId) {
            return (int) $relationId;
        }

        $insertData = [
            'nationality_id' => $nationalityId,
            $languageColumn => $languageId,
        ];

        if (Schema::hasColumn($table, 'created_at')) {
            $insertData['created_at'] = now();
        }

        if (Schema::hasColumn($table, 'updated_at')) {
            $insertData['updated_at'] = now();
        }

        return (int) DB::table($table)->insertGetId($insertData);
    }

    private function getPositionLabel($positionId): ?string
    {
        if (! $positionId || ! Schema::hasTable('positions')) {
            return null;
        }

        $column = $this->getExistingColumn('positions', [
            'name',
            'name_ar',
            'name_en',
            'title',
        ]);

        if (! $column) {
            return null;
        }

        return DB::table('positions')
            ->where('id', $positionId)
            ->value($column);
    }

    private function getNationalityLabel($nationalityId): ?string
    {
        if (! $nationalityId || ! Schema::hasTable('nationalities')) {
            return null;
        }

        $column = $this->getExistingColumn('nationalities', [
            'nationality',
            'name',
            'name_ar',
            'name_en',
        ]);

        if (! $column) {
            return null;
        }

        return DB::table('nationalities')
            ->where('id', $nationalityId)
            ->value($column);
    }

    private function getLanguageLabel($languageId): ?string
    {
        if (! $languageId) {
            return null;
        }

        $table = $this->getExistingTable([
            'prefered_languages',
            'preferred_languages',
            'languages',
        ]);

        if (! $table) {
            return null;
        }

        $column = $this->getExistingColumn($table, [
            'prefered_language',
            'preferred_language',
            'name',
            'name_ar',
            'name_en',
        ]);

        if (! $column) {
            return null;
        }

        return DB::table($table)
            ->where('id', $languageId)
            ->value($column);
    }

    private function getLanguageCode($languageId): ?string
    {
        if (! $languageId) {
            return null;
        }

        $table = $this->getExistingTable([
            'prefered_languages',
            'preferred_languages',
            'languages',
        ]);

        if (! $table || ! Schema::hasColumn($table, 'code')) {
            return null;
        }

        return DB::table($table)
            ->where('id', $languageId)
            ->value('code');
    }

    private function getNationalityLabelFromRelation($relationId): ?string
    {
        if (! $relationId) {
            return null;
        }

        $table = $this->getExistingTable([
            'nationality_prefered_languages',
            'nationality_preferred_languages',
            'nationality_prefered_language',
            'nationality_preferred_language',
        ]);

        if (! $table || ! Schema::hasColumn($table, 'nationality_id')) {
            return null;
        }

        $nationalityId = DB::table($table)
            ->where('id', $relationId)
            ->value('nationality_id');

        return $this->getNationalityLabel($nationalityId);
    }

    private function getLanguageLabelFromRelation($relationId): ?string
    {
        if (! $relationId) {
            return null;
        }

        $table = $this->getExistingTable([
            'nationality_prefered_languages',
            'nationality_preferred_languages',
            'nationality_prefered_language',
            'nationality_preferred_language',
        ]);

        if (! $table) {
            return null;
        }

        $languageColumn = $this->getExistingColumn($table, [
            'prefered_language_id',
            'preferred_language_id',
            'language_id',
        ]);

        if (! $languageColumn) {
            return null;
        }

        $languageId = DB::table($table)
            ->where('id', $relationId)
            ->value($languageColumn);

        return $this->getLanguageLabel($languageId);
    }

    private function getNationalityFilterOptions()
    {
        if (! Schema::hasTable('nationalities')) {
            return collect();
        }

        $labelColumn = $this->getExistingColumn('nationalities', [
            'nationality',
            'name',
            'name_ar',
            'name_en',
        ]);

        if (! $labelColumn) {
            return collect();
        }

        return DB::table('nationalities')
            ->select('id', $labelColumn.' as label')
            ->whereNotNull($labelColumn)
            ->where($labelColumn, '!=', '')
            ->orderBy($labelColumn)
            ->get()
            ->map(function ($item) {
                return [
                    'value' => (string) $item->id,
                    'label' => $item->label,
                ];
            });
    }

    private function getLanguageFilterOptions()
    {
        $table = $this->getExistingTable([
            'prefered_languages',
            'preferred_languages',
            'languages',
        ]);

        if (! $table) {
            return collect();
        }

        $labelColumn = $this->getExistingColumn($table, [
            'prefered_language',
            'preferred_language',
            'name',
            'name_ar',
            'name_en',
        ]);

        if (! $labelColumn) {
            return collect();
        }

        return DB::table($table)
            ->select('id', $labelColumn.' as label')
            ->whereNotNull($labelColumn)
            ->where($labelColumn, '!=', '')
            ->orderBy($labelColumn)
            ->get()
            ->map(function ($item) {
                return [
                    'value' => (string) $item->id,
                    'label' => $item->label,
                ];
            });
    }

    private function applyNationalityFilter($query, $nationalityId, array $columns): void
    {
        if (! $nationalityId) {
            return;
        }

        if (($columns['nationality'] ?? null) === 'nationality_id') {
            $query->where('nationality_id', $nationalityId);

            return;
        }

        if (! empty($columns['nationality_relation'])) {
            $table = $this->getExistingTable([
                'nationality_prefered_languages',
                'nationality_preferred_languages',
                'nationality_prefered_language',
                'nationality_preferred_language',
            ]);

            if ($table && Schema::hasColumn($table, 'nationality_id')) {
                $relationIds = DB::table($table)
                    ->where('nationality_id', $nationalityId)
                    ->pluck('id');

                $query->whereIn($columns['nationality_relation'], $relationIds);

                return;
            }
        }

        if (! empty($columns['nationality'])) {
            $label = $this->getNationalityLabel($nationalityId);

            $query->where($columns['nationality'], $label ?: $nationalityId);
        }
    }

    private function applyLanguageFilter($query, $languageId, array $columns): void
    {
        if (! $languageId) {
            return;
        }

        if (! empty($columns['language']) && str_ends_with($columns['language'], '_id')) {
            $query->where($columns['language'], $languageId);

            return;
        }

        if (! empty($columns['nationality_relation'])) {
            $table = $this->getExistingTable([
                'nationality_prefered_languages',
                'nationality_preferred_languages',
                'nationality_prefered_language',
                'nationality_preferred_language',
            ]);

            if ($table) {
                $languageColumn = $this->getExistingColumn($table, [
                    'prefered_language_id',
                    'preferred_language_id',
                    'language_id',
                ]);

                if ($languageColumn) {
                    $relationIds = DB::table($table)
                        ->where($languageColumn, $languageId)
                        ->pluck('id');

                    $query->whereIn($columns['nationality_relation'], $relationIds);

                    return;
                }
            }
        }

        if (! empty($columns['language'])) {
            $label = $this->getLanguageLabel($languageId);

            $query->where($columns['language'], $label ?: $languageId);
        }
    }

    private function validationRules(array $columns, ?int $workerId = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
        ];

        if (Schema::hasColumn('workers', 'email')) {
            $rules['email'] = ['nullable', 'email', 'max:255'];
        }

        if (Schema::hasColumn('workers', 'email')) {
            $uniqueEmailRule = Rule::unique('workers', 'email');

            if ($workerId) {
                $uniqueEmailRule->ignore($workerId);
            }

            $rules['email'] = [
                'nullable',
                'email',
                'max:255',
                $uniqueEmailRule,
            ];
        }

        if ($columns['iqama']) {
            $uniqueRule = Rule::unique('workers', $columns['iqama']);

            if ($workerId) {
                $uniqueRule->ignore($workerId);
            }

            $rules['iqama_number'] = [
                'nullable',
                'string',
                'max:50',
                $uniqueRule,
            ];
        }

        if ($columns['position']) {
            if ($columns['position'] === 'position_id') {
                $rules['position_id'] = ['nullable', 'integer'];

                if (Schema::hasTable('positions')) {
                    $rules['position_id'][] = Rule::exists('positions', 'id');
                }
            } else {
                $rules['position'] = ['nullable', 'string', 'max:255'];
            }
        }

        if (($columns['city'] ?? null) === 'city_id') {
            $rules['city_id'] = ['required', 'integer'];

            if (Schema::hasTable('cities')) {
                $rules['city_id'][] = Rule::exists('cities', 'id')->where(fn ($query) => $query->where('status', 'active'));
            }
        }

        if (($columns['operating_company'] ?? null) === 'operating_company') {
            $rules['operating_company'] = ['nullable', 'string', 'max:255'];
        }

        if ($columns['nationality_relation'] || $columns['nationality']) {
            $rules['nationality_id'] = ['nullable', 'integer'];

            if (Schema::hasTable('nationalities')) {
                $rules['nationality_id'][] = Rule::exists('nationalities', 'id');
            }
        }

        if ($columns['language'] || $columns['nationality_relation']) {
            $rules['prefered_language_id'] = ['nullable', 'integer'];

            $languageTable = $this->getExistingTable([
                'prefered_languages',
                'preferred_languages',
                'languages',
            ]);

            if ($languageTable) {
                $rules['prefered_language_id'][] = Rule::exists($languageTable, 'id');
            }
        }

        if (Schema::hasColumn('workers', 'status')) {
            $rules['status'] = [
                'required',
                Rule::in(['active', 'pending', 'suspended']),
            ];
        }

        if ($columns['open_tickets']) {
            $rules['open_tickets_count'] = ['nullable', 'integer', 'min:0'];
        }

        if ($columns['image']) {
            $rules['image'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
        }

        return $rules;
    }

    private function validationMessages(): array
    {
        return [
            'name.required' => __('company_workers.validation.name_required'),
            'name.max' => __('company_workers.validation.name_max'),

            'email.email' => __('company_workers.validation.email_invalid'),
            'email.max' => __('company_workers.validation.email_max'),
            'email.unique' => __('company_workers.validation.email_unique'),
            'phone.max' => __('company_workers.validation.phone_max'),

            'iqama_number.unique' => __('company_workers.validation.iqama_unique'),
            'iqama_number.max' => __('company_workers.validation.iqama_max'),

            'position.max' => __('company_workers.validation.position_max'),
            'position_id.exists' => __('company_workers.validation.position_invalid'),
            'operating_company.max' => __('company_workers.validation.operating_company_max'),
            'city_id.required' => 'المدينة مطلوبة.',
            'city_id.exists' => 'المدينة المحددة غير صحيحة.',

            'nationality_id.exists' => __('company_workers.validation.nationality_invalid'),

            'prefered_language_id.exists' => __('company_workers.validation.language_invalid'),

            'status.required' => __('company_workers.validation.status_required'),
            'status.in' => __('company_workers.validation.status_invalid'),

            'open_tickets_count.integer' => __('company_workers.validation.open_tickets_integer'),
            'open_tickets_count.min' => __('company_workers.validation.open_tickets_min'),

            'image.image' => __('company_workers.validation.image_invalid'),
            'image.mimes' => __('company_workers.validation.image_mimes'),
            'image.max' => __('company_workers.validation.image_max'),

            'password.required' => __('company_workers.validation.password_required'),
            'password.min' => __('company_workers.validation.password_min'),
            'password.confirmed' => __('company_workers.validation.password_confirmed'),
        ];
    }

    private function syncWorkerNationalityLanguage(Worker $worker, Request $request): void
    {
        $nationalityId = $request->input('nationality_id');
        $languageId = $request->input('prefered_language_id');

        if (! $nationalityId && ! $languageId) {
            return;
        }

        $table = $this->getExistingTable([
            'nationalities_prefered_language',
            'nationalities_preferred_language',
            'nationalities_prefered_languages',
            'nationalities_preferred_languages',
            'nationality_prefered_languages',
            'nationality_preferred_languages',
        ]);

        if (! $table) {
            return;
        }

        if (! Schema::hasColumn($table, 'worker_id') || ! Schema::hasColumn($table, 'nationality_id')) {
            return;
        }

        $languageColumn = $this->getExistingColumn($table, [
            'prefered_language_id',
            'preferred_language_id',
            'language_id',
        ]);

        if (! $languageColumn) {
            return;
        }

        $payload = [
            'nationality_id' => $nationalityId,
            $languageColumn => $languageId,
        ];

        if (Schema::hasColumn($table, 'updated_at')) {
            $payload['updated_at'] = now();
        }

        $exists = DB::table($table)
            ->where('worker_id', $worker->id)
            ->exists();

        if (! $exists && Schema::hasColumn($table, 'created_at')) {
            $payload['created_at'] = now();
        }

        DB::table($table)->updateOrInsert(
            ['worker_id' => $worker->id],
            $payload
        );
    }
}
