<?php
/**
 * Created by PhpStorm.
 * User: kinkop
 * Date: 6/27/2017 AD
 * Time: 3:18 PM
 */

namespace MakroSdk;

use MakroSdk\Exceptions\SDKException;

class Banner
{

    protected $client;

    public function __construct(MakroSdk $client)
    {
        $this->client = $client;
    }

    public function get(array $parameters = [])
    {
        $rs = $this->client->api('banners', $parameters, 'GET');

        if ($rs['httpCode'] != 200) {
            $errors = json_decode($rs['response'], true);
            throw new SDKException($errors);
        }

        $response = json_decode($rs['response'], TRUE);

        return $response;
    }

    public function getByGroupPosition($groupSlug)
    {
        $makroSdk = app()->make('makroSdk');

        try {
            //Get group data by group slug
            $response = $makroSdk->group()->findBySlug($groupSlug);
            $bannerGroupCollection = collect($response['data']['content']);
            $slugs = $bannerGroupCollection->unique('value')->implode('value', ',');

            //Get banner by slugs
            $response = $this->get([
                'slugs' => $slugs
            ]);


            //Group position
            $bannerCollection = collect($response['data']);
            $positions = [];

            foreach ($bannerGroupCollection as $item) {
                $banner = $bannerCollection->where('slug', $item['value'])->first();
                if (!empty($banner)) {
                    $position = $banner['position'];
                    if (!isset($positions[$position])) {
                        $positions[$position] = [];
                    }

                    $banner['priority'] = $item['priority'];
                    $positions[$position][] = $banner;
                }

            }
        } catch (\Exception $e) {
            throw $e;
        }

        //Sort A1  (ASC)
        if (isset($positions['A1']) && !empty($positions['A1'])) {
            $positions['A1'] = collect($positions['A1'])->sortBy('priority');
        }


        return $positions;

    }

}