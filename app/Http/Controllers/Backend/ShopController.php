<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(): View
    {
        $shops = Shop::withCount(['users', 'products', 'sales'])->orderBy('name')->paginate(20);

        return view('backend.shops.index', compact('shops'));
    }

    public function create(): View
    {
        return view('backend.shops.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255|unique:shops,name',
            'address'  => 'nullable|string|max:500',
            'phone'    => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        Shop::create($data);

        return redirect()->route('backend.shops.index')->with('success', 'Do\'kon qo\'shildi.');
    }

    public function edit(Shop $shop): View
    {
        $users = User::where('shop_id', $shop->id)->orderBy('name')->get();
        return view('backend.shops.edit', compact('shop', 'users'));
    }

    public function update(Request $request, Shop $shop): RedirectResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255|unique:shops,name,' . $shop->id,
            'address'  => 'nullable|string|max:500',
            'phone'    => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $shop->update($data);

        return redirect()->route('backend.shops.index')->with('success', 'Do\'kon yangilandi.');
    }

    public function destroy(Shop $shop): RedirectResponse
    {
        if ($shop->users()->exists()) {
            return back()->with('error', 'Do\'konda foydalanuvchilar bor, avval ularni o\'chiring.');
        }

        $shop->delete();

        return redirect()->route('backend.shops.index')->with('success', 'Do\'kon o\'chirildi.');
    }
}
