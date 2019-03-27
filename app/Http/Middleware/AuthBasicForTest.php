<?php

namespace App\Http\Middleware;

use Closure;

class AuthBasicForTest
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
        if (app()->environment() != 'alpha') {
            return $next($request);
        }

        $users = [
            'eCommerce' => '!@cm8Cfi',
            'igw' => 'igw1234',
        ];

        if (app()->environment() == 'production') {
            $users['eCommerce'] = 's5TM6tn?';
        }

        $requestUser = \Request::getUser();
        $requestPass = \Request::getPassword();

        if (array_get($users, $requestUser) !== $requestPass) {
            $headers = array('WWW-Authenticate' => 'Basic');
            return new \Symfony\Component\HttpFoundation\Response('Invalid credentials.', 401, $headers);
        }

        return $next($request);
    }
}
