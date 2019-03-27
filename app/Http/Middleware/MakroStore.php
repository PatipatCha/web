<?php
/**
 * Created by PhpStorm.
 * User: kinkop
 * Date: 5/9/2017 AD
 * Time: 12:03 PM
 */

namespace App\Http\Middleware;

use Closure;

class MakroStore
{

    /**
     * Handle the request that require makro store
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (app()->environment() != 'local') {
            $users = [
                'makro' => 'makro2560'
            ];

            $requestUser = \Request::getUser();
            $requestPass = \Request::getPassword();

            if (array_get($users, $requestUser) !== $requestPass) {
                $headers = array('WWW-Authenticate' => 'Basic');
                return new \Symfony\Component\HttpFoundation\Response('Invalid credentials.', 401, $headers);
            }
        }

        return $next($request);
    }

}