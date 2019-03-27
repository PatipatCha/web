<?php

namespace MakroSdk;

use App\Bootstrap\Helpers\MakroHelper;
use \Firebase\JWT\JWT;
use MakroSdk\Exceptions\SDKException;

class Member
{
    protected $client;

    public function __construct(MakroSdk $client)
    {
        $this->client = $client;
    }

    public function profile()
    {
        $rs = $this->client->api('members/profile', array(), 'GET');
        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function updateProfile($parameter)
    {
        if (isset($parameter['birth_day'])) {
            if (preg_match('/\//', $parameter['birth_day'])) {
                $date = explode('/', $parameter['birth_day']);
                $parameter['birth_day'] = $date[2] . '-' . $date[1] . '-' . $date[0];
            }
        }

        $rs = $this->client->api('members/profile', $parameter, 'PUT');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function updateMakroCardId($makroCardId)
    {
        $parameter['makro_card_id'] = $makroCardId;
        $rs = $this->client->api('members/makro-card', $parameter, 'PUT');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function updateBusiness($parameter)
    {
        $rs = $this->client->api('members/business', $parameter, 'PUT');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function create(array $attributes)
    {
        if (! empty($attributes['password'])) {
            $attributes['password'] = MakroHelper::encryptPassword($attributes['password']);
        }

        $rs = $this->client->api('members', $attributes, 'POST');


        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getMakroCardInfo($cardId, $parameter = [])
    {
        $rs = $this->client->api("makro-card/{$cardId}", $parameter, 'GET');
        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function isFacebookUserIdAlreadyExists($facebookUserId)
    {
        $rs = $this->client->api("members/facebook-user-id-exists/" . $facebookUserId, array(), 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);
        return $response;
    }

    public function isUsernameExists($emailOrPhone)
    {
        $rs = $this->client->api("members/username-exists/" . $emailOrPhone, array(), 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);
        return $response;
    }

    public function login(array $params)
    {
        $apiParams = [
            'username' => $params['username'],
            'password' => MakroHelper::encryptPassword($params['password'])
        ];
        $rs = $this->client->api('auth/login', $apiParams, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], true);
        return $response;
    }

    public function logout()
    {
        $apiParams = [];

        $rs = $this->client->api('auth/logout', $apiParams, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);
        return $response;
    }

    public function facebookLogin($facebookId)
    {
        $apiParams = [
            'facebook_id' => $facebookId
        ];
        $rs = $this->client->api("auth/connect", $apiParams, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);
        return $response;
    }

    public function activateEmail($memberId, $activateCode, $forceLogin = false)
    {
        $attributes = [
            'member_id' => $memberId,
            'activate_code' => $activateCode,
            'login' => $forceLogin,
        ];

        $rs = $this->client->api('members/activate/email', $attributes, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function activatePhone($memberId, $otpRef, $otp, $forceLogin = false)
    {
        $attributes = [
            'member_id' => $memberId,
            'ref' => $otpRef,
            'otp' => $otp,
            'login' => $forceLogin,
        ];

        $rs = $this->client->api('members/activate/phone', $attributes, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function activateResend($username)
    {
        $attributes = [
            'username' => $username,
        ];

        $rs = $this->client->api('members/activate/resend', $attributes, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getDefaultStore(array $parameter = [])
    {
        $rs = $this->client->api('/members/default-store', $parameter, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function setCurrentStore($storeId)
    {
        $parameter = [
            'store_id' => $storeId
        ];

        $rs = $this->client->api('/members/set-current-store', $parameter, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }
}
