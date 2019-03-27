<?php

namespace App\Bootstrap\Helpers;

class MakroHelper
{
    /**
     * Get group link.
     *
     * @param  array $content Group content
     * @return array
     */
    public static function getGroupLink(array $content)
    {
        $data = [
            'url' => '',
            'target' => array_get($content, 'target', '_self')
        ];

        switch (strtolower($content['type'])) {
            case 'content':
            case 'contents':
                $data['url'] = route('contents.show', ['slug' => $content['value']]);
                break;
            case 'campaign':
            case 'campaigns':
                $data['url'] = route('campaigns.show', ['slug' => $content['value']]);
                break;
            case 'category':
            case 'categories':
            case 'business_category':
            case 'product_category':
                $data['url'] = route('categories.show', ['slug' => $content['value']]);
                break;
            case 'external':
            case 'link_external':
            case 'external_link':
                $data['url'] = $content['value'];
                break;
            case 'internal':
            case 'link_internal':
            case 'internal_link':
                $data['url'] = url($content['value']);
                break;
        }

        return $data;
    }

    public static function encryptPassword($password)
    {
        return static::encrypt_decrypt('encrypt', $password);
    }

    public static function decryptPassword($secret)
    {
        return static::encrypt_decrypt('decrypt', $secret);
    }

    public static function encrypt_decrypt($action, $string)
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = 'bada258b0c42b8d464cba6b03417f554';
        $iv = '38a2b72245c15855';

        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    /**
     * Centralize Logging
     *
     * @param  string  $level info|error|debug
     * @param  string  $activity Name of route
     * @param  string  $activity_name Name of function + _request or + _response
     * @param  string  $activity_message request or response of message
     * @param  string $uuid uuid
     * @return null
     */
    public static function log($level, $activity, $activity_name, $activity_message, $uuid = null)
    {
        if (empty($uuid)) {
            $uuid = self::getUUID();
        }

        /*
        Notification /var/log/ms_notification/
        Subscription /var/log/ms_subscription/
        MSIS (BSS) /var/log/bss/
        FrontEnd (B2C) /var/log/web/
        */
        $path = env('CENTRALIZE_LOGGING_PATH', '/var/log/web/');

        $log = new \MakroLog\LogProvider($path);

        $memberData = AuthHelper::user(false);
        $client_name = null;
        if (! empty(array_get($memberData, 'profile.first_name')) && ! empty(array_get($memberData, 'profile.last_name'))) {
            $client_name = array_get($memberData, 'profile.first_name') . ' ' . array_get($memberData, 'profile.last_name');
        }

        // Data
        $data = [
            'environment' => app()->environment(), // required (develop|alpha|staging|production)
            'client_ip' => $_SERVER['SERVER_ADDR'], // required ($_SERVER[‘SERVER_ADDR’])
            'client_id' => array_get($memberData, 'id', null), // Optimal (online customer id)
            'client_uuid' => $uuid, // required ($_REQUEST[‘client_uuid’] or microtime(true))
            'client_name' => $client_name, // Optimal (online customer name)
            'service' => 'web', // required

            'level' => $level, // required (info|error|debug)
            'activity' => $activity, // required
            'activity_name' => $activity_name, // required (name of function + _request or + _response)
            'activity_message' => $activity_message // required (request or response of message)
        ];

        // Write Log
        $log->write($data);
    }

    private static function generateUUID()
    {
        return uniqid();
    }

    private static function setUUID($uuid = null)
    {
        if (empty($uuid)) {
            $uuid = self::generateUUID();
        }

        session()->put('client_uuid', $uuid);

        $day = 60 * 60 * 24;
        $expire = time() + ($day * 365);
        setcookie('_makroclickClientUUID', $uuid, $expire, '/', config('session.domain'), config('session.secure'));
    }

    public static function getUUID($refresh = false)
    {
        if ($refresh) {
            $uuid = self::generateUUID();
            self::setUUID($uuid);
        } else {
            if (isset($_COOKIE['_makroclickClientUUID'])) {
                $uuid = $_COOKIE['_makroclickClientUUID'];
            } else {
                $uuid = session()->get('client_uuid', null);
            }

            if (empty($uuid)) {
                $uuid = $uuid = self::generateUUID();
                self::setUUID($uuid);
            }
        }

        return $uuid;
    }

    public static function isAcceptedDelivery()
    {
        $isAcceptedDelivery = false;
        $user = AuthHelper::user();

        if ($user) {
            $isAcceptedDelivery = strtolower(array_get($user, 'profile.accept_delivery_service', 'n')) == 'y';
        }

        if (! $isAcceptedDelivery && session()->has('makroclickAcceptDeliveryExpireAt')) {
            $isAcceptedDelivery = time() <= session()->get('makroclickAcceptDeliveryExpireAt');
        }

        if (! $isAcceptedDelivery && isset($_COOKIE['_makroclickAcceptDelivery'])) {
            $isAcceptedDelivery = $_COOKIE['_makroclickAcceptDelivery'];
        }

        return $isAcceptedDelivery;
    }

    public static function setAcceptDelivery()
    {
        $day = 60 * 60 * env('DELIVERY_NOTIFY_DUE_HOURS', 24);
        $expire = time() + $day;

        session()->put('makroclickAcceptDeliveryExpireAt', $expire);

        setcookie('_makroclickAcceptDelivery', true, $expire, '/', config('session.domain'), config('session.secure'));
    }

    public static function useMockUP()
    {
        $useMockUp = false;

        try{
            $data = [
                'config_type' => 'Setting',
                'status' => 'active',
                'name' => 'mockup',
            ];

            $makroSdk = app()->make('makroSdk');

            $configs = $makroSdk->config()->get($data);

            if (! empty($configs['data']) && is_array($configs['data'])) {
                $config = head($configs['data']);
                $useMockUp = array_get($config, 'value', false);
            }
        } catch (\Exception $e) {

        }


        return $useMockUp;
    }

    public static function validateEmail($email)
    {
        $email = strtolower($email);

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $domain = explode("@", $email, 2);

            $valid = \Cache::tags(['global'])->remember('check-domain-dns-' . $domain[1], 1440, function () use ($domain) {
                if (checkdnsrr($domain[1])) {
                    return 'pass';
                }

                return null;
            });

            if (empty($valid)) {
                throw new  \Exception('EMAIL_IS_INVALID', 100003);
            }
        } else {
            throw new  \Exception('EMAIL_IS_INVALID', 100003);
        }

        return $email; //"This ($email) email address is considered valid."
    }

    public static function memberIdentityCacheKey()
    {
        $user = AuthHelper::user();
        return  'member_' . array_get($user, 'id') . array_get($user, 'profile.customer_channel');
    }
}