<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'is_active',
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function users(): HasMany{
        return $this->hasMany(User::class);
    }
    public function products(): HasMany{
        return $this->hasMany(Product::class);
    }
    public function categories(): HasMany{
        return $this->hasMany(Category::class);
    }
    public function suppliers(): HasMany{
        return $this->hasMany(Supplier::class);
    }
    public function customers(): HasMany{
        return $this->hasMany(Customer::class);
    }
    public function sales(): HasMany{
        return $this->hasMany(Sale::class);
    }
    public function purchases(): HasMany{
        return $this->hasMany(Purchase::class);
    }
}
