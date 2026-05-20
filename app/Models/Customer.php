<?php

namespace App\Models;

use App\Models\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use ShopScoped;
    protected $fillable = [
        'shop_id',
        'name',
        'phone',
        'note',
        'balance',
        'is_active'
    ];
    public function shop(){
        return $this->belongsTo(Shop::class);
    }
    public function sales(){
        return $this->hasMany(Sale::class);
    }
    public function scopeActive($query){
        return $query->where('is_active', 1);
    }

}
