<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Lawyer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompanyLawyerController extends Controller
{
    public function show()
    {
        $company = auth('company')->user();

        $lawyer = null;

        if (!empty($company->lawyer_id)) {
            $lawyer = Lawyer::query()
                ->with(['admin', 'creator'])
                ->find($company->lawyer_id);
        }

        if (!$lawyer && method_exists($company, 'lawyer')) {
            $lawyer = $company->lawyer;
        }

        $stats = [
            'workers' => $this->workersCount($company->id),
            'total_tickets' => $this->totalTicketsCount($company->id),
            'active_cases_count' => $lawyer ? (int) ($lawyer->active_cases_count ?? 0) : 0,
            'avg_response_hours' => $lawyer ? round(((int) ($lawyer->avg_response_minutes ?? 0)) / 60, 1) : 0,
        ];

        return view('company.lawyer.show', compact(
            'company',
            'lawyer',
            'stats'
        ));
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
