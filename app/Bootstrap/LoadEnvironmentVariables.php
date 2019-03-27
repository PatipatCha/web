<?php namespace App\Bootstrap;

use Dotenv\Dotenv;
use InvalidArgumentException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables as BaseLoadEnvironmentVariables;

class LoadEnvironmentVariables extends BaseLoadEnvironmentVariables {

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        if ($app->configurationIsCached()) {
            return;
        }

        $this->checkForSpecificEnvironmentFile($app);

        $app->detectEnvironment(function() {
            if (getenv('ENVIRONMENT') == 'develop') {
                return 'develop';
            } elseif (getenv('ENVIRONMENT') == 'staging') {
                return 'staging';
            } elseif (getenv('ENVIRONMENT') == 'production') {
                return 'production';
            } elseif (getenv('ENVIRONMENT') == 'docker') {
                return 'docker';
            } elseif (getenv('ENVIRONMENT') == 'stagingptvn') {
                return 'stagingptvn';
            } elseif (getenv('ENVIRONMENT') == 'stagingptvn01') {
                return 'stagingptvn01';
            } elseif (getenv('ENVIRONMENT') == 'stagingptvn02') {
                return 'stagingptvn02';
            } elseif (getenv('ENVIRONMENT') == 'production01') {
                return 'production01';
            } elseif (getenv('ENVIRONMENT') == 'production02') {
                return 'production02';
            } else {
                 $environments = array(
                     'local' => array('*.loc', 'localhost', '*.app', 'scotchbox', '*.vagr', '*.dock', '*.dev', '*.local', '*.test'),
                     'develop'    => array('*.igetapp.com', 'dep-code.igetapp.com'),
                     'alpha'    => array('*alpha*.eggdigital.com', 'alpha*'),
                     'staging'    => array('staging.makroclick.com', '*.eggdigital.com'),
                     'stagingptvn' => array('staging-www.makroclick.*'),
                     'production' => array('*.com')
                 );

                 $hostname = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : gethostname();
                 foreach ($environments as $environment => $hosts) {
                     foreach ((array) $hosts as $host) {
                         if (str_is($host, $hostname)) {
                             return $environment;
                         }
                     }
                 }

                 return 'production';
            }
        });

        try {
            (new Dotenv($app->environmentPath(), $app->environmentFile() . '.' . $app->environment()))->load();
        } catch (InvalidArgumentException $e) {
            (new Dotenv($app->environmentPath(), '.env.production'))->load();
        }
    }
}