<?php

namespace MakroSdk;

use MakroSdk\Exceptions\SDKException;

class Content
{
    protected $client;

    public function __construct(MakroSdk $client)
    {
        $this->client = $client;
    }

    public function all(array $parameters = [])
    {
        $rs = $this->client->api('contents', $parameters, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function findBySlug($slug, array $parameters = [])
    {
        $rs = $this->client->api("contents/{$slug}", $parameters, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }
}
