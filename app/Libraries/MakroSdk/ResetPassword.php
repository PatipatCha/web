<?php

namespace MakroSdk;

use \Firebase\JWT\JWT;
use MakroSdk\Exceptions\SDKException;

class ResetPassword
{
    protected $client;

    public function __construct(MakroSdk $client)
    {
        $this->client = $client;
    }
    public function requestResetPassword($data)
    {
        $rs = $this->client->api('reset-passwords/request', $data, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function resetPassword($data)
    {
        $rs = $this->client->api('reset-passwords/reset-password', $data, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function checkResetCode($data)
    {
        $rs = $this->client->api('reset-passwords/check-reset-code', $data, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

}
