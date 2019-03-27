<?php

namespace App\Http\Middleware;

use App\Bootstrap\Helpers\AuthHelper;
use App\Bootstrap\Helpers\StoreHelper;
use Closure;

class CartNotEmpty
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $makroSdk = app()->make('makroSdk');
        $empty = false;
        try {
            $response = $makroSdk->cart()->get(StoreHelper::getCurrentStore(), AuthHelper::tempMemberId());
        } catch (\Exception $e) {
            $empty = true;
        }

        if (!isset($response['data']) || sizeof($response['data']) < 1) {
            $empty = true;
        }

        if ($empty) {
            return redirect(route('carts.index'))->withErrors([
                'Cart is empty!'
            ]);
        }

        $request->request->add(['cart' => $response]);

        return $next($request);
    }
}
