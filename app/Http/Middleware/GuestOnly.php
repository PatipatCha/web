<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class GuestOnly
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
        $user = \App\Bootstrap\Helpers\AuthHelper::user();

        if ($user) {
            return redirect()->route('home.index'); // To member profile
        }

        return $next($request);
    }
}
