<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function create(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $data['total_cost'] = round((float) $data['unit_cost'] * (float) $data['quantity'], 2);
            $data['purchased_at'] = $data['purchased_at'] ?? now();

            $purchase = Purchase::create($data);

            $product = Product::lockForUpdate()->findOrFail($purchase->product_id);
            $product->increaseStock((float) $purchase->quantity);
            $product->update(['cost_price' => $purchase->unit_cost]);

            return $purchase;
        });
    }

    public function update(Purchase $purchase, array $data): Purchase
    {
        return DB::transaction(function () use ($data, $purchase) {
            $oldQty = (float) $purchase->quantity;
            $newQty = isset($data['quantity']) ? (float) $data['quantity'] : $oldQty;
            $diff   = $newQty - $oldQty;

            $data['total_cost'] = round((float) $data['unit_cost'] * $newQty, 2);

            $purchase->update($data);

            $product = Product::lockForUpdate()->findOrFail($purchase->product_id);

            if ($diff > 0) {
                $product->increaseStock($diff);
            } elseif ($diff < 0) {
                if (!$product->hasStock(abs($diff))) {
                    throw new \DomainException('Stock yetarli emas.');
                }
                $product->decreaseStock(abs($diff));
            }

            return $purchase->fresh();
        });
    }

    public function delete(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            $product = Product::lockForUpdate()->findOrFail($purchase->product_id);

            if (!$product->hasStock($purchase->quantity)) {
                throw new \DomainException("O'chirishda stock yetarli emas.");
            }

            $product->decreaseStock((float) $purchase->quantity);
            $purchase->delete();
        });
    }
}
