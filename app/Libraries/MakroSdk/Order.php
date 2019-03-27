<?php

namespace MakroSdk;

use MakroSdk\Exceptions\SDKException;

class Order
{
    protected $client;

    public function __construct(MakroSdk $client)
    {
        $this->client = $client;
    }

    public function get($parameters)
    {
        $rs = $this->client->api("orders", $parameters, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function create($parameters)
    {
        $rs = $this->client->api("orders", $parameters, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getOrderDetail($order_no, $parameters = [])
    {
        $rs = $this->client->api("orders/{$order_no}", $parameters, 'get');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function update($orderId, $parameters)
    {
        $rs = $this->client->api("orders/{$orderId}", $parameters, 'PUT');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function delete($orderId)
    {
        $parameters = [];

        $rs = $this->client->api("orders/{$orderId}", $parameters, 'DELETE');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function reserve($parameters)
    {
        $rs = $this->client->api('reserves', $parameters, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function cancelReserve($reserveId)
    {
        $parameters = [];

        $rs = $this->client->api("reserves/{$reserveId}", $parameters, 'DELETE');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getReserveDetail($reserveId, $parameters = [])
    {
        $rs = $this->client->api("reserves/{$reserveId}", $parameters, 'get');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function notify($orderNo, array $parameters = [])
    {
        $rs = $this->client->api("orders/{$orderNo}/notify-status", $parameters, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }
}
