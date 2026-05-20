<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SaleReturn;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleReturnController extends Controller
{
    public function index(Request $request): View
    {
        $returns = SaleReturn::with(['sale', 'product'])
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('backend.sale-returns.index', compact('returns'));
    }
}
