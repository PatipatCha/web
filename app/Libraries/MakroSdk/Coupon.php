<?php

namespace MakroSdk;

use MakroSdk\Exceptions\SDKException;

class Coupon
{
    protected $client;

    public function __construct(MakroSdk $client)
    {
        $this->client = $client;
    }

    public function checkAvailable($couponCode, array $parameters = [])
    {
        $rs = $this->client->api("coupons/{$couponCode}/check", $parameters, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }
}
