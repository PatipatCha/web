<?php

namespace MakroSdk;

use MakroSdk\Exceptions\SDKException;

class Notification
{
    protected $client;

    public function __construct(MakroSdk $client)
    {
        $this->client = $client;
    }

    public function sendSms(array $parameters = [])
    {
        $rs = $this->client->api('notifications/sms/send', $parameters, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function sendOtp(array $parameters = [])
    {
        $rs = $this->client->api('notifications/otp/send', $parameters, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function resendOtp(array $parameters = [])
    {
        $rs = $this->client->api('notifications/otp/resend', $parameters, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function verifyOtp(array $parameters = [])
    {
        $rs = $this->client->api('notifications/otp/verify', $parameters, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function sendContactMessage(array $parameters = [])
    {
        $params = [
            'issue_date' => date('Y-m-d H:i:s')
        ];
        $parameters = array_merge($parameters, $params);

        $rs = $this->client->api('notifications/contact-message', $parameters, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }
}
