<?php

namespace MakroSdk;

use App\Bootstrap\Helpers\StoreHelper;
use Faker\Generator;
use MakroSdk\Exceptions\SDKException;

class Address
{
    protected $client;

    public function __construct(MakroSdk $client)
    {
        $this->client = $client;
    }

    public function getProvinces($params = [])
    {
        $rs = $this->client->api('addresses/provinces', $params, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getDistricts($params = [])
    {
        $defaults = [
            'province_id' => ''
        ];
        $params = array_merge($defaults, $params);

        $rs = $this->client->api('addresses/districts', $params, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getSubDistricts($params = [])
    {
        $defaults = [
            'district_id' => '',
            'postcode'    => ''
        ];
        $params = array_merge($defaults, $params);

        $rs = $this->client->api('addresses/sub-districts', $params, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getProvinceById($provinceId)
    {
        $rs = $this->client->api("addresses/provinces/{$provinceId}", [], 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getDistrictById($districtId)
    {
        $rs = $this->client->api("addresses/districts/{$districtId}", [], 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getSubDistrictById($subDistrictId)
    {
        $rs = $this->client->api("addresses/sub-districts/{$subDistrictId}", [], 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function checkDeliveryLocation($params = [])
    {
        $rs = $this->client->api('addresses/check-delivery-location', $params, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getDeliveryProvinces($params = [])
    {
        $rs = $this->client->api('addresses/delivery/provinces', $params, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getDeliveryDistricts($params = [])
    {
        $defaults = [
            'province_id' => ''
        ];
        $params = array_merge($defaults, $params);

        $rs = $this->client->api('addresses/delivery/districts', $params, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getDeliverySubDistricts($params = [])
    {
        $defaults = [
            'district_id' => '',
            'postcode'    => ''
        ];
        $params = array_merge($defaults, $params);

        $rs = $this->client->api('addresses/delivery/sub-districts', $params, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getDeliveryProvinceById($provinceId)
    {
        $rs = $this->client->api("addresses/delivery/provinces/{$provinceId}", [], 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getDeliveryDistrictById($districtId)
    {
        $rs = $this->client->api("addresses/delivery/districts/{$districtId}", [], 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getDeliverySubDistrictById($subDistrictId)
    {
        $rs = $this->client->api("addresses/delivery/sub-districts/{$subDistrictId}", [], 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function searchDeliveryArea($parameters = [])
    {
        $rs = $this->client->api('addresses/delivery/search', $parameters, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }
}