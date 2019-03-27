<?php

namespace App\Http\Middleware;

use App\Bootstrap\Helpers\AuthHelper;
use Closure;

class AuthToken
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
        //ทดสอบชั่วคราว เพื่อให้ ajax call ผ่าน
        if (!$request->ajax()) {
            if (!AuthHelper::user()) {
                return redirect(route('home.index'))
                    ->with('required_login', 1)
                    ->with('loggedin_redirect_url', $request->url());
            }
        }

        return $next($request);
    }
}
