<?php
/**
 * Created by PhpStorm.
 * User: kinkop
 * Date: 5/9/2017 AD
 * Time: 10:16 AM
 */

namespace App\Bootstrap\Helpers;


class StoreHelper
{
    protected static $rememberDays = 365;

    public static function getCurrentStore()
    {
//        if (isset($_COOKIE['_makroclickStore'])) {
//            return intval($_COOKIE['_makroclickStore']);
//        }
//
//        return intval(session()->get('current_store_id', null));

        $storeId = session()->get('current_store_id', null);

        if (!empty($storeId)) {
            return $storeId;
        }

        if (isset($_COOKIE['_makroclickStore'])) {
            return intval($_COOKIE['_makroclickStore']);
        }

        return null;
    }

    public static function setCurrentStore($storeId, $updateCurrentStore = true )
    {
        $storeId = intval($storeId);
        session()->put('current_store_id', $storeId);
        $day = 60 * 60 * 24;
        $data = $storeId;
        $expire = time() + ($day * static::$rememberDays);
        setcookie('_makroclickStore', $data, $expire, '/', config('session.domain'), config('session.secure'));

        //Set current store to API
        if ($updateCurrentStore && !empty(AuthHelper::user())) {
            try {
                $makroSdk = app()->make('makroSdk');
                $makroSdk->member()->setCurrentStore($storeId);
            } catch (\Exception $e) {

            }
        }
    }

    public static function unsetCurrentStore()
    {
        session()->forget('current_store_id');
        setcookie('_makroclickStore', null, -1, '/', config('session.domain'), config('session.secure'));
    }

    public static function getCurrentStorePrice()
    {
        $storeId = session()->get('current_store_price_id', null);

        if (!empty($storeId)) {
            return $storeId;
        }

        if (isset($_COOKIE['_makroclickStorePrice'])) {
            return intval($_COOKIE['_makroclickStorePrice']);
        }

        return null;
    }

    public static function setCurrentStorePrice($storeId, $updateCurrentStore = true )
    {
        $storeId = intval($storeId);
        session()->put('current_store_price_id', $storeId);
        $day = 60 * 60 * 24;
        $data = $storeId;
        $expire = time() + ($day * static::$rememberDays);
        setcookie('_makroclickStorePrice', $data, $expire, '/', config('session.domain'), config('session.secure'));
    }

    public static function unsetCurrentStorePrice()
    {
        session()->forget('current_store_price_id');
        setcookie('_makroclickStorePrice', null, -1, '/', config('session.domain'), config('session.secure'));
    }

    public static function setCurrentDeliveryType($type)
    {
        $storeId = $type;
        session()->put('current_delivery_type', $storeId);
        $day = 60 * 60 * 24;
        $data = $storeId;
        $expire = time() + ($day * static::$rememberDays);
        setcookie('_makroclickDeliveryType', $data, $expire, '/', config('session.domain'), config('session.secure'));
    }

    public static function getCurrentDeliveryType()
    {
        $storeId = session()->get('current_delivery_type', null);

        if (!empty($storeId)) {
            return $storeId;
        }

        if (isset($_COOKIE['_makroclickDeliveryType'])) {
            return $_COOKIE['_makroclickDeliveryType'];
        }

        return null;
    }

}