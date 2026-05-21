<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DebtPayment;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DebtController extends Controller
{
    public function index(Request $request): View
    {
        $showAll = $request->boolean('show_cleared');

        $salesQuery = Sale::with(['customer', 'debtPayments'])
            ->where('payment_type', Sale::PAYMENT_DEBT);

        if (!$showAll) {
            $salesQuery->whereRaw(
                '(SELECT COALESCE(SUM(amount), 0) FROM debt_payments WHERE sale_id = sales.id) < total_amount'
            );
        }

        $allSales = $salesQuery->orderByDesc('created_at')->get();

        $customerDebts = $allSales
            ->groupBy('customer_id')
            ->map(function ($sales) {
                $customer  = $sales->first()->customer;
                $totalDebt = (float) $sales->sum('total_amount');
                $totalPaid = (float) $sales->sum(fn($s) => $s->debtPayments->sum('amount'));
                $remaining = $totalDebt - $totalPaid;
                return [
                    'customer'   => $customer,
                    'debt_count' => $sales->count(),
                    'total_debt' => $totalDebt,
                    'total_paid' => $totalPaid,
                    'remaining'  => $remaining,
                    'last_at'    => $sales->max('created_at'),
                ];
            })
            ->sortByDesc('remaining')
            ->values();

        $summaryTotal     = $customerDebts->sum('total_debt');
        $summaryRemaining = $customerDebts->sum('remaining');
        $customerCount    = $customerDebts->count();

        return view('backend.debts.index', compact(
            'customerDebts', 'summaryTotal', 'summaryRemaining', 'customerCount', 'showAll'
        ));
    }

    public function customer(Customer $customer): View
    {
        $sales = Sale::with(['debtPayments', 'saleItems.product', 'cashier'])
            ->where('customer_id', $customer->id)
            ->where('payment_type', Sale::PAYMENT_DEBT)
            ->orderByDesc('id')
            ->get();

        $totalDebt = (float) $sales->sum('total_amount');
        $totalPaid = (float) $sales->sum(fn($s) => $s->debtPayments->sum('amount'));
        $remaining = $totalDebt - $totalPaid;

        return view('backend.debts.customer', compact('customer', 'sales', 'totalDebt', 'totalPaid', 'remaining'));
    }

    public function show(Sale $sale): View
    {
        abort_if($sale->payment_type !== Sale::PAYMENT_DEBT, 404);

        $sale->load(['customer', 'saleItems.product', 'cashier', 'debtPayments']);

        return view('backend.debts.show', compact('sale'));
    }

    public function pay(Request $request, Sale $sale): RedirectResponse
    {
        abort_if($sale->payment_type !== Sale::PAYMENT_DEBT, 404);

        $sale->load('debtPayments');
        $remaining = $sale->debtBalance();

        if ($remaining <= 0) {
            return back()->with('error', 'Bu qarz allaqachon to\'liq to\'langan.');
        }

        $data = $request->validate([
            'amount'  => "required|numeric|min:0.01|max:{$remaining}",
            'note'    => 'nullable|string|max:500',
            'paid_at' => 'nullable|date',
        ]);

        DebtPayment::create([
            'shop_id' => $sale->shop_id,
            'sale_id' => $sale->id,
            'amount'  => $data['amount'],
            'note'    => $data['note'] ?? null,
            'paid_at' => $data['paid_at'] ?? now(),
        ]);

        return redirect()->route('backend.debts.show', $sale)
            ->with('success', 'To\'lov qabul qilindi.');
    }
}
