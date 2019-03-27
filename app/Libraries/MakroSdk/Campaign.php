<?php

namespace MakroSdk;

use MakroSdk\Exceptions\SDKException;

class Campaign
{
    protected $client;

    public function __construct(MakroSdk $client)
    {
        $this->client = $client;
    }

    public function all(array $parameters = [])
    {
        $rs = $this->client->api('campaigns', $parameters, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function findBySlug($slug, array $parameters = [])
    {
        $parameters = array_merge(['include_products' => 1], $parameters);

        $rs = $this->client->api("campaigns/{$slug}", $parameters, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }
}
