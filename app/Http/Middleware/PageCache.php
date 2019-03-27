<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class PageCache
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
//        $cacheKey = "product-{$request->route()->parameter('id')}";
//        if(\Cache::has($cacheKey)){
//            return response(\Cache::get($cacheKey));
//        }


        return $next($request);
    }

    public function terminate($request,Response $response)
    {
//        $cacheKey = "product-{$request->route()->parameter('id')}";
//        \Cache::put($cacheKey,$response->getContent(), 6000);
    }
}
