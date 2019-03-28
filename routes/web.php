<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

if (! function_exists('getVersionAction')) {
    function getVersionAction() {
        exec('git tag', $tag);
        exec("git branch | sed -n '/\* /s///p'", $branch);
        exec('git log -1', $line);

        $line = toKeyValue($line);
        $line['tag'] = isset($tag[0]) ? $tag[0] : '';
        $line['current_branch'] = isset($branch[0]) ? $branch[0] : '';

        return $line;
    }
}

if (! function_exists('toKeyValue')) {
    function toKeyValue($data)
    {
        $version = [];

        if (!empty($data)) {
            foreach ($data as $kLine => $vLine) {
                if($kLine === 0) {
                    $pieces = explode(' ', $vLine, 2);
                    $version[strtolower($pieces[0])] = trim($pieces[1]);
                } else {
                    $pieces = explode(':', $vLine, 2);
                    switch (count($pieces)) {
                        case 2:
                            $version[strtolower($pieces[0])] = trim($pieces[1]);
                            break;
                        case 1:
                            $version['comment'] = trim($pieces[0]);
                            break;
                    }
                }
            }
        }

        return $version;
    }
}

Route::get('/version', function() {
    return response()->json(['data' => [getVersionAction()]]);
});

Route::group(['middleware' => ['web']], function () {

    Route::any('/truemoney-background-process', [
        'as'   => 'truemoney.background.process',
        'uses' => 'GatewayController@truemoneyBackgroundProcess',
        'middleware' => ['log'],
        'nocsrf' => true
    ]);

    Route::any('/gateway/{driver}/background', [
        'as'   => 'gateway.background.process',
        'uses' => 'GatewayController@backgroundProcess',
        'middleware' => ['log'],
        'nocsrf' => true
    ]);

    Route::any('/members/logout', [
        'as'   => 'members.logout',
        'uses' => 'MemberAuthController@anyLogout',
        'middleware' => ['log'],
    ]);

    Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['auth.basic.for-test', 'localeSessionRedirect', 'localizationRedirect', 'log', 'maintenancepage']], function()
    {
        Route::any('/members/login', [
            'as'   => 'members.login',
            'uses' => 'MemberAuthController@postLogin',
            'middleware' => ['guest-only'],
        ]);

        Route::get('/', [
            'as' => 'home.index',
            'uses' => 'HomeController@index'
        ]);

        // FWG
        Route::get('/mkp', [
            'as' => 'home.index_mkp',
            'uses' => 'mkpController@index'
        ]);
        //End
        Route::get('/search', [
            'as' => 'search.index',
            'uses' => 'ProductController@search'
        ]);

        Route::get('/categories', [
            'as' => 'home.site-map',
            'uses' => 'HomeController@siteMap'
        ]);

        Route::get('/contact-us', [
            'as' => 'home.contact-us',
            'uses' => 'HomeController@contactUs'
        ]);

        Route::any('/contact-message', [
            'as' => 'contact-message',
            'uses' => 'HomeController@contactMessage'
        ]);

        Route::get('/autoSearch', [
            'as' => 'autoSearch',
            'uses' => 'ProductController@autoSearch'
        ]);

        Route::group(['prefix' => 'category', 'as' => 'categories.'], function()
        {
            Route::get('/{category_id}', [
                'as' => 'show',
                'uses' => 'CategoryController@show'
            ])->where('category_id', '[1-9][0-9]*');

            Route::get('/{category_id}-', [
                'as' => 'show',
                'uses' => 'CategoryController@show'
            ])->where('category_id', '[1-9][0-9]*');

            Route::get('/{category_id}-{name?}', [
                'as' => 'show',
                'uses' => 'CategoryController@show'
            ])->where('category_id', '[1-9][0-9]*');

            Route::get('/{slug}', [
                'as' => 'show',
                'uses' => 'CategoryController@showBySlug'
            ]);

            Route::get('/recommended-products/{slug}', [
                'as' => 'recommended-products',
                'uses' => 'CategoryController@getRecommendedProducts'
            ]);
        });

        Route::group(['prefix' => 'brand', 'as' => 'brands.'], function()
        {
            Route::get('/{id}', [
                'as' => 'show',
                'uses' => 'BrandController@show'
            ])->where('id', '[1-9][0-9]*');

            Route::get('/{id}-', [
                'as' => 'show',
                'uses' => 'BrandController@show'
            ])->where('id', '[1-9][0-9]*');

            Route::get('/{id}-{name?}', [
                'as' => 'show',
                'uses' => 'BrandController@show'
            ])->where('id', '[1-9][0-9]*');

            Route::get('/{slug}', [
                'as' => 'show-by-slug',
                'uses' => 'BrandController@showBySlug'
            ]);
        });

        Route::group(['prefix' => 'stores', 'as' => 'stores.'], function()
        {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'StoreController@index'
            ]);

            Route::get('/all', [
                'as' => 'all',
                'uses' => 'StoreController@index'
            ]);

            Route::post('/set-current-store', [
                'as' => 'set-current-store',
                'uses' => 'StoreController@setCurrentStore'
            ]);

            Route::get('/get-current-store', [
                'as' => 'get-current-store',
                'uses' => 'StoreController@getCurrentStore'
            ]);

            Route::get('/set-current-store-redirect', [
                'as' => 'set-current-store-redirect',
                'uses' => 'StoreController@setCurrentStoreRedirect'
            ]);

            Route::get('/{id}', [
                'as' => 'select-store',
                'uses' => 'StoreController@selectStore'
            ])->where('id', '[1-9][0-9]*');

            Route::get('/location', [
                'as' => 'location',
                'uses' => 'StoreController@getLocation'
            ]);

            Route::get('/pickup-store', [
                'as' => 'pickup-store',
                'uses' => 'StoreController@getPickupStore'
            ]);

        });

        Route::group(['prefix' => 'product', 'as' => 'products.'], function()
        {
            Route::get('/{id}', [
                'as' => 'show',
                'uses' => 'ProductController@show'
            ])->where('id', '[1-9][0-9]*');

            Route::get('/{id}-', [
                'as' => 'show',
                'uses' => 'ProductController@show'
            ])->where('id', '[1-9][0-9]*');

            Route::get('/{id}-{name?}', [
                'as' => 'show',
                'uses' => 'ProductController@show'
            ])->where('id', '[1-9][0-9]*');

            Route::get('/{slug}', [
                'as' => 'show-by-slug',
                'uses' => 'ProductController@show'
            ]);

            Route::get('/{id}/related', [
                'as' => 'related',
                'uses' => 'ProductController@getRelatedProducts'
            ])->where('id', '[1-9][0-9]*');

            Route::get('/{id}/recent', [
                'as' => 'recent',
                'uses' => 'ProductController@getRecentProducts'
            ]);

            Route::get('/{id}/increate-view', [
                'as' => 'increate-view',
                'uses' => 'ProductController@increaseView'
            ])->where('id', '[1-9][0-9]*');

        });

        Route::group(['prefix' => 'carts', 'as' => 'carts.'], function() {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'CartController@index'
            ]);

            Route::get('/json', [
                'as' => 'json',
                'uses' => 'CartController@getCart'
            ]);

            Route::post('/add-item', [
                'as' => 'add',
                'uses' => 'CartController@addItem'
            ]);

            Route::post('/update-item', [
                'as' => 'update',
                'uses' => 'CartController@updateItem'
            ]);

            Route::post('/update-items', [
                'as' => 'update-items',
                'uses' => 'CartController@updateItems'
            ]);

            Route::post('/remove-item', [
                'as' => 'remove',
                'uses' => 'CartController@removeItem'
            ]);

            Route::get('/clear', [
                'as' => 'clear',
                'uses' => 'CartController@clearCart'
            ]);

            //Step 2: Payment
            Route::get('/payment', [
                'as' => 'checkout',
                'uses' => 'CartController@getPayment',
                'middleware' => ['auth-token']
            ]);

            Route::post('/payment', [
                'as' => 'checkout.post',
                'uses' => 'CartController@postPayment',
                'middleware' => ['auth-token']
            ]);


            //Step 3: Shipping
            Route::get('/shipping', [
                'as' => 'shipping',
                'uses' => 'CartController@getShipping',
                'middleware' => ['auth-token']
            ]);

            Route::get('/shipping-test', [
                'as' => 'shipping-test',
                'uses' => 'CartController@getShippingTest',
                'middleware' => ['auth-token']
            ]);

            Route::post('/shipping', [
                'as' => 'shipping.post',
                'uses' => 'CartController@postShipping',
                'middleware' => ['auth-token']
            ]);


            Route::post('/reserve-order', [
                'as' => 'reserve-order.post',
                'uses' => 'CartController@reserveOrderPost',
                'middleware' => ['auth-token']
            ]);


            Route::get('/payment/gateway-payment', [
                'as' => 'payment.gateway-payment',
                'uses' => 'CartController@gatewayPayment',
                'middleware' => ['auth-token']
            ]);

            Route::get('/payment/offline-payment', [
                'as' => 'payment.offline-payment',
                'uses' => 'CartController@offlinePayment',
                'middleware' => ['auth-token']
            ]);


            Route::get('/payment/success/{id}', [
                'as' => 'payment.success',
                'uses' => 'CartController@paymentSuccess',
                'middleware' => ['auth-token']
            ]);

            Route::get('/payment/fail', [
                'as' => 'payment.fail',
                'uses' => 'CartController@paymentFail',
                'middleware' => ['auth-token']
            ]);

            //Credit Gateway Foreground
            Route::any('/payment/{driver}/{status?}', [
                'as' => 'payment.gateway.foreground',
                'uses' => 'CartController@paymentGatewayForeground',
                'middleware' => ['auth-token']
            ]);

            Route::post('/update-reserve-order', [
                'as' => 'update-reserve-order.post',
                'uses' => 'CartController@updateReserveOrder',
                'middleware' => ['auth-token']
            ]);

            Route::post('/re-order', [
                'as' => 're-order',
                'uses' => 'CartController@reOrder'
            ]);

            Route::post('/add-items', [
                'as' => 'add-items',
                'uses' => 'CartController@addItems'
            ]);

            Route::post('/move-cart', [
                'as' => 'move-cart',
                'uses' => 'CartController@moveCart'
            ]);
        });

        Route::group(['prefix' => 'members', 'as' => 'members.'], function(){
            Route::get('/', [
                'as' => 'index',
                'uses' => 'MemberController@index',
                'middleware' => ['auth-token']
            ]);

            Route::get('/makro-card/{cardId}', [
                'as' => 'makro-card.show',
                'uses' => 'HomeController@makroCardShow'
            ]);

            /***************** Member Auth *****************/
            Route::get('/register', [
                'as' => 'register',
                'uses' => 'MemberAuthController@register',
                'middleware' => ['guest-only'],
            ]);

            Route::post('/register', [
                'as' => 'register.post',
                'uses' => 'MemberAuthController@registerPost',
                'middleware' => ['guest-only'],
            ]);

            Route::get('/register/facebook', [
                'as' => 'register.facebook',
                'uses' => 'MemberAuthController@registerFacebook',
                'middleware' => ['guest-only'],
            ]);

            Route::post('/register/facebook', [
                'as' => 'register.facebook.post',
                'uses' => 'MemberAuthController@registerFacebookPost',
                'middleware' => ['guest-only'],
            ]);

            Route::get('/register/facebook/success', [
                'as' => 'register.facebook.success',
                'uses' => 'MemberAuthController@registerFacebookSuccess'
            ]);

            Route::get('/register/success', [
                'as' => 'register.success',
                'uses' => 'MemberAuthController@registerSuccess',
                'middleware' => ['guest-only'],
            ]);

            Route::group(['prefix' => 'forget-password', 'as' => 'forget-password.'], function () {
                Route::get('/', [
                    'as' => 'index',
                    'uses' => 'ForgetPasswordController@forgetPassword',
                    'middleware' => ['guest-only'],
                ]);

                Route::post('/check-username', [
                    'as' => 'check-username',
                    'uses' => 'ForgetPasswordController@checkUsername',
                    'middleware' => ['guest-only'],
                ]);

                Route::get('/reset-password', [
                    'as' => 'reset-password',
                    'uses' => 'ForgetPasswordController@resetPassword',
                    'middleware' => ['guest-only'],
                ]);

                Route::post('/reset-password', [
                    'as' => 'reset-password.post',
                    'uses' => 'ForgetPasswordController@resetPasswordPost',
                    'middleware' => ['guest-only'],
                ]);
            });


            Route::get('/verify-username', [
                'as' => 'verify-username',
                'uses' => 'MemberAuthController@verifyUsername'
            ]);

            Route::get('/facebook-login', [
                'as' => 'facebook-login',
                'uses' => 'MemberAuthController@facebookLogin',
                'middleware' => ['guest-only'],
            ]);

            Route::get('/facebook-callback', [
                'as' => 'facebook-callback',
                'uses' => 'MemberAuthController@facebookCallback',
                'middleware' => ['guest-only'],
            ]);


            Route::get('/re-create', [
                'as' => 're-create',
                'uses' => 'MemberAuthController@reCreateCart'
            ]);

            Route::get('/profile', [
                'as'   => 'profile',
                'uses' => 'MemberController@profile',
                'middleware' => ['auth-token']
            ]);

            Route::put('/profile', [
                'as'   => 'profile.update',
                'uses' => 'MemberController@profileUpdate',
                'middleware' => ['auth-token']
            ]);

            Route::any('/shipping', [
                'as'   => 'shipping',
                'uses' => 'MemberController@shipping',
                'middleware' => ['auth-token']
            ]);

            Route::get('/shipping-list', [
                'as'   => 'shipping-list',
                'uses' => 'MemberController@getShippingAddresses',
                'middleware' => ['auth-token']
            ]);

            Route::post('/postAddress', [
                'as'   => 'postAddress',
                'uses' => 'MemberController@postShippingAddress',
                'middleware' => ['auth-token']
            ]);

            Route::get('/wishlist', [
                'as'   => 'wishlist',
                'uses' => 'MemberController@wishList',
                'middleware' => ['auth-token']
            ]);

            Route::get('/list-wish-list', [
                'as'   => 'list-wish-list',
                'uses' => 'MemberController@listWishList',
                'middleware' => ['auth-token']
            ]);

            Route::post('/removeShipping', [
                'as'   => 'removeShipping',
                'uses' => 'MemberController@removeShipping',
                'middleware' => ['auth-token']
            ]);

            Route::get('/store', [
                'as'   => 'store',
                'uses' => 'MemberController@store',
                'middleware' => ['auth-token']
            ]);

            Route::put('/store', [
                'as'   => 'store.update',
                'uses' => 'MemberController@storeUpdate',
                'middleware' => ['auth-token']
            ]);

            Route::get('/taxAddress', [
                'as'   => 'taxAddress',
                'uses' => 'MemberController@taxAddress',
                'middleware' => ['auth-token']
            ]);

            Route::put('/tax', [
                'as'   => 'tax.update',
                'uses' => 'MemberController@taxUpdate',
                'middleware' => ['auth-token']
            ]);

            Route::post('/taxRemove', [
                'as'   => 'taxRemove',
                'uses' => 'MemberController@taxRemove',
                'middleware' => ['auth-token']
            ]);

            Route::get('/orders', [
                'as'   => 'orders',
                'uses' => 'MemberController@orders',
                'middleware' => ['auth-token']
            ]);

            Route::get('/order-detail/{order_id}', [
                'as'   => 'order-detail',
                'uses' => 'MemberController@orderDetail',
                'middleware' => ['auth-token']
            ]);

            Route::group(['prefix' => '', 'middleware' => ['guest-only'], 'as' => 'register.'], function() {
                Route::get('/waiting-activate-email', [
                    'as' => 'email.activate.waiting',
                    'uses' => 'MemberAuthController@activateEmailWaiting',
                ]);

                Route::get('/activate/email', [
                    'as' => 'email.activate',
                    'uses' => 'MemberAuthController@activateEmail',
                ]);

                Route::get('/activate/phone', [
                    'as' => 'activate.otp.get',
                    'uses' => 'MemberAuthController@activateOtpGet',
                ]);

                Route::post('/activate/phone', [
                    'as' => 'activate.otp.post',
                    'uses' => 'MemberAuthController@activateOtpPost',
                ]);

                Route::get('/activate/resend', [
                    'as' => 'activate.resend.get',
                    'uses' => 'MemberAuthController@activateResendGet',
                ]);

                Route::post('/activate/resend', [
                    'as' => 'activate.resend.post',
                    'uses' => 'MemberAuthController@activateResendPost',
                ]);
            });

            Route::get('/activate/success', [
                'as' => 'register.activate.success',
                'uses' => 'MemberAuthController@activateSuccess',
            ]);

            Route::get('/activate/email/success', [
                'as' => 'register.activate-email.success',
                'uses' => 'MemberAuthController@activateSuccess',
            ]);
        });

        Route::group(['prefix' => 'favorites', 'as' => 'favorites.'], function(){
            Route::get('/', [
                'as' => 'index',
                'uses' => 'FavoriteController@index'
            ]);

            Route::post('/add-item', [
                'as' => 'add',
                'uses' => 'FavoriteController@addItem'
            ]);


            Route::post('/remove-item', [
                'as' => 'add',
                'uses' => 'FavoriteController@removeItem'
            ]);
        });

        Route::group(['prefix' => 'content', 'as' => 'contents.'], function(){
            Route::get('/{slug}', [
                'as' => 'show',
                'uses' => 'ContentController@show'
            ]);
        });

        Route::group(['prefix' => 'campaign', 'as' => 'campaigns.'], function(){
            Route::get('/{slug}', [
                'as' => 'show',
                'uses' => 'CampaignController@show'
            ]);
        });

        Route::group(['prefix' => 'brand', 'as' => 'brands.'], function()
        {
            Route::get('/{brand_id}-', [
                'as' => 'show',
                'uses' => 'BrandController@show'
            ])->where('brand_id', '[1-9][0-9]*');

            Route::get('/{brand_id}-{name?}', [
                'as' => 'show',
                'uses' => 'BrandController@show'
            ])->where('brand_id', '[1-9][0-9]*');
        });

        Route::group(['prefix' => 'subscribe', 'as' => 'subscribes.'], function()
        {
            Route::post('/', [
                'as' => 'subscribe.store',
                'uses' => 'SubscribeController@subscribe'
            ]);

        });

        Route::group(['prefix' => 'address', 'as' => 'address'], function(){

            Route::any('/SubDistrictById', [
                'as'   => 'SubDistrictById',
                'uses' => 'AddressController@SubDistrictById'
            ]);

            Route::any('/getCityById', [
                'as'   => 'getCityById',
                'uses' => 'AddressController@getCityById'
            ]);

            Route::get('/provinces', [
                'as'   => 'provinces',
                'uses' => 'AddressController@getProvinces'
            ]);

            Route::get('/delivery-sub-districts', [
                'as'   => 'delivery-sub-districts',
                'uses' => 'AddressController@getDeliverySubDistrictById'
            ]);

            Route::get('/delivery-cities', [
                'as'   => 'delivery-cities',
                'uses' => 'AddressController@getDeliveryCityById'
            ]);

            Route::get('/delivery-provinces', [
                'as'   => 'delivery-provinces',
                'uses' => 'AddressController@getDeliveryProvinces'
            ]);
            Route::get('/delivery-by-postcode', [
                'as'   => 'delivery-by-postcode',
                'uses' => 'AddressController@getDeliveryByPostcode'
            ]);
        });

        Route::post('/accept-delivery', [
            'as' => 'accept-delivery',
            'uses' => 'HomeController@acceptDelivery'
        ]);

    });
});

Route::post('/accept-delivery', [
    'as' => 'accept-delivery',
    'uses' => 'HomeController@acceptDelivery',
    'middleware' => ['web']
]);

Route::post('/validate-email', [
    'as' => 'validate-email',
    'uses' => 'HomeController@validateEmail',
    'middleware' => ['web']
]);