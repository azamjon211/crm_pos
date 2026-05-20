<?php

namespace App\Models;

use App\Models\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class DebtPayment extends Model
{
    use ShopScoped;

    protected $fillable = ['shop_id', 'sale_id', 'amount', 'note', 'paid_at'];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
