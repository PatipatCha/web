<?php

namespace App\Http\Controllers;

use App\Bootstrap\Helpers\AuthHelper;
use App\Bootstrap\Helpers\MakroHelper;
use App\Bootstrap\Helpers\ProductHelper;
use App\Bootstrap\Helpers\StoreHelper;
use MakroSdk\Exceptions\SDKException;
use Cache;

class BrandController extends BaseController
{
    public function show($id, $name = null)
    {
        $params = ['status' => 'active'];
        $cacheKey = md5(json_encode(array_merge($params, ['id' => $id])));
        $cacheTags = [
            'global',
            'lang_' . app()->getLocale(),
            'brand.show',
        ];
        $brand = Cache::tags($cacheTags)->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($id, $params) {
            try {
                $makroSdk = app()->make('makroSdk');
                $response = $makroSdk->brand()->getById($id, $params);

                if (!empty($response['data'])) {
                    return $response['data'];
                }
            } catch (\Exception $e) {
                if ($e instanceof SDKException) {
                    // dd($e);
                }
            }

            return null;
        });

        return $this->showBrand($brand);
    }

    public function showBySlug($slug)
    {
        $params = ['status' => 'active'];
        $cacheKey = md5(json_encode(array_merge($params, ['slug' => $slug])));
        $cacheTags = [
            'global',
            'lang_' . app()->getLocale(),
            'brand.show',
        ];
        $brand = Cache::tags($cacheTags)->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($slug, $params) {
            try {
                $makroSdk = app()->make('makroSdk');
                $response = $makroSdk->brand()->getBySlug($slug, $params);

                if (!empty($response['data'])) {
                    return $response['data'];
                }
            } catch (\Exception $e) {
                if ($e instanceof SDKException) {
                    // dd($e);
                }
            }

            return null;
        });

        return $this->showBrand($brand);
    }

    private function showBrand($brand)
    {
        if (empty($brand['id'])) {
            abort(404);
        }

        $id = $brand['id'];
        $data['brand'] = collect($brand);
        $data['products'] = collect([]);
        $data['filters'] = [];
        $data['pagination'] = collect([]);
        $data['total_items'] = 0;

        $param = array_merge(request()->except(['r', 'page']), [
            'per_page' => env('PRODUCT_LIST_DISPLAY_PER_PAGE', 20),
            'page' => request()->get('page', '1'),
            'brand_name_id' => $id
        ]);
        $param = array_merge($param, ProductHelper::getListParameters());

        $cacheKey = md5(json_encode($param));
        $cacheTags = [
            'global',
            'lang_' . app()->getLocale(),
            'store_' . StoreHelper::getCurrentStore(),
            'store_price_' . StoreHelper::getCurrentStorePrice(),
            MakroHelper::memberIdentityCacheKey(),
            'brand.show'
        ];
        $response = Cache::tags($cacheTags)->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($param) {
            try {
                $makroSdk = app()->make('makroSdk');
                $response = $makroSdk->product()->get($param);
            } catch (\Exception $e) {
                $response = null;
            }

            return $response;
        });

        if (!empty($response)) {
            $data['filters'] = $response['meta']['filters'];
            $pagination = collect($response['meta']['pagination']);

            $options = ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()];
            $total = $pagination['total'];
            $perPage = $pagination['per_page'];
            $currentPage = $pagination['current_page'];
            $currentItems = $response['data'];
            $data['total_items'] = $total;
            $data['products'] = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, $total, $perPage, $currentPage, $options);
        }

        $data['product_inputs'] = json_encode(request()->input());
        $data['page_name'] = $data['brand']['name'];

//        $data['headerTitle'] = empty(array_get($data, 'brand.seo_title')) ? array_get($data, 'brand.name') : array_get($data, 'brand.seo_title');
//        $data['headerTitle'] .= ' - ' . trans('frontend.website_title');
//        $data['seo_keywords'] = array_get($data, 'brand.seo_keywords');
//        $data['seo_description'] = array_get($data, 'brand.seo_description');

        $data['headerTitle'] = trans('frontend.brand_seo_title', ['brand_name' => array_get($data, 'brand.name')]);
        $data['seo_description'] = trans('frontend.brand_seo_description', ['brand_name' => array_get($data, 'brand.name')]);
        $data['seo_keywords'] = trans('frontend.brand_seo_keywords', ['brand_keywords' => array_get($data, 'brand.name_original.en') . ', ' . array_get($data, 'brand.name_original.th')]);

        $this->addBreadcrumb('brands.show', $data['brand']['name'], route('brands.show-by-slug', ['slug' => $data['brand']['slug']]));
        return view('brand.show', $data);
    }
}