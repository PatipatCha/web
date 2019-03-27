<?php

namespace MakroSdk;

use MakroSdk\Exceptions\SDKException;

class Subscribe
{
    protected $client;

    public function __construct(MakroSdk $client)
    {
        $this->client = $client;
    }

    public function create($email)
    {
        $parameters = [
            'email' => $email
        ];

        $rs = $this->client->api('subscribe', $parameters, 'POST');
        
        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }
}
