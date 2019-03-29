<?php

namespace App\Http\Controllers;

use App\Bootstrap\Helpers\StoreHelper;
use Breadcrumbs;
use Illuminate\Http\Request;
use App\Bootstrap\Helpers\MakroHelper;
use MakroSdk\Exceptions\SDKException;
use Cache;

class mkpController extends BaseController
{
    protected $cacheMinutes;

    public function __construct()
    {
        parent::__construct();

        $this->cacheMinutes = env('CACHE_LIFE_TIME_IN_MINUTE', 5);
    }
    public function index(Request $request)
    {
        $arr = ['address_name' => ''];
        $makroSdk = app()->make('makroSdk');

        if ($request->get('nocache', 0) == 1) {
            Cache::tags('global')->flush();
        }

        $locale = app()->getLocale();

        /************ Group Menu ************/
        $slugs = [
            env('HOME_MENU_TOP'),
            env('HOME_MENU_LEFT_1'),
            env('HOME_MENU_LEFT_2'),
            env('HOME_MENU_LEFT_3'),
            env('HOME_MENU_LEFT_4')
        ];
        $response = Cache::tags(['global', "lang_{$locale}", 'home.index_mkp'])->remember('homeGroupMenu_' . implode('_', $slugs), $this->cacheMinutes, function () use ($makroSdk, $slugs) {
            try {
                $response = $makroSdk->group()->all([
                    'slug' => implode(',', $slugs),
                    'status' => 'active'
                ]);

                if (!empty($response['data'])) {
                    return $response['data'];
                }
            } catch (\Exception $e) {

            }

            return null;
        });

        $menuResponse = collect($response);

        $homeGroupMenu['top'] = $menuResponse->where('slug', env('HOME_MENU_TOP'))->first();
        $homeGroupMenu['left_1'] = $menuResponse->where('slug', env('HOME_MENU_LEFT_1'))->first();
        $homeGroupMenu['left_2'] = $menuResponse->where('slug', env('HOME_MENU_LEFT_2'))->first();
        $homeGroupMenu['left_3'] = $menuResponse->where('slug', env('HOME_MENU_LEFT_3'))->first();
        $homeGroupMenu['left_4'] = $menuResponse->where('slug', env('HOME_MENU_LEFT_4'))->first();

        $data['homeGroupMenu'] = $homeGroupMenu;
        /************ END : Group Menu ************/

        /************ Campaign ************/
        $currentStore = StoreHelper::getCurrentStore();
        $slugs = [
            env('HOME_CAMPAIGN_1'),
            env('HOME_CAMPAIGN_2'),
            env('HOME_CAMPAIGN_3'),
        ];
        $response = Cache::tags(['global', "lang_{$locale}_store_{$currentStore}", 'home.index_mkp'])->remember('homeCampaigns_' . implode('_', $slugs), $this->cacheMinutes, function () use ($makroSdk, $slugs) {
            try {
                $response = $makroSdk->campaign()->all([
                    'slug' => $slugs,
                    'include_products' => 1,
                    'status' => 'active'
                ]);

                if (!empty($response['data'])) {
                    return $response['data'];
                }
            } catch (\Exception $e) {

            }

            return null;
        });

        $campaignResponse = collect($response);

        $homeCampaigns[env('HOME_CAMPAIGN_1')] = $campaignResponse->where('slug', env('HOME_CAMPAIGN_1'))->first();
        $homeCampaigns[env('HOME_CAMPAIGN_2')] = $campaignResponse->where('slug', env('HOME_CAMPAIGN_2'))->first();
        $homeCampaigns[env('HOME_CAMPAIGN_3')] = $campaignResponse->where('slug', env('HOME_CAMPAIGN_3'))->first();

        foreach ($homeCampaigns as $key => $homeCampaign) {
            if (empty($homeCampaign)) {
                unset($homeCampaigns[$key]);
                continue;
            }

            $homeCampaigns[$key] = $homeCampaign;
            $homeCampaigns[$key]['products'] = [];

            $homeCampaignProductsCollection = array_get($homeCampaign, 'products');
            $productMongoIds = collect($homeCampaignProductsCollection)->pluck('product_id')->toArray();
            if (!empty($productMongoIds)) {
                $response = Cache::tags(['global', "lang_{$locale}_store_{$currentStore}", 'store_price_' . StoreHelper::getCurrentStorePrice(), 'home.index_mkp'])->remember("homeCampaignProducts-{$key}", $this->cacheMinutes, function () use ($makroSdk, $homeCampaign, $productMongoIds) {
                    try {
                        $param = [
                            'per_page' => count($productMongoIds),
                            'campaign_mongo_id' => $homeCampaign['id'],
                            'mongo_id' => $productMongoIds,
                        ];

                        $response = $makroSdk->product()->get($param, 'post');

                        if (!empty($response['data'])) {
                            return $response['data'];
                        }
                    } catch (\Exception $e) {

                    }

                    return null;
                });

                $responseProducts = collect($response);

                $products = [];
                foreach ($homeCampaignProductsCollection as $homeCampaignProductCollection) {
                    if (count($products) >= 12) {
                        break;
                    }

                    $responseProduct = $responseProducts->where('mongo_id', $homeCampaignProductCollection['product_id'])->first();

                    if (!empty($responseProduct)) {
                        $products[] = $responseProduct;
                    }
                }

                $homeCampaigns[$key]['products'] = $products;
            }
        }

        $data['homeCampaigns'] = $homeCampaigns;
        /************ END : Campaign ************/

        /************ Brands ************/
        $response = Cache::tags(['global', "lang_{$locale}", 'home.index_mkp'])->remember('brandsArr', $this->cacheMinutes, function () use ($makroSdk) {
            try {
                $response = $makroSdk->brand()->get(['per_page' => 16, 'order' => 'priority|ASC']);

                if (!empty($response['data'])) {
                    return $response['data'];
                }
            } catch (\Exception $e) {

            }

            return null;
        });

        $brands = collect($response);
        $brandLeftBanners = [];

        foreach ($brands as $loop => $brand) {
            if ($loop > 4) {
                break;
            }

            $brandImageCollection = collect(array_get($brand, 'images'));
            $brandImageB = $brandImageCollection->where('position', 'B')->first();

            if (!empty($brandImageB['image'])) {
                $brandLeftBanners[] = $brandImageB;
            }
        }

        $data['brands'] = $brands;
        $data['brandLeftBanners'] = $brandLeftBanners;
        /************ END : Brands ************/

        /************ Banners ************/
        $banner1 = Cache::tags(['global', "lang_{$locale}", 'home.index_mkp'])->remember('banners1', $this->cacheMinutes, function () use ($makroSdk) {
            try {
                return $makroSdk->banner()->getByGroupPosition('banner-1');
            } catch (\Exception $e) {

            }

            return null;
        });

        $banner2 = Cache::tags(['global', "lang_{$locale}", 'home.index_mkp'])->remember('banners2', $this->cacheMinutes, function () use ($makroSdk) {
            try {
                return $makroSdk->banner()->getByGroupPosition('banner-2');
            } catch (\Exception $e) {

            }

            return null;
        });

        $banner3 = Cache::tags(['global', "lang_{$locale}", 'home.index_mkp'])->remember('banners3', $this->cacheMinutes, function () use ($makroSdk) {
            try {

                $response = $makroSdk->banner()->get([
                    'slugs' => 'banner-3'
                ]);

                if (isset($response['data']) && !empty($response['data'])) {
                    return $response['data'][0]['image_url'];
                }
            } catch (\Exception $e) {

            }

            return null;
        });

        $banner4 = Cache::tags(['global', "lang_{$locale}", 'home.index_mkp'])->remember('banners4', $this->cacheMinutes, function () use ($makroSdk) {
            try {
                return $makroSdk->banner()->getByGroupPosition('banner-4');
            } catch (\Exception $e) {

            }

            return null;
        });

        $data['banner1'] = $banner1;
        $data['banner2'] = $banner2;
        $data['banner3'] = $banner3;
        $data['banner4'] = $banner4;
        /************ END : Banners ************/

        $data['login_success'] = $request->session()->get('login_success');

        $data['headerTitle'] = trans('frontend.website_title');
        $data['seo_keywords'] = trans('frontend.website_seo_keywords');
        $data['seo_description'] = trans('frontend.website_seo_description');

        $data['mkp'] = trans('frontend.mkp');

        // dd($data);
        // $data['homeCampaigns']['campaign-1']['products'] = array_slice($data['homeCampaigns']['campaign-1']['products'], 0, 4);
        return view('home.index_mkp', $data )->with( 'p','mkp')
                                            ->with('first_name','Patipat TestTest');
    }

    public function makroCardShow($cardId)
    {
        try {
            $makroSdk = app()->make('makroSdk');
            $response = $makroSdk->member()->getMakroCardInfo($cardId, ['includes' => 'member']);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $errorCode = null;
            if ($e instanceof SDKException) {
                $message = $e->getUserMessage();
                $errorCode = $e->getErrorCode();
            }

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'message' => trans('frontend.not_found_makrocard', ['card_id' => $cardId]),
                'errorCode' => $errorCode
            ]);
        }

        if (!empty($response['data']['member'])) {
            return response()->json([
                'status' => 'used',
                'message' => 'UnSuccessful'
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Successful',
            'data' => [
                'card_id' => $response['data']['id'],
                'customer_name' => $response['data']['customerName']
            ]
        ]);
    }

    public function siteMap()
    {
        $makroSdk = app()->make('makroSdk');

        $categories = Cache::tags(['global', 'lang_' . app()->getLocale(), 'home.site-map'])->remember('home.site-map.product', env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk) {
            $categories = null;

            try {
                $response = $makroSdk->category()->getRootCategories(['type' => 'product', 'status' => 'active', 'includes' => 'children']);
                $categories = $response['data'];
            } catch (\Exception $e) {

            }

            return $categories;
        });

        $data['categories'] = collect($categories);

        $categories = Cache::tags(['global', 'lang_' . app()->getLocale(), 'home.site-map'])->remember('home.site-map.business', env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk) {
            $categories = null;

            try {
                $response = $makroSdk->category()->getRootCategories(['type' => 'business', 'status' => 'active', 'includes' => 'children']);
                $categories = $response['data'];
            } catch (\Exception $e) {

            }

            return $categories;
        });

        $data['business'] = collect($categories);

        $this->addBreadcrumb('sitemap', trans('frontend.sitemap'), route('home.site-map'));
        return view('home.sitemap-categories-list', $data);
    }

    public function contactUs()
    {
        $locale = \App::getLocale();
        $subject = [
            'en' => [
                'How to order',
                'About makro click site',
                'About product',
                'Services',
                'ETC'
            ],
            'th' => [
                'การสั่งซื้อสินค้า',
                'การใช้งานเว็บไซต์',
                'ข้อมูลสินค้า',
                'บริการ',
                'อื่นๆ'
            ]
        ];
        $data['subjects'] = $subject[$locale];
        $this->addBreadcrumb('contactus', trans('frontend.contact_us'), route('home.contact-us'));
        return view('home.contact-us', $data);
    }

    public function contactMessage(Request $request)
    {

        $this->validate($request, [
            'full_name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            // 'subject' => 'required',
            'detail' => 'required',
            'g-recaptcha-response' => 'required|captcha',
        ], [], [
            'full_name' => trans('frontend.full_name'),
            'subject' => trans('frontend.subject'),
            'detail' => trans('frontend.detail'),
            'phone' => trans('frontend.phone'),
            'email' => trans('frontend.email'),
            'g-recaptcha-response' => trans('frontend.google_recaptcha'),
        ]);
        $makroSdk = app()->make('makroSdk');

        try {
            $rs = $makroSdk->notification()->sendContactMessage($request->all());
        } catch (\Exception $e) {
            $errors = trans('frontend.send_contact_message_fail');
            return redirect()->back()->withInput()->withErrors($errors);
        }
        if (!empty($rs['status']['code'])) {
            if ($rs['status'] != 200) {
                return redirect()->back()->withInput()->withErrors($rs['errors']['message']);
            }
        }
        $alerts = [
            'success' => [
                'messages' => [
                    trans('frontend.send_contact_message_success')
                ]
            ]
        ];

        return redirect()->route('home.contact-us')->withAlerts($alerts);
    }

    public function acceptDelivery()
    {
        MakroHelper::setAcceptDelivery();
    }

    public function validateEmail()
    {
        $email = request()->get('email');

        try {
            MakroHelper::validateEmail($email);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => "This ({$email}) email address is invalid.",
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => "This ({$email}) email address is considered valid",
        ]);
    }
}