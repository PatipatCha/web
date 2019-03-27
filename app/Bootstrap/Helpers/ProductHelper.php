<?php
/**
 * Created by PhpStorm.
 * User: kinkop
 * Date: 5/12/2017 AD
 * Time: 10:43 AM
 */

namespace App\Bootstrap\Helpers;


class ProductHelper
{
    protected static $sorterMap = [
        'order' => 'order_score',
        'newest' => 'created_date',
        'price' => 'normal_price'
    ];

    public static function getListParameters()
    {
        $param = [];

        //Sorter
        if (request()->has('sorter_field')) {
            if (array_key_exists(request()->input('sorter_field'), static::$sorterMap)) {
                $param['sort'] = static::$sorterMap[request()->input('sorter_field')] . ' ' . (request()->has('sorter_direction') ? request()->input('sorter_direction') : 'asc');
            }
        }

        return $param;
    }

}