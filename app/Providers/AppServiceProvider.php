<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*if (
            array_get($_SERVER, 'HTTP_X_FORWARDED_PROTO') == 'https'
            || array_get($_SERVER, 'HTTPS') == 'on'
            || str_is('https://*', request()->url())
        ) {
            $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
            $_SERVER['HTTPS'] = 'on';
            \URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }*/

        if (env('FORCE_HTTPS', false)) {
            \URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('makroSdk', function($app)
        {
            $config['app_id']   = env('BS_API_KEY');
            $config['secret']   = env('BS_API_SECRET');
            $config['endpoint'] = env('BS_ENDPOINT');
            $config['locale']   = app()->getLocale();
            return new \MakroSdk\MakroSdk($config);
        });
    }
}
