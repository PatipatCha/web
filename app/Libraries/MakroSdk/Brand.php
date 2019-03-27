<?php

namespace MakroSdk;

use MakroSdk\Exceptions\SDKException;

class Brand
{
    protected $client;

    public function __construct(MakroSdk $client)
    {
        $this->client = $client;
    }

    public function get(array $parameters = [])
    {
        $rs = $this->client->api('brands', $parameters, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getById($id, $params = [])
    {
        $rs = $this->client->api("brands/{$id}", $params, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getBySlug($slug, array $parameters = [])
    {
        $rs = $this->client->api("brands/slug/{$slug}", $parameters, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }
}
