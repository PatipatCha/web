<?php

namespace App\Http\Middleware;

use Closure;

class AuthBasicDebug
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
        $users = array(
            '9gag' => 'lol',
            'igw' => 'igw1234',
        );

        $requestUser = \Request::getUser();
        $requestPass = \Request::getPassword();

        if (array_get($users, $requestUser) !== $requestPass)
        {
            $headers = array('WWW-Authenticate' => 'Basic');

            return new \Symfony\Component\HttpFoundation\Response('Invalid credentials.', 401, $headers);
        }

        return $next($request);
    }
}
