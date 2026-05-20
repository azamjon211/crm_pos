<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\DailyClosingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private DailyClosingService $closingService) {}

    public function monthly(Request $request): View
    {
        $year = (int) $request->input('year', date('Y'));
        $year = max(2000, min((int) date('Y') + 1, $year));
        $data = $this->closingService->getMonthlyReport($year);

        return view('backend.reports.monthly', $data);
    }
}
