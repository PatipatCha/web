<?php

namespace MakroSdk;

use MakroSdk\Exceptions\SDKException;

class Config
{
    protected $client;

    public function __construct(MakroSdk $client)
    {
        $this->client = $client;
    }

    public function get(array $parameters = [])
    {
        $response = [];

        $rs = $this->client->api('configs/', $parameters, 'GET');

        if (isset($rs['httpCode']) && $rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        if (isset($rs['response'])) {
            $response = json_decode($rs['response'], TRUE);
        }

        return $response;
    }

    public function getPaymentMethods(array $parameters = [])
    {
        $response = [];

        $rs = $this->client->api("configs/payment-methods", $parameters, 'GET');

        if (isset($rs['httpCode']) && $rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        if (isset($rs['response'])) {
            $response = json_decode($rs['response'], TRUE);
        }

        return $response;
    }

    public function getShippingRate(array $parameters = [])
    {
        $response = [];

        $rs = $this->client->api('configs/shipping-rate', $parameters, 'GET');

        if (isset($rs['httpCode']) && $rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        if (isset($rs['response'])) {
            $response = json_decode($rs['response'], TRUE);
        }

        return $response;
    }

    public function getDeliveryDate(array $parameters = [])
    {
        $response = [];

        $rs = $this->client->api('configs/delivery-date', $parameters, 'GET');

        if (isset($rs['httpCode']) && $rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        if (isset($rs['response'])) {
            $response = json_decode($rs['response'], TRUE);
        }

        return $response;
    }

    public function getPickupDate(array $parameters = [])
    {
        $response = [];

        $rs = $this->client->api('configs/pickup-date', $parameters, 'GET');

        if (isset($rs['httpCode']) && $rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        if (isset($rs['response'])) {
            $response = json_decode($rs['response'], TRUE);
        }

        return $response;
    }

    public function getMaintenancePage(array $parameters = [])
    {
        $response = [];

        $rs = $this->client->api('configs/maintenancepage', $parameters, 'GET');

        if (isset($rs['httpCode']) && $rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        if (isset($rs['response'])) {
            $response = json_decode($rs['response'], TRUE);
        }

        return $response;
    }
}
