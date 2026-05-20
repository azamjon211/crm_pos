<?php

namespace App\Models;

use App\Models\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use ShopScoped;
    protected $fillable = [
        'shop_id',
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'unit_cost',
        'line_total'
    ];
    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'line_total' => 'decimal:2'
    ];
    public function sale():belongsTo{
        return $this->belongsTo(Sale::class);
    }
    public function shop():belongsTo{
        return $this->belongsTo(Shop::class);
    }
    public function product():belongsTo{
        return $this->belongsTo(Product::class);
    }

}
