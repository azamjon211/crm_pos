<?php

namespace App\Models;

use App\Models\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use ShopScoped;
    protected $fillable = [
        'shop_id',
        'name',
        'is_active',
        'parent_id',

    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function shop(){
        return $this->belongsTo(Shop::class);
    }
    public function products(){
        return $this->hasMany(Product::class);
    }
    public function scopeActive($query){
        return $query->where('is_active', 1);
    }
    public function parent(){
        return $this->belongsTo(Category::class, 'parent_id');
    }
    public function children(){
        return $this->hasMany(Category::class, 'parent_id');
    }
    public function scopeRoots($query){
        return $query->whereNull('parent_id');
    }
}
