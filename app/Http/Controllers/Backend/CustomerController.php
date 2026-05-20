<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::withCount('sales');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('phone', 'ilike', "%{$search}%")
                    ->orwhere('phone', 'ilike', "%{$search}%");
            });
        }

        $customers = $query->orderBy('name')->paginate(20)->withQueryString();
        return view('backend.customers.index', compact('customers'));
    }

    public function show(Customer $customer): View
    {
        $customer->load(['sales' => fn($q) => $q->latest()->limit(20)]);
        return view('backend.customers.show', compact('customer'));
    }

    public function create(): View
    {
        return view('backend.customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'phone'     => 'nullable|string|max:64',
            'note'      => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Customer::create($data);
        return redirect()->route('backend.customers.index')->with('success', 'Mijoz qo\'shildi.');
    }

    public function edit(Customer $customer): View
    {
        return view('backend.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'phone'     => 'nullable|string|max:64',
            'note'      => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $customer->update($data);
        return redirect()->route('backend.customers.index')->with('success', 'Mijoz yangilandi.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->sales()->exists()) {
            return back()->with('error', 'Mijozga bog\'liq sotuvlar mavjud.');
        }

        $customer->delete();
        return redirect()->route('backend.customers.index')->with('success', 'Mijoz o\'chirildi.');
    }
}
