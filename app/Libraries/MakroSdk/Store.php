<?php

namespace MakroSdk;

use MakroSdk\Exceptions\SDKException;

class Store
{
    protected $client;

    public function __construct(MakroSdk $client)
    {
        $this->client = $client;
    }

    public function get(array $parameters = [])
    {
        $parameters['with'] = 'address';

        $rs = $this->client->api('stores', $parameters, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    /*
     * $storeId = makro ship node not ms store id (mongo)
     */
    public function getById($storeId, array $parameters = [])
    {
        $rs = $this->client->api("stores/{$storeId}", $parameters, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getAvailableRegister(array $parameters = [])
    {
        $rs = $this->client->api('stores/available-register', $parameters, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }
}
