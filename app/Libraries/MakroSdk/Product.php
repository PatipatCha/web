<?php

namespace MakroSdk;

use MakroSdk\Exceptions\SDKException;

class Product
{
    protected $client;

    public function __construct(MakroSdk $client)
    {
        $this->client = $client;
    }

    public function get($params = [], $method = 'GET')
    {
        $rs = $this->client->api('products', $params, $method);

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getRecommend(array $params = [])
    {
        $rs = $this->client->api('products/recommend', $params, 'GET');
        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function gerPersonalizeRecommend(array $params = [])
    {
        $rs = $this->client->api('products/personalize-recommend', $params, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getDetail($id, $params = [])
    {
        $rs = $this->client->api("products/{$id}", $params, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function increaseView($id) {

        $params = [
            'id' => $id
        ];
        $rs = $this->client->api("products/increase-view", $params, 'PUT');
        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getAutoComplete($params)
    {
        $rs = $this->client->api("products/autocomplete", $params, 'GET');
        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }
}
