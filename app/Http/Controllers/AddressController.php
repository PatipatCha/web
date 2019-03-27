<?php

namespace App\Http\Controllers;

use Breadcrumbs;
use Illuminate\Http\Request;

class AddressController extends BaseController
{
    public function getProvinces()
    {
        try {
            $makroSdk = app()->make('makroSdk');

            $response = $makroSdk->address()->getProvinces();
            $provinces = collect(array_values(collect($response['data'])->sortBy('name')->toArray()));
            $filter = $provinces->filter(function ($item, $key) {
                return $item['province_id'] == 1;
            });
            $key = $filter->keys()->first();
            $bankkok = $provinces->splice($key, 1);
            $provinces->prepend($bankkok->first());
        } catch (\Exception $e) {

        }
        return response()->json($provinces);
    }


    public function getCityById(Request $request)
    {
        try {
            $makroSdk = app()->make('makroSdk');
            $data = [
                'province_id' => empty($request['province_id']) ? '' : $request['province_id']
            ];
            $cities = $makroSdk->address()->getDistricts($data);
            $cities = collect($cities['data'])->sortBy('name')->toArray();
        } catch (\Exception $e) {

        }
        return response()->json(array_values($cities));
    }

    public function SubDistrictById(Request $request)
    {
        try {
            $makroSdk = app()->make('makroSdk');
            $data = [
                'district_id' => empty($request['district_id']) ? '' : $request['district_id']
            ];
            $SubDistricts = $makroSdk->address()->getSubDistricts($data);
            $SubDistricts['data'] = collect($SubDistricts['data'])->sortBy('name')->toArray();
        } catch (\Exception $e) {

        }
        return response()->json(array_values($SubDistricts['data']));
    }


    public function getDeliveryProvinces()
    {
        try {
            $makroSdk = app()->make('makroSdk');

            $response = $makroSdk->address()->getDeliveryProvinces();
            $provinces = collect(array_values(collect($response['data'])->sortBy('name')->toArray()));
            $filter = $provinces->filter(function ($item, $key) {
                return $item['province_id'] == 1;
            });
            $key = $filter->keys()->first();
            $bankkok = $provinces->splice($key, 1);
            $provinces->prepend($bankkok->first());
        } catch (\Exception $e) {

        }
        return response()->json($provinces);
    }


    public function getDeliveryCityById(Request $request)
    {
        try {
            $makroSdk = app()->make('makroSdk');
            $data = [
                'province_id' => empty($request['province_id']) ? '' : $request['province_id']
            ];
            $cities = $makroSdk->address()->getDeliveryDistricts($data);
            $cities = collect($cities['data'])->sortBy('name')->toArray();
        } catch (\Exception $e) {

        }
        return response()->json(array_values($cities));
    }

    public function getDeliverySubDistrictById(Request $request)
    {
        try {
            $makroSdk = app()->make('makroSdk');
            $data = [
                'district_id' => empty($request['district_id']) ? '' : $request['district_id']
            ];
            $SubDistricts = $makroSdk->address()->getDeliverySubDistricts($data);
            $SubDistricts['data'] = collect($SubDistricts['data'])->sortBy('name')->toArray();
        } catch (\Exception $e) {

        }
        return response()->json(array_values($SubDistricts['data']));
    }

    public function getDeliveryByPostcode(Request $request)
    {
        $addresses = [];
        try {
            $makroSdk = app()->make('makroSdk');
            $postcode = $request->get('postcode');

            $addresses = $makroSdk->address()->searchDeliveryArea(
                [
                    'q' => $postcode,
                    'per_page' => 10000
                ]);

            $addresses = array_get($addresses, 'data', []);
        } catch (\Exception $e) {
            $errorCode = null;
            $message = trans('frontend.could_not_get_delivery_address_please_try_again', ['code' => $e->getCode()]);
            if ($e instanceof SDKException) {
                $errorCode = $e->getErrorCode();
            }

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'errors' => is_callable([$e, 'getErrors']) ? $e->getErrors() : null,
                'error_code' => $errorCode,
            ]);
        }

        return response()->json($addresses);
    }
}