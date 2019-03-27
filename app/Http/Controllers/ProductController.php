<?php

namespace App\Http\Controllers;

use App\Bootstrap\Helpers\AuthHelper;
use App\Bootstrap\Helpers\CartHelper;
use App\Bootstrap\Helpers\MakroHelper;
use App\Bootstrap\Helpers\ProductHelper;
use App\Bootstrap\Helpers\StoreHelper;
use Cookie;
use Cache;
use Illuminate\Support\Facades\Redis;

class ProductController extends BaseController
{
    public function search()
    {
        if (!request()->has('q')) {
            return redirect()->route('home.index');
        }

        $data['page_name'] = trans('frontend.search_result_for', ['keyword' => request()->get('q')]);
        $this->addBreadcrumb('product.search', $data['page_name'], route('search.index', ['q' => htmlspecialchars(request()->get('q'))]));
        $makroSdk = app()->make('makroSdk');

        $data['products'] = collect([]);
        $data['filters'] = [];
        $data['pagination'] = collect([]);

        $param = [
            'per_page' => env('PRODUCT_LIST_DISPLAY_PER_PAGE'),
            'page' => request()->get('page', '1'),
            'q' => request()->get('q')
        ];
        $param = array_merge(request()->except(['r', 'page', 'q']), $param);
        $param = array_merge($param, ProductHelper::getListParameters());

        $cacheKey = md5(json_encode($param));
        $cacheTags = [
            'global',
            'lang_' . app()->getLocale(),
            'search.index',
            'store_' . StoreHelper::getCurrentStore(),
            'store_price_' . StoreHelper::getCurrentStorePrice(),
            MakroHelper::memberIdentityCacheKey()
        ];

        $response = Cache::tags($cacheTags)->remember("getProduct_{$cacheKey}", env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $param) {
            try {
                $response = $makroSdk->product()->get($param);

                if (empty($response['meta']['pagination']['total']) || $response['meta']['pagination']['total'] <= 0) {
                    $response = null;
                }

                return $response;
            } catch (\Exception $e) {
                $response = null;
            }

            return $response;
        });

        if (empty($response) || empty($response['meta']['pagination']['total']) || $response['meta']['pagination']['total'] <= 0) {
            $suggestKeywords = Cache::tags($cacheTags)->remember("getProduct_{$cacheKey}_suggestion", env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $param) {
                $suggestKeywords = null;
                $param['per_page'] = 1;

                try {
                    $suggestKeywords = $makroSdk->product()->get($param);
                } catch (\Exception $e) {
                    $suggestKeywords = null;
                }

                return $suggestKeywords;
            });

            return view('product.search-empty', ['keyword' => request()->get('q'), 'suggestKeywords' => array_get($suggestKeywords, 'meta.suggest_keywords')]);
        }

        $data['filters'] = $response['meta']['filters'];
        $data['pagination'] = collect($response['meta']['pagination']);

        $options = ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()];
        $total = $data['pagination']['total'];
        $perPage = $data['pagination']['per_page'];
        $currentPage = $data['pagination']['current_page'];
        $currentItems = $response['data'];
        $data['products'] = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, $total, $perPage, $currentPage, $options);

        $params = ['type' => 'product', 'status' => 'active'];
        $cacheKey = md5(json_encode($params));
        $cacheTags = [
            'global',
            'lang_' . app()->getLocale(),
            'search.index',
        ];
        $response = Cache::tags($cacheTags)->remember("getRootCategories_{$cacheKey}", env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $params) {
            try {
                $response = $makroSdk->category()->getRootCategories($params);

                if (!empty($response['data'])) {
                    return $response;
                }
            } catch (\Exception $e) {

            }

            return null;
        });

        if (!empty($response['data'])) {
            $data['filters']['categories'] = $response['data'];
        }

        $data['product_inputs'] = json_encode(request()->input());

        $data['list_url'] = route('search.index', ['q' => request()->get('q')]);

        $data['total_items'] = array_get($data['pagination'], 'total', 0);

        return view('product.search', $data);
    }

    public function autoSearch()
    {
        $data = [];
        if (!request()->has('q')) {
            return json_encode($data);
        }

        $makroSdk = app()->make('makroSdk');

        try {
            $params = [
                'q' => request()->get('q')
            ];
            $response = $makroSdk->product()->getAutoComplete($params);

        } catch (\Exception $e) {
            return json_encode($data);
        }

        return json_encode($response);
    }

    public function show($id, $name = '')
    {
        $makroSdk = app()->make('makroSdk');
        $user = AuthHelper::user();

        $data['product'] = $this->getProduct($id, $user);
        if (empty($data['product'])) {
            abort(404);
        }

        $product_keywords[] = array_get($data, 'product.name');

        $data['product']['receive_date'] = env('PRODUCT_RECEIVE_IN_DAY', 7);

        $data['recentViewProducts'] = collect([]);

        $data['productCategories'] = collect($data['product']['product_categories']);
        $data['businessCategories'] = collect($data['product']['business_categories']);

        $category = $this->getCategory($data['product']);
        if (!empty($category)) {
            if (!empty($category['parent_id'])) {
                $params = [
                    'includes' => 'parent,children',
                    'status' => 'active'
                ];
                $cacheKey = md5(json_encode(array_merge($params, ['category_id' => $category['parent_id']])));
                $cacheTags = [
                    'global',
                    'lang_' . app()->getLocale(),
                    'category.show',
                ];

                $parent = Cache::tags($cacheTags)->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $category, $params) {
                    try {
                        $response = $makroSdk->category()->getCategory($category['parent_id'], $params);

                        if (!empty($response['data'])) {
                            return $response['data'];
                        }
                    } catch (\Exception $e) {

                    }

                    return null;
                });

                if (!empty($parent['parent']) && !empty($parent['parent']['data'])) {
                    $parentLevel0 = head($parent['parent']['data']);
                    $this->addBreadcrumb('category.level-0', $parentLevel0['name'], route('categories.show', ['slug' => $parentLevel0['slug']]));
                    $product_keywords[] = $parentLevel0['name'];
                }

                if (!empty($parent)) {
                    $this->addBreadcrumb('category.level-1', $parent['name'], route('categories.show', ['slug' => $parent['slug']]));
                    $product_keywords[] = $parent['name'];
                }
            }

            $this->addBreadcrumb('category.level-2', $category['name'], route('categories.show', ['slug' => $category['slug']]));
            $product_keywords[] = $category['name'];
        }

        $this->addBreadcrumb('product.show', $data['product']['name'], route('products.show-by-slug', ['slug' => $data['product']['slug']]));

        if (!empty(array_get($data, 'product.brand.name'))) {
            $product_keywords[] = array_get($data, 'product.brand.name');
        }

        $data['headerTitle'] = trans('frontend.product_seo_title', ['product_name' => array_get($data, 'product.name')]);
        $data['seo_description'] = trans('frontend.product_seo_description', ['product_name' => array_get($data, 'product.name')]);
        $data['seo_keywords'] = trans('frontend.product_seo_keywords', ['product_keywords' => implode(', ', $product_keywords)]);
        $data['product_id'] = $id;

        //Get product group
        $product = [
            [
                'content' => [
                    'data' => $data['product']
                ]
            ]
        ];
        $data['group_product'] = CartHelper::getCartGroup($product, [], true, false);

//        $data['product']['stock'] = 0;
//        $data['product']['preorder'] = 'n';

        $data['can_add_to_cart'] = true;
        if (strtolower(array_get($data, 'product.preorder')) == 'n') {
            if ($data['product']['stock'] < 1
                || $data['product']['stock'] < $data['product']['minimum_order_limit']) {
                $data['can_add_to_cart'] = false;
            }
        }


        return response()->view('product.show', $data);
    }

    protected function getProduct($id, $user)
    {
        $makroSdk = app()->make('makroSdk');
        $product = Cache::tags(['global', 'lang_' . app()->getLocale(), 'store_' . StoreHelper::getCurrentStore(), 'store_price_' . StoreHelper::getCurrentStorePrice(), MakroHelper::memberIdentityCacheKey(), 'products.show'])->remember("products-{$id}", env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $id) {
            try {
                $response = $makroSdk->product()->getDetail($id);

                if (!empty($response['data'])) {
                    return $response['data'];
                }
            } catch (\Exception $e) {

            }

            return null;
        });

        return $product;
    }

    protected function getCategory($product)
    {
        $productCategories = collect($product['product_categories']);

        $category = $productCategories->filter(function ($item) {
            return $item['level'] == 2;
        })->first();

        if (empty($category)) {
            $category = $productCategories->filter(function ($item) {
                return $item['level'] == 1;
            })->first();
        }

        if (empty($category)) {
            $category = $productCategories->filter(function ($item) {
                return $item['level'] == 0;
            })->first();
        }

        return $category;

    }

    public function getRelatedProducts($id)
    {
        $makroSdk = app()->make('makroSdk');
        $user = AuthHelper::user();

        $relateProducts = Cache::tags(['global', 'lang_' . app()->getLocale(), 'store_' . StoreHelper::getCurrentStore(), 'store_price_' . StoreHelper::getCurrentStorePrice(), MakroHelper::memberIdentityCacheKey(), 'products.show'])->remember("products.relateProducts-{$id}", env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $id) {
            try {
                $response = $makroSdk->product()->getRecommend(['per_page' => 15, 'placement' => 'product', 'productId' => $id]);

                if (!empty($response['data'])) {
                    return $response['data'];
                }
            } catch (\Exception $e) {

            }

            return null;
        });

        $products = [];
        if (!empty($relateProducts)) {
            foreach ($relateProducts as $relateProduct) {
                $products[$relateProduct['id']] = $this->removeChar($relateProduct);
            }
        }

        return response()->json([
            'content' => view('partials.product.item-owl-carousel', ['products' => $relateProducts, 'listName' => env('GA_RELATED_PRODUCT_LIST')])->render(),
            'products' => $products
        ]);
    }

    public function removeChar($string)
    {
        $str = preg_replace('/\'/', '%27', json_encode($string));
        $str = preg_replace('/&quot;/', '%22', $str);
        return $str;
    }

    public function increaseView($id = '')
    {
        try {
            $makroSdk = app()->make('makroSdk');
            return $makroSdk->product()->increaseView($id);
        } catch (\Exception $e) {

        }
    }

    public function getRecentProducts($id)
    {
        $makroSdk = app()->make('makroSdk');
        $user = AuthHelper::user();
        $recentViewProducts = collect([]);
        $recentView = [];
        $member_id = AuthHelper::getMemberId();
        if (!empty(Redis::get('recent_product_view_' . $member_id))) {
            try {
                $tempRecentView = Redis::get('recent_product_view_' . $member_id);
                $recentView = json_decode($tempRecentView);
                if (($key = array_search($id, $recentView)) !== false) {
                    unset($recentView[$key]);
                }

                if (!empty($recentView)) {
                    $result = array_unique($recentView);
                    $recentView = array_slice($result, 0, 30);

                    $param = [
                        'per_page' => 30,
                        'id' => $recentView,
                    ];
                    $cacheKey = md5(json_encode($param));

                    $recentViewProducts = Cache::tags(['global', 'lang_' . app()->getLocale(), 'store_' . StoreHelper::getCurrentStore(), 'store_price_' . StoreHelper::getCurrentStorePrice(), MakroHelper::memberIdentityCacheKey(), 'products.show'])->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $param) {
                        try {
                            $response = $makroSdk->product()->get($param);

                            if (!empty($response['data'])) {
                                return $response['data'];
                            }
                        } catch (\Exception $e) {

                        }

                        return null;
                    });

                    $recentViewProducts = collect($recentViewProducts);
                }
            } catch (\Exception $e) {
                $recentViewProducts = collect([]);
            }
        }

        if ($id > 0) {
            array_unshift($recentView, $id);
            Redis::set('recent_product_view_'. $member_id, json_encode($recentView));
            // setcookie('recent_product_view', serialize($recentView), time() + 365 * 60 * 60 * 24, '/', config('session.domain'), config('session.secure'));
        }

        $products = [];
        if (!empty($recentViewProducts)) {
            foreach ($recentView as $recentViewId) {
                $product = $recentViewProducts->where('item_id', $recentViewId)->first();

                if ($product) {
                    $products[$recentViewId] = $this->removeChar($product);
                }
            }
        }

        return response()->json([
            'content' => view('partials.product.item-owl-carousel', ['products' => $recentViewProducts, 'listName' => env('GA_RECENT_PRODUCT_LIST')])->render(),
            'products' => $products
        ]);
    }
}