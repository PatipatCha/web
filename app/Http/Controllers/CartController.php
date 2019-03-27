<?php

namespace App\Http\Controllers;

use App\Bootstrap\Helpers\AuthHelper;
use App\Bootstrap\Helpers\CartHelper;
use App\Bootstrap\Helpers\MakroHelper;
use App\Bootstrap\Helpers\StoreHelper;
use App\Bootstrap\Helpers\MaHelper;
use Illuminate\Http\Request;
use MakroSdk\Cart;
use Log;
use MakroSdk\Exceptions\SDKException;
use MakroSdk\MemberAddress;

class CartController extends BaseController
{
    protected $storeId;
    protected $memberId;
    protected $groupLimit = 5;
    protected $reserveData;
    protected $checkProductContent = false;

    protected $paymentMethodMapper = [
        'credit_card' => 'CC',
        'pay_at_store' => 'PayAtStore'
    ];

    protected $reversePaymentMethodMapper = [
        'CC' => 'credit_card',
        'PayAtStore' => 'pay_at_store'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->storeId = StoreHelper::getCurrentStore();
        $this->memberId = AuthHelper::getMemberId(false);
        $this->addBreadcrumb('carts.index', trans('frontend.cart'), route('carts.index'));
    }

    /*
     * Step1:  In Cart
     */

    public function index(Request $request)
    {
        //dd(StoreHelper::getCurrentStore(), StoreHelper::getCurrentStorePrice());
        $makroSdk = app()->make('makroSdk');
        $response = null;
        $summary = null;

        try {
            $cartData = $this->getCartData(Cart::CART_TYPE_GENERAL, StoreHelper::getCurrentDeliveryType());
            $response = $cartData['response'];
            $summary = $cartData['summary'];
        } catch (\Exception $e) {
            if (in_array($e->getCode(),
                ['205002', '205404', '100002', '216001', '208001', '209404', '200404', '216404', '219404'])) {
                return view('cart.connection-error', ['step' => 1, 'code' => $e->getCode()]);
            }
        }

        $shippingRates = [];
        try {
            $minimumCheckout = $this->getMinimumCheckout();
            $shippingRates = $makroSdk->config()->getShippingRate();
            $shippingRates = $shippingRates['data'];
        } catch (\Exception $e) {
            $minimumCheckout = [];
        }

        try {
            $maximumCheckout = CartHelper::getMaximumBasket();
        } catch (\Exception $e) {
            // What dose to do if got getMaximumBasket exception.
            return view('cart.connection-error', ['step' => 1, 'code' => $e->getCode()]);
        }

        $data['data'] = [];
        $data['data']['cart'] = isset($response['data']) ? $response['data'] : [];
        $data['data']['summary'] = $summary;
        $data['data']['minimumCheckout'] = empty($minimumCheckout['data']['attributes']['minimum_checkout']) ? 0 : $minimumCheckout['data']['attributes']['minimum_checkout'];
        $data['data']['maximumCheckout'] = $maximumCheckout;
        $data['meta'] = isset($response['meta']) ? $response['meta'] : [];
        $data['data']['cart'] = $this->groupCartData($response);
        $data['data']['cart_count'] = $this->getCartCount($data['data']['cart']);

        $deliveryDate = null;
        $pickupDate = null;
        try {
            $deliveryDate = $makroSdk->config()->getDeliveryDate();
        } catch (\Exception $e) {
            return view('cart.connection-error', ['step' => 1, 'code' => $e->getCode()]);
        }
        try {
            $pickupDate = $makroSdk->config()->getPickupDate();
        } catch (\Exception $e) {
            return view('cart.connection-error', ['step' => 1, 'code' => $e->getCode()]);
        }

        $data['data']['dateConfig']['deliveryDate'] = array_get($deliveryDate, 'data', []);
        $data['data']['dateConfig']['pickupDate'] = array_get($pickupDate, 'data', []);
        //Get continue shopping url
        $data['continue_shopping_url'] = CartHelper::getContinueShoppingUrl();
        $data['confirm_change_store'] = true;
        $data['shipping_rates'] = CartHelper::getShippingRateInfos($shippingRates);

        return view('cart.index', $data);
    }

    /*
     * Get cart with json response
     */
    public function getCart()
    {
        $type = '';
        if ($this->checkProductContent === true) {
            $type = Cart::CART_TYPE_GENERAL;
        }

        try {
            $cartData = $this->getCartData($type);
        } catch (\Exception $e) {
            $cartData = [
                'response' => null,
                'summary' => null,
            ];
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Successful',
            'data' => $this->groupCartData($cartData['response'], $this->checkProductContent),
            'summary' => $cartData['summary']
        ]);
    }

    /*
     * Get cart data
     */
    protected function getCartData($type = Cart::CART_TYPE_GENERAL, $deliveryType = null)
    {
        $response = [];
        $summary = null;

        try {
            $response = $this->getCartItems($type, $deliveryType);
            $summary = $this->getCartSummaryData($response);
        } catch (\Exception $e) {
            throw $e;
        }

        //Get default cart summary data
        if (!isset($summary)) {
            $summary = $this->getCartSummaryData(false);
        }

        if (isset($response['data'])) {
            foreach ($response['data'] as $index => $data) {
                $response['data'][$index] = $data;
                if (isset($response['data'][$index]['content'])) {
                    $response['data'][$index]['content']['data']['url'] = route('products.show', [
                        'id' => $data['content']['data']['item_id'],
                        'name' => urlencode(str_replace(['/', '#'], '-', $data['content']['data']['name']))
                    ]);

                }
            }
        }

        return [
            'response' => $response,
            'summary' => $summary
        ];
    }

    protected function getCartCount($cartDatas)
    {
        $count = 0;
        foreach ($cartDatas as $cartData) {
            $count += sizeof($cartData['items']);
        }

        return $count;
    }

    /*
     * Step 2:  Payment
     */
    public function getPayment(Request $request)
    {
        //Log Start
        $user = AuthHelper::user();
        $username = array_get($user, 'username', 'unknown');
        $logName = 'getPayment';
        $fileName = str_replace(['@', '.'], '-', $username) . '-pass';
        $logs = [
            'log_step' => '1001',
        ];
        $this->writeCheckoutLog($logName, $fileName, $logs);
        //Log End

        // เช็ค เวลาก่อน Deploy  return true / false
        $maPage = MaHelper::getMaPage();

        if ($maPage['success']) {

            $request->session()->flash('show_popup_error', 1);

            return redirect(route('carts.index'))->withErrors([
                __('frontend.mapage_error_on', [
                    'end_datetime_th' => $maPage['message']['end_datetime_th'],
                    'end_datetime_en' => $maPage['message']['end_datetime_en']
                ])
            ]);
        }

        //Check cart must not empty
        try {
            $cart = $this->getCartItems(Cart::CART_TYPE_PROMOTION, StoreHelper::getCurrentDeliveryType());
        } catch (\Exception $e) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'getPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0001',
                'Exception.getCode' => $e->getCode(),
                'Exception.getMessage' => $e->getMessage(),
            ];

            if ($e instanceof SDKException) {
                $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                $logs['SDKException.getCode'] = $e->getCode();
                $logs['SDKException.getErrors'] = $e->getErrors();
            }

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            $errorMessage = __('frontend.cart_is_empty');

            if (in_array($e->getCode(), ['205002', '205404', '100002', '216001', '208001', '209404', '200404', '216404', '219404'])) {
                return view('cart.connection-error', ['step' => 2, 'code' => $e->getCode()]);
            } else if ($e->getCode() == 100002) { //Can not call RM3
                $request->session()->flash('show_popup_error', 1);
                $errorMessage = trans('frontend.can_not_get_cart_because_can_not_call_rm3', ['code' => $e->getCode()]);
            } else {
                $errorMessage .= ' (' . $e->getCode() . ')';
            }

            return redirect(route('carts.index'))->withErrors([$errorMessage]);
        }

        if (empty($cart['data'])) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'getPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0002',
                'error' => 'empty cart.data',
            ];

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return redirect(route('carts.index'))->withErrors([
                __('frontend.cart_is_empty')
            ]);
        }

        try {
            $maximumCheckout = CartHelper::getMaximumBasket();
        } catch (\Exception $e) {
            return view('cart.connection-error', ['step' => 2, 'code' => $e->getCode()]);
        }

        // check maximum product in basket
        if (count($cart['data']) > intval($maximumCheckout)) {
            $errorMessage = trans('frontend.maximum_product_in_cart', ['items' => $maximumCheckout]);
            return redirect(route('carts.index'))->withErrors([$errorMessage]);
        }
        //Group product
        $data['cart'] = CartHelper::getCartGroup($cart['data']);
        $data['cart_summary'] = $this->getCartSummaryData($cart);
        $data['cart_promotions'] = $cart['meta']['promotions'];

        //Check order amount
        if (!$this->checkOrderAmount($data['cart'])) {
            return redirect(route('carts.index'))->withErrors([
                __('frontend.please_check_order_amount')
            ]);
        }

        //Check minimum checkout amount is passed
        $checkMinimumCheckout = $this->checkMinimumCheckout($cart);
        if (!$checkMinimumCheckout['status']) {
            return redirect(route('carts.index'))->withErrors([
                __('frontend.minimum_checkout_warning_message', ['minimum' => $checkMinimumCheckout['amount']])
            ]);
        }

        //Get datas
        $makroSdk = app()->make('makroSdk');
        $stores = [];
        $profile = [];
        $memberAddresses = null;
        $billingAddress = null;
        $taxAddress = null;
        $paymentMethods = [];
        $makroCardInfo = null;
        $hasMakroCard = false;
        $isCreatedSiebel = 0;

        try {
            $memberAddresses = $this->getBuyerInformation();
            $billingAddress = $this->getBillingAddress();
            $profile = $makroSdk->member()->profile();
            AuthHelper::updateUserFromProfile($profile);
            $taxAddress = $this->getTaxAddress($profile);
            $paymentMethods = $makroSdk->config()->getPaymentMethods(['status' => 'active']);
            $paymentMethods = $paymentMethods['data'];
            $isCreatedSiebel = isset($profile['is_created_siebel']) && strtolower($profile['is_created_siebel']) == 'y' ? 1 : 0;
            //Get makro card info if makro card ID is not empty
            if (!empty($profile['makro_member_card'])) {
                $makroCardInfoResult = $this->getMakroCardInfo($profile['makro_member_card']);
                $hasMakroCard = $makroCardInfoResult['has_makro_card'];
                $makroCardInfo = $makroCardInfoResult['makro_card_info'];
            }

        } catch (\Exception $e) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'getPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0003',
                'Exception.getMessage' => $e->getMessage(),
            ];

            $message = $e->getMessage();

            if ($e instanceof SDKException) {
                $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                $logs['SDKException.getCode'] = $e->getCode();
                $logs['SDKException.getErrors'] = $e->getErrors();

                $message = $e->getUserMessage();
            }

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            if ($e instanceof SDKException) {
                $errorCode = $e->getCode();

                if (str_is('219*', $errorCode)) {
                    $request->session()->flash('show_popup_error', 1);

                    return redirect(route('carts.index'))->withErrors([
                        trans('frontend.makro_card_error', ['code' => $errorCode])
                    ]);
                } else {
                    $request->session()->flash('show_popup_error', 1);

                    return redirect(route('carts.index'))->withErrors([
                        trans('frontend.please_contact_call_center_sorry_for_your_inconvenience_with_code', ['code' => $errorCode])
                    ]);
                }
            }

            abort(400, $message);
        }

        //Get shipping addresses
        $data['shipping_addresses'] = [];
        try {
            $makroSdk = app()->make('makroSdk');
            $parameter = [
                'address_type' => 'shipping',
                'per_page' => 999999
            ];
            $shippingAddresses = $makroSdk->memberAddress()->getMemberAddresses($parameter);
            $data['shipping_addresses'] = $shippingAddresses['data'];
        } catch (\Exception $e) {

        }

        //Member Address
        if (!empty($memberAddresses['contact_phone'])) {
            if (strtolower($memberAddresses['contact_phone']) == 'n/a') {
                $memberAddresses['contact_phone'] = '';
            }
        }
        $data['member_addresses'] = $memberAddresses;
        $data['billing_address'] = $billingAddress;
        $data['tax_address'] = $taxAddress;
        if (!empty($profile['phone'])) {
            if (strtolower($profile['phone']) == "n/a") {
                $profile['phone'] = '';
            }
        }
        $data['profile'] = $profile;
        //$data['profile']['first_name'] = '';
        //Payment methods
        $data['payment_methods'] = $paymentMethods;

        //Get current form data
        $paymentData = CartHelper::getPaymentData();

        $data['payment_method'] = '';
        if (!empty($paymentData)) {
            $data['payment_method'] = isset($this->reversePaymentMethodMapper[$paymentData['payment_method']])
                ? $this->reversePaymentMethodMapper[$paymentData['payment_method']]
                : '';
        }

        $data['cart_empty'] = false;

        //Makro card info
        $data['makro_card_info'] = $makroCardInfo;
        $data['has_makro_card'] = $hasMakroCard ? 1 : 0;
        $data['is_created_siebel'] = $isCreatedSiebel;
        $data['hide_select_store_button'] = true;

        // แก้ปัญหาเรื่อง crete siebel แล้วได้รับ response ช้า ทำให้ฟอร์มกาง
        $orderCount = \Cache::tags(['global', 'order'])->rememberForever('count_member_order_' . AuthHelper::getMemberId(), function() use ($makroSdk) {
            try {
                $makroSdk = app()->make('makroSdk');

                $response = $makroSdk->order()->get(['limit' => 1]);

                if (array_get($response, 'meta.pagination.total', 0) > 0) {
                    return 1;
                }
            } catch (\Exception $e) {

            }

            return null;
        });

        $data['member_was_ordered'] = $orderCount > 0 ? 1 : 0;

//        $data['makro_card_info'] = null;
//        $data['has_makro_card'] = 0;
//        $data['is_created_siebel'] = 0;

        $this->addBreadcrumb('carts.checkout', trans('frontend.cart_step_2', []), route('carts.checkout'));

        //Log Start
        $user = AuthHelper::user();
        $username = array_get($user, 'username', 'unknown');
        $logName = 'getPayment';
        $fileName = str_replace(['@', '.'], '-', $username) . '-pass';
        $logs = [
            'log_step' => '1002',
        ];
        $this->writeCheckoutLog($logName, $fileName, $logs);
        //Log End
        $deliveryDate = null;
        $pickupDate = null;
        try {
            $deliveryDate = $makroSdk->config()->getDeliveryDate();
        } catch (\Exception $e) {
            return view('cart.connection-error', ['step' => 2, 'code' => $e->getCode()]);
        }
        try {
            $pickupDate = $makroSdk->config()->getPickupDate();
        } catch (\Exception $e) {
            return view('cart.connection-error', ['step' => 2, 'code' => $e->getCode()]);
        }

        $data['dateConfig']['deliveryDate'] = array_get($deliveryDate, 'data', []);
        $data['dateConfig']['pickupDate'] = array_get($pickupDate, 'data', []);

        return view('cart.payment', $data);
    }

    /*
     * Step 2:  Submit payment form
     */
    public function postPayment(Request $request)
    {
        //Log Start
        $user = AuthHelper::user();
        $username = array_get($user, 'username', 'unknown');
        $logName = 'postPayment';
        $fileName = str_replace(['@', '.'], '-', $username) . '-pass';
        $logs = [
            'log_step' => '1003',
        ];
        $this->writeCheckoutLog($logName, $fileName, $logs);
        //Log End

        // เช็ค เวลาก่อน Deploy  return true / false
        $maPage = MaHelper::getMaPage();

        if ($maPage['success']) {

            return response()->json([
                'status' => 'error',
                'message' => __('frontend.mapage_error_on', [
                    'end_datetime_th' => $maPage['message']['end_datetime_th'],
                    'end_datetime_en' => $maPage['message']['end_datetime_en']
                ]),
                'error_ma' => 1,
                'next_url' => route('carts.index')
            ]);
        }
        //Get cart
        try {
            $cart = $this->getCartItems(Cart::CART_TYPE_PROMOTION);
        } catch (\Exception $e) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0004',
                'Exception.getCode' => $e->getCode(),
                'Exception.getMessage' => $e->getMessage(),
            ];

            if ($e instanceof SDKException) {
                $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                $logs['SDKException.getCode'] = $e->getCode();
                $logs['SDKException.getErrors'] = $e->getErrors();
            }

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            if (in_array($e->getCode(),
                ['205002', '205404', '100002', '216001', '208001', '209404', '200404', '216404', '219404'])) {

                return response()->json([
                    'status' => 'error_connect',
                    'message' => __('frontend.there_was_a_connection_problem_please_retry_again',
                        ['code' => $e->getCode()])
                ]);

            } else {
                if ($e->getCode() == 100002) { //Can not call RM3
                    return response()->json([
                        'status' => 'error',
                        'message' => __('frontend.can_not_reserve_because_can_not_call_rm3', ['code' => $e->getCode()]),
                        'empty_cart' => 1
                    ]);
                }
            }

            return response()->json([
                'status' => 'error',
                'message' => __('frontend.could_not_reserve_order'),
                'empty_cart' => 1,
                'error_code' => $e->getCode()
            ]);
        }

        //Check cart empty
        if (empty($cart['data'])) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0005',
                'cart' => $cart,
                'error' => 'empty cart.data',
            ];

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return response()->json([
                'status' => 'error',
                'message' => __('frontend.could_not_reserve_order'),
                'empty_cart' => 1
            ]);
        }

        //Check order amount
        $_cart = CartHelper::getCartGroup($cart['data']);
        if (!$this->checkOrderAmount($_cart)) {
            return response()->json([
                'status' => 'error',
                'message' => __('frontend.please_check_order_amount'),
                'error_order_amount' => 1
            ]);
        }

        //Check minimum checkout amount is passed
        $checkMinimumCheckout = $this->checkMinimumCheckout($cart);
        if (!$checkMinimumCheckout['status']) {
            return redirect(route('carts.index'))->withErrors([
                __('frontend.minimum_checkout_warning_message', ['minimum' => $checkMinimumCheckout['amount']])
            ]);
        }

        // Check delivery store address (Is same selected_address?)
        // Shipping address must not exiting (From address form)
        // check available ship&pick area
        if ($request->get('delivery_type') == 'pickup') {
            if(!$this->checkPickupStoreIsAvailable($request->get('shipping_address_id'))) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('frontend.pickup_store_not_available'),
                    'error_code' => 221001,
                ]);
            }
        } else {
            //Delivery
            //Check delivery store is same
            if ($request->has('shipping_address_id') && ! empty($request->get('shipping_address_id'))) {
                $postcode = $request->get('postcode');
                $subDistrictId = $request->get('sub_district_id');
            } else if ($request->has('new_shipping_address')) {
                $shippingAddressData = $request->get('new_shipping_address');
                $postcode = array_get($shippingAddressData, 'postcode');
                $subDistrictId = array_get($shippingAddressData, 'subdistrict_id');
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => __('frontend.could_not_reserve_order'),
                    'error_code' => 400 //TODO(kinkop): handle error code later
                ]);
            }

            $isDeliveryStoreIsChanged = false;
            try {

                $result = $this->checkDeliveryStoreAddressIsChanged($postcode, $subDistrictId);
                $isDeliveryStoreIsChanged = $result['changed'];
            } catch (\Exception $e) {
                if ($e instanceof SDKException) {
                    return response()->json([
                        'status' => 'error_connect',
                        'message' => __('frontend.there_was_a_connection_problem_please_retry_again',
                            ['code' => $e->getCode()]),
                        'error_code' => $e->getCode(),
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => __('frontend.delivery_store_address_is_empty'),
                        'error_code' => $e->getCode(),
                    ]);
                }
            }

            if ($isDeliveryStoreIsChanged) {
                return response()->json([
                    'status' => 'error',
                    'delivery_store_address_is_changed' => 1,
                    'address' => $result['address'],
                    'error_code' => 221001,
                ]);
            }
        }

        //Reserve order
        $paymentMethod = $request->input('payment_method.payment_method');
        $paymentData['delivery_type'] = $request->get('delivery_type', null);

        //Checkout delivery type
        if (empty($paymentData['delivery_type'])) {
            return response()->json([
                'status' => 'error',
                'message' => __('frontend.delivery_type_is_empty'),
            ]);
        }

        try {
            $reserve = $this->reserveOrder($cart, $paymentMethod, $paymentData['delivery_type']);
        } catch (\Exception $e) {
            $message = __('frontend.could_not_reserve_order');

            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0006',
                'error' => 'can not reserve',
                'Exception.getMessage' => $e->getMessage(),
            ];

            if ($e instanceof SDKException) {
                $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                $logs['SDKException.getCode'] = $e->getCode();
                $logs['SDKException.getErrors'] = $e->getErrors();
            }

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            if (in_array($e->getCode(),
                ['205002', '205404', '100002', '216001', '208001', '209404', '200404', '216404', '219404'])) {

                return response()->json([
                    'status' => 'error_connect',
                    'message' => __('frontend.there_was_a_connection_problem_please_retry_again',
                        ['code' => $e->getCode()])
                ]);

            }

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'error_code' => $e->getCode()
            ]);
        }

        //Check unavailable product
        if (isset($reserve['fail'])
            && $reserve['fail'] == true
            && isset($reserve['response'])
        ) {

            $reserve = $reserve['response'];
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0007',
                'error' => 'have unavailable products',
                'unavailable_products' => array_get($reserve, 'data'),
            ];

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return response()->json([
                'status' => 'error',
                'message' => '',
                'has_unavailable_product' => 1,
                'unavailable_products' => $reserve['data'],
                'is_product_empty' => $this->getIsReserveProductEmpty($reserve) ? 1 : 0,
                'reserve_id' => null

            ]);
        }

        //Check reserve ID  and available items
        if (!isset($reserve['id'])
            || empty($reserve['id'])
            || !isset($reserve['items'])
            || empty($reserve['items'])) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0008',
                'error' => 'Check reserve ID  and available items fail',
                'reserve' => $reserve,
            ];

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return response()->json([
                'status' => 'error',
                'message' => __('frontend.could_not_reserve_order')
            ]);
        }

        //Keep payment data
        $paymentData['payment_method'] = $paymentMethod;

        //Get buyer address
        try {
            $memberProfileAndAddress = $this->getMemberProfileAndAddress($request);
            $paymentData['member_address'] = $memberProfileAndAddress['address'];
        } catch (\Exception $e) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0009',
                'error' => 'getMemberProfileAndAddress error',
                'Exception.getMessage' => $e->getMessage(),
            ];

            if ($e instanceof SDKException) {
                $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                $logs['SDKException.getCode'] = $e->getCode();
                $logs['SDKException.getErrors'] = $e->getErrors();
            }

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return response()->json([
                'status' => 'error',
                'message' => __('frontend.could_not_reserve_order'),
                'error_code' => $e->getCode()
            ]);
        }

        //Check buyer information is empty
        if (empty($paymentData['member_address'])) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0010',
                'error' => 'empty paymentData.member_address',
                'paymentData' => $paymentData,
            ];

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return response()->json([
                'status' => 'error',
                'message' => __('frontend.could_not_reserve_order')
            ]);
        }

        //Get makro card info
        $makroCardInfo = null;
        $makroCardId = $request->get('makro_card_id');

        if (!empty($makroCardId)) {
            try {
                $makroCardInfo = $this->getMakroCardInfo($makroCardId);
            } catch (\Exception $e) {
                //Log Start
                $user = AuthHelper::user();
                $username = array_get($user, 'username', 'null');
                $logName = 'postPayment';
                $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
                $logs = [
                    'log_step' => '0011',
                    'error' => 'getMakroCardInfo error',
                    'Exception.getMessage' => $e->getMessage(),
                ];

                if ($e instanceof SDKException) {
                    $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                    $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                    $logs['SDKException.getCode'] = $e->getCode();
                    $logs['SDKException.getErrors'] = $e->getErrors();
                }

                $this->writeCheckoutLog($logName, $fileName, $logs);
                //Log End

                return response()->json([
                    'status' => 'error',
                    'message' => __('frontend.could_not_reserve_order'),
                    'error_code' => $e->getCode()
                ]);
            }
        }

        //Get tax invoice address
        try {
            $paymentData['invoice_address'] = $this->getInvoiceAdddress($request, $makroCardInfo['makro_card_info'], $paymentData['member_address'], $memberProfileAndAddress['profile']);
        } catch (\Exception $e) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0012',
                'error' => 'getInvoiceAdddress error',
                'Exception.getMessage' => $e->getMessage(),
            ];

            if ($e instanceof SDKException) {
                $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                $logs['SDKException.getCode'] = $e->getCode();
                $logs['SDKException.getErrors'] = $e->getErrors();
            }

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return response()->json([
                'status' => 'error',
                'message' => __('frontend.could_not_reserve_order'),
                'error_code' => $e->getCode()
            ]);
        }

        //Get billing address
        try {
            if (isset($paymentData['invoice_address']) && !empty($paymentData['invoice_address'])) {
                $paymentData['billing_address'] = $this->getBillingAdddress($request,
                    $paymentData['invoice_address'],
                    $makroCardInfo['makro_card_info'],
                    $paymentData['member_address']);
            }
        } catch (\Exception $e) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0013',
                'error' => 'getBillingAdddress error',
                'paymentData' => $paymentData,
                'Exception.getMessage' => $e->getMessage(),
            ];

            if ($e instanceof SDKException) {
                $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                $logs['SDKException.getCode'] = $e->getCode();
                $logs['SDKException.getErrors'] = $e->getErrors();
            }

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return response()->json([
                'status' => 'error',
                'message' => __('frontend.could_not_reserve_order'),
                'error_code' => $e->getCode()
            ]);
        }

        //Save member addresses
        try {
            //Save member address
            $this->saveMemberAddress($paymentData['member_address']);

            //Save billing address
            if (isset($paymentData['billing_address'])
                && !empty($paymentData['billing_address'])
                && empty($makroCardInfo['makro_card_info'])) {
                $this->saveBillingAddress($paymentData['billing_address']);
            }

            //Save tax invoice address
            if (isset($paymentData['invoice_address'])
                && !empty($paymentData['invoice_address'])
                && empty($makroCardInfo['makro_card_info'])) {
                $this->saveTaxInvoiceAddress($paymentData['invoice_address']);
            }

        } catch (\Exception $e) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0014',
                'error' => 'Save member addresses error',
                'paymentData' => $paymentData,
                'Exception.getMessage' => $e->getMessage(),
            ];

            if ($e instanceof SDKException) {
                $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                $logs['SDKException.getCode'] = $e->getCode();
                $logs['SDKException.getErrors'] = $e->getErrors();
            }

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            if ($e->getCode() == 100003) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('frontend.please_enter_valid_email_format'),
                    'error_code' => $e->getCode(),
                    'use_custom_message' => 1,
                    'action' => 'close_popup',
                    'action_target' => ''
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => __('frontend.could_not_reserve_order'),
                'error_code' => $e->getCode()
            ]);
        }

        //Handle shipping address
        $paymentData['shipping_address_data'] = null;
        $createdShippingAddress = null;
        $deliveryStoreAddress = null;
        if ($request->has('shipping_address_id') && !empty($request->get('shipping_address_id'))) {
            $paymentData['shipping_address_id'] = $request->get('shipping_address_id');
        } else {
            //Create new shipping address
            try {
                //Check delivery store address
                $shippingAddressData = $request->get('new_shipping_address');
                $deliveryStoreAddressResult = $this->checkDeliveryStoreAddressIsChanged(array_get($shippingAddressData, 'postcode'), array_get($shippingAddressData, 'subdistrict_id'));
                $deliveryStoreAddress = array_get($deliveryStoreAddressResult, 'address');

                $shippingAddressData = $request->get('new_shipping_address');
                $data = [
                    'address_name' => array_get($shippingAddressData, 'shop_name'),
                    'first_name' => array_get($shippingAddressData, 'first_name'),
                    'last_name' => array_get($shippingAddressData, 'last_name'),
                    'contact_phone' => array_get($shippingAddressData, 'phone'),
                    'contact_email' => empty(array_get($shippingAddressData, 'email')) ? '' : array_get($shippingAddressData, 'email'),
                    'address_type' => 'shipping',
                    'address' => array_get($shippingAddressData, 'address_line1'),
                    'address2' => array_get($shippingAddressData, 'address2', ''),
                    'address3' => '-',
                    'subdistrict_id' => array_get($shippingAddressData, 'subdistrict_id'),
                    'district_id' => array_get($shippingAddressData, 'district_id'),
                    'province_id' => array_get($shippingAddressData, 'province_id'),
                    'country_code' => 'TH',
                    'postcode' => array_get($shippingAddressData, 'postcode'),
                ];

                if (! empty($data['contact_email'])) {
                    $data['contact_email'] = MakroHelper::validateEmail($data['contact_email']);
                }

                $makroSdk = app()->make('makroSdk');

                $response = $makroSdk->memberAddress()->createMemberAddress($data);
                $createdShippingAddress = $response;
                $paymentData['shipping_address_data'] = array_get($response, 'data');
                $paymentData['shipping_address_id'] = $paymentData['shipping_address_data']['id'];
            } catch (\Exception $e) {
                $useCustomMessage = 0;
                $action = '';
                if ($e->getCode() == 100003) {
                    $response['message'] = __('frontend.please_enter_valid_email_format');
                    $useCustomMessage = 1;
                    $action = 'close_popup';
                }

                return response()->json([
                    'status' => 'error',
                    'message' => __('frontend.could_not_reserve_order'),
                    'error_code' => $e->getCode(),
                    'use_custom_message' => $useCustomMessage,
                    'action' => $action,
                    'action_target' => ''
                ]);
            }
        }
        //Save payment data

        //Set tax invoice require
        $paymentData['required_tax_invoice'] = false;
        if ($request->has('is_use_invoice_address') && $request->input('is_use_invoice_address') == 1) {
            $paymentData['required_tax_invoice'] = true;
        }

        CartHelper::savePaymentData($paymentData);

        //Log Start
        $user = AuthHelper::user();
        $username = array_get($user, 'username', 'unknown');
        $logName = 'postPayment';
        $fileName = str_replace(['@', '.'], '-', $username) . '-pass';
        $logs = [
            'log_step' => '1004',
        ];
        $this->writeCheckoutLog($logName, $fileName, $logs);
        //Log End

        return response()->json([
            'status' => 'ok',
            'next_url' => route('carts.shipping'),
            'reserve_id' => $reserve['reserve_id'],
            'payment_method' => $paymentMethod,
            'created_shipping_address' => $createdShippingAddress,
            'delivery_store_address' => $deliveryStoreAddress
        ]);
    }

    public function reserveOrderPost()
    {
        //Get cart
        try {
            $cart = $this->getCartItems(Cart::CART_TYPE_PROMOTION);

        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($e instanceof SDKException) {
                $message = $e->getUserMessage();
            }

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'empty_cart' => 1
            ]);
        }

        //Check cart empty
        if (empty($cart['data'])) {
            return response()->json([
                'status' => 'error',
                'message' => __('frontend.cart_is_empty'),
                'empty_cart' => 1
            ]);
        }

        //Get payment data
        $paymentData = CartHelper::getPaymentData();

        if (empty($paymentData) || !isset($paymentData['payment_method'])) {
            return response()->json([
                'status' => 'error',
                'message' => __('frontend.payment_method_not_chosen'),
                'empty_cart' => 1
            ]);
        }

        if (!isset($paymentData['delivery_type']) || empty($paymentData['delivery_type'])) {
            return response()->json([
                'status' => 'error',
                'message' => __('frontend.delivery_type_is_empty'),
            ]);
        }

        //Reserve order
        try {
            $paymentMethod = $paymentData['payment_method'];
            $reserve = $this->reserveOrder($cart, $paymentMethod, array_get($paymentData, 'delivery_type'));
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }

        //Check unavailable product
        if (isset($reserve['fail'])
            && $reserve['fail'] == true
            && isset($reserve['response'])
        ) {

            $reserve = $reserve['response'];
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0007',
                'error' => 'have unavailable products',
                'unavailable_products' => array_get($reserve, 'data'),
            ];

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return response()->json([
                'status' => 'error',
                'message' => '',
                'has_unavailable_product' => 1,
                'unavailable_products' => $reserve['data'],
                'is_product_empty' => $this->getIsReserveProductEmpty($reserve) ? 1 : 0,
                'reserve_id' => null

            ]);
        }

        //Check unavailable product
//        if (isset($reserve['unavailable_products']) && !empty($reserve['unavailable_products'])) {
//            return response()->json([
//                'status' => 'error',
//                'message' => '',
//                'has_unavailable_product' => 1,
//                'unavailable_products' => $reserve['unavailable_products'],
//                'is_product_empty' => $this->getIsReserveProductEmpty($reserve) ? 1 : 0,
//                'reserve_id' => $reserve['reserve_id']
//
//            ]);
//        }

        return response()->json([
            'status' => 'ok',
            'next_url' => route('carts.shipping'),
            'reserve_id' => $reserve['reserve_id']
        ]);

    }

    protected function getIsReserveProductEmpty($reserve)
    {
        if (isset($reserve['items'])
            && isset($reserve['items']['data'])
            && !empty($reserve['items']['data'])
        ) {
            return true;
        }

        return false;
    }

    /*
     * Step3: Shipping address
     *
     */
    public function getShipping(Request $request)
    {
        //Get cart
        try {
            $cart = $this->getCartItems(Cart::CART_TYPE_ONLY_CART);
        } catch (\Exception $e) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0004-2',
                'Exception.getCode' => $e->getCode(),
                'Exception.getMessage' => $e->getMessage(),
            ];

            if ($e instanceof SDKException) {
                $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                $logs['SDKException.getCode'] = $e->getCode();
                $logs['SDKException.getErrors'] = $e->getErrors();
            }

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return redirect()->route('carts.index');
        }

        //Check cart empty
        if (empty($cart['data'])) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0005-2',
                'cart' => $cart,
                'error' => 'empty cart.data',
            ];

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return redirect()->route('carts.index');
        }

        $paymentData = CartHelper::getPaymentData();
        $reserveData = CartHelper::getReserveData();

        //Log Start
        $user = AuthHelper::user();
        $username = array_get($user, 'username', 'unknown');
        $logName = 'getShipping';
        $fileName = str_replace(['@', '.'], '-', $username) . '-pass';
        $logs = [
            'log_step' => '1005',
            'reserveData' => $reserveData,
            'paymentData' => $paymentData,
        ];
        $this->writeCheckoutLog($logName, $fileName, $logs);
        CartHelper::savePaymentData($paymentData);
        CartHelper::saveReserveData($reserveData);
        //Log End

        //Payment data and Reserve data must not empty
        if (empty($paymentData) || empty($reserveData)) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'getShipping';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0015',
                'error' => 'Payment data and Reserve data are empty',
                'reserveData' => $reserveData,
                'paymentData' => $paymentData,
            ];

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return redirect(route('carts.checkout'))->withErrors([
                __('frontend.could_not_proceeding_to_shipping_step_please_try_again')
            ]);
        }

        //Check reserve id
        if (!isset($reserveData['reserve_id']) || empty($reserveData['reserve_id'])) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'getShipping';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0016',
                'error' => 'reserveData.reserve_id is empty',
                'reserveData' => $reserveData,
            ];

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return redirect(route('carts.checkout'))->withErrors([
                __('frontend.could_not_proceeding_to_shipping_step_please_try_again')
            ]);
        }

        //Get reserve detail
        $makroSdk = app()->make('makroSdk');
        try {
            $parameters = [
                'includes' => 'item_detail'
            ];
            $reserveDetail = $makroSdk->order()->getReserveDetail($reserveData['id'], $parameters);
        } catch (\Exception $e) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'getShipping';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0017',
                'error' => 'getReserveDetail error',
                'Exception.getMessage' => $e->getMessage(),
            ];

            if ($e instanceof SDKException) {
                $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                $logs['SDKException.getCode'] = $e->getCode();
                $logs['SDKException.getErrors'] = $e->getErrors();
            }

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            if (in_array($e->getCode(),
                ['205002', '205404', '100002', '216001', '208001', '209404', '200404', '216404', '219404'])) {
                return view('cart.connection-error', ['step' => 3, 'code' => $e->getCode()]);
            }

            return redirect(route('carts.checkout'))->withErrors([
                $e->getMessage()
            ]);
        }

        //Check reserve data
        if (!isset($reserveDetail['data'])
            || !isset($reserveDetail['data']['items'])
            || empty($reserveDetail['data']['items'])
            || !isset($reserveDetail['data']['items']['data'])
            || empty($reserveDetail['data']['items']['data'])) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'getShipping';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0018',
                'error' => 'reserveDetail.items is empty',
                'reserveDetail' => $reserveDetail,
            ];

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return redirect(route('carts.checkout'))->withErrors([
                __('frontend.could_not_proceeding_to_shipping_step_please_try_again')
            ]);
        }

        if (
            array_get($reserveDetail, 'data.store_price_id') != StoreHelper::getCurrentStorePrice() // Store จากตอน reserve ไม่ตรงกับ Store จาก select address ปัจจุบัน
            //|| array_get($reserveDetail, 'data.delivery_type') != '' // Delivery type ตอน reserve ไม่ตรงกับ delivery type ที่ select อยู่ปัจจุบัน
        ) {
            try {
                $makroSdk->order()->cancelReserve($reserveData['id']);
                CartHelper::saveReserveData(null);
            } catch (\Exception $e) {
                //throw $e;
            }

            return redirect(route('carts.index'))->withErrors([
                __('frontend.delivery_address_was_changed_please_try_again')
            ]);
        }

        //Convert reserve detail items to cart items
        $cart = CartHelper::convertReserveDetailDataToCartData($reserveDetail);

        /*//Check minimum checkout amount is passed // reserve ได้แล้ว ไม่ควรเช็คอีก
        $checkMinimumCheckout = $this->checkMinimumCheckout($cart);
        if (! $checkMinimumCheckout['status']) {
            return redirect(route('carts.index'))->withErrors([
                __('frontend.minimum_checkout_warning_message', ['minimum' => $checkMinimumCheckout['amount']])
            ]);
        }*/

        //Check minimum checkout amount is passed
        //Get data
        try {
            $makroSdk = app()->make('makroSdk');

            //Get store
            $store = $makroSdk->store()->getById($reserveDetail['data']['store_id'], ['with' => 'address']);
        } catch (\Exception $e) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'getShipping';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0019',
                'error' => 'get store by id error',
                'Exception.getMessage' => $e->getMessage(),
            ];

            if ($e instanceof SDKException) {
                $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                $logs['SDKException.getCode'] = $e->getCode();
                $logs['SDKException.getErrors'] = $e->getErrors();
            }

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            if ($e instanceof SDKException) {
                abort(400, $e->getUserMessage());
            }

            abort(400, $e->getMessage());
        }

        $data['store'] = $store['data'];

        //Group product
        $reserveItems = CartHelper::generateReserveData($reserveDetail['data']['items']['data']);
        $data['cart'] = CartHelper::getCartGroup($cart['data'], $reserveItems);

        //---------------------------------------------------------------------------------
        //จำลอง non assortment ทั้ง without และ with
        //$data['cart']['non_assortment_without_installment'] = $data['cart']['assortment'];
        //$data['cart']['non_assortment_with_installment'] = $data['cart']['assortment'];
        //---------------------------------------------------------------------------------

        $data['cart_arr'] = CartHelper::getCartGroup($cart['data']);
        $data['cart_summary'] = $this->getCartSummaryData($cart);
        $data['cart_promotions'] = $cart['meta']['promotions'];
        $data['reserve_id'] = $reserveData['reserve_id'];

        //Init form data
        $data['member_address'] = $paymentData['member_address'];
        if (!empty($data['member_address'])) {
            //Get Subdistricta
            try {
                $response = $makroSdk->address()->getSubDistrictById($paymentData['member_address']['subdistrict_id']);
                $data['member_address']['township'] = $response['data']['name'];
            } catch (\Exception $e) {
                //Log Start
                $user = AuthHelper::user();
                $username = array_get($user, 'username', 'null');
                $logName = 'getShipping';
                $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
                $logs = [
                    'log_step' => '0020',
                    'error' => 'get address getSubDistrictById error',
                    'Exception.getMessage' => $e->getMessage(),
                ];

                if ($e instanceof SDKException) {
                    $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                    $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                    $logs['SDKException.getCode'] = $e->getCode();
                    $logs['SDKException.getErrors'] = $e->getErrors();
                }

                $this->writeCheckoutLog($logName, $fileName, $logs);
                //Log End

                abort(400, __('frontend.get_buyer_information_error'));
            }

            //Get District
            try {
                $response = $makroSdk->address()->getDistrictById($paymentData['member_address']['district_id']);
                $data['member_address']['city'] = $response['data']['name'];
            } catch (\Exception $e) {
                //Log Start
                $user = AuthHelper::user();
                $username = array_get($user, 'username', 'null');
                $logName = 'getShipping';
                $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
                $logs = [
                    'log_step' => '0021',
                    'error' => 'get address getDistrictById error',
                    'Exception.getMessage' => $e->getMessage(),
                ];

                if ($e instanceof SDKException) {
                    $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                    $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                    $logs['SDKException.getCode'] = $e->getCode();
                    $logs['SDKException.getErrors'] = $e->getErrors();
                }

                $this->writeCheckoutLog($logName, $fileName, $logs);
                //Log End

                abort(400, __('frontend.get_buyer_information_error'));
            }

            //Get Province
            try {
                $response = $makroSdk->address()->getProvinceById($paymentData['member_address']['province_id']);
                $data['member_address']['province'] = $response['data']['name'];
            } catch (\Exception $e) {
                //Log Start
                $user = AuthHelper::user();
                $username = array_get($user, 'username', 'null');
                $logName = 'getShipping';
                $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
                $logs = [
                    'log_step' => '0022',
                    'error' => 'get address getProvinceById error',
                    'Exception.getMessage' => $e->getMessage(),
                ];

                if ($e instanceof SDKException) {
                    $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                    $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                    $logs['SDKException.getCode'] = $e->getCode();
                    $logs['SDKException.getErrors'] = $e->getErrors();
                }

                $this->writeCheckoutLog($logName, $fileName, $logs);
                //Log End

                abort(400, __('frontend.get_buyer_information_error'));
            }
        }

        //Get shipping addresses if has product non assortment with installment
        $data['shipping_addresses'] = [];
//        if (isset($data['cart']['non_assortment_with_installment']) && !empty($data['cart']['non_assortment_with_installment'])) {
        try {
            $makroSdk = app()->make('makroSdk');
            $parameter = [
                'address_type' => 'shipping',
                'per_page' => '9999'
            ];
            $shippingAddresses = $makroSdk->memberAddress()->getMemberAddresses($parameter);
            $data['shipping_addresses'] = $shippingAddresses['data'];
        } catch (\Exception $e) {

        }
//        }

        //Get delivery dates
        $data['delivery_dates'] = null;
        try {
            $makroSdk = app()->make('makroSdk');
            $data['delivery_dates'] = CartHelper::getDeliveryDates($makroSdk->config()->getDeliveryDate());
        } catch (\Exception $e) {
            //Do nothing
        }
        // get delivery Date
        $deliveryDate = null;
        $pickupDate = null;
        try {
            $deliveryDate = $makroSdk->config()->getDeliveryDate();
        } catch (\Exception $e) {
            return view('cart.connection-error', ['step' => 2, 'code' => $e->getCode()]);
        }
        try {
            $pickupDate = $makroSdk->config()->getPickupDate();
        } catch (\Exception $e) {
            return view('cart.connection-error', ['step' => 2, 'code' => $e->getCode()]);
        }

        $data['dateConfig']['deliveryDate'] = array_get($deliveryDate, 'data', []);
        $data['dateConfig']['pickupDate'] = array_get($pickupDate, 'data', []);

        $data['cart_empty'] = false;
        $data['hide_select_store_button'] = true;

        $this->addBreadcrumb('carts.checkout', trans('frontend.cart_step_3', []), route('carts.checkout'));

        //Log Start
        $user = AuthHelper::user();
        $username = array_get($user, 'username', 'unknown');
        $logName = 'getShipping';
        $fileName = str_replace(['@', '.'], '-', $username) . '-pass';
        $logs = [
            'log_step' => '1006',
        ];
        $this->writeCheckoutLog($logName, $fileName, $logs);
        //Log End

        return view('cart.shipping', $data);
    }

    public function getShippingTest(Request $request)
    {
        return view('cart.shipping-test', []);
    }
    /*
     * Step3: Submit shipping form
     */
    public function postShipping(Request $request)
    {
        $maPage = MaHelper::getMaPage();

        if ($maPage['success']) {

            return response()->json([
                'status' => 'error',
                'message' => __('frontend.mapage_error_on', [
                    'end_datetime_th' => $maPage['message']['end_datetime_th'],
                    'end_datetime_en' => $maPage['message']['end_datetime_en']
                ]),
                'error_ma' => 1,
                'next_url' => route('carts.index')
            ]);
        }

        try {
            $cart = $this->getCartItems(Cart::CART_TYPE_ONLY_CART);
        } catch (\Exception $e) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0004-3',
                'Exception.getCode' => $e->getCode(),
                'Exception.getMessage' => $e->getMessage(),
            ];

            if ($e instanceof SDKException) {
                $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                $logs['SDKException.getCode'] = $e->getCode();
                $logs['SDKException.getErrors'] = $e->getErrors();
            }

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            if (in_array($e->getCode(),
                ['205002', '205404', '100002', '216001', '208001', '209404', '200404', '216404', '219404'])) {

                return response()->json([
                    'status' => 'error_connect',
                    'message' => __('frontend.there_was_a_connection_problem_please_retry_again',
                        ['code' => $e->getCode()])
                ]);

            }

            return response()->json([
                'status' => 'ok',
                'next_url' => route('carts.index')
            ]);
        }

        //Check cart empty
        if (empty($cart['data'])) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0005-3',
                'cart' => $cart,
                'error' => 'empty cart.data',
            ];

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return response()->json([
                'status' => 'ok',
                'next_url' => route('carts.index')
            ]);
        }

        $paymentData = CartHelper::getPaymentData();
        $reserveData = CartHelper::getReserveData();

        //Log Start
        $user = AuthHelper::user();
        $username = array_get($user, 'username', 'unknown');
        $logName = 'postShipping';
        $fileName = str_replace(['@', '.'], '-', $username) . '-pass';
        $logs = [
            'log_step' => '1007',
            'reserveData' => $reserveData,
            'paymentData' => $paymentData,
        ];
        $this->writeCheckoutLog($logName, $fileName, $logs);
        CartHelper::savePaymentData($paymentData);
        CartHelper::saveReserveData($reserveData);
        //Log End

        if (empty($paymentData)) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postShipping';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0023',
                'error' => 'Payment data is empty',
                'paymentData' => $paymentData,
            ];

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return response()->json([
                'status' => 'error',
                'message' => __('frontend.payment_data_and_reserve_data_must_not_empty'),
                'payment_data_empty' => 1
            ]);
        }

        //Payment data and Reserve data must not empty
        if (empty($reserveData)) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postShipping';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0023-2',
                'error' => 'Reserve data is empty',
                'reserveData' => $reserveData,
            ];

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return response()->json([
                'status' => 'error',
                'message' => __('frontend.payment_data_and_reserve_data_must_not_empty'),
                'reserve_empty' => 1
            ]);
        }

        //Check delivery type
        //Pickup at store
        if ($request->get('address_type') == 'store') {
            if(!$this->checkPickupStoreIsAvailable($request->get('address_id'))) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('frontend.pickup_store_not_available'),
                    'error_code' => 221001,
                ]);
            }
        } else {
            //Delivery
            //Check delivery store is same
            $isDeliveryStoreIsChanged = false;
            try {

                $result = $this->checkDeliveryStoreAddressIsChanged($request->get('postcode'), $request->get('sub_district_id'));
                $isDeliveryStoreIsChanged = $result['changed'];
            } catch (\Exception $e) {
                if ($e instanceof SDKException) {
                    return response()->json([
                        'status' => 'error_connect',
                        'message' => __('frontend.there_was_a_connection_problem_please_retry_again',
                            ['code' => $e->getCode()]),
                        'error_code' => $e->getCode(),
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => __('frontend.delivery_store_address_is_empty'),
                        'error_code' => $e->getCode(),
                    ]);
                }
            }

            if ($isDeliveryStoreIsChanged) {
                return response()->json([
                    'status' => 'error',
                    'delivery_store_address_is_changed' => 1,
                    'address' => $result['address'],
                    'error_code' => 221001,
                ]);
            }
        }


        //Save shipping data
        CartHelper::saveShippingData($request->only([
            'product_assortment',
            'product_non_assortment_without_installment',
            'product_non_assortment_with_installment'
        ])
        );

        //Get reserve detail
        $makroSdk = app()->make('makroSdk');
        try {
            $parameters = [
                'includes' => 'item_detail'
            ];
            $reserveDetail = $makroSdk->order()->getReserveDetail($reserveData['id'], $parameters);
        } catch (\Exception $e) {
            $message = __('frontend.could_not_reserve_message');
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postShipping';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0024',
                'error' => 'order getReserveDetail error',
                'Exception.getMessage' => $e->getMessage(),
            ];

            if ($e instanceof SDKException) {
                $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                $logs['SDKException.getCode'] = $e->getCode();
                $logs['SDKException.getErrors'] = $e->getErrors();
            }

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            if (in_array($e->getCode(),
                ['205002', '205404', '100002', '216001', '208001', '209404', '200404', '216404', '219404', '204404'])) {

                return response()->json([
                    'status' => 'error_connect',
                    'message' => __('frontend.there_was_a_connection_problem_please_retry_again',
                        ['code' => $e->getCode()])
                ]);

            }
            if ($e->getCode()) {
                $message = __('frontend.could_not_reserve_message_with_code', ['code' => $e->getCode()]);
            }
            return response()->json([
                'status' => 'error',
                'message' => $message
            ]);
        }

        if (array_get($reserveDetail, 'data.store_price_id') != StoreHelper::getCurrentStorePrice()) {
            try {
                $makroSdk->order()->cancelReserve($reserveData['id']);
                CartHelper::saveReserveData(null);
            } catch (\Exception $e) {
                // throw $e;
            }

            return response()->json([
                'status' => 'error',
                'message' => __('frontend.delivery_address_was_changed_please_try_again')
            ]);
        }

        // ถ้า reserve ถูกใช้ create order ไปแล้ว ต้อง cancel แล้วให้ user create reserve ใหม่
        if (!empty($reserveDetail['data']['order_no'])) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postShipping';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0023-3',
                'error' => 'Reserve data was use',
                'reserve_detail' => $reserveDetail,
            ];

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            try {
                CartHelper::saveReserveData(null);
                $makroSdk->order()->cancelReserve($reserveDetail['data']['id']);
            } catch (\Exception $e) {
                //Log Start
                $user = AuthHelper::user();
                $username = array_get($user, 'username', 'null');
                $logName = 'postShipping';
                $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
                $logs = [
                    'log_step' => '0023-4',
                    'error' => 'Can not cancel reserve',
                    'Exception.getMessage' => $e->getMessage(),
                ];

                if ($e instanceof SDKException) {
                    $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                    $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                    $logs['SDKException.getCode'] = $e->getCode();
                    $logs['SDKException.getErrors'] = $e->getErrors();
                }

                $this->writeCheckoutLog($logName, $fileName, $logs);
                //Log End
            }

            return response()->json([
                'status' => 'error',
                'message' => __('frontend.reservation_was_used'),
                'reserve_empty' => 1
            ]);
        }

        try {
            //Get data to create order
            $data = CartHelper::getCheckoutData($reserveDetail);
        } catch (\Exception $e) {
            if ($e->getCode() == 100003) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('frontend.please_enter_valid_email_format'),
                    'error_code' => $e->getCode(),
                    'use_custom_message' => 1,
                    'action' => 'redirect',
                    'action_target' => route('carts.checkout'),
                ]);
            }

            return response()->json([
                'status' => 'error_connect',
                'message' => __('frontend.please_contact_call_center_sorry_for_your_inconvenience',
                    ['code' => $e->getCode()])
            ]);
        }


        try {
            $order = $makroSdk->order()->create($data);
        } catch (\Exception $e) {
            $reserve_expired = 0;
            $message = trans('frontend.can_not_create_order', ['code' => $e->getCode()]);

            if ($e instanceof SDKException) {
                $message = trans('frontend.can_not_create_order', ['code' => $e->getCode()]);
                if ($e->getErrorCode() == 204001) {
                    $reserve_expired = 1;
                } elseif ($e->getErrorCode() == 100002) { // Cannot call rm3
                    $message = trans('frontend.can_not_crete_order_because_can_not_call_rm3');
                } elseif ($e->getErrorCode() == 204002) { // Cannot call rm3
                    $message = trans('frontend.can_not_crete_order_because_can_not_call_rms');
                }
            }

            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postShipping';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0025',
                'error' => 'order create error',
                'Exception.getMessage' => $e->getMessage(),
            ];

            if ($e instanceof SDKException) {
                $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                $logs['SDKException.getCode'] = $e->getCode();
                $logs['SDKException.getErrors'] = $e->getErrors();
            }

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            if (in_array($e->getCode(),
                ['205002', '205404', '100002', '216001', '208001', '209404', '200404', '216404', '219404'])) {

                return response()->json([
                    'status' => 'error_connect',
                    'message' => __('frontend.there_was_a_connection_problem_please_retry_again',
                        ['code' => $e->getCode()])
                ]);

            }

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'reserve_expired' => $reserve_expired,
                'errors' => is_callable([$e, 'getErrors']) ? $e->getErrors() : []
            ]);
        }

        //get order detail
        $orderNo = $order['order_no'];

        try {
            $orderDetail = $makroSdk->order()->getOrderDetail($orderNo, ['includes' => 'detail']);
        } catch (\Exception $e) {
            if ($e instanceof SDKException) {
                if ($e->getCode() == 203404) {
                    $message = __('frontend.not_found_order', ['code' => $e->getCode()]);
                } else {
                    $message = $e->getUserMessage();
                }
            } else {
                $message = $e->getMessage();
            }

            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postShipping';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0026',
                'error' => 'order getOrderDetail error',
                'Exception.getMessage' => $e->getMessage(),
            ];

            if ($e instanceof SDKException) {
                $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                $logs['SDKException.getCode'] = $e->getCode();
                $logs['SDKException.getErrors'] = $e->getErrors();
            }

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return response()->json([
                'status' => 'error',
                'message' => $message
            ]);
        }

        if (empty($orderDetail['detail'])) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postShipping';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0027',
                'error' => 'Order not have detail',
                'orderNo' => $orderNo,
            ];

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return response()->json([
                'status' => 'error',
                'message' => 'detail is null'
            ]);
        }

        if (empty($orderDetail['detail']['items'])) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'postShipping';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0027',
                'error' => 'Order not have items',
                'orderNo' => $orderNo,
            ];

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return response()->json([
                'status' => 'error',
                'message' => 'detail.items is null'
            ]);
        }

        $request->session()->put('order_detail', $orderDetail);

        //Continue payment
        $paymentType = array_get($orderDetail, 'detail.payment.payment_type');

        if ($paymentType == 'gateway') {
            $nextUrl = route('carts.payment.gateway-payment');
        } else {
            $nextUrl = route('carts.payment.offline-payment');
        }


        //Log Start
        $user = AuthHelper::user();
        $username = array_get($user, 'username', 'unknown');
        $logName = 'postShipping';
        $fileName = str_replace(['@', '.'], '-', $username) . '-pass';
        $logs = [
            'log_step' => '1008',
            'orderNo' => $orderNo,
            'payment_gateway_driver' => array_get($orderDetail, 'detail.payment.payment_gateway_driver'),
            'next_url' => $nextUrl,
        ];
        $this->writeCheckoutLog($logName, $fileName, $logs);
        //Log End

        return response()->json([
            'status' => 'ok',
            'next_url' => $nextUrl
        ]);
    }

    /*
    * Get exist member address
    */
    protected function getBuyerInformation()
    {
        try {
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->memberAddress()->getMemberAddresses(['address_type' => MemberAddress::ADDRESS_TYPE_MEMBER]);

        } catch (\Exception $e) {
            return null;
        }

        if (!empty($response['data'])) {
            return $response['data'][0];
        }

        return null;
    }

    /*
     * Get billing address
     */
    protected function getBillingAddress()
    {
        try {
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->memberAddress()->getMemberAddresses(['address_type' => MemberAddress::ADDRESS_TYPE_BILL]);
        } catch (\Exception $e) {
            return null;
        }

        if (!empty($response['data'])) {
            return $response['data'][0];
        }

        return null;
    }

    /*
     * Get tax address
     */
    protected function getTaxAddress($profile = null)
    {
        try {
            //Business address
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->memberAddress()->getMemberAddresses(['address_type' => MemberAddress::ADDRESS_TYPE_TAX]);

            //Get profile
            if (empty($profile)) {
                $profile = $makroSdk->member()->profile();
                AuthHelper::updateUserFromProfile($profile);
            }
        } catch (\Exception $e) {
            return null;
        }

        $data['tax_address'] = null;
        $data['profile'] = null;
        if (!empty($response['data'])) {
            $data['tax_address'] = $response['data'][0];
        }

        if (!empty($profile)) {
            $data['profile'] = $profile;
        }


        return $data;
    }

    /*
     * Create or Update Member address (type = member)
     */
    protected function saveMemberAddress($data)
    {
        //This member address type is only create
        try {
            if (! empty($data['email'])) {
                $data['email'] = MakroHelper::validateEmail($data['email']);
            }

            $makroSdk = app()->make('makroSdk');
            $param = $this->convertAddressFormToAddressParam($data);

            if (!isset($data['id']) || empty($data['id'])) {
                $response = $makroSdk->memberAddress()->saveMemberAddress($param, MemberAddress::ADDRESS_TYPE_MEMBER);
            } else {
                $response = $makroSdk->memberAddress()->updateMemberAddress($data['id'], array_except($param, ['id']));
            }
            $member = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => empty($data['phone']) ? '' : $data['phone'],
                'email' => empty($data['email']) ? '' : $data['email'],
            ];
            $makroSdk->member()->updateProfile($member);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /*
     * Create or Update Member address (type = bill)
     */
    protected function saveBillingAddress($data)
    {
        $makroSdk = app()->make('makroSdk');

        //Get billing address
        try {
            $response = $makroSdk->memberAddress()->getMemberAddresses([
                'address_type' => MemberAddress::ADDRESS_TYPE_BILL
            ]);
        } catch (\Exception $e) {
            throw $e;
        }

        $param = $this->convertAddressFormToAddressParam($data);

        if (! empty($param['contact_email'])) {
            $param['contact_email'] = MakroHelper::validateEmail($param['contact_email']);
        }

        $param['first_name'] = empty($param['first_name']) ? '' : $param['first_name'];
        $param['last_name'] = empty($param['last_name']) ? '' : $param['last_name'];
        $param['contact_phone'] = empty($param['contact_phone']) ? '' : $param['contact_phone'];

        //Get billing address
        if (isset($response['data']) && !empty($response['data'])) {
            //Update
            $param['id'] = $response['data'][0]['id'];
            $response = $makroSdk->memberAddress()->saveMemberAddress($param, MemberAddress::ADDRESS_TYPE_BILL);
        } else {
            //Create
            if (isset($param['id'])) {
                unset($param['id']);
            }

            $response = $makroSdk->memberAddress()->saveMemberAddress($param, MemberAddress::ADDRESS_TYPE_BILL);
        }
    }

    /*
     * Create or Update Member address (type = tax)  (profile, business, tax)
     */
    protected function saveTaxInvoiceAddress($data)
    {
        if (!empty($data)) {
            $makroSdk = app()->make('makroSdk');

            //Get tax address
            try {
                $response = $makroSdk->memberAddress()->getMemberAddresses([
                    'address_type' => MemberAddress::ADDRESS_TYPE_TAX
                ]);
            } catch (\Exception $e) {
                throw $e;
            }

            $param = $this->convertAddressFormToAddressParam($data);

            if (! empty($param['contact_email'])) {
                $param['contact_email'] = MakroHelper::validateEmail($param['contact_email']);
            }

            if (empty($param['address_name'])) {
                $param['address_name'] = $data['company_name'];
            }

            if (isset($response['data']) && !empty($response['data'])) {
                //Update
                $param['id'] = $response['data'][0]['id'];
                $makroSdk->memberAddress()->saveMemberAddress($param, MemberAddress::ADDRESS_TYPE_TAX);
            } else {
                //Create
                if (isset($param['id'])) {
                    unset($param['id']);
                }
                $makroSdk->memberAddress()->saveMemberAddress($param, MemberAddress::ADDRESS_TYPE_TAX);
            }

            //Update business
            try {
                $business = [
                    'shop_name' => $data['company_name'],
                    'branch' => $data['branch_no'],
                    'mobile_phone' => empty($data['phone']) ? '' : $data['phone'],
                    'email' => empty($data['email']) ? '' : $data['email']
                ];

                if (empty($business['email']) || !filter_var($business['email'], FILTER_VALIDATE_EMAIL)) {
                    $business['email'] = '';
                }

                $makroSdk->member()->updateBusiness($business);
            } catch (\Exception $e) {
                throw $e;
            }

            //Update profile
            try {
                $profile = [
                    'tax_id' => $data['tax_id_number']
                ];
                $makroSdk->member()->updateProfile($profile);
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }

    protected function convertAddressFormToAddressParam($data)
    {
        $param = [
            'id' => array_get($data, 'id'),
            'address_name' => array_get($data, 'shop_name'),
            'first_name' => array_get($data, 'first_name'),
            'last_name' => array_get($data, 'last_name'),
            'contact_phone' => array_get($data, 'phone'),
            'contact_email' => strtolower(array_get($data, 'email')),
            'address' => array_get($data, 'address_line1'),
            'address2' => array_get($data, 'address_line2'),
            'address3' => '-',
            'subdistrict_id' => array_get($data, 'subdistrict_id'),
            'district_id' => array_get($data, 'district_id'),
            'province_id' => array_get($data, 'province_id'),
            'country_code' => 'TH',
            'postcode' => array_get($data, 'postcode'),
        ];

        foreach ($param as $key => $val) {
            if (empty($val)) {
                $param[$key] = '';
            }
        }

        return $param;
    }

    /*
     * Addresses
     */
    protected function getMemberProfileAndAddress(Request $request)
    {
        try {
            $existMemberAddress = $this->getBuyerInformation();
            $makroSdk = app()->make('makroSdk');
            $profile = $makroSdk->member()->profile();
            AuthHelper::updateUserFromProfile($profile);
        } catch (\Exception $e) {
            throw $e;
        }

        $address = [];
        if ($request->has('use_existing_buyer_information') && $request->input('use_existing_buyer_information') == 1) {
            if ($existMemberAddress) {
                $address = [
                    'id' => $existMemberAddress['id'],
                    'shop_name' => $existMemberAddress['address_name'],
                    'first_name' => $profile['first_name'],
                    'last_name' => $profile['last_name'],
                    'phone' => $profile['phone'],
                    'email' => $profile['email'],
                    'address_line1' => $existMemberAddress['address'],
                    'address_line2' => $existMemberAddress['address2'],
                    'address_line3' => $existMemberAddress['address3'],
                    'road_street' => 'Test Road',
                    'township' => $existMemberAddress['subdistrict'],
                    'city' => $existMemberAddress['district'],
                    'province' => $existMemberAddress['province'],
                    'postcode' => $existMemberAddress['postcode'],
                    'subdistrict_id' => $existMemberAddress['original_subdistrict']['id'],
                    'district_id' => $existMemberAddress['original_district']['id'],
                    'province_id' => $existMemberAddress['original_province']['id'],
                ];

            }

        } else {
            $address = $request->input('customer');

            if (!empty($existMemberAddress['id'])) {
                $address['id'] = $existMemberAddress['id'];
            }

            //Get address names
            try {
                $makroSdk = app()->make('makroSdk');

                $township = $makroSdk->address()->getSubDistrictById($address['subdistrict_id']);
                $city = $makroSdk->address()->getDistrictById($address['district_id']);
                $province = $makroSdk->address()->getProvinceById($address['province_id']);

                $address['township'] = $township['data']['name'];
                $address['city'] = $city['data']['name'];
                $address['province'] = $province['data']['name'];
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return [
            'profile' => $profile,
            'address' => $address
        ];
    }

    protected function getBillingAdddress(Request $request, $memberAddress, $makroCardInfo, $buyerAddress)
    {
        if (!empty($makroCardInfo)) {
            try {
                //Orange card or other card
                $address_line1 = array_get($makroCardInfo, 'billing_address.address', '');
                $postcode = array_get($makroCardInfo, 'billing_address.postcode', '');
                $province = array_get($makroCardInfo, 'billing_address.province', '');
                $city = array_get($makroCardInfo, 'billing_address.district', '');
                $township = array_get($makroCardInfo, 'billing_address.subdistrict', '');
                $phone = array_get($buyerAddress, 'phone');
                $email = array_get($makroCardInfo, 'billing_address.contact_email', '');

                //If billing_address from siebel is empty then use invoice address instead
                if (!isset($makroCardInfo['billing_address']) || empty($makroCardInfo['billing_address'])) {
                    $address_line1 = array_get($memberAddress, 'address_line1', '');
                    $postcode = array_get($memberAddress, 'postcode', '');
                    $province = array_get($memberAddress, 'province', '');
                    $city = array_get($memberAddress, 'city', '');
                    $township = array_get($memberAddress, 'township', '');
                    $phone = array_get($memberAddress, 'phone', '');
                    $email = array_get($memberAddress, 'email', '');
                }

                //Green card
                if (strtolower($makroCardInfo['makro_member_type']) == 'green') {
                    $address_line1 = array_get($buyerAddress, 'address_line1', '');
                    $postcode = array_get($buyerAddress, 'postcode', '');
                    $province = array_get($buyerAddress, 'province', '');
                    $city = array_get($buyerAddress, 'city', '');
                    $township = array_get($buyerAddress, 'township', '');
                }

                $data = [
                    'first_name' => array_get($makroCardInfo, 'billing_address.first_name', ''),
                    'last_name' => array_get($makroCardInfo, 'billing_address.last_name', ''),
                    'city' => $city,
                    'township' => $township,
                    'province' => $province,
                    'phone' => $phone,
                    'email' => $email,
                    'address_line1' => $address_line1,
                    'address_line2' => '',
                    'postcode' => $postcode
                ];

            } catch (\Exception $e) {
                throw $e;
            }

            return $data;
        }

        $same = false;
        if ($request->has('billing_address_same') && $request->input('billing_address_same') == 1) {
            $same = true;
        }

        if ($same) {
            $memberAddress['first_name'] = '';
            $memberAddress['last_name'] = '';
            return $memberAddress;
        }

        $billingAddress = $request->input('billing_address');

        if (!empty($billingAddress)) {
            try {
                $makroSdk = app()->make('makroSdk');

                $township = $makroSdk->address()->getSubDistrictById($billingAddress['subdistrict_id']);
                $city = $makroSdk->address()->getDistrictById($billingAddress['district_id']);
                $province = $makroSdk->address()->getProvinceById($billingAddress['province_id']);

                $billingAddress['township'] = $township['data']['name'];
                $billingAddress['city'] = $city['data']['name'];
                $billingAddress['province'] = $province['data']['name'];
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return $billingAddress;
    }

    protected function getInvoiceAdddress(Request $request, $makroCardInfo, $buyerAddress, $memberProfile)
    {
        //Get address from makro card info first
        if (!empty($makroCardInfo)) {
            try {
                //Orange card
                $phone = array_get($makroCardInfo, 'member.0.business.mobile_phone', '');
                $email = array_get($makroCardInfo, 'member.0.business.email', '');
                $address_line1 = array_get($makroCardInfo, 'tax_address.address', '');
                $postcode = array_get($makroCardInfo, 'tax_address.postcode', '');
                $province = array_get($makroCardInfo, 'tax_address.province', '');
                $city = array_get($makroCardInfo, 'tax_address.district', '');
                $township = array_get($makroCardInfo, 'tax_address.subdistrict', '');

                //Green card
                if (strtolower($makroCardInfo['makro_member_type']) == 'green') {
                    $address_line1 = array_get($buyerAddress, 'address_line1', '');
                    $postcode = array_get($buyerAddress, 'postcode', '');
                    $province = array_get($buyerAddress, 'province', '');
                    $city = array_get($buyerAddress, 'city', '');
                    $township = array_get($buyerAddress, 'township', '');
                }

                $companyName = array_get($makroCardInfo, 'request_response.CustName', '');
                $taxIdNumber = array_get($makroCardInfo, 'request_response.CustTax', '');
                $branchNo = array_get($makroCardInfo, 'request_response.CustBranch', '');

                $data = [
                    "company_name" => empty($companyName) ? '' : $companyName,
                    "tax_id_number" => empty($taxIdNumber) ? '' : $taxIdNumber,
                    "branch_no" => empty($branchNo) ? '' : $branchNo,
                    "phone" => $phone,
                    "email" => $email,
                    "address_line1" => $address_line1,
                    "address_line2" => "",
                    "province_id" => "",
                    "district_id" => "",
                    "subdistrict_id" => "",
                    "postcode" => $postcode,
                    "province" => $province,
                    "city" => $city,
                    "township" => $township
                ];
            } catch (\Exception $e) {
                throw $e;
            }

            return $data;
        }

        if ($request->has('is_use_invoice_address') && $request->input('is_use_invoice_address') == 1) { // แสดงว่ากรอกข้อใบกำกับภาษีมา
            $invoiceAddress = $request->input('invoice_address');

            try {
                $makroSdk = app()->make('makroSdk');

                $resProvince = $makroSdk->address()->getProvinceById($invoiceAddress['province_id']);
                $invoiceAddress['province'] = $resProvince['data']['name'];

                $resCity = $makroSdk->address()->getDistrictById($invoiceAddress['district_id']);
                $invoiceAddress['city'] = $resCity['data']['name'];

                $resTownship = $makroSdk->address()->getSubDistrictById($invoiceAddress['subdistrict_id']);
                $invoiceAddress['township'] = $resTownship['data']['name'];
            } catch (\Exception $e) {
                throw $e;
            }

            return $invoiceAddress;
        } else { // ถ้าไม่กรอกให้เอาข้อมูลของ buyer ส่งไปแทน
            $address_line1 = array_get($buyerAddress, 'address_line1', '');
            $postcode = array_get($buyerAddress, 'postcode', '');
            $province = array_get($buyerAddress, 'province', '');
            $city = array_get($buyerAddress, 'city', '');
            $township = array_get($buyerAddress, 'township', '');

            $data = [
                "company_name" => array_get($buyerAddress, 'shop_name', ''),
                "tax_id_number" => '',
                "branch_no" => '',
                "phone" => array_get($buyerAddress, 'phone', ''),
                "email" => array_get($buyerAddress, 'email', ''),
                "address_line1" => $address_line1,
                "address_line2" => "",
                "province_id" => array_get($buyerAddress, 'province_id', ''),
                "district_id" => array_get($buyerAddress, 'district_id', ''),
                "subdistrict_id" => array_get($buyerAddress, 'subdistrict_id', ''),
                "postcode" => $postcode,
                "province" => $province,
                "city" => $city,
                "township" => $township
            ];

            // กรณีที่ is_created_siebel == y แต่ยังไม่ได้ makro_card // 2018-10-19 เอาข้อมุลจาก tax address มาใส่
            if (strtolower(array_get($memberProfile,'is_created_siebel', 'n')) == 'y') {
                try {
                    $makroSdk = app()->make('makroSdk');
                    $response = $makroSdk->memberAddress()->getMemberAddresses(['address_type' => MemberAddress::ADDRESS_TYPE_TAX]);
                    $memberTaxAddress = head(array_get($response, 'data', []));
                } catch (\Exception $e) {
                    throw $e;
                }

                $data = [
                    "company_name" => array_get($memberTaxAddress, 'address_name', ''),
                    "tax_id_number" => array_get($memberProfile, 'tax_id', ''),
                    "branch_no" => array_get($memberProfile, 'business.branch', ''),
                    "phone" => array_get($memberTaxAddress, 'contact_phone', ''),
                    "email" => array_get($memberTaxAddress, 'contact_email', ''),
                    "address_line1" => array_get($memberTaxAddress, 'address', ''),
                    "address_line2" => array_get($memberTaxAddress, 'address2', ''),
                    "province_id" => array_get($memberTaxAddress, 'original_province.id', ''),
                    "district_id" => array_get($memberTaxAddress, 'original_district.id', ''),
                    "subdistrict_id" => array_get($memberTaxAddress, 'original_subdistrict.id', ''),
                    "postcode" => array_get($memberTaxAddress, 'postcode', ''),
                    "province" => array_get($memberTaxAddress, 'province', ''),
                    "city" => array_get($memberTaxAddress, 'district', ''),
                    "township" => array_get($memberTaxAddress, 'subdistrict', '')
                ];
            }

            return $data;
        }
    }

    protected function checkMinimumCheckout($cart)
    {
        $return = [
            'status' => true,
        ];

        try {
            $minimumCheckout = $this->getMinimumCheckout();
            $summary = $this->getCartSummaryData($cart);

            $amount = doubleval(array_get($minimumCheckout, 'data.attributes.minimum_checkout'));
            if ($summary['grand_total'] < $amount) {
                $return = [
                    'status' => false,
                    'amount' => $amount,
                ];
            }
        } catch (\Exception $e) {

        }

        return $return;
    }

    protected function reserveOrder($cart, $paymentType = 'PayAtStore', $deliveryType = 'shipping')
    {
        $makroSdk = app()->make('makroSdk');
        //Cancel old reserve
        $oldReserveData = CartHelper::getReserveData();
        if (!empty($oldReserveData['reserve_id'])) {
            try {
                $makroSdk->order()->cancelReserve($oldReserveData['reserve_id']);
            } catch (\Exception $e) {
                // throw $e;
            }
        }

        try {
            session()->put('paymentType', $paymentType);
            $response = $makroSdk->order()->reserve([
                'payment_id' => $paymentType,
                'delivery_type' => $deliveryType
            ]);
        } catch (\Exception $e) {
            throw $e;
        }


        if (array_get($response, 'status') == 'fail') {
            return [
                'fail' => true,
                'response' => $response
            ];
        }

        $response = $response['data'];
        $response['id'] = isset($response['id']) ? $response['id'] : null;
        $response['reserve_id'] = isset($response['id']) ? $response['id'] : null;

        $newItems = [];
        if (isset($response['items']) && isset($response['items']['data']) && !empty($response['items']['data'])) {
            foreach ($response['items']['data'] as $item) {
                $newItems[$item['item_id']] = $item;
            }
        }

        $response['items'] = $newItems;

        //Store reserve data to session
        $reserveData = $response;
        $reserveData['cart_items'] = $cart['data'];
        $reserveData['store_id'] = StoreHelper::getCurrentStore();
        $reserveData['cart_summary'] = $this->getCartSummaryData($cart);

        $reserveData = [
            'id' => array_get($response, 'id'),
            'store_id' => array_get($response, 'store_id'),
            'reserve_id' => array_get($response, 'id'),
        ];

        CartHelper::saveReserveData($reserveData);

        return $response;
    }

    protected function getReserveItems($cartItems)
    {
        $items = [];
        foreach ($cartItems as $cartItem) {
            $items[] = [
                'id' => $cartItem['content_id'],
                'quantity' => $cartItem['quantity']
            ];
        }

        return $items;
    }

    public function gatewayPayment(Request $request)
    {
        $orderDetail = $request->session()->get('order_detail', null);

        if (empty($orderDetail) && !isset($orderDetail['detail'])) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'gatewayPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0028',
                'error' => 'session order_detail is empty',
            ];

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return redirect()->route('carts.index');
        }

        $request->session()->forget('order_detail');

        //Log Start
        $user = AuthHelper::user();
        $username = array_get($user, 'username', 'unknown');
        $logName = 'gatewayPayment';
        $fileName = str_replace(['@', '.'], '-', $username) . '-pass';
        $logs = [
            'log_step' => '1009',
            'orderNo' => array_get($orderDetail, 'ms_order_no'),
        ];
        $this->writeCheckoutLog($logName, $fileName, $logs);
        //Log End

        if (MakroHelper::useMockUP()) {
            try {
                $orderNo = $orderDetail['ms_order_no'];
                $storeId = $orderDetail['store_id'];
                $memberId = $orderDetail['member_id'];

                $makroSdk = app()->make('makroSdk');
                $makroSdk->cart()->clear($storeId, $memberId);
            } catch (\Exception $e) {

            }

            return redirect()->route('carts.payment.success', $orderNo);
        }

        try {
            $makroSdk = app()->make('makroSdk');

            $paymentGatewayDriver = array_get($orderDetail, 'detail.payment.payment_gateway_driver');

            $params = [
                'success_url' => route('carts.payment.gateway.foreground',
                    ['driver' => $paymentGatewayDriver, 'status' => 'success']),
                'cancel_url' => route('carts.payment.gateway.foreground',
                    ['driver' => $paymentGatewayDriver, 'status' => 'fail']),
                'backend_url' => route('gateway.background.process', ['driver' => $paymentGatewayDriver]),
            ];

            $response = $makroSdk->gateway()->requestPayment($orderDetail['ms_order_no'], $params);
        } catch (\Exception $e) {
            // Nothing to do now.
            if ($e instanceof SDKException) {
                $message = $e->getErrors();
            } else {
                $message = $e->getMessage();
            }

            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'gatewayPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0029',
                'error' => 'gatewayPayment error',
                'Exception.getMessage' => $e->getMessage(),
            ];

            if ($e instanceof SDKException) {
                $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                $logs['SDKException.getCode'] = $e->getCode();
                $logs['SDKException.getErrors'] = $e->getErrors();
            }

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            if (in_array($e->getCode(),
                ['205002', '205404', '100002', '216001', '208001', '209404', '200404', '216404', '219404'])) {
                return view('cart.connection-error', ['step' => 3]);
            }

            if (str_is('220*', $e->getCode())) { //Gateway error
                return redirect()->back()->withErrors([__('frontend.generate_gateway_payment_form_failed_please_try_again')]);
            }

            return redirect()->back()->withErrors($message);
        }

        if (empty($response['data']['form']) || !preg_match('/form/', $response['data']['form'])) {
            //Log Start
            $user = AuthHelper::user();
            $username = array_get($user, 'username', 'null');
            $logName = 'gatewayPayment';
            $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
            $logs = [
                'log_step' => '0030',
                'error' => 'gatewayPayment error generate payment form fail',
            ];

            $this->writeCheckoutLog($logName, $fileName, $logs);
            //Log End

            return redirect()->back()->withErrors([
                __('frontend.generate_gateway_payment_form_failed_please_try_again')
            ]);
        }

        if (!empty($response['data']['reference_id'])) {
            try {
                $updateParameters = [
                    'PaymentStatus' => 'PENDING',
                    'PaymentReference1' => $response['data']['reference_id']
                ];

                $makroSdk->order()->update($orderDetail['ms_order_no'], $updateParameters);
            } catch (\Exception $e) {
                //Log Start
                $user = AuthHelper::user();
                $username = array_get($user, 'username', 'null');
                $logName = 'gatewayPayment';
                $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
                $logs = [
                    'log_step' => '0031',
                    'error' => 'gatewayPayment error update order',
                    'Exception.getMessage' => $e->getMessage(),
                    'payment_id' => $response['data']['reference_id'],
                ];

                if ($e instanceof SDKException) {
                    $logs['SDKException.getDeveloperMessage'] = $e->getDeveloperMessage();
                    $logs['SDKException.getUserMessage'] = $e->getUserMessage();
                    $logs['SDKException.getCode'] = $e->getCode();
                    $logs['SDKException.getErrors'] = $e->getErrors();
                }

                $this->writeCheckoutLog($logName, $fileName, $logs);
                //Log End
            }
        }

        //Log Start
        $user = AuthHelper::user();
        $username = array_get($user, 'username', 'unknown');
        $logName = 'gatewayPayment';
        $fileName = str_replace(['@', '.'], '-', $username) . '-pass';
        $logs = [
            'log_step' => '1010',
            'orderNo' => array_get($orderDetail, 'ms_order_no'),
            'reference_id' => array_get($response, 'reference_id'),
            // 'paymentConnecting' => array_get($response, 'data'),
        ];
        $this->writeCheckoutLog($logName, $fileName, $logs);
        //Log End

        $data = [
            'paymentConnecting' => $response['data']['form'],
        ];

        $this->addBreadcrumb('carts.checkout', trans('frontend.checkout', []), route('carts.checkout'));

        return view('cart.gateway', $data);
    }

    public function offlinePayment(Request $request)
    {
        $orderDetail = $request->session()->get('order_detail', null);

        if (empty($orderDetail) && !isset($orderDetail['detail'])) {
            abort(400, __('frontend.invalid_access'));
        }

        $request->session()->forget('order_detail');

        $makroSdk = app()->make('makroSdk');

        $orderNo = null;
        if (!empty($orderDetail)) {
            $orderNo = $orderDetail['ms_order_no'];
            $storeId = $orderDetail['store_id'];
            $memberId = $orderDetail['member_id'];

            // update order status
            try {
                $response = $makroSdk->order()->update($orderNo,
                    ['PaymentStatus' => 'PENDING', 'PaymentReference1' => $orderNo]);
            } catch (\Exception $e) {

            }

            //clear cart
            try {
                $response = $makroSdk->cart()->clear($storeId, $memberId);
            } catch (\Exception $e) {

            }

            //ยิง notify order WAIT_PAYMENT
            try {
                $makroSdk->order()->notify($orderNo, ['type' => 'WAIT_PAYMENT']);
            } catch (\Exception $e) {

            }
        }

        request()->session()->flash('paymentSuccessData', ['paymentType' => 'PayAtStore', 'orderNo' => $orderNo]);

        return redirect()->route('carts.payment.success', $orderNo);
    }

    public function paymentGatewayForeground($driver, $status = '')
    {
        $makroSdk = app()->make('makroSdk');
        $_orderDetail = request()->session()->get('order_detail', null);

        //Log Start
        $user = AuthHelper::user();
        $username = array_get($user, 'username', 'unknown');
        $logName = 'paymentGatewayForeground';
        $fileName = str_replace(['@', '.'], '-', $username) . '-pass';
        $logs = [
            'log_step' => '1011',
            'orderNo' => array_get($_orderDetail, 'ms_order_no'),
            'request' => request()->all(),
        ];
        $this->writeCheckoutLog($logName, $fileName, $logs);
        //Log End

        try {
            // Call API recheck TrueMoney
            $foregroundResult = $makroSdk->gateway()->foregroundResult($driver, request()->all());
        } catch (\Exception $e) {
            // Nothing to do now.
            abort(404);
        }

        // invoice คือ order_no
        $orderNo = $foregroundResult['data']['order_no'];

        try {
            $orderDetail = $makroSdk->order()->getOrderDetail($orderNo, ['includes' => 'detail']);
        } catch (\Exception $e) {
            if ($e instanceof SDKException) {
                if ($e->getCode() == 203404) {
                    $message = __('frontend.not_found_order', ['code' => $e->getCode()]);
                } else {
                    $message = $e->getUserMessage();
                }
            } else {
                $message = $e->getMessage();
            }
        }

        // If success
        // 2018-07-17 ดัก case wallet จ่ายไม่สำเร็จ แต่ สถานะการชำระเงินเป็น waiting พอแปลงผ่าน api gateway ก็จะเป็น pending ซึ่งต้องไม่ส่ง email แล้วต้องไปหน้า fail
        if (!empty($foregroundResult['data']['status']) && in_array($foregroundResult['data']['status'],
                ['success', 'pending']) && $status == 'success') {
            CartHelper::saveReserveData(null);

            if (!empty($orderDetail)) {
                //clear cart
                try {
                    $response = $makroSdk->cart()->clear($orderDetail['store_id'], $orderDetail['member_id']);
                } catch (\Exception $e) {

                }

                try {
                    if ($foregroundResult['data']['status'] == 'success') {
                        $makroSdk->order()->notify($orderNo, ['type' => 'PAY_SUCCESS']);
                    } else {
                        $makroSdk->order()->notify($orderNo, ['type' => 'WAIT_PAYMENT']);
                    }
                } catch (\Exception $e) {

                }
            }

            return redirect()->route('carts.payment.success', $orderNo);
        } else {
            if (!empty($orderDetail)) {
                // cancel reserve
                try {
                    $response = $makroSdk->order()->cancelReserve($orderDetail['ms_reserve_id']);
                    CartHelper::saveReserveData(null);
                } catch (\Exception $e) {

                }
            }

            return redirect()->route('carts.payment.fail');
        }
    }

    public function paymentSuccess($id)
    {
        //Get order detail
        $makroSdk = app()->make('makroSdk');
        try {
            $orderDetail = $makroSdk->order()->getOrderDetail($id, ['includes' => 'detail']);
        } catch (\Exception $e) {
            if ($e instanceof SDKException) {
                if ($e->getCode() == 203404) {
                    $message = __('frontend.not_found_order', ['code' => $e->getCode()]);
                } else {
                    $message = $e->getUserMessage();
                }
            } else {
                $message = $e->getMessage();
            }

            abort(400, $message);
        }

        //Check member_id of order detail is match current login member
        if (AuthHelper::getMemberId() != $orderDetail['member_id']) {
            abort(400, __('frontend.invalid_access'));
        }

        if (strtolower(array_get($orderDetail, 'detail.payment.payment_type')) == 'gateway'
            && !in_array(snake_case(array_get($orderDetail, 'detail.order_status')),
                ['waiting_for_payment', 'order_pending', 'order_created', 'order_confirmed', 'in_progress'])) {
           abort(404);
        }

        //Convert order detail data to Cart data
        $orderDetail['convert_type'] = 'detail';
        $cart = CartHelper::convertOrderDetailDataToCartData($orderDetail);
        $cart = CartHelper::generateOrderItem($cart['data']);
        $data['cart'] = CartHelper::getCartGroup($cart);
        $data['summary'] = $orderDetail['detail']['summary'];
        $data['promotions'] = $orderDetail['detail']['promotions'];
        $data['detail'] = CartHelper::getOrderDetailData($orderDetail, $data['cart']);
        $data['orderDetail'] = $orderDetail;


        //Sum VAT
        $items = array_get($data['orderDetail'], 'detail.items');
        $vatTotal = 0;
        foreach ($items as $item) {
            $vat = array_get($item, 'vat_total', 0);
            $vatTotal += $vat;
        }

        $data['vat_total'] = $vatTotal;
        $data['is_first'] = request()->session()->has('paymentSuccessData') ? true : false;

        //Get delivery dates
        $data['delivery_dates'] = null;
        try {
            $makroSdk = app()->make('makroSdk');
            $data['delivery_dates'] = CartHelper::getDeliveryDates($makroSdk->config()->getDeliveryDate());
        } catch (\Exception $e) {
            //Do nothing
        }

        $this->addBreadcrumb('carts.checkout', trans('frontend.thank_you', []), route('carts.checkout'));

        return view('cart.payment-success', $data);
    }

    public function paymentFail(Request $request)
    {
        $data = [];
        return view('cart.payment-fail', $data);
    }

    public function addItem(Request $request)
    {
        $this->validate($request, [
            'content_id' => 'required',
            'quantity' => 'required'
        ]);
        $makroSdk = app()->make('makroSdk');
        try {
            $cartData = $this->getCartData(Cart::CART_TYPE_GENERAL, StoreHelper::getCurrentDeliveryType());

            $count = count(array_get($cartData, 'response.data', []));
            $isUpdate = collect($cartData['response']['data'])->where('content_id', $request->get('content_id'));
            $max = CartHelper::getMaximumBasket();
            if (count($isUpdate) == 0) {
                if ($count >= $max) {
                    return response()->json([
                        'status' => 'error',
                        'message' => trans('frontend.maximum_product_in_cart', ['items' => $max]),
                        'errors' => null,
                    ]);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error',
                'message' => trans('frontend.could_not_add_product_to_cart_please_try_again', ['code' => $e->getErrorCode()]),
                'errors' => is_callable([$e, 'getErrors']) ? $e->getErrors() : null,
                'error_code' => $e->getCode(),//'Exception' => (array) $e,
            ]);
        }

        $response = null;
        try {
            $storeId = $request->input('store_id');
            if (empty($storeId)) {
                $storeId = StoreHelper::getCurrentStore();
            }

            StoreHelper::setCurrentStore($storeId, false);

            $input = $request->input();
            $data = [
                'store_id' => $storeId,
                'content_id' => $input['content_id'],
                'quantity' => $input['quantity'],
                'session_id' => AuthHelper::tempMemberId()
            ];

            $response = $makroSdk->cart()->addItem($data);
        } catch (\Exception $e) {
            $errorCode = null;
            $message = trans('frontend.could_not_add_product_to_cart_please_try_again', ['code' => $e->getErrorCode()]);
            if ($e instanceof SDKException) {
                $errorCode = $e->getErrorCode();

                switch ($errorCode) {
                    case 205404 ://Product not found on MS
                    case 205405 ://Product unavailable in store
                        $message = trans('frontend.could_not_add_product_to_cart_please_try_again',
                            ['code' => $errorCode]);
                        break;
                    case 205406 ://Product unavailable in store
                        $message = trans('frontend.product_not_in_store', ['code' => $errorCode]);
                        break;
                }
            }

            if (empty($errorCode)) {

            }

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'errors' => is_callable([$e, 'getErrors']) ? $e->getErrors() : null,
                'error_code' => $errorCode,
                //'Exception' => (array) $e,
            ]);
        }

        return $this->getCart();
    }

    public function updateItem(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);

        $this->validate($request, [
            'store_id' => 'required',
            'id' => 'required',
            'quantity' => 'required'
        ]);

        try {
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->cart()->updateItem($request->input('id'), $request->input('quantity'));
        } catch (\Exception $e) {
            $errorCode = null;
            $message = trans('frontend.could_not_update_product_to_cart_please_try_again', ['code' => $e->getCode()]);
            if ($e instanceof SDKException) {
                $errorCode = $e->getErrorCode();

                switch ($errorCode) {
                    case 205404 ://Product not found on MS
                    case 205405 ://Product not publish
                        $message = trans('frontend.could_not_update_product_to_cart_please_delete_it',
                            ['code' => $errorCode]);
                        break;
                    case 205406 ://Product unavailable in store
                        $message = trans('frontend.product_not_in_store', ['code' => $errorCode]);
                        break;
                }
            }

            if (empty($errorCode)) {

            }

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'errors' => is_callable([$e, 'getErrors']) ? $e->getErrors() : null,
                'error_code' => $errorCode,
                //'Exception' => (array) $e,
            ]);

//            $message = trans('frontend.could_not_update_product_to_cart_please_try_again');
//            if ($e instanceof SDKException) {
//                switch ($e->getErrorCode()) {
//                    case 208006 ://MS Bracket update error
//                        break;
//                }
//            }
//
//            return response()->json([
//                'status' => 'error',
//                'message' => $message,
//                'errors' => is_callable([$e, 'getErrors']) ? $e->getErrors() : null
//            ]);
        }

        $this->checkProductContent = true;
        return $this->getCart();
    }

    public function removeItem(Request $request)
    {
        try {
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->cart()->deleteItem($request->input('id'));
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
                'error_code' => $e->getCode()
            ]);
        }

        $this->checkProductContent = true;
        return $this->getCart($request);
    }

    protected function getCartItems($type = 'content.price', $deliveryType = null)
    {
        try {
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->cart()->get(StoreHelper::getCurrentStore(), AuthHelper::tempMemberId(), [], $type, $deliveryType);
        } catch (\Exception $e) {
            if ($e instanceof SDKException) {
                //Log Start
                $user = AuthHelper::user();
                $username = array_get($user, 'username', 'null');
                $logName = 'getCartItems';
                $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
                $logs = [
                    'log_step' => 'null',
                    'Exception.getErrorCode' => $e->getErrorCode(),
                    'Exception.getDeveloperMessage' => $e->getDeveloperMessage(),
                    'Exception.getErrors' => $e->getErrors(),
                    'getCurrentStore' => StoreHelper::getCurrentStore(),
                ];

                $this->writeCheckoutLog($logName, $fileName, $logs);
                //Log End
            } else {
                //Log Start
                $user = AuthHelper::user();
                $username = array_get($user, 'username', 'null');
                $logName = 'getCartItems';
                $fileName = str_replace(['@', '.'], '-', $username) . '-fail';
                $logs = [
                    'log_step' => 'null',
                    'Exception.getCode' => $e->getCode(),
                    'Exception.getMessage' => $e->getMessage(),
                    'getCurrentStore' => StoreHelper::getCurrentStore(),
                ];

                $this->writeCheckoutLog($logName, $fileName, $logs);
                //Log End
            }

            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return $response;
    }

    protected function getMinimumCheckout()
    {
        try {
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->cart()->getMinimumCheckout();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return $response;
    }

    protected function groupCartData($data, $checkProductContent = true)
    {
        if (isset($data['data'])) {
            return CartHelper::getCartGroup($data['data'], [], $checkProductContent);
        }

        return [];


//        $numGroup = sizeof($data['data']) / $this->groupLimit;
//
//        $groupData = [];
//        for ($i = 0; $i < $numGroup; ++$i) {
//            $groupData[$i] = [];
//            $groupData[$i]['title'] = 'Group ' . ($i + 1);
//            $groupData[$i]['items'] = $this->getGroup($data['data'], ($i * 5), $this->groupLimit);
//        }
//
//        return $groupData;
    }

    protected function getGroup($data, $start, $limit)
    {
        $newData = [];
        for ($i = $start; $i < ($start + $limit); ++$i) {
            if (isset($data[$i]) && (isset($data[$i]['content']) && !empty($data[$i]['content']))) {
                $newData[] = $data[$i];
            }
        }

        return $newData;
    }

    protected function getCartSummaryData($cart)
    {
        $default = [
            'grand_total' => 0,
            'product_fee' => 0,
            'sale_tax' => 0,
            'delivery_fee' => 0,
            'sub_total' => 0
        ];

        return (isset($cart['meta']['summary'])) ? $cart['meta']['summary'] : $default;
    }

    public function clearCart()
    {
        /*
         * ทดสอบเคลียร์ชั่วคราว
         */
        try {
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->cart()->clear($this->storeId, $this->memberId);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return $response['data']['attributes'];
    }

    protected function getMakroCardInfo($makroCardId)
    {
        $response = \Cache::tags(['global', 'makroCard'])->remember('makroCard_' . $makroCardId, 60, function () use ($makroCardId) {
            try {
                $makroSdk = app()->make('makroSdk');
                $response = $makroSdk->member()->getMakroCardInfo($makroCardId, ['includes' => 'member']);

                if (! empty($response['data'])) {
                    return $response['data'];
                }
            } catch (\Exception $e) {
                throw $e;
            }

            return null;
        });

        $makroCardInfo = null;
        $hasMakroCard = false;

        if (! empty($response)) {
            $hasMakroCard = true;
            $makroCardInfo = $response;
        }

        return [
            'has_makro_card' => $hasMakroCard,
            'makro_card_info' => $makroCardInfo
        ];
    }

    private function writeCheckoutLog($logName, $fileName, array $logs = [])
    {
        if (env('ENABLE_CUSTOM_CHECKOUT_LOG')) {
            $date = date('Y-m-d');

            $h = date('H');
            switch ($h) {
                case 0:
                case 1:
                    $period = '00-02';
                    break;
                case 2:
                case 3:
                    $period = '02-04';
                    break;
                case 4:
                case 5:
                    $period = '04-06';
                    break;
                case 6:
                case 7:
                    $period = '06-08';
                    break;
                case 8:
                case 9:
                    $period = '08-10';
                    break;
                case 10:
                case 11:
                    $period = '11-12';
                    break;
                case 12:
                case 13:
                    $period = '12-14';
                    break;
                case 14:
                case 15:
                    $period = '14-16';
                    break;
                case 16:
                case 17:
                    $period = '16-18';
                    break;
                case 18:
                case 19:
                    $period = '18-20';
                    break;
                case 20:
                case 21:
                    $period = '20-22';
                    break;
                case 22:
                case 23:
                    $period = '22-00';
                    break;
            }

            $logFIle = storage_path("logs/checkout/{$date}/{$period}/{$fileName}.log");

            $logs = array_merge(['date_time' => \Carbon\Carbon::now()->toDateTimeString()], $logs);

            $view_log = new \Monolog\Logger('CHECKOUT');
            $view_log->pushHandler(new \Monolog\Handler\StreamHandler($logFIle, \Monolog\Logger::INFO));
            $view_log->addInfo($logName, $logs);
            //Log End
        }
    }

    protected function checkOrderAmount($cart)
    {
        $passed = true;
        foreach ($cart as $group) {
            foreach ($group['items'] as $item) {
                $min = array_get($item, 'content.data.minimum_order_limit', null);
                $max = array_get($item, 'content.data.maximum_order_limit', null);
                $stock = array_get($item, 'content.data.stock', null);

                if (is_numeric($stock) && $stock >= 0) {
                    $max = $stock;
                }

                if ($min) {
                    if ($item['quantity'] < $min) {
                        return false;
                    }
                }

                if ($max) {
                    if ($item['quantity'] > $max) {
                        return false;
                    }
                }
            }
        }

        return $passed;
    }

    public function updateItems(Request $request)
    {
        $data = $request->all();

        try {
            foreach ($data as $group => $items) {
                switch ($group) {
                    case 'unpublished':
                        $this->updateUnpublishedItems($items);
                        break;
                    case 'not_enough_inventory':
                        $this->updateNotEnoughInventoryItems($items);
                        break;
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('frontend.could_not_confirmation_to_proceed'),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'ok',
        ]);
    }

    protected function updateUnpublishedItems($items)
    {
        $makroSdk = app()->make('makroSdk');
        foreach ($items as $item) {
            $makroSdk->cart()->deleteItem($item['id']);
        }
    }

    protected function updateNotEnoughInventoryItems($items)
    {
        $makroSdk = app()->make('makroSdk');
        foreach ($items as $item) {
            $makroSdk->cart()->updateItem($item['id'], $item['qty']);
        }
    }

    public function reOrder(Request $request)
    {
        try {
            $makroSdk = app()->make('makroSdk');

            if (!empty($request->all())) {
                $data['items'] = $request->all();
                $response = $makroSdk->cart()->checkItemStatus($data);

                $collection = collect($response['data']);

                $reject = $collection->reject(function ($arr, $key) {
                    return $arr['status'] == 'available';
                })->values()->all();

                $available = $collection->reject(function ($arr, $key) {
                    return !in_array($arr['status'], ['not_enough_inventory', 'available']);
                })->values()->all();

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'passed' => empty($reject),
                        'items' => [
                            'all' => $data['items'],
                            'available' => $available,
                            'reject' => $reject
                        ]
                    ],
                ]);
            }
        } catch (\Exception $exception) {
            $message = __('frontend.could_not_add_product_to_cart_please_try_again', ['code' => $exception->getCode()]);
            if ($exception->getCode() == 100002) { //Can not call RM3
                $message = trans('frontend.can_not_get_cart_because_can_not_call_rm3',
                    ['code' => $exception->getCode()]);
            }
            return response()->json([
                'status' => 'error',
                'error_code' => $exception->getCode(),
                'message' => $message
            ]);
        }

    }

    public function addItems(Request $request)
    {
        $makroSdk = app()->make('makroSdk');

        try {
            $cartData = $this->getCartData(Cart::CART_TYPE_GENERAL, StoreHelper::getCurrentDeliveryType());

            $collectCartData = collect($cartData['response']['data']);
            $content_ids = collect($request->get('items'))->pluck('content_id')->toArray();
            $cartData = $collectCartData->reject(function ($item) use ($content_ids) {
                return in_array($item['content_id'], $content_ids);
            });

            $max = CartHelper::getMaximumBasket();
            $count = count($cartData->toArray()) + count($content_ids);
            if ($count > $max) {
                return response()->json([
                    'status' => 'error',
                    'message' => trans('frontend.maximum_product_in_cart', ['items' => $max]),
                    'errors' => null,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error',
                'message' => trans('frontend.product_not_available_in_all_stores') . $e->getMessage(),
                'errors' => is_callable([$e, 'getErrors']) ? $e->getErrors() : null,
                'error_code' => $e->getCode(),//'Exception' => (array) $e,
            ]);
        }

        $response = null;
        try {
            $storeId = $request->input('store_id');
            if (empty($storeId)) {
                $storeId = StoreHelper::getCurrentStore();
            }

            StoreHelper::setCurrentStore($storeId, false);

            $input = $request->input();
            $data = [
                'store_id' => $storeId,
                'items' => $input['items'],
                'session_id' => AuthHelper::tempMemberId()
            ];

            $response = $makroSdk->cart()->addItems($data);
        } catch (\Exception $e) {
            $errorCode = null;
            $message = trans('frontend.there_was_a_connection_proble_please_retry_again', ['code' => $e->getCode()]);
            if ($e instanceof SDKException) {
                $errorCode = $e->getErrorCode();

                switch ($errorCode) {
                    case 205404 ://Product not found on MS
                    case 205405 ://Product unavailable in store
                        $message = trans('frontend.there_was_a_connection_proble_please_retry_again',
                            ['code' => $errorCode]);
                        break;
                    case 205406 ://Product unavailable in store
                        $message = trans('frontend.product_not_in_store', ['code' => $errorCode]);
                        break;
                }
            }

            if (empty($errorCode)) {

            }

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'errors' => is_callable([$e, 'getErrors']) ? $e->getErrors() : null,
                'error_code' => $errorCode,
                //'Exception' => (array) $e,
            ]);
        }

        return $this->getCart();
    }

    public function moveCart(Request $request)
    {
        //Validate
        $rules = [
            'store_id' => 'required',
            'store_price_id' => 'required'
        ];

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ]);
        }

        $newStoreId = $request->get('store_id');
        $newStorePriceId = $request->get('store_price_id');
        $deliveryType = $request->get('delivery_type');
        $oldStoreId = StoreHelper::getCurrentStore();
        $oldStorePriceId = StoreHelper::getCurrentStorePrice();

        $makroSdk = app()->make('makroSdk');

        //Get current(old) store cart
        $carts = [];
        try {
            $carts = $this->getCartData();
            $carts = collect(array_get($carts, 'response.data', []))->map(function ($item) {
                return [
                    'content_id' => $item['content_id'],
                    'quantity' => $item['quantity']
                ];
            })->toArray();
        } catch (\Exception $e) {
            $errorCode = null;
            $message = trans('frontend.could_not_remove_cart', ['code' => $e->getCode()]);
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

        StoreHelper::setCurrentStore($newStoreId);
        StoreHelper::setCurrentStorePrice($newStorePriceId);
        StoreHelper::setCurrentDeliveryType($deliveryType);

        if ($newStoreId != $oldStoreId) {
            //Move cart
            try {
                $parameters = [
                    'items' => $carts,
                    'session_id' => AuthHelper::tempMemberId(),
                    'store_id' => $newStoreId
                ];

                //Get items to move
                $items = $this->getItemsToMove($parameters);

                //Add cart items
                if (!empty($items)) {
                    $response = $makroSdk->cart()->addItems([
                        'items' => $items,
                        'session_id' => AuthHelper::tempMemberId()
                    ]);
                    $carts = array_get($response, 'data', []);
                } else {
                    $carts = [];
                }
            } catch (\Exception $e) {
                StoreHelper::setCurrentStore($oldStoreId);
                StoreHelper::setCurrentStorePrice($oldStorePriceId);
                StoreHelper::setCurrentDeliveryType($deliveryType);

                $errorCode = null;
                $message = trans('frontend.could_not_move_cart', ['code' => $e->getCode()]);
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

            //Clear cart
            try {
                $makroSdk->cart()->clear($oldStoreId, AuthHelper::getMemberId());
            } catch (\Exception $e) {

            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'ok',
            'items' => $carts
        ]);
    }

    protected function getItemsToMove($parameters)
    {
        $available = [];
        try {

            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->cart()->checkItemStatus($parameters);
            $collection = collect($response['data']);

            $available = $collection->reject(function ($arr, $key) {
                return !in_array($arr['status'], ['not_enough_inventory', 'available']);
            })->values()->all();

            $available = collect($available)->map(function ($item) {
                return [
                    'content_id' => $item['content_id'],
                    'quantity' => $item['available_quantity']
                ];
            })->toArray();

        } catch (\Exception $e) {
            throw $e;
        }

        return $available;
    }

    protected function checkDeliveryStoreAddressIsChanged($postcode, $subDistrictId)
    {
        $makroSdk = app()->make('makroSdk');
        $addresses = $makroSdk->address()->searchDeliveryArea(
            [
                'q' => $postcode,
                'per_page' => 999999
            ]);

        $addresses = array_get($addresses, 'data', []);
        $storeAddress = collect($addresses)->filter(function ($item) use ($subDistrictId)  {
            return $item['sub_districts']['id'] == $subDistrictId;
        })->first();

        if (empty($storeAddress)) {
            throw new \Exception('Delivery store address is not available.', 221001);
        }

        $mainStoreId = StoreHelper::getCurrentStore();
        $priceStoreId = StoreHelper::getCurrentStorePrice();

        $newMainStoreId = array_get($storeAddress, 'main_inventory_store');
        $newPriceStoreId = array_get($storeAddress, 'store_price');

        $return = [
            'changed' => false,
            'address' => $storeAddress
        ];
        if ($newMainStoreId != $mainStoreId || $newPriceStoreId != $priceStoreId) {
            $return['changed'] = true;
        }

        return $return;
    }

    public function checkPickupStoreIsAvailable($id)
    {
        try {
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->store()->get([
                'status' => 'active',
                'pickup' => 'Y',
                'limit' => 2000,
            ]);

            if (! empty($response['data'])) {
                $response = $response['data'];
            }
        } catch (\Exception $e) {
            $response = null;
        };

        $stores = collect($response);
        $store = $stores->where('id', $id)->first();
        if (empty($store)) {
            return false;
        }

        return true;
    }
}