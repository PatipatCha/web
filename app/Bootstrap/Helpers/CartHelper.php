<?php

namespace App\Bootstrap\Helpers;

use Carbon\Carbon;
use Cache;

class CartHelper
{
    public static $PRODUCT_STOCKED = 'stocked';
    public static $PRODUCT_PREORDER = 'preorder';

    public static function getCartGroup($items, $reserveData = [], $checkProductContent = true, $namedId = true)
    {
        $group = [];
        if (!empty($items)) {
            foreach ($items as $item) {
                if ($checkProductContent) {
                    //Assortment
                    if (isset($item['content']['data'])
                        && !empty($item['content']['data'])
                    )
                    {
                        if (isset($item['content']['data']['delivery_group'])) {
                            if (strtolower($item['content']['data']['delivery_group']) == 'preorder') {
                                //Group preorder
                                static::groupPreorder($group, $item, $namedId);
                            } else if (strtolower($item['content']['data']['delivery_group']) == 'instock') {
                                //Group stocked
                                static::groupStocked($group, $item, $namedId);
                            } else {
                                //Group to anonymous (just use for count items)
                                static::groupAnonymous($group, $item);
                            }
                        } else {
                            //Group to anonymous (just use for count items)
                            static::groupAnonymous($group, $item);
                        }
                    } else {
                        //Group to anonymous (just use for count items)
                        static::groupAnonymous($group, $item);
                    }
                } else {
                    //Group to anonymous (just use for count items)
                    static::groupAnonymous($group, $item);
                }
            }
        }

        usort($group, ['static', 'sort']);

        if (empty($reserveData)) {
            return $group;$id = 'id="lbl_information_pre_order_product"';
            if (!$namedId) {
                $id = '';
            }

            $idSidebar = 'id="lbl_pre_order_information"';
            if (!$namedId) {
                $idSidebar = '';
            }
        }

        return static::getReallyPickupDate($group);
    }

    protected  static function groupStocked(&$group, $item, $namedId = true)
    {
        $id = 'id="lbl_information_ready_to_ship_product"';
        if (!$namedId) {
            $id = '';
        }

        $idSidebar = 'id="lbl_ready_to_ship_information"';
        if (!$namedId) {
            $idSidebar = '';
        }
        if (!isset($group[static::$PRODUCT_STOCKED])) {
            $group[static::$PRODUCT_STOCKED] = [
                'title' => __('frontend.stoked_product_title'),
                'description' => '<div ' . $id . '>' . __('frontend.stocked_product_description') . '</div>',
                'shipping_description' => '<div id="lbl_infomation_ready_to_ship_product">' . __('frontend.you_will_be_contacted_by_phone_to_confirm_delivery_date') . '</div>',
                'items' => [],
                'order' => 1,
                'alias' => static::$PRODUCT_STOCKED,
                'id_btn' => 'tip_ready_to_ship_product',
                'delivery_date_label' => 'lbl_ready_to_ship_product_days',
                'sidebar_id_btn' => 'tip_ready_to_ship_information',
                'sidebar_description' => '<div ' . $idSidebar . '>' . __('frontend.stocked_product_description') . '</div>',
                'product_detail_description' => __('frontend.product_detail_stocked_product_description')
            ];
        }

        $group[static::$PRODUCT_STOCKED]['items'][] = $item;
    }

    protected  static function groupPreorder(&$group, $item, $namedId = true)
    {
        $id = 'id="lbl_information_pre_order_product"';
        if (!$namedId) {
            $id = '';
        }

        $idSidebar = 'id="lbl_pre_order_information"';
        if (!$namedId) {
            $idSidebar = '';
        }

        if (!isset($group[static::$PRODUCT_PREORDER])) {
            $group[static::$PRODUCT_PREORDER] = [
                'title' => __('frontend.preorder_product_title'),
                'description' => '<div ' . $id . '>' . __('frontend.preorder_product_description') . '</div>',
                'shipping_description' => '<div id="lbl_infomation_ready_to_ship_product">' . __('frontend.you_will_be_contacted_by_phone_to_confirm_delivery_date') . '</div>',
                'items' => [],
                'order' => 2,
                'alias' => static::$PRODUCT_PREORDER,
                'id_btn' => 'tip_pre_order_product',
                'delivery_date_label' => 'lbl_pre_order_product_days',
                'sidebar_id_btn' => 'tip_pre_order_information',
                'sidebar_description' => '<div ' . $idSidebar . '>' . __('frontend.preorder_product_description') . '</div>',
                'product_detail_description' => __('frontend.product_detail_preorder_product_description')
            ];
        }

        $group[static::$PRODUCT_PREORDER]['items'][] = $item;
    }

    public static function getCartGroup_old($items, $reserveData = [], $checkProductContent = true)
    {
        $group = [];
        if (!empty($items)) {
            foreach ($items as $item) {
                if ($checkProductContent) {
                    //Assortment
                    if (isset($item['content']['data'])
                        && !empty($item['content']['data'])
                    )
                    {

                        $product = $item['content']['data'];
                        $item['content']['data']['pickup_date'] = static::getPickupDate($item['content']['data']['item_id'], $reserveData);
                        if (isset($product['type'])) {
                            if ($product['type'] == 'assortment') {
                                //Assortment
                                static::assortmentGroup($group, $item);
                            } else {
                                //Non-Assortment (Need to check Installment)
                                static::nonAssortment($group, $item);
                            }
                        } else {
                            //Non-Assortment (Need to check Installment)
                            static::nonAssortment($group, $item);
                        }
                    } else {
                        //No product content (Not show on the cart)
                    }
                } else {
                    //Group to anonymous (just use for count items)
                    static::groupAnonymous($group, $item);
                }
            }
        }

        usort($group, ['static', 'sort']);

        if (empty($reserveData)) {
            return $group;
        }

        return static::getReallyPickupDate($group);
    }

    protected static function groupAnonymous(&$group, $item)
    {
        if (!isset($group['stocked'])) {
            $group['stocked'] = [
                'title' => 'stocked',
                'items' => [],
                'order' => 1,
                'alias' => 'stocked'
            ];
        }

        $group['stocked']['items'][] = $item;
    }

    protected static function sort($a, $b)
    {
        if ($a['order'] == $b['order']) {
            return 0;
        }

        return ($a['order'] < $b['order']) ? -1 : 1;
    }

    protected static function getReallyPickupDate($groups)
    {
        $groupTemp = [];
        foreach ($groups as $group) {
            $dates = array_map(function ($item) {
                return strtotime($item['content']['data']['pickup_date']);
            }, $group['items']);

            $maxDate = max($dates);
            $startDate = date('Y-m-d', $maxDate);
            $group['group_pickup_start_date'] = $startDate;
            $group['group_pickup_end_date'] = date('Y-m-d', strtotime('+6 day', $maxDate));
            $groupTemp[$group['alias']] = $group;
        }

        return $groupTemp;
    }

    protected static function nonAssortment(&$group, $item)
    {
        if (isset($item['content']['data'])
            && isset($item['content']['data']['type'])
        )
        {
            if (strtolower($item['content']['data']['type']) == 'non_assortment_with_install') {
                //Non-Assortment with installment
                static::nonAssortmentWithInstallmentGroup($group, $item);
            } else {
                //Non-Assortment without installment
                static::nonAssortmentWithoutInstallmentGroup($group, $item);
            }
        } else {
            //Non-Assortment without installment
            static::nonAssortmentWithoutInstallmentGroup($group, $item);
        }
    }

    protected static function assortmentGroup(&$group, $item)
    {
        if (!isset($group['assortment'])) {
            $group['assortment'] = [
                'title' => __('frontend.assortment'),
                'items' => [],
                'order' => 1,
                'alias' => 'assortment'
            ];
        }

        $group['assortment']['items'][] = $item;
    }

    protected static function nonAssortmentWithInstallmentGroup(&$group, $item)
    {
        if (!isset($group['non_assortment_with_installment'])) {
            $group['non_assortment_with_installment'] = [
                'title' => __('frontend.non_assortment_with_installment'),
                'items' => [],
                'order' => 2,
                'alias' => 'non_assortment_with_installment'
            ];
        }

        $group['non_assortment_with_installment']['items'][] = $item;
    }

    protected static function nonAssortmentWithoutInstallmentGroup(&$group, $item)
    {
        if (!isset($group['non_assortment_without_installment'])) {
            $group['non_assortment_without_installment'] = [
                'title' => __('frontend.non_assortment_without_installment'),
                'items' => [],
                'order' => 3,
                'alias' => 'non_assortment_without_installment'
            ];
        }

        $group['non_assortment_without_installment']['items'][] = $item;
    }

    protected static function getPickupDate($productId, $reserveData)
    {
        if (isset($reserveData[$productId]['pickup_date'])) {
            return $reserveData[$productId]['pickup_date'];
        }

        return '';
    }

    public static function saveReserveData($reserveData)
    {
        session()->put('makro_click_reserve_data', $reserveData);
    }

    public static function getReserveData()
    {
        return session()->get('makro_click_reserve_data', null);
    }

    public static function generateOrderBarcode($orderNo, $barcodeNo)
    {
        return static::generateOrderBarcodeNew($orderNo, $barcodeNo);
    }

    public static function generateOrderBarcodeNewResize($orderNo, $barcodeNo, $width = 225, $height = 100)
    {
        $filename = "images/barcode/{$orderNo}.jpg";

        if (! file_exists(public_path($filename))) {
            try {
                $scale = $width / 10;

                $barcode = new \App\Libraries\PHPBarcode\Barcode($barcodeNo, $scale);

                $oldWidth = ImageSX($barcode->image());
                $oldHeight = ImageSY($barcode->image());

                if (($oldWidth / $width) > ($oldHeight / $height)) { // ต้องตั้งความกว้างใหม่
                    $width = ($height * $oldWidth) / $oldHeight;
                } else { // ต้องตั้งความสูงใหม่
                    $height = ($width * $oldHeight) / $oldWidth;
                }

                $thumb = imagecreatetruecolor($width, $height);
                $source = $barcode->image();

                imagecopyresized($thumb, $source, 0, 0, 0, 0, $width, $height, $oldWidth, $oldHeight);

                ImageJPEG($thumb, public_path($filename), 95);
            } catch (\Exception $e) {

            }
        }

        return url($filename);
    }

    public static function generateOrderBarcodeNew($orderNo, $barcodeNo, $scale = 2.1)
    {
        $filename = "images/barcode/{$orderNo}.jpg";

        if (! file_exists(public_path($filename))) {
            try {
                $barcode = new \App\Libraries\PHPBarcode\Barcode($barcodeNo, $scale);
                ImageJPEG($barcode->image(), public_path($filename), 95);
            } catch (\Exception $e) {

            }
        }

        return url($filename);
    }

    public static function getCartStepClass($currentStep, $stepToCheck) {
        if ($currentStep == 4) {
            return 'visited';
        }
        if ($currentStep == $stepToCheck) {
            return 'current';
        }

        if ($currentStep > $stepToCheck) {
            return 'visited';
        }

        return '';
    }

    public static function getCartStepLink($currentStep, $stepToCheck, $text, $link)
    {
        if ($currentStep > $stepToCheck) {
            //return "<a href=\"{$link}\"><em>{$text}</em></a>";
        }

        return "<em>{$text}</em>";
    }



    public static function savePaymentData($data)
    {
        session()->put('makro_click_payment_data', $data);
    }

    public static function getPaymentData()
    {
        return session()->get('makro_click_payment_data', null);
    }

    public static function saveShippingData($data)
    {
        session()->put('makro_click_shipping_data', $data);
    }

    public static function getShippingData()
    {
        return session()->get('makro_click_shipping_data', null);
    }

    /*
    * Convert reserve detail to cart  data
    */
    public static function convertReserveDetailDataToCartData($reserveDetail)
    {
        $data['meta'] = [
            'promotions' => $reserveDetail['data']['items']['promotions'],
            'summary' => $reserveDetail['data']['items']['summary']
        ];

        $cartItems = [];
        foreach ($reserveDetail['data']['items']['data'] as $item) {
            $quantity = 0;
            if (isset($item['price']) && isset($item['price']['quantity'])) {
                $quantity = $item['price']['quantity'];
            } else if (isset($item['order_detail'])) {
                $quantity = $item['order_detail']['quantity'];
            }

            $price = [];
            if (isset($item['price']) && is_array($item['price'])) {
                $price = $item['price'];
            } else if (isset($item['order_detail'])) {
                $price = [
                    'price' => $item['order_detail']['price'],
                    'normal_price' => $item['order_detail']['normal_price']
                ];
            }

            $title = '';
            if (isset($item['title'])) {
                $title = $item['title'];
            } else if (isset($item['order_detail'])) {
                $title = $item['order_detail']['name'];
            }
            $item['title'] = $title;
            $cartItems[] = [
                'content_id' => $item['item_id'],
                'quantity' => $quantity,
                'content' => [
                    'data' => $item
                ],
                'price' => [
                    'data' => $price
                ]
            ];
        }


        $data['data'] = $cartItems;

        return $data;
    }

    public static function convertDetailDataToCartData($reserveDetail)
    {
        $data['meta'] = [
            'promotions' => $reserveDetail['data']['items']['promotions'],
            'summary' => $reserveDetail['data']['items']['summary']
        ];

        $cartItems = [];
        foreach ($reserveDetail['data']['items']['data'] as $item) {
            $quantity = $item['quantity'];

            $price = [
                'price' => $item['price'],
                'normal_price' => $item['normal_price']
            ];

            $title = '';
            if (isset($item['title'])) {
                $title = $item['title'];
            } else if (isset($item['order_detail'])) {
                $title = $item['order_detail']['name'];
            } else if (isset($item['name'])) {
                $title = $item['name'];
            }
            $item['title'] = $title;
            $cartItems[] = [
                'content_id' => $item['item_id'],
                'quantity' => $quantity,
                'content' => [
                    'data' => $item
                ],
                'price' => [
                    'data' => $price
                ]
            ];
        }

        $data['data'] = $cartItems;

        return $data;
    }

    public static function getCheckoutData($reserveDetail)
    {
        $paymentData = static::getPaymentData();
        $shippingData = static::getShippingData();
        $reserveData = static::getReserveData();

        //Convert reserve detail items to cart items
        $cart = static::convertReserveDetailDataToCartData($reserveDetail);
        $reserveItems = static::generateReserveData($reserveDetail['data']['items']['data']);
        $cartGroup = static::getCartGroup($cart['data'], $reserveItems);


        $data = [];

        //Store ID
        $data['store_id'] = $reserveData['store_id'];


        //Reserve ID
        $data['reserve_id'] = $reserveData['id'];

        //Buyer information
        $data['buyer_information'] = $paymentData['member_address'];

        //Assortment (รับสินค้าที่สาขา)
        $data['product_assortment'] = isset($shippingData['product_assortment']) ? $shippingData['product_assortment'] : null;
        if (!empty($data['product_assortment'])) {
            $date = explode('/', $data['product_assortment']['pickup_date']);
            $data['product_assortment']['pickup_date'] = $date[2] . '-' . $date[1] . '-' . $date[0];
            $time = explode('-', $data['product_assortment']['pickup_time']);
            $data['product_assortment']['pickup_time_start'] = $time[0];
            $data['product_assortment']['pickup_time_end'] = $time[1];
            unset($data['product_assortment']['pickup_time']);

            $startDate = '';
            $endDate = '';
            if (isset($cartGroup['assortment'])) {
                $startDate = $cartGroup['assortment']['group_pickup_start_date'];
                $endDate = $cartGroup['assortment']['group_pickup_end_date'];
            }

            $data['product_assortment']['extn_date_start'] = $startDate;
            $data['product_assortment']['extn_date_end'] = $endDate;
        }


        //Non Assortment Without Installment  (จองสินค้า รับสินค้าที่สาขา)
        $data['product_none_assortment_without_installment'] = isset($shippingData['product_non_assortment_without_installment']) ? $shippingData['product_non_assortment_without_installment'] : null;
        if (!empty($data['product_none_assortment_without_installment'])) {
            $date = explode('/', $data['product_none_assortment_without_installment']['pickup_date']);
            $data['product_none_assortment_without_installment']['pickup_date'] = $date[2] . '-' . $date[1] . '-' . $date[0];
            $time = explode('-', $data['product_none_assortment_without_installment']['pickup_time']);
            $data['product_none_assortment_without_installment']['pickup_time_start'] = $time[0];
            $data['product_none_assortment_without_installment']['pickup_time_end'] = $time[1];
            unset($data['product_none_assortment_without_installment']['pickup_time']);

            $startDate = '';
            $endDate = '';
            if (isset($cartGroup['assortment'])) {
                $startDate = $cartGroup['assortment']['group_pickup_start_date'];
                $endDate = $cartGroup['assortment']['group_pickup_end_date'];
            }

            $data['product_none_assortment_without_installment']['extn_date_start'] = $startDate;
            $data['product_none_assortment_without_installment']['extn_date_end'] = $endDate;
        }

        //Non Assortment With Installment (ส่งสินค้าที่บ้าน)
        $data['product_none_assortment_with_installment'] = null;
        if (isset($shippingData['product_non_assortment_with_installment'])) {
            $deliveryMethods = [
                'same_customer_address' => 'same_as_buyer_address',
                'other' => 'other_address'
            ];
            $data['product_none_assortment_with_installment']['method'] = $deliveryMethods[$shippingData['product_non_assortment_with_installment']['delivery_method']];
            $data['product_none_assortment_with_installment']['address'] = null;

            switch ($data['product_none_assortment_with_installment']['method']) {
                case 'same_as_buyer_address':
                    $data['product_none_assortment_with_installment']['address'] = $paymentData['member_address'];
                    break;
                case 'other_address':
                    //Get shipping address
                    try {
                        $id = request()->input('shipping_address_id');
                        $makroSdk = $makroSdk = app()->make('makroSdk');
                        $shippingAddress = $makroSdk->memberAddress()->getMemberAddressByAddressId($id);

                        if (!isset($shippingAddress['data']) || empty($shippingAddress['data'])) {
                            throw new \Exception('Shipping address not found.');
                        }

                        $data['product_none_assortment_with_installment']['address'] = [
                            "id" => $shippingAddress['data']['id'],
                            "shop_name" => $shippingAddress['data']['address_name'],
                            "first_name" => $shippingAddress['data']['first_name'],
                            "last_name" => $shippingAddress['data']['last_name'],
                            "phone" =>  $shippingAddress['data']['contact_phone'],
                            "email" => $shippingAddress['data']['contact_email'],
                            "address_line1" => $shippingAddress['data']['address'],
                            "address_line2" => $shippingAddress['data']['address2'],
                            "address_line3" => "-",
                            "road_street" => "",
                            "township" => $shippingAddress['data']['subdistrict'],
                            "city" => $shippingAddress['data']['district'],
                            "province" => $shippingAddress['data']['province'],
                            "postcode" => $shippingAddress['data']['postcode'],
                            "subdistrict_id" =>  $shippingAddress['data']['original_subdistrict']['id'],
                            "district_id" => $shippingAddress['data']['original_district']['id'],
                            "province_id" => $shippingAddress['data']['original_province']['id']
                        ];
                    } catch (\Exception $e) {
                        throw $e;
                    }
                    break;
            }
        }

        //Get delivery address
        $data['delivery_address'] = null;
        if (array_get($reserveDetail, 'data.delivery_type', 'shipping') == 'shipping') {
            if (isset($paymentData['shipping_address_data']) && !empty($paymentData['shipping_address_data'])) {
                $data['delivery_address'] = static::getShippingAddressData($paymentData['shipping_address_data']);
            } else {
                if (empty($shippingAddress)) {
                    $shipping_address_id = request()->input('shipping_address_id', array_get($paymentData, 'shipping_address_id'));

                    if (empty($shipping_address_id)) {
                        throw new \Exception('Shipping address is empty.');
                    }

                    try {
                        $makroSdk = app()->make('makroSdk');
                        $shippingAddress = $makroSdk->memberAddress()->getMemberAddressByAddressId($shipping_address_id);

                        if (empty($shippingAddress['data'])) {
                            throw new \Exception('Shipping address not found.');
                        }
                    } catch (\Exception $e) {
                        throw $e;
                    }
                }

                $data['delivery_address'] = static::getShippingAddressData($shippingAddress['data']);
            }

            if (! empty($data['delivery_address']['email'])) {
                try {
                    $data['delivery_address']['email'] = MakroHelper::validateEmail($data['delivery_address']['email']);
                } catch (\Exception $e) {
                    throw $e;
                }
            }
        }

        //Billing address
        $data['billing_address'] = isset($paymentData['billing_address']) ? $paymentData['billing_address'] : null;

        if (!empty($data['billing_address'])) {
            $data['billing_address']['phone'] = $data['buyer_information']['phone'];
        }


        //Tax invoice
        $data['tax_invoice'] = [
            'required' => isset($paymentData['required_tax_invoice']) ? boolval($paymentData['required_tax_invoice']) : false,
            'address' => null
        ];

        if (!empty($paymentData['invoice_address'])) {
            $data['tax_invoice']['address'] = $paymentData['invoice_address'];
            $data['tax_invoice']['address']['phone'] = $data['buyer_information']['phone'];
        }

        $data['cart_items'] = $cart['data'];
        $data['cart_summary'] = $cart['meta']['summary'];

        return $data;
    }

    protected static function getShippingAddressData($shippingAddress)
    {
        $data = [
            "id" => array_get($shippingAddress, 'id'),
            "shop_name" => array_get($shippingAddress, 'address_name'),
            "first_name" => array_get($shippingAddress, 'first_name'),
            "last_name" => array_get($shippingAddress, 'last_name'),
            "phone" =>  array_get($shippingAddress, 'contact_phone'),
            "email" => array_get($shippingAddress, 'contact_email'),
            "address_line1" => array_get($shippingAddress, 'address'),
            "address_line2" => array_get($shippingAddress, 'address2'),
            "address_line3" => "-",
            "road_street" => "",
            "township" => array_get($shippingAddress, 'subdistrict'),
            "city" => array_get($shippingAddress, 'district'),
            "province" => array_get($shippingAddress, 'province'),
            "postcode" => array_get($shippingAddress, 'postcode'),
            "subdistrict_id" =>  array_get($shippingAddress, 'original_subdistrict.id'),
            "district_id" => array_get($shippingAddress, 'original_district.id'),
            "province_id" => array_get($shippingAddress, 'original_province.id')
        ];

        return $data;
    }

    public static function generateReserveData($reserveDetailItems)
    {
        $newData = [];
        foreach ($reserveDetailItems as $item) {
            $newData[$item['item_id']] = $item;
        }

        return $newData;
    }

    public static function getOrderDetailData($orderDetail, $cartGroup)
    {
        $data = [];
        $data['order_no'] = $orderDetail['detail']['order_no'];

        //TODO: ชั่วคราว  เดี๋ยวต้องแยก format วันที่ ไปเป็น function เพื่อเรียกใช้หลายที่
        $orderDate = new Carbon($orderDetail['detail']['order_date']);
        $data['order_date'] = strtoupper($orderDate->format('j M Y'));

        $data['pickup_type_names'] = static::getPickupTypeNames($orderDetail, $cartGroup);
        $data['buyer'] = $orderDetail['detail']['buyer'];
        $data['store'] = $orderDetail['detail']['store'];
        $data['shipping_address'] = isset($orderDetail['detail']['shipping_address']) ? $orderDetail['detail']['shipping_address'] : [];
        $data['tax_address'] = $orderDetail['detail']['tax_address'];
        $data['billing_address'] = $orderDetail['detail']['billing_address'];
        $data['payment_type'] = __('frontend.payment_type_' . strtolower($orderDetail['detail']['payment']['payment_type']), ['store_name' => $orderDetail['detail']['store']['name']]);
        $data['barcode'] = static::generateOrderBarcode($data['order_no'], $orderDetail['barcode_no']);

        //dd($orderDetail);

        return $data;
    }

    public static function getPickupTypeNames($orderDetail, $productTypes)
    {
        $shippingType = array_get($orderDetail, 'detail.delivery_type', 'delivery');

        if ($shippingType == 'pickup') {
            return __('frontend.pick_up_at_your_own_branch');
        }

        return __('frontend.shipping_type_name');
    }


    public static function getFullAddress($address)
    {
        $addressLine1 = '';
        if (isset($address['address'])) {
            $addressLine1 = $address['address'];
        } else if (isset($address['address_line1'])) {
            $addressLine1 = $address['address_line1'];
        }

        // $addressLine2 = '';
        // if (isset($address['address2'])) {
        //     $addressLine2 = $address['address2'];
        // } else if (isset($address['address_line2'])) {
        //     $addressLine2 = $address['address_line2'];
        // }

        $subDistrict = '';
        if (isset($address['subdistrict'])) {
            $subDistrict = $address['subdistrict'];
        }

        if (isset($address['address_line4'])) {
            $subDistrict = $address['address_line4'];
        }

        $district = '';
        if (isset($address['city'])) {
            $district = $address['city'];
        }

        $province = '';
        if (isset($address['state'])) {
            $province = $address['state'];
        } else if (isset($address['province'])) {
            $province = $address['province'];
        }

        $postCode = '';
        if (isset($address['zip_code'])) {
            $postCode = $address['zip_code'];
        } else if (isset($address['postcode'])) {
            $postCode = $address['postcode'];
        }

        $fullAddresses = [];

        if (!empty($addressLine1)) {
            $fullAddresses[] = $addressLine1;
        }

        // if (!empty($addressLine2)) {
        //     $fullAddresses[] = $addressLine2;
        // }

        if (!empty($subDistrict)) {
            $fullAddresses[] = $subDistrict;
        }

        if (!empty($district)) {
            $fullAddresses[] = $district;
        }

        if (!empty($province)) {
            $fullAddresses[] = $province;
        }

        if (!empty($postCode)) {
            $fullAddresses[] = $postCode;
        }

        return implode(', ', $fullAddresses);
    }

    /*
     * Convert order detail to cart  data
     */
    public static function convertOrderDetailDataToCartData($orderDetail)
    {
        $cartData = [
            'data' => [
                'items' => [
                    'promotions' => array_get($orderDetail, 'detail.promotions', []),
                    'summary' => array_get($orderDetail, 'detail.summary', []),
                    'data' => array_get($orderDetail, 'detail.items', [])
                ]
            ]
        ];

        if($orderDetail['convert_type'] == 'detail'){
            $cart = static::convertDetailDataToCartData($cartData);
        }else{
            $cart = static::convertReserveDetailDataToCartData($cartData);
        }

        return $cart;
    }

    public static function getContinueShoppingUrl()
    {
        return route('home.index');
        //Get continue shopping url

        /*$acceptUrl = '/(category|campaign|search|product|brand)/i';
        $continueShoppingUrl = '';
        if (isset($_SERVER['HTTP_REFERER'])
            && !empty($_SERVER['HTTP_REFERER'])
            && preg_match($acceptUrl, $_SERVER['HTTP_REFERER'])
        ) {
            $continueShoppingUrl = $_SERVER['HTTP_REFERER'];
        } else {
            $continueShoppingUrl = route('home.index');
        }

        return $continueShoppingUrl;*/
    }

    public static function getShippingRateInfos($shippingRates)
    {
//        $shippingRates = [
//            [
//                'min' => 0,
//                'fee' => 0
//            ],
//            [
//                'min' => 1000,
//                'fee' => 0
//            ],
//            [
//                'min' => 2000,
//                'fee' => 0
//            ],
//        ];

        $texts = [];
        foreach ($shippingRates as $index => $shippingRate) {
            $min = $shippingRate['min'];
            $fee = $shippingRate['fee'];
            $feeFormat = number_format($fee);
            $minFormat = number_format($min);

            $text = '';
            if (sizeof($shippingRates) == 1) {
                //Flat rate
                if ($fee <= 0) {
                    $text = __('frontend.delivery_fee_flat_rate_free');
                } else {
                    $text = __('frontend.delivery_fee_flat_rate_has_fee', ['fee' => $feeFormat]);
                }
            } else {
                if ($index == 0) {
                    if ($fee <= 0) {
                        $text = __('frontend.delivery_fee_first_level_free', ['min' => number_format($shippingRates[$index + 1]['min'])]);
                    } else {
                        $text = __('frontend.delivery_fee_first_level_has_fee', ['min' => number_format($shippingRates[$index + 1]['min']), 'fee' => $feeFormat]);
                    }
                } else {
                    //Middle level
                    if (isset($shippingRates[$index + 1])) {
                        if ($fee <= 0) {
                            $text = __('frontend.delivery_fee_middle_level_free', ['min' => $minFormat, 'max' => number_format($shippingRates[$index + 1]['min'])]);
                        } else {
                            $text = __('frontend.delivery_fee_middle_level_has_fee', ['min' => $minFormat, 'max' => number_format($shippingRates[$index + 1]['min']), 'fee' => $feeFormat]);
                        }
                    } else {
                        //Last level
                        if ($fee <= 0) {
                            $text = __('frontend.delivery_fee_last_level_free', ['min' => $minFormat, 'fee' => $feeFormat]);
                        } else {
                            $text = __('frontend.delivery_fee_last_level_has_fee', ['min' => $minFormat, 'fee' => $feeFormat]);
                        }
                    }
                }
            }

            $texts[] = $text;
        }

        return $texts;
    }

    public static function getDeliveryDates($datas, $isConvertAlias = true)
    {
//        $datas['data'] = [
//            [
//                "group" => "product_instock",
//                "min" => 1,
//                "max" => 2,
//            ],
//            [
//                "group" => "product_preorder",
//                "min" => 3,
//                "max" => 5
//            ]
//        ];

        $convertAlias = [
            'product_instock' => static::$PRODUCT_STOCKED,
            'product_preorder' => static::$PRODUCT_PREORDER
        ];

        $originalAlias = [
            'product_instock' => 'instock' ,
            'product_preorder' => 'preorder'
        ];

        $groupData = null;
        if (!empty($datas['data'])) {
            foreach ($datas['data'] as $data) {
                $alias = $originalAlias[$data['group']];
                if ($isConvertAlias) {
                    $alias = $convertAlias[$data['group']];
                }
                $groupData[$alias] = __('frontend.delivery_estimated_dates', ['min' => $data['min'], 'max' => $data['max']]);
            }
            /*$thaiDate = new ThaiDate();
            foreach ($datas['data'] as $data) {
                $minDate = Carbon::now()->addDays($data['min']);
                $maxDate = Carbon::now()->addDays($data['max']);

                if (\App::isLocale('th')) {
                    //Is min, max same year
                    if ($minDate->year == $maxDate->year) {
                        //Not show year on min date
                        $minDate = $thaiDate->date('d M', $minDate->timestamp);
                    } else {
                        //Show year on min date
                        $minDate = $thaiDate->date('d M y', $minDate->timestamp);
                    }
                    $maxDate = $thaiDate->date('d M y', $maxDate->timestamp);
                } else {
                    //Is min, max same year
                    if ($minDate->year == $maxDate->year) {
                        //Not show year on min date
                        $minDate = $minDate->format('j M');
                    } else {
                        //Show year on min date
                        $minDate = $minDate->format('j M Y');
                    }
                    $maxDate = $maxDate->format('j M Y');
                }

                $data['min_date'] = $minDate;
                $data['max_date'] = $maxDate;

                $groupData[$convertAlias[$data['group']]] = $data;
            }*/
        }

        return $groupData;
    }

    public static function generateOrderItem($cartItems)
    {
        foreach ($cartItems as $index => $item) {
            //Get delivery date
            $cartItems[$index]['content']['data']['est_delivery_date']['delivery_date'] = static::getEstimateDeliveryDate(array_get($item, 'content.data'));

            //Get item status
            $cartItems[$index]['content']['data']['item_status_text'] = static::getOrderItemStatus(array_get($item, 'content.data'));

            //Get item status icon
            $cartItems[$index]['content']['data']['item_status_icon'] = static::getOrderItemStatusIcon(array_get($item, 'content.data'));

            //Is item cancelled
            $cartItems[$index]['content']['data']['is_cancelled'] =  static::isOrderCancelled(array_get($item, 'content.data'));
        }

        return $cartItems;
    }

    public static function getEstimateDeliveryDate($item)
    {
        $thaiDate = new ThaiDate();
        $isCancel = static::isOrderCancelled($item);

        //If item is cancelled then no estimate delivery date
        if ($isCancel) {
            return '';
        }

        $deliveryDate = '';
        try {
            $estMinDate = array_get($item, 'est_delivery_date.min');
            $estMaxDate = array_get($item, 'est_delivery_date.max');

            $sameDate = false;
            $minDate = Carbon::parse($estMinDate);
            $maxDate = Carbon::parse($estMaxDate);

            if ($minDate->diffInDays($maxDate) < 1) {
                $sameDate = true;
            }

            if (\App::isLocale('th')) {
                if ($sameDate) {
                    $deliveryDate = $thaiDate->date('j M Y', strtotime($estMinDate));
                } else {
                    $deliveryDate = $thaiDate->date('j M', strtotime($estMinDate));
                    $deliveryDate .= ' - ' . $thaiDate->date('j M Y', strtotime($estMaxDate));
                }

            } else {
                if ($sameDate) {
                    $deliveryDate = $maxDate->format('j M Y');
                } else {
                    $deliveryDate = $minDate->format('j M');
                    $deliveryDate .= ' - ' . $maxDate->format('j M Y');
                }
            }
        } catch (\Exception $e) {

        }

        return $deliveryDate;
    }

    public static function getOrderItemStatus($item)
    {
        if (empty(array_get($item, 'item_status', 'pending'))) {
            return '';
        }

        $fastestEstimateDate = Carbon::createFromFormat('Y-m-d H:i:s', $item['est_delivery_date']['min']);
        $latestEstimateDate = Carbon::createFromFormat('Y-m-d H:i:s', $item['est_delivery_date']['max']);

        if (app()->isLocale('th')) {
            $thaiDate = new ThaiDate;
            $thaiDate->buddhist_era = true;

            $estimatedDate = $thaiDate->date('d M', $fastestEstimateDate->getTimestamp());

            if ($fastestEstimateDate->format('Y') !== $latestEstimateDate->format('Y')) {
                $estimatedDate .= ' ';
                $estimatedDate .= $thaiDate->date('Y', $fastestEstimateDate->getTimestamp());
            }

            $estimatedDate .= ' - ';
            $estimatedDate .= $thaiDate->date('d M Y', $latestEstimateDate->getTimestamp());
        } else {
            $estimatedDate = $fastestEstimateDate->format('d M');

            if ($fastestEstimateDate->format('Y') !== $latestEstimateDate->format('Y')) {
                $estimatedDate .= ' ';
                $estimatedDate .= $fastestEstimateDate->format('Y');
            }

            $estimatedDate .= ' - ';
            $estimatedDate .= $latestEstimateDate->format('d M Y');
        }

        $statusText = __('frontend.order_item_status_' . array_get($item, 'delivery_type', 'delivery') . '_' . array_get($item, 'item_status', 'pending'), ['estimatedDate' => $estimatedDate ]);

        return $statusText;
    }

    public static function getOrderItemStatusIcon($item)
    {
        if (empty(array_get($item, 'item_status', 'pending'))) {
            return null;
        }

        if (array_get($item, 'delivery_type', 'delivery') == 'pickup') {
            switch (array_get($item, 'item_status', 'pending')) {
                case 'create_template':
                    $icon = asset('images/item_status_created.png');
                    break;
                case 'ready_for_backroom_pickup':
                case 'ready_for_packing': // N/A
                case 'packed': // ไม่มีข้อมูลในไฟล์ excel
                    $icon = asset('images/item_status_in_progress.png');
                    break;
                case 'ready_for_customer_pickup':
                    $icon = asset('images/item_status_in_progress.png'); //พร้อมรับสินค้า
                    break;
                case 'shipped':
                    $icon = asset('images/item_status_completed.png');
                    break;
                case 'delivered': // N/A
                    $icon = asset('images/item_status_completed.png');
                    break;
                case 'cancel_order':
                    $icon = asset('images/item_status_canceled.png');
                    break;
                case 'pending':
                default:
                    $icon = asset('images/item_status_pending.png');
            }
        } else {
            switch (array_get($item, 'item_status', 'pending')) {
                case 'create_template':
                    $icon = asset('images/item_status_created.png');
                    break;
                case 'ready_for_backroom_pickup':
                case 'ready_for_packing':
                case 'packed': // ไม่มีข้อมูลในไฟล์ excel
                    $icon = asset('images/item_status_in_progress.png');
                    break;
                case 'ready_for_customer_pickup': // N/A
                    $icon = asset('images/item_status_in_progress.png');
                    break;
                case 'shipped':
                    $icon = asset('images/item_status_shipping.png');
                    break;
                case 'delivered':
                    $icon = asset('images/item_status_completed.png');
                    break;
                case 'cancel_order':
                    $icon = asset('images/item_status_canceled.png');
                    break;
                case 'pending':
                default:
                    $icon = asset('images/item_status_pending.png');
            }
        }

        return $icon;
    }

    public static function isOrderCancelled($item)
    {
        if (array_get($item, 'item_status') == 'cancel_order') {
            return true;
        }

        return false;
//        $sumCancel = intval(array_get($item, 'cancel.summary.quantity', 0));
//        $sumRefund = intval(array_get($item, 'refund.summary.quantity', 0));
//        $quantity = intval(array_get($item, 'quantity', 0));
//
//        if (($sumCancel + $sumRefund) >= $quantity) {
//            return true;
//        }
//
//        return false;
    }

    public static function getMaximumBasket()
    {
        $params = [
            "config_type" => "order",
            "status" => "active",
            "name" => "limit_item_per_order"
        ];

        $cacheKey = json_encode($params);

        $max = Cache::tags(['global', 'config'])->remember("getMaximumBasket_{$cacheKey}", 1440, function () use ($params) {
            try {
                $makroSdk = app()->make('makroSdk');
                $config = $makroSdk->config()->get($params);

                $config_data = head(array_get($config, 'data', []));
                $max = array_get($config_data, 'value');

                if (! is_null($max)) {
                    return $max;
                }
            } catch (\Exception $e) {
                throw $e;
            }

            return null;
        });

        return is_null($max) ? 150 : $max;
    }
}