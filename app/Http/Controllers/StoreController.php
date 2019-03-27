<?php

namespace App\Http\Controllers;

use App\Bootstrap\Helpers\StoreHelper;
use Breadcrumbs;
use Illuminate\Http\Request;
use Cache;

class StoreController extends BaseController
{
    public function index()
    {
        $makroSdk = app()->make('makroSdk');

        try {
            $response = $makroSdk->store()->get(['status' => 'active', 'limit' => 2000]);

            if (!empty($response['data'])) {
                $response = $response['data'];
            } else {
                $response = null;
            }
        } catch (\Exception $e) {
            $response = null;
        };

        $stores = collect($response);

        return response()->json($stores);
    }

    public function setCurrentStore(Request $request)
    {
        if ($request->has('store_id')) {
            StoreHelper::setCurrentStore($request->get('store_id'));
        }

        if ($request->has('store_price_id')) {
            StoreHelper::setCurrentStorePrice($request->get('store_price_id'));
        }

        if ($request->has('delivery_type')) {
            StoreHelper::setCurrentDeliveryType($request->get('delivery_type'));
        }
    }

    public function getCurrentStore()
    {
        return response()->json([
            'status' => 'ok',
            'data' => [
                'store_id' => StoreHelper::getCurrentStore(),
                'store_price_id' => StoreHelper::getCurrentStorePrice(),
            ]
        ]);
    }

    public function getLocation()
    {
        $data = [];
        $stores = collect([]);
        $lists = [];
        $makroSdk = app()->make('makroSdk');

        try {
            $response = $makroSdk->store()->get(['status' => 'active', 'with' => 'address', 'limit' => 2000]);

            if (!empty($response['data'])) {
                $response = $response['data'];
            } else {
                $response = null;
            }
        } catch (\Exception $e) {
            $response = null;
        }

        if (empty($response)) {
            abort(404);
        }

        $stores = collect($response);

        $this->addBreadcrumb('stores.location', trans('frontend.store_location'), route('stores.location'));

        $items = $stores->filter(function ($value, $key) {
            return $value['address']['original_location'] != null;
        })->toArray();
        $data['items'] = [];
        foreach ($items as $key => $item) {
            $item = collect($item)->only(['id', 'name', 'address', 'contact_phone', 'contact_fax'])->toArray();
            $item['address'] = collect($item['address'])->only('location', 'id', 'postcode', 'address', 'address2', 'address3', 'subdistrict', 'district', 'province')->toArray();
            $lists[str_replace("\xc2\xa0", '', trim($item['address']['province']))][$item['id']] = $item;
            $data['items'][$key] = $item;
            $data['items'][$key]['full_address'] = '<b>' . array_get($item, 'name') . '</b><br>' . array_get($item, 'address.address') . ' ' . array_get($item, 'address.address2') . ' ' . array_get($item, 'address.address3') . '<br>' . array_get($item, 'address.subdistrict') . ' ' . array_get($item, 'address.district') . ' ' . array_get($item, 'address.province') . '<br>' . trans('frontend.phone') . ' : ' . array_get($item, 'contact_phone', '-') . '<br>' . trans('frontend.fax') . ' : ' . array_get($item, 'contact_fax', '-');
        }

        // Sort lists
        $lists = array_sort($lists, function ($value) {
            $r = array_first($value);
            return $r['address']['province'];
        });
        $data['lists'] = $lists;

        return view('store.location', $data);
    }

    public function setCurrentStoreRedirect(Request $request)
    {
        StoreHelper::setCurrentStore(intval($request->input('store_id')));

        return redirect()->back();
    }

    public function selectStore(Request $request, $id)
    {
        StoreHelper::setCurrentStore($id);

        return redirect()->route('home.index');
    }

   public function getPickupStore()
   {
        $makroSdk = app()->make('makroSdk');
        $params = [
            'status' => 'active', 
            'limit' => 20000, 
            'with' => 'address',
            'pickup' => 'Y'
        ];

       try {
           $response = $makroSdk->store()->get($params);

           if (!empty($response['data'])) {
               $response = $response['data'];
           } else {
               $response = null;
           }
       } catch (\Exception $e) {
           $response = null;
       };

        $stores = collect($response);

        return response()->json($stores);
   } 
}