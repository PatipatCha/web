<?php

namespace MakroSdk;

use MakroSdk\Exceptions\SDKException;

class Gateway
{
    protected $client;

    public function __construct(MakroSdk $client)
    {
        $this->client = $client;
    }

    public function requestPayment($orderNo, array $parameters = [])
    {
        $params = [
            'order_no' => $orderNo,
            'parameters' => $parameters
        ];

        $rs = $this->client->api('gateway/request-payment', $params, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function foregroundResult($driver, array $parameters = [])
    {
        $params = [
            'driver' => $driver,
            'parameters' => $parameters
        ];

        $rs = $this->client->api('gateway/foreground-result', $params, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function backgroundResult($driver, array $parameters = [])
    {
        $params = [
            'driver' => $driver,
            'parameters' => $parameters
        ];

        $rs = $this->client->api('gateway/background-result', $params, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }
}
