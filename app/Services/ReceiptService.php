<?php

namespace App\Services;

use App\Models\Sale;

class ReceiptService
{
    public function generateHtml(Sale $sale): string
    {
        $sale->loadMissing(['saleItems.product', 'shop', 'cashier', 'customer']);

        $shopName    = $sale->shop->name    ?? config('app.name');
        $shopAddress = $sale->shop->address ?? '';
        $shopPhone   = $sale->shop->phone   ?? '';
        $paymentLabel = Sale::PAYMENT_TYPES[$sale->payment_type] ?? $sale->payment_type;

        $html  = '<div style="font-family:monospace;font-size:14px;max-width:320px;">';
        $html .= '<h3 style="text-align:center;">' . e($shopName) . '</h3>';

        if ($shopAddress) $html .= '<div style="text-align:center;font-size:12px;">' . e($shopAddress) . '</div>';
        if ($shopPhone)   $html .= '<div style="text-align:center;font-size:12px;">Tel: ' . e($shopPhone) . '</div>';

        $html .= '<hr>';
        $html .= 'Chek #' . $sale->id . '<br>';
        $html .= 'Sana: ' . $sale->created_at->format('d.m.Y H:i') . '<br>';

        if ($sale->cashier)  $html .= 'Kassir: ' . e($sale->cashier->name) . '<br>';
        if ($sale->customer) $html .= 'Mijoz: ' . e($sale->customer->name) . '<br>';

        $html .= '<hr><strong>Mahsulotlar:</strong><br>';

        $grandTotal = 0.0;
        foreach ($sale->saleItems as $i => $item) {
            $name      = $item->product ? e($item->product->name) : 'Noma\'lum';
            $qty       = (float) $item->quantity;
            $unitPrice = (float) $item->unit_price;
            $lineTotal = (float) $item->line_total;
            $grandTotal += $lineTotal;

            $html .= ($i + 1) . '. ' . $name . '<br>';
            $html .= '&nbsp;&nbsp;' . $qty . ' × ' . number_format($unitPrice, 0, '.', ' ')
                . ' = <strong>' . number_format($lineTotal, 0, '.', ' ') . '</strong><br>';
        }

        $html .= '<hr>';
        $html .= '<strong>Jami: ' . number_format($grandTotal, 0, '.', ' ') . '</strong><br>';
        $html .= "To'lov: " . e($paymentLabel) . '<br>';
        if ($sale->note) $html .= 'Izoh: ' . e($sale->note) . '<br>';
        $html .= '<hr><div style="text-align:center;font-size:12px;">Rahmat!</div>';
        $html .= '</div>';

        return $html;
    }
}
