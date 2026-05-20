<?php

namespace App\Models;

use App\Models\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    use ShopScoped;
    const TYPE_REFUND = 'refund';
    const TYPE_EXCHANGE = 'exchange';

    const TYPES = [
        self::TYPE_REFUND => 'Pul qaytarish',
        self::TYPE_EXCHANGE => 'Almashtirish',
    ];
    protected $fillable = [
        'shop_id',
        'sale_id',
        'sale_item_id',
        'product_id',
        'quantity',
        'reason',
        'return_type',
        'return_at'
    ];
    protected $casts = [
        'quantity' => 'decimal:2',
        'return_at' => 'datetime'
    ];
    public function shop(){
        return $this->belongsTo(Shop::class);
    }
    public function sale(){
        return $this->belongsTo(Sale::class);
    }
    public function saleItem(){
        return $this->belongsTo(SaleItem::class);
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
