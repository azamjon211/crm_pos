<?php

namespace App\Models\Traits;

use App\Scopes\ShopScope;

trait ShopScoped
{
    public static function bootShopScoped(): void
    {
        static::addGlobalScope(new ShopScope());

        static::creating(function (self $model) {
            if (empty($model->shop_id)) {
                $shopId = session('shop_id');
                if (!$shopId && auth()->check()) {
                    $shopId = auth()->user()->shop_id;
                }
                if ($shopId) {
                    $model->shop_id = $shopId;
                }
            }
        });
    }
}
