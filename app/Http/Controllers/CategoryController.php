<?php

namespace App\Http\Controllers;

use App\Bootstrap\Helpers\AuthHelper;
use App\Bootstrap\Helpers\MakroHelper;
use App\Bootstrap\Helpers\ProductHelper;
use App\Bootstrap\Helpers\StoreHelper;
use Breadcrumbs;
use Cache;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    public function show($category_id, $name = '')
    {
        $params = ['status' => 'active', 'includes' => 'children'];
        $cacheKey = md5(json_encode(array_merge($params, ['category_id' => $category_id])));
        $cacheTags = [
            'global',
            'lang_' . app()->getLocale(),
            'category.show',
        ];

        $response = Cache::tags($cacheTags)->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($category_id, $params) {
            try {
                $makroSdk = app()->make('makroSdk');
                $response = $makroSdk->category()->getCategory($category_id, $params);

                if (!empty($response['data'])) {
                    return $response['data'];
                }
            } catch (\Exception $e) {

            }

            return null;
        });

        if (empty($response)) {
            abort(404);
        }

        $category = collect($response);

        if ($category['level'] == 0) {
            return $this->categoryLevelB($category);
        } else {
            return $this->categoryLevelC($category);
        }
    }

    public function showBySlug($slug)
    {
        $params = ['status' => 'active', 'includes' => 'children'];
        $cacheKey = md5(json_encode(array_merge($params, ['slug' => $slug])));
        $cacheTags = [
            'global',
            'lang_' . app()->getLocale(),
            'category.show',
        ];

        $response = Cache::tags($cacheTags)->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($slug, $params) {
            try {
                $makroSdk = app()->make('makroSdk');
                $response = $makroSdk->category()->getCategoryBySlug($slug, $params);

                if (!empty($response['data'])) {
                    return $response['data'];
                }
            } catch (\Exception $e) {

            }

            return null;
        });

        if (empty($response)) {
            abort(404);
        }

        $category = collect($response);

        if ($category['level'] == 0) {
            return $this->categoryLevelB($category);
        } else {
            return $this->categoryLevelC($category);
        }
    }

    private function categoryLevelB($category)
    {
        $makroSdk = app()->make('makroSdk');
        $data['category'] = $category;
        $data['topBanners'] = [];
        $data['bannerB'] = null;
        $data['children'] = collect([]);

        if (!empty($category['images'])) {
            $images = collect($category['images']);
            $data['bannerB'] = $images->where('position', 'B')->first();

            $banners = $images->where('position', 'A')->all();
            foreach ($banners as $banner) {
                $data['topBanners'][] = $banner;
            }
        }

        $childrenIds = [];
        if (!empty($category['children']) && !empty($category['children']['data'])) {
            $children = collect($category['children']['data']);
            $childrenIds = $children->pluck('id');
        }

        if (!empty($childrenIds)) {
            $cacheKey = md5(json_encode($category));
            $cacheTags = [
                'global',
                'lang_' . app()->getLocale(),
                'category.show',
            ];

            $response = Cache::tags($cacheTags)->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $category, $childrenIds) {
                try {
                    $response = $makroSdk->category()->getCategories($childrenIds->toArray(), ['status' => 'active', 'includes' => 'children']);

                    if (!empty($response['data'])) {
                        return $response['data'];
                    }
                } catch (\Exception $e) {

                }

                return null;
            });

            $categories = collect($response);
            $categories = $categories->sortBy('priority');
            $children = [];
            foreach ($categories as $key => $category) {
                if (strtolower(array_get($category, 'is_show_level_b', 'n')) == 'y') {
                    $children[$key] = $category;
                }
            }

            $data['children'] = collect($children);
        }

        $data['product_inputs'] = $this->getInputs();

        $seo_keywords = [];
        $seo_keywords[] = array_get($data, 'category.name');

        // โค้ดนี้เพื่อเอา bran name ไปใส่ใน keyword เท่านั้น
        try {
            switch (strtolower($data['category']['type'])) {
                case 'business':
                    $categoryType = 'business';
                    break;
                default:
                    $categoryType = 'product';
            }

            $key = $categoryType . '_category_lv' . $data['category']['level'] . '_id';
            $param['per_page'] = 1;
            $param['page'] = 1;
            $param[$key] = $data['category']['id'];

            $param = array_merge($param, ProductHelper::getListParameters());
            $cacheKey = md5(json_encode($param));

            $response = Cache::tags(['global', 'lang_' . app()->getLocale(), 'store_' . StoreHelper::getCurrentStore(), 'store_price_' . StoreHelper::getCurrentStorePrice(), MakroHelper::memberIdentityCacheKey(), 'category.show'])->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $param) {
                try {
                    return $makroSdk->product()->get($param);
                } catch (\Exception $e) {

                }

                return null;
            });

            if (!empty($response['meta']['filters']['brand']['items'])) {
                $loop = 0;
                foreach ($response['meta']['filters']['brand']['items'] as $brand) {
                    if ($loop > 3) {
                        break;
                    }

                    if (!empty($brand['name'])) {
                        $seo_keywords[] = $brand['name'];
                        $loop++;
                    }
                }
            }
        } catch (\Exception $e) {

        }

        $data['headerTitle'] = trans('frontend.category_b_seo_title', ['category_name' => array_get($data, 'category.name')]);
        $data['seo_description'] = trans('frontend.category_b_seo_description', ['category_name' => array_get($data, 'category.name')]);
        $data['seo_keywords'] = trans('frontend.category_b_seo_keywords', ['keywords' => implode(', ', $seo_keywords)]);

        $this->addBreadcrumb('category_level_b', $data['category']['name'], route('categories.show', ['slug' => $data['category']['slug']]));
        return view('category.level-b', $data);
    }

    private function categoryLevelC($category)
    {
        $makroSdk = app()->make('makroSdk');
        $data['total_items'] = 0;
        $data['category'] = $category;
        $data['category_level_' . $category['level']] = $category->toArray();

        switch (strtolower($data['category']['type'])) {
            case 'business':
                $categoryType = 'business';
                break;
            default:
                $categoryType = 'product';
        }

        try {
            $key = $categoryType . '_category_lv' . $data['category']['level'] . '_id';

            $param = request()->except(['page']);

            $param['per_page'] = env('PRODUCT_LIST_DISPLAY_PER_PAGE');
            $param['page'] = request()->get('page', '1');
            $param[$key] = $data['category']['id'];

            $param = array_merge($param, ProductHelper::getListParameters());
            $cacheKey = md5(json_encode($param));
            $response = Cache::tags(['global', 'lang_' . app()->getLocale(), 'store_' . StoreHelper::getCurrentStore(), 'store_price_' . StoreHelper::getCurrentStorePrice(), MakroHelper::memberIdentityCacheKey(), 'category.show'])->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $param) {
                try {
                    return $makroSdk->product()->get($param);
                } catch (\Exception $e) {

                }

                return null;
            });
            $data['filters'] = $response['meta']['filters'];
            $pagination = collect($response['meta']['pagination']);

            $options = ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()];
            $total = $pagination['total'];
            $perPage = $pagination['per_page'];
            $currentPage = $pagination['current_page'];
            $currentItems = $response['data'];
            $data['total_items'] = $total;
            $data['products'] = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, $total, $perPage, $currentPage, $options);
        } catch (\Exception $e) {
            $data['products'] = collect([]);
        }
        $data['product_inputs'] = $this->getInputs();
        $data['list_url'] = route('categories.show', ['slug' => $category['slug']]);

        // parent มาจาก sdk get category
        $data['parentChildren'] = collect([]);
        $param = [
            'includes' => 'parent,children',
            'status' => 'active'
        ];
        $cacheKey = md5(json_encode(array_merge($param, [$data['category']['parent_id']])));
        $response = Cache::tags(['global', 'lang_' . app()->getLocale(), 'category.show'])->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $param, $data) {
            try {
                return $makroSdk->category()->getCategory($data['category']['parent_id'], $param);
            } catch (\Exception $e) {

            }

            return null;
        });

        if (!empty($response['data'])) {
            $parent = $response['data'];
            $data['category_level_' . $parent['level']] = $parent;

            if (!empty($parent['children']) && !empty($parent['children']['data'])) {
                $data['filters']['categories'] = $parent['children']['data'];
            }

            if (!empty($parent['parent']) && !empty($parent['parent']['data'])) {
                $parentLevel0 = head($parent['parent']['data']);
                $data['category_level_' . $parentLevel0['level']] = $parentLevel0;
                $this->addBreadcrumb('category.level-0', $parentLevel0['name'], route('categories.show', ['slug' => $parentLevel0['slug']]));
            }

            if (!empty($parent)) {
                $this->addBreadcrumb('category.level-1', $parent['name'], route('categories.show', ['slug' => $parent['slug']]));
            }
        }

        $param = [
            'type' => $categoryType,
            'status' => 'active'
        ];
        $cacheKey = md5(json_encode($param));
        $response = Cache::tags(['global', 'lang_' . app()->getLocale(), 'get_category_tree'])->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $param) {
            try {
                return $makroSdk->category()->tree($param);
            } catch (\Exception $e) {

            }

            return null;
        });

        $data['categoriesTree'] = empty($response['data']) ? [] : $response['data'];

        $data['direct_category_id'] = $category['id'];

        $seo_keywords = [];

        if (!empty($data['category_level_0']['name'])) {
            $seo_keywords[] = $data['category_level_0']['name'];
        }

        if (!empty($data['category_level_1']['name'])) {
            $seo_keywords[] = $data['category_level_1']['name'];
        }

        if (!empty($data['filters']['brand']['items'])) {
            $loop = 0;
            foreach ($data['filters']['brand']['items'] as $brand) {
                if ($loop > 3) {
                    break;
                }

                if (!empty($brand['name'])) {
                    $seo_keywords[] = $brand['name'];
                    $loop++;
                }
            }
        }

        $data['headerTitle'] = trans('frontend.category_c_seo_title', ['category_name' => array_get($data, 'category.name')]);
        $data['seo_description'] = trans('frontend.category_c_seo_description', ['category_name' => array_get($data, 'category.name')]);
        $data['seo_keywords'] = trans('frontend.category_c_seo_keywords', ['keywords' => implode(', ', $seo_keywords)]);

        $this->addBreadcrumb('category.level-2', $data['category']['name'], route('categories.show', ['slug' => $data['category']['slug']]));

        return view('category.level-c', $data);
    }

    private function getInputs()
    {
        return json_encode(request()->input());

    }

    public function getRecommendedProducts(Request $request, $slug)
    {
        $makroSdk = app()->make('makroSdk');

        $apiType = $request->get('apiType', 'recommend');
        $categoryType = $request->get('categoryType');
        $categoryId = $request->get('categoryId');
        $categoryLevel = $request->get('categoryLevel', 0);
        $hasBanner = $request->get('hasBanner');

        if ($apiType == 'search') {
            $key = $categoryType . '_category_lv' . $categoryLevel . '_id';

            $param = [];
            $param['per_page'] = 12;
            $param['page'] = 1;
            $param[$key] = $categoryId;

            $cacheKey = md5(json_encode($param));
            $response = Cache::tags(['global', 'lang_' . app()->getLocale(), 'store_' . StoreHelper::getCurrentStore(), 'store_price_' . StoreHelper::getCurrentStorePrice(), MakroHelper::memberIdentityCacheKey(), 'category.recommend-products'])->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $param) {
                try {
                    $response = $makroSdk->product()->get($param);

                    if (!empty($response['data'])) {
                        return $response['data'];
                    }
                } catch (\Exception $e) {

                }

                return null;
            });
        } else {
            $recommendCategoryKey = 'productCategoryId';
            if ($categoryType == 'business') {
                $recommendCategoryKey = 'businessCategoryId';
            }
            $recmemberid = empty($_COOKIE['recmemberid']) ? 0 : $_COOKIE['recmemberid']; // ใช้แบบนี้เพราะ recmemberid มี cokie แล้ว แต่มีค่าเป็น null


            $params = [
                'per_page' => 12,
                $recommendCategoryKey => $categoryId,
                'personalizedId' => $recmemberid
            ];
            $cacheKey = md5(json_encode($params));
            $cacheTags = [
                'global',
                'lang_' . app()->getLocale(),
                'store_' . StoreHelper::getCurrentStore(),
                'store_price_' . StoreHelper::getCurrentStorePrice(),
                MakroHelper::memberIdentityCacheKey(),
                'category.show',
            ];

            $response = Cache::tags($cacheTags)->remember($cacheKey, env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $params) {
                try {
                    $response = $makroSdk->product()->getRecommend($params);

                    if (!empty($response['data'])) {
                        return $response['data'];
                    }
                } catch (\Exception $e) {

                }

                return null;
            });
        }

//        if ($hasBanner) {
//            $products = $products->slice(0, 4)->all();
//        }

        $products = [];
        if (!empty($response)) {
            foreach ($response as $product) {
                $products[$product['id']] = $this->removeChar($product);
            }
        }

        $products = collect($products);

        return response()->json([
            'content' => view('partials/product/item-owl-carousel', ['products' => collect($response), 'listName' => env('GA_RECOMMENDED_PRODUCT_LIST')])->render(),
            'products' => $products
        ]);

    }

    public function removeChar($string)
    {
        //'%27' = '  single quot
        $str = preg_replace('/\'/', '%27', json_encode($string));
        $str = preg_replace('/&quot;/', '%22', $str);
        return $str;
    }
}