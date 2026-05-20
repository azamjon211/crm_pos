<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DailyClosing;
use App\Services\DailyClosingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DailyClosingController extends Controller
{
    public function __construct(private DailyClosingService $closingService) {}

    public function index(): View
    {
        $closings     = DailyClosing::orderByDesc('date')->paginate(30);
        $todayClosing = DailyClosing::whereDate('date', today())->first();
        $todayStats   = $this->closingService->getTodayStats();

        return view('backend.daily-closing.index', compact('closings', 'todayClosing', 'todayStats'));
    }

    public function close(Request $request): RedirectResponse
    {
        $date     = $request->input('date', today()->toDateString());
        $force    = (bool) $request->input('force', false);
        $existing = DailyClosing::whereDate('date', $date)->first();

        if ($existing && ! $force) {
            return back()->with('warning', "Bu kun ({$date}) allaqachon yopilgan.");
        }

        $this->closingService->close($date);

        return back()->with('success', $existing ? "Qayta hisoblandi." : "Kun yopildi.");
    }
}
