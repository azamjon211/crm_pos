<?php

namespace App\Models;

use App\Models\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class DailyClosing extends Model
{
    use ShopScoped;
    const UPDATED_AT = null;
    protected $fillable = [
        'shop_id',
        'date',
        'total_sales',
        'total_cost',
        'total_profit',
        'total_returns',
        'net_sales'
    ];
    protected $casts = [
        'date' => 'date',
        'total_sales' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'total_profit' => 'decimal:2',
        'total_returns' => 'decimal:2',
        'net_sales' => 'decimal:2',
    ];
    public function shop(){
        return $this->belongsTo(Shop::class);
    }

}
