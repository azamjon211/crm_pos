<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopSwitchController extends Controller
{
    public function index(): View
    {
        abort_if(! auth()->user()->isSuperAdmin(), 403);

        $shops = Shop::where('is_active', true)->orderBy('name')->get();

        return view('backend.switch-shop', compact('shops'));
    }

    public function switch(Request $request): RedirectResponse
    {
        abort_if(! auth()->user()->isSuperAdmin(), 403);

        $request->validate([
            'shop_id' => 'required|exists:shops,id',
        ]);

        $shop = \App\Models\Shop::findOrFail((int) $request->shop_id);
        session(['shop_id' => $shop->id, 'shop_name' => $shop->name]);

        return redirect()->route('backend.dashboard');
    }
}
