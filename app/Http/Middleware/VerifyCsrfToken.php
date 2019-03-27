<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Route;
use Closure;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '*carts*',
        '*gateway*',
        'truemoney-background-process',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = Route::getRoutes()->match($request);
        $routeAction = $route->getAction();

        if (isset($routeAction['nocsrf']) && $routeAction['nocsrf']) {
            return $next($request);
        }

        /*// Handle TokenMismatchExceptions
        if (! $this->isReading($request) && ! $this->tokensMatch($request)) {
            return redirect()->back()->withInput($request->except('_token'))->withErrors('Your session has expired. Please try again.');
        }*/

        return parent::handle($request, $next);
    }
}
