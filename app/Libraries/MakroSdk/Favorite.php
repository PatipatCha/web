<?php

namespace MakroSdk;

use MakroSdk\Exceptions\SDKException;

class Favorite
{
    protected $client;

    public function __construct(MakroSdk $client)
    {
        $this->client = $client;
    }

    public function get($data)
    {
        $rs = $this->client->api('favorites', $data, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function add($data)
    {
        $rs = $this->client->api('favorites/items', $data, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function remove($data)
    {
        $rs = $this->client->api("favorites/items/", $data, 'DELETE');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }
}