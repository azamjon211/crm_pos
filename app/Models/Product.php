<?php

namespace App\Models;

use App\Models\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use ShopScoped;
    protected $fillable = [
        'shop_id',
        'category_id',
        'name',
        'sku',
        'barcode',
        'cost_price',
        'sale_price',
        'discount',
        'stock_quantity',
        'is_active'
    ];
    protected $casts = [
        'is_active' => 'boolean'
    ];
    public function getFinalPrice(): float
    {
        $base = (float) $this->sale_price;
        $discount = (int) $this->discount;
        if($discount <=0){
            return $base;
        }
        return round($base *(1 - $discount/100), 2);
    }
    public function hasStock(float $quantity):bool
    {
        return (float)$this->stock_quantity >= $quantity;
    }
    public function decreaseStock(float $qty):void
    {
        if(!$this->hasStock($qty)){
            throw new \DomainException("'{$this->name}' uchun yetarli zahira yo'q" . "Mavjud : '{$this->stock_quantity}' ");
        }
        $this->decrement('stock_quantity', $qty);
    }
    public function increaseStock(float $qty):void
    {
        $this->increment('stock_quantity', $qty);
    }
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
    public function saleItems(){
        return $this->hasMany(SaleItem::class);
    }
    public function scopeActive($query){
        return $query->where('is_active', 1);
    }
    public function scopeLowStock($query, float $threshold = 5.0){
        return $query ->where('stock_quantity', '<=', $threshold)
            ->where('is_active', 1);
    }

}
