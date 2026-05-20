<?php

namespace App\Models;

use App\Models\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use ShopScoped;
    protected $fillable = [
        'shop_id',
        'product_id',
        'supplier_id',
        'quantity',
        'unit_cost',
        'total_cost',
        'note',
        'purchased_at'
    ];
    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'purchased_at' => 'datetime'
    ];
    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function shop(){
        return $this->belongsTo(Shop::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
