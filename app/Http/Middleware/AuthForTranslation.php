<?php

namespace App\Http\Middleware;

use Closure;

class AuthForTranslation
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
        if (app()->environment() == 'local' || app()->environment() == 'develop') {
            return $next($request);
        }

        $users = [
            'translator' => 'makro@trans',
            'igw' => 'igw1234',
        ];

        $requestUser = \Request::getUser();
        $requestPass = \Request::getPassword();

        if (array_get($users, $requestUser) !== $requestPass) {
            $headers = array('WWW-Authenticate' => 'Basic');
            return new \Symfony\Component\HttpFoundation\Response('Invalid credentials.', 401, $headers);
        }

        return $next($request);
    }
}
