<?php

namespace App\Http\Controllers;

use App\Bootstrap\Helpers\AuthHelper;
use App\Bootstrap\Helpers\MakroHelper;
use App\Bootstrap\Helpers\ProductHelper;
use App\Bootstrap\Helpers\StoreHelper;
use Breadcrumbs;
use Cache;

class CampaignController extends BaseController
{
    public function show($slug)
    {
        $makroSdk = app()->make('makroSdk');

        $campaign = Cache::tags(['global', 'lang_' . app()->getLocale(), 'campaign.show'])->remember("campaign-{$slug}", env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $slug) {
            try {
                $response = $makroSdk->campaign()->findBySlug($slug, ['include_products' => 1, 'status' => 'active']);

                if (!empty($response['data'])) {
                    return $response['data'];
                }
            } catch (\Exception $e) {

            }

            return null;
        });

        if (empty($campaign)) {
            abort(404);
        }

        $campaign = collect($campaign);

        $allCampaigns = Cache::tags(['global', 'lang_' . app()->getLocale(), 'campaign.show'])->remember('allCampaigns-active', env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk) {
            try {
                $response = $makroSdk->campaign()->all(['status' => 'active']);

                if (!empty($response['data'])) {
                    return $response['data'];
                }
            } catch (\Exception $e) {

            }

            return null;
        });

        $data['campaign'] = $campaign;
        $data['allCampaigns'] = collect($allCampaigns)->sortBy('name');

        $data['products'] = collect([]);
        $data['filters'] = [];
        $data['total_items'] = 0;

        if (!empty($campaign['products'])) {
            $campaignProducts = collect($campaign['products']);

            if (request()->has('sorter_field')) {
                $param = [
                    'per_page' => env('PRODUCT_LIST_DISPLAY_PER_PAGE'),
                    'page' => request()->get('page', '1'),
                    'campaign_mongo_id' => $campaign['id'],
                    'mongo_id' => $campaignProducts->pluck('product_id')->toArray(),
                ];

                $param = array_merge(request()->except(['page']), $param);
                $param = array_merge($param, ProductHelper::getListParameters());
                $cacheKey = md5(json_encode($param));

                $apiProducts = Cache::tags(['global', 'lang_' . app()->getLocale(), 'store_' . StoreHelper::getCurrentStore(), 'store_price_' . StoreHelper::getCurrentStorePrice(), MakroHelper::memberIdentityCacheKey(), 'campaign.show'])->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $param) {
                    try {
                        return $makroSdk->product()->get($param, 'post');
                    } catch (\Exception $e) {

                    }

                    return null;
                });
            } else { // ms search ไม่ support priority จาก ms campaign ดังนั้นจึงต้องมาทำ logic นี้
                $param = [
                    'per_page' => 2000,
                    'campaign_mongo_id' => $campaign['id'],
                    'mongo_id' => $campaignProducts->pluck('product_id')->toArray(),
                    'ignore_detail' => true,
                ];

                $param = array_merge(request()->except(['page']), $param);
                $param = array_merge($param, ProductHelper::getListParameters());
                $cacheKey = md5(json_encode($param));
                $msSearchProducts = Cache::tags(['global', 'lang_' . app()->getLocale(), 'store_' . StoreHelper::getCurrentStore(), 'campaign.show'])->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $param) {
                    try {
                        $msSearchProducts = $makroSdk->product()->get($param, 'post');

                        if (array_get($msSearchProducts, 'numFound', 0) > 0) {
                            return array_get($msSearchProducts, 'data');
                        }
                    } catch (\Exception $e) {

                    }

                    return null;
                });

                if (!empty($msSearchProducts)) {
                    //จัดเรียง product ตาม priority ก่อน
                    $cacheKey = md5(json_encode($campaignProducts) . json_encode($msSearchProducts));
                    $activeItems = Cache::tags(['global', 'lang_' . app()->getLocale(), 'campaign.show'])->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($msSearchProducts, $campaignProducts) {
                        $activeItems = [];
                        $responseProducts = collect($msSearchProducts);

                        foreach ($campaignProducts as $index => $campaignProduct) {
                            $responseProduct = $responseProducts->where('mongo_id', $campaignProduct['product_id'])->first();

                            if (!empty($responseProduct)) {
                                $activeItems[] = $responseProduct;
                            }
                        }

                        return $activeItems;
                    });

                    // จัด page เพื่อเอา mongo_id ที่เรียง priority แล้วไป get product ใหม่
                    if (!empty($activeItems)) {
                        $page = request()->get('page', '1');
                        $perPage = env('PRODUCT_LIST_DISPLAY_PER_PAGE');
                        $offset = ($page - 1) * $perPage;
                        $activeItems = array_slice($activeItems, $offset, $perPage);

                        // get product (include detail) ตาม per page
                        $param = [
                            'per_page' => env('PRODUCT_LIST_DISPLAY_PER_PAGE'),
                            'campaign_mongo_id' => $campaign['id'],
                            'mongo_id' => collect($activeItems)->pluck('mongo_id')->toArray(),
                        ];

                        $cacheKey = md5(json_encode($param));
                        $msProducts = Cache::tags(['global', 'lang_' . app()->getLocale(), 'store_' . StoreHelper::getCurrentStore(), 'store_price_' . StoreHelper::getCurrentStorePrice(), MakroHelper::memberIdentityCacheKey(), 'campaign.show'])->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $param) {
                            try {
                                $msProducts = $makroSdk->product()->get($param, 'post');

                                if (!empty($msProducts['data'])) {
                                    return $msProducts['data'];
                                }
                            } catch (\Exception $e) {

                            }

                            return null;
                        });

                        // จัดเรียง product ตาม priority อีกที
                        $msProducts = collect($msProducts);
                        $products = []; // นี่คือ product ที่จัดเรียง  priority แล้ว
                        foreach ($activeItems as $index => $activeItem) {
                            $msProduct = $msProducts->where('mongo_id', $activeItem['mongo_id'])->first();

                            if (!empty($msProduct)) {
                                $products[] = $msProduct;
                            }
                        }

                        // ต้อง call อีกครั้ง เพื่อเอา filter จริง
                        $param = [
                            'per_page' => 1,
                            'campaign_mongo_id' => $campaign['id'],
                            'mongo_id' => $campaignProducts->pluck('product_id')->toArray(),
                        ];

                        $param = array_merge(request()->except(['page']), $param);
                        $param = array_merge($param, ProductHelper::getListParameters());
                        $cacheKey = md5(json_encode($param));
                        $msProducts = Cache::tags(['global', 'lang_' . app()->getLocale(), 'store_' . StoreHelper::getCurrentStore(), 'campaign.show'])->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $param) {
                            try {
                                $msProducts = $makroSdk->product()->get($param, 'post');

                                if (!empty($msProducts['data'])) {
                                    return $msProducts;
                                }
                            } catch (\Exception $e) {

                            }

                            return null;
                        });

                        if (!empty($msProducts)) {
                            $apiProducts = $msProducts;
                            $apiProducts['data'] = $products;
                            $apiProducts['meta']['pagination']['count'] = count($products);
                            $apiProducts['meta']['pagination']['per_page'] = env('PRODUCT_LIST_DISPLAY_PER_PAGE');
                            $apiProducts['meta']['pagination']['current_page'] = $page;
                            $apiProducts['meta']['pagination']['total_pages'] = ceil($msProducts['meta']['pagination']['total'] / env('PRODUCT_LIST_DISPLAY_PER_PAGE'));
                        }
                    }
                }
            }

            if (!empty($apiProducts)) {
                $data['filters'] = $apiProducts['meta']['filters'];
                $pagination = collect($apiProducts['meta']['pagination']);

                $options = ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()];
                $total = $pagination['total'];
                $perPage = $pagination['per_page'];
                $currentPage = $pagination['current_page'];
                $currentItems = $apiProducts['data'];

                $data['total_items'] = $total;
                $data['products'] = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, $total, $perPage, $currentPage, $options);
            }
        }

        $params = ['type' => 'product', 'status' => 'active'];
        $cacheKey = md5(json_encode($params));
        $data['filters']['categories'] = Cache::tags(['global', 'lang_' . app()->getLocale(), 'get_root_categories'])->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $params) {
            try {
                $response = $makroSdk->category()->getRootCategories(['type' => 'product', 'status' => 'active']);

                if (!empty($response['data'])) {
                    return $response['data'];
                }
            } catch (\Exception $e) {

            }

            return null;
        });

        $data['product_inputs'] = $this->getInputs();
        $data['list_url'] = route('campaigns.show', ['slug' => $slug]);

//        $data['headerTitle'] = empty(array_get($data, 'campaign.seo_title')) ? array_get($data, 'campaign.name') : array_get($data, 'campaign.seo_title');
//        $data['headerTitle'] .= ' - ' . trans('frontend.website_title');
//        $data['seo_keywords'] = array_get($data, 'campaign.seo_keywords');
//        $data['seo_description'] = array_get($data, 'campaign.seo_description');

        $data['headerTitle'] = trans('frontend.campaign_seo_title', ['campaign_name' => array_get($data, 'campaign.name')]);
        $data['seo_keywords'] = trans('frontend.campaign_seo_keywords', ['campaign_name' => array_get($data, 'campaign.name')]);
        $data['seo_description'] = trans('frontend.campaign_seo_description', ['campaign_name' => array_get($data, 'campaign.name')]);

        $this->addBreadcrumb('campaign', $campaign['name'], route('campaigns.show', ['slug' => $slug]));
        return view('campaign.show', $data);
    }

    private function getInputs()
    {
        return json_encode(request()->input());

    }

}