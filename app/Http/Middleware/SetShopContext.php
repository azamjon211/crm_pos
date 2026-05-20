<?php

namespace App\Http\Middleware;

use App\Models\Shop;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetShopContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            if (! session()->has('shop_id')) {
                if ($user->shop_id) {
                    session(['shop_id' => $user->shop_id, 'shop_name' => $user->shop?->name]);
                } elseif ($user->isSuperAdmin()) {
                    $excluded = [
                        'backend.switch-shop',
                        'backend.switch-shop.post',
                        'backend.shops.*',
                        'logout',
                    ];

                    if (! $request->routeIs($excluded)) {
                        $shops = Shop::withoutGlobalScopes()->get(['id', 'name']);

                        if ($shops->isEmpty()) {
                            return redirect()->route('backend.shops.create')
                                ->with('info', 'Tizimdan foydalanish uchun avval magazin yarating.');
                        }

                        if ($shops->count() === 1) {
                            session(['shop_id' => $shops->first()->id, 'shop_name' => $shops->first()->name]);
                            return $next($request);
                        }

                        return redirect()->route('backend.switch-shop');
                    }
                }
            }
        }

        return $next($request);
    }
}
