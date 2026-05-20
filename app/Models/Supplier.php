<?php

namespace App\Models;

use App\Models\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use ShopScoped;
    protected $fillable = [
        'shop_id',
        'name',
        'phone',
        'note'
    ];
    public function shop(){
        return $this->belongsTo(Shop::class);
    }
    public function purchases(){
        return $this->hasMany(Purchase::class);
    }

}
