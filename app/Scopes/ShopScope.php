<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ShopScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $shopId = session('shop_id');

        if (!$shopId && auth()->check()) {
            $shopId = auth()->user()->shop_id ?? null;
        }

        if ($shopId) {
            $builder->where($model->getTable() . '.shop_id', $shopId);
        } else {
            $builder->whereRaw('FALSE');
        }
    }
}
