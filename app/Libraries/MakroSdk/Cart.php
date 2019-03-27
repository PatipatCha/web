<?php

namespace MakroSdk;

use App\Bootstrap\Helpers\AuthHelper;
use MakroSdk\Exceptions\SDKException;

class Cart
{
    protected $client;
    const CART_TYPE_ONLY_CART = '';
    const CART_TYPE_GENERAL = 'content,price';
    const CART_TYPE_PROMOTION = 'content,cart_price';

    public function __construct(MakroSdk $client)
    {
        $this->client = $client;
    }

    public function get($store_id, $member_id, $additionalParameters = [], $type = '', $deliveryType = null)
    {
        $data = [
            'store_id' => $store_id,
            'session_id' => AuthHelper::tempMemberId()
        ];

        if (!empty($deliveryType)) {
            $data['delivery_type'] = $deliveryType;
        }

        $data = array_merge($data, $additionalParameters);

//        dd($data);

        if (! empty($type)) {
            $data['includes'] = $type;
        }

        $rs = $this->client->api('carts', $data, 'GET');
        $response = json_decode($rs['response'], TRUE);

        if ($rs['httpCode'] != 200 || array_get($response, 'code', 0) != 0) {
            throw new SDKException($response);
        }

        return $response;
    }

    public function getItem($id)
    {

    }

    public function getMinimumCheckout()
    {
        $rs = $this->client->api('carts/minimum-checkout', [], 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function addItem($data)
    {
        $rs = $this->client->api('carts/items', $data, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function addItems($data)
    {
        $rs = $this->client->api('carts/items/multiple', $data, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function updateItem($item_id, $quantity = 1)
    {
        $rs = $this->client->api("carts/items/{$item_id}", ['quantity' => $quantity], 'PUT');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function deleteItem($item_id)
    {
        $rs = $this->client->api("carts/items/{$item_id}", [], 'DELETE');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function clear($store_id, $member_id)
    {
        $rs = $this->client->api("carts/clear", ['store_id' => $store_id, 'member_id' => $member_id], 'DELETE');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function reCreateCart($data)
    {
        $rs = $this->client->api('carts/re-create', $data, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function checkItemStatus($data)
    {
        $rs = $this->client->api('carts/items/check-status', $data, 'POST');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }
}