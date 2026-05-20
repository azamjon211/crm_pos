<?php

namespace App\Models;

use App\Models\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use ShopScoped;

    const PAYMENT_CASH     = 'cash';
    const PAYMENT_CARD     = 'card';
    const PAYMENT_TRANSFER = 'transfer';
    const PAYMENT_DEBT     = 'debt';

    const PAYMENT_TYPES = [
        self::PAYMENT_CASH     => 'Naqt',
        self::PAYMENT_CARD     => 'Kart',
        self::PAYMENT_TRANSFER => 'Transfer',
        self::PAYMENT_DEBT     => 'Qarz',
    ];

    protected $fillable = [
        'shop_id',
        'cashier_id',
        'customer_id',
        'payment_type',
        'total_amount',
        'total_cost',
        'note',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'total_cost'   => 'decimal:2',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function saleReturns()
    {
        return $this->hasMany(SaleReturn::class);
    }

    public function debtPayments()
    {
        return $this->hasMany(DebtPayment::class);
    }

    public function debtBalance(): float
    {
        return (float) $this->total_amount - (float) $this->debtPayments->sum('amount');
    }

    public function isDebtCleared(): bool
    {
        return $this->debtBalance() <= 0;
    }
}
