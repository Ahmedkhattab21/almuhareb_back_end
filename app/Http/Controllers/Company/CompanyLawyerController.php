<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompanyLawyerController extends Controller
{
    public function show()
    {
        $company = auth('company')->user();

        $legalAssignments = $this->legalAssignments($company->id);

        $stats = [
            'workers' => $this->workersCount($company->id),
            'total_tickets' => $this->totalTicketsCount($company->id),
            'assigned_lawyers' => $legalAssignments->count(),
            'case_categories' => $legalAssignments
                ->pluck('categories')
                ->flatten(1)
                ->unique('id')
                ->count(),
        ];

        return view('company.lawyer.show', compact(
            'company',
            'legalAssignments',
            'stats'
        ));
    }

    private function legalAssignments(int $companyId)
    {
        if (
            !Schema::hasTable('lawyers_categories') ||
            !Schema::hasTable('lawyers') ||
            !Schema::hasTable('categories')
        ) {
            return collect();
        }

        return DB::table('lawyers_categories')
            ->join('lawyers', 'lawyers.id', '=', 'lawyers_categories.lawyer_id')
            ->join('categories', 'categories.id', '=', 'lawyers_categories.category_id')
            ->where('lawyers_categories.company_id', $companyId)
            ->select([
                'lawyers.id as lawyer_id',
                'lawyers.name as lawyer_name',
                'lawyers.email as lawyer_email',
                'categories.id as category_id',
                'categories.name as category_name',
            ])
            ->orderBy('lawyers.name')
            ->orderBy('categories.name')
            ->get()
            ->groupBy('lawyer_id')
            ->map(function ($rows) {
                $first = $rows->first();

                return [
                    'lawyer' => [
                        'id' => $first->lawyer_id,
                        'name' => $first->lawyer_name,
                        'email' => $first->lawyer_email,
                    ],
                    'categories' => $rows
                        ->unique('category_id')
                        ->map(fn ($row) => [
                            'id' => $row->category_id,
                            'name' => $row->category_name,
                        ])
                        ->values(),
                ];
            })
            ->values();
    }

    private function workersCount(int $companyId): int
    {
        if (!Schema::hasTable('workers') || !Schema::hasColumn('workers', 'company_id')) {
            return 0;
        }

        return DB::table('workers')
            ->where('company_id', $companyId)
            ->count();
    }

    private function totalTicketsCount(int $companyId): int
    {
        if (!Schema::hasTable('tickets') || !Schema::hasColumn('tickets', 'company_id')) {
            return 0;
        }

        return DB::table('tickets')
            ->where('company_id', $companyId)
            ->count();
    }

}
