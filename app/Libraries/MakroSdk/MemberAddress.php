<?php

namespace MakroSdk;

use MakroSdk\Exceptions\SDKException;

class MemberAddress
{
    protected $client;
    const ADDRESS_TYPE_MEMBER = 'member';
    const ADDRESS_TYPE_BUSINESS = 'business';
    const ADDRESS_TYPE_BILL = 'bill';
    const ADDRESS_TYPE_SHIPPING = 'shipping';
    const ADDRESS_TYPE_TAX = 'tax';

    public function __construct(MakroSdk $client)
    {
        $this->client = $client;
    }

    public function getMemberAddresses($params = [])
    {
        $defaults = [
            'address_type' => '',
            'page'         => 1,
            'per_page'     => 20
        ];
        $params = array_merge($defaults, $params);

        $rs = $this->client->api('members/addresses', $params, 'GET');
        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getMemberAddressByAddressId($addressId)
    {
        $rs = $this->client->api("members/addresses/{$addressId}", [], 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function createMemberAddress($params = [])
    {
        $defaults = [
            'address_type'   => 'shipping', // (member|business|bill|shipping)
            'address'        => '',
            'address2'       => '',
            'address3'       => '',
            'subdistrict_id' => '',
            'district_id'    => '',
            'province_id'    => '',
            'country_code'   => 'TH',
            'postcode'       => '',
        ];
        $params = array_merge($defaults, $params);

        $rs = $this->client->api('members/addresses', $params, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function updateMemberAddress($addressId, $params = [])
    {
        $defaults = [
            'address'        => '',
            'address2'       => '',
            'address3'       => '',
            'subdistrict_id' => '',
            'district_id'    => '',
            'province_id'    => '',
            'country_code'   => 'TH',
            'postcode'       => '',
        ];
        //$params = array_only(array_merge($defaults, $params), ['address', 'address2', 'address3', 'subdistrict_id', 'district_id', 'province_id', 'country_code', 'postcode']);
        $params = array_merge($defaults, $params);

        $rs = $this->client->api('members/addresses/' . $addressId, $params, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function deleteMemberAddress($addressId)
    {
        $rs = $this->client->api("members/addresses/{$addressId}", [], 'DELETE');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function saveMemberAddress($param, $type)
    {
        $data = [
            'address_name'      => array_get($param, 'address_name'),
            'first_name'        => array_get($param, 'first_name'),
            'last_name'         => array_get($param, 'last_name'),
            'contact_phone'     => array_get($param, 'contact_phone'),
            'contact_email'     => array_get($param, 'contact_email'),
            'address_type'      => $type,
            'address'           => array_get($param, 'address'),
            'address2'          => array_get($param, 'address2'),
            'address3'          => '-',
            'subdistrict_id'    => array_get($param, 'subdistrict_id'),
            'district_id'       => array_get($param, 'district_id'),
            'province_id'       => array_get($param, 'province_id'),
            'country_code'      => 'TH',
            'postcode'          => array_get($param, 'postcode'),
        ];

        $mode = 'create';
        if (isset($param['id']) && !empty($param['id'])) {
            $mode = 'update';
        }

        $response = null;
        try {
            if ($mode == 'create') {
                $response = $this->createMemberAddress($data);
            } else {
                $response = $this->updateMemberAddress($param['id'], $data);
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $response;
    }
}