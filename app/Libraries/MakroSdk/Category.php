<?php

namespace MakroSdk;

use MakroSdk\Exceptions\SDKException;

class Category
{
    protected $client;

    public function __construct(MakroSdk $client)
    {
        $this->client = $client;
    }

    public function getRootCategories(array $parameters = [])
    {
        $rs = $this->client->api('categories/root', $parameters, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function tree(array $parameters = [])
    {
        $rs = $this->client->api('categories/tree', $parameters, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getCategory($category_id, array $parameters = [])
    {
        $rs = $this->client->api("categories/{$category_id}", $parameters, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getCategoryBySlug($slug, array $parameters = [])
    {
        $rs = $this->client->api("categories/slug/{$slug}", $parameters, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getCategories(array $category_ids, array $parameters = [])
    {
        $parameters['id'] = $category_ids;
        $rs = $this->client->api('categories', $parameters, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }
}
