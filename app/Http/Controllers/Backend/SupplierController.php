<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::withCount('purchases')->orderBy('name')->paginate(20);
        return view('backend.suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('backend.suppliers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:64',
            'note'  => 'nullable|string',
        ]);

        Supplier::create($data);
        return redirect()->route('backend.suppliers.index')->with('success', 'Yetkazuvchi qo\'shildi.');
    }

    public function edit(Supplier $supplier): View
    {
        return view('backend.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:64',
            'note'  => 'nullable|string',
        ]);

        $supplier->update($data);
        return redirect()->route('backend.suppliers.index')->with('success', 'Yetkazuvchi yanglandi.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->purchases()->exists()) {
            return back()->with('error', 'Bu yetkazuvchiga bog\'liq xaridlar mavjud.');
        }

        $supplier->delete();
        return redirect()->route('backend.suppliers.index')->with('success', 'Yetkazuvchi o\'chirildi.');
    }
}
