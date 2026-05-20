<?php

namespace App\Http\Middleware;

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
                    if (! $request->routeIs('backend.switch-shop') && ! $request->routeIs('backend.switch-shop.post') && ! $request->routeIs('logout')) {
                        return redirect()->route('backend.switch-shop');
                    }
                }
            }
        }

        return $next($request);
    }
}
