<?php

namespace MakroSdk;

use App\Bootstrap\Helpers\AuthHelper;
use App\Bootstrap\Helpers\StoreHelper;

abstract class ClientAbstract
{
    protected $endpoint, $appId, $appSecret, $locale;

    public function __construct(array $config = array())
    {
        $defaultEndpoint  = '';
        $defaultAppId     = '';
        $defaultAppSecret = '';
        $defaultLocale    = '';

        $this->endpoint  = (!empty($config['endpoint'])) ? $config['endpoint'] : $defaultEndpoint;
        $this->appId     = (!empty($config['app_id'])) ? $config['app_id'] : $defaultAppId;
        $this->appSecret = (!empty($config['secret'])) ? $config['secret'] : $defaultAppSecret;
        $this->locale    = (!empty($config['locale'])) ? $config['locale'] : $defaultLocale;
    }

    public function api($path, array $args = [], $method = 'GET')
    {
        $url = $this->getEndpoint($path);

        return $this->makeRequest($url, $args, $method);
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    protected function getEndpoint($path)
    {
        return rtrim($this->endpoint, '/') . '/' . ltrim($path, '/');
    }

    protected function makeRequest($url, array $data = [], $method = 'GET', $curl_opts_extends = array())
    {
        $curl = curl_init();

        // $data = array_merge($data, ['app_id' => $this->appId, 'secret' => $this->appSecret]);

        $url = rtrim($url, '/');

        if (preg_match('/get/i', $method)) {
            $url = strpos($url, '?') ? $url . '&' . http_build_query($data) : $url . '?' . http_build_query($data);
            $data = null;
        } else {
            $data = json_encode($data);
        }

        $user = AuthHelper::user(false);

        $curl_opts = array(
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => 2,
//            CURLOPT_CONNECTTIMEOUT => 10,
//            CURLOPT_TIMEOUT => 60,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'], // ฝั่ง BS ต้อง Detect mobile
            CURLOPT_HTTPHEADER => [
                'api-key:' . $this->appSecret,
                'locale:' . $this->locale,
                'store-id:' . StoreHelper::getCurrentStore(),
                'store-price-id:' . StoreHelper::getCurrentStorePrice(),
                'token-id:' . (!empty($user) && isset($user['token_id']) ? $user['token_id'] : null),
                'Authorization:Bearer ' . (!empty($user) && isset($user['access_token']) ? $user['access_token'] : null),
                'client-uuid:' . request()->attributes->get('client_uuid')
            ]
        );

        // necessary options ???
        if (preg_match('/^https/', $url)) {
            $curl_opts[CURLOPT_SSL_VERIFYHOST] = 0;
            $curl_opts[CURLOPT_SSL_VERIFYPEER] = 0;
        }

        // Override or extend curl options
        if (count($curl_opts_extends) > 0) {
            foreach ($curl_opts_extends as $key => $val) {
                $curl_opts[$key] = $val;
            }
        }

        curl_setopt_array($curl, $curl_opts);

        // Response returned.
        $response = curl_exec($curl);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $curl_error = null;
        if (!empty(curl_error($curl))) {
            $curl_error = curl_error($curl);
        }

        curl_close($curl);

        return array(
            'httpCode' => $httpCode,
            'response' => $response,
            'error' => $curl_error
        );
    }
}
