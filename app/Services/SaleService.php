<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function createFromCart(
        array $items,
        string $paymentType = Sale::PAYMENT_CASH,
        ?string $note = null,
        ?int $customerId = null
    ): Sale {
        if (empty($items)) {
            throw new \InvalidArgumentException('Korzinka bo\'sh.');
        }

        $shopId = session('shop_id') ?? auth()->user()?->shop_id;

        if (!$shopId) {
            throw new \DomainException('Magazin aniqlanmadi. Iltimos, tizimga qayta kiring.');
        }

        return DB::transaction(function () use ($items, $paymentType, $note, $customerId, $shopId) {
            $sale = Sale::create([
                'shop_id'      => $shopId,
                'cashier_id'   => auth()->id(),
                'customer_id'  => $customerId,
                'total_amount' => 0,
                'total_cost'   => 0,
                'payment_type' => $paymentType,
                'note'         => $note,
            ]);

            $totalAmount = 0.0;
            $totalCost   = 0.0;

            foreach ($items as $row) {
                $productId = (int) ($row['product_id'] ?? 0);
                $qty       = (float) ($row['quantity'] ?? 0);

                if ($productId <= 0 || $qty <= 0) continue;

                $product = Product::lockForUpdate()->findOrFail($productId);

                if (! $product->hasStock($qty)) {
                    throw new \DomainException(
                        "'{$product->name}' uchun yetarli zaxira yo'q. Mavjud: {$product->stock_quantity}"
                    );
                }

                $unitPrice = $product->getFinalPrice();
                $unitCost  = (float) $product->cost_price;
                $lineTotal = round($unitPrice * $qty, 2);

                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'unit_price' => $unitPrice,
                    'unit_cost'  => $unitCost,
                    'line_total' => $lineTotal,
                ]);

                $product->decreaseStock($qty);

                $totalAmount += $lineTotal;
                $totalCost   += $unitCost * $qty;
            }

            $sale->update([
                'total_amount' => round($totalAmount, 2),
                'total_cost'   => round($totalCost, 2),
            ]);

            return $sale->fresh(['saleItems.product']);
        });
    }

    public function processReturn(
        int $saleItemId,
        float $quantity,
        string $returnType = SaleReturn::TYPE_REFUND,
        ?string $reason = null
    ): SaleReturn {
        return DB::transaction(function () use ($saleItemId, $quantity, $returnType, $reason) {
            $saleItem = SaleItem::with('product')->lockForUpdate()->findOrFail($saleItemId);

            $alreadyReturned = SaleReturn::where('sale_item_id', $saleItemId)->sum('quantity');
            $maxReturnable   = (float) $saleItem->quantity - (float) $alreadyReturned;

            if ($quantity > $maxReturnable) {
                throw new \DomainException(
                    "Maksimal qaytarish miqdori: {$maxReturnable}, so'ralgan: {$quantity}"
                );
            }

            $saleReturn = SaleReturn::create([
                'sale_id'      => $saleItem->sale_id,
                'sale_item_id' => $saleItemId,
                'product_id'   => $saleItem->product_id,
                'quantity'     => $quantity,
                'return_type'  => $returnType,
                'reason'       => $reason,
                'returned_at'  => now(),
            ]);

            $saleItem->product->increaseStock($quantity);

            return $saleReturn;
        });
    }
}
