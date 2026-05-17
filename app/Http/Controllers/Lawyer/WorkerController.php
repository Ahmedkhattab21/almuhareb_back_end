<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Nationality;
use App\Models\Position;
use App\Models\PreferedLanguage;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WorkerController extends Controller
{
    private const NPL_TABLE = 'nationalities_prefered_language';

    public function index(Request $request)
    {
        $lawyerId = auth('lawyer')->id();
        $companyIds = Company::assignedToLawyer($lawyerId)->pluck('id');

        $query = Worker::query()
            ->whereIn('company_id', $companyIds)
            ->with([
                'company',
                'position',
                'nationalityPreferredLanguage.nationality',
                'nationalityPreferredLanguage.preferedLanguage',
            ]);

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
            $query->whereIn('company_id', $companyIds)
                ->where('company_id', $request->company_id);
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
                ->join('workers as workers', 'workers.id', '=', 'npl.worker_id')
                ->join('prefered_languages as pl', 'npl.prefered_language_id', '=', 'pl.id')
                ->whereIn('workers.company_id', $companyIds)
                ->selectRaw('pl.prefered_language, COUNT(*) as total')
                ->groupBy('pl.id', 'pl.prefered_language')
                ->orderByDesc('total')
                ->first();
        }

        $stats = [
            'total' => Worker::whereIn('company_id', $companyIds)->count(),
            'active' => Worker::whereIn('company_id', $companyIds)->where('status', 'active')->count(),
            'pending' => Worker::whereIn('company_id', $companyIds)->where('status', 'pending')->count(),

            'nationalities' => Schema::hasTable(self::NPL_TABLE)
                ? DB::table(self::NPL_TABLE)
                    ->join('workers as workers', 'workers.id', '=', self::NPL_TABLE . '.worker_id')
                    ->whereIn('workers.company_id', $companyIds)
                    ->distinct(self::NPL_TABLE . '.nationality_id')
                    ->count(self::NPL_TABLE . '.nationality_id')
                : 0,

            'top_language' => $topLanguage?->prefered_language ?? '-',
        ];

        $companies = Company::assignedToLawyer($lawyerId)
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

        return view('lawyer.workers.index', compact(
            'workers',
            'stats',
            'companies',
            'nationalities',
            'preferedLanguages',
            'positions'
        ));
    }

    public function show(Worker $worker)
    {
        $lawyerId = auth('lawyer')->id();

        abort_unless(
            $worker->company()
                ->where('lawyer_id', $lawyerId)
                ->exists(),
            404
        );

        $worker->load([
            'company',
            'position',
            'nationalityPreferredLanguage.nationality',
            'nationalityPreferredLanguage.preferedLanguage',
        ]);

        return view('lawyer.workers.show', compact('worker'));
    }
}
