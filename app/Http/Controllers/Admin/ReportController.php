<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminReportBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    public function show(Request $request, string $report, string $format, AdminReportBuilder $builder)
    {
        abort_unless(in_array($format, ['pdf', 'excel'], true), 404);

        $data = $builder->build($report, $request);

        if ($format === 'excel') {
            return $this->excelResponse($data);
        }

        return view('admin.reports.print', $data);
    }

    private function excelResponse(array $data): Response
    {
        $html = view('admin.reports.excel', $data)->render();
        $filename = $data['slug'] . '-' . now()->format('Y-m-d-His') . '.xls';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
