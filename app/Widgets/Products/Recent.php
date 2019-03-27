<?php

namespace App\Widgets\Products;

use App\Bootstrap\Helpers\MakroHelper;
use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Facades\Redis;
use App\Bootstrap\Helpers\AuthHelper;
use App\Bootstrap\Helpers\StoreHelper;
use MakroSdk\Exceptions\SDKException;
use Cache;

class Recent extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [
        'product_id' => null,
        'minimum' => 1,
        'show' => 5,
    ];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        //
        $id = $this->config['product_id'];
        $makroSdk = app()->make('makroSdk');
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

                        return [];
                    });

                    $recentViewProducts = collect($recentViewProducts);
                }
            } catch (\Exception $e) {
                $recentViewProducts = collect([]);
            }
        }


        // dd(Redis::get('recent_product_view_' . $member_id));
        $products = [];

        if (!empty($recentViewProducts) && count($recentViewProducts) >= $this->config['minimum']) {
            foreach ($recentView as $recentId) {
                $product = $recentViewProducts->where('id', $recentId)->first();

                if($product) {
                    $products[$recentId] = $product;
                }
            }
        }

        if ($id > 0) {
            array_unshift($recentView, $id);
            Redis::set('recent_product_view_' . $member_id, json_encode($recentView));
            // setcookie('recent_product_view', serialize($recentView), time() + 365 * 60 * 60 * 24, '/', config('session.domain'), config('session.secure'));
        }

        return view('widgets.products.recent', [
            'products' => collect($products),
            'show' => $this->config['show']
        ]);
    }
}
