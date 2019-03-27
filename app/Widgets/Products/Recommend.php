<?php

namespace App\Widgets\Products;

use App\Bootstrap\Helpers\MakroHelper;
use Arrilot\Widgets\AbstractWidget;
use App\Bootstrap\Helpers\AuthHelper;
use App\Bootstrap\Helpers\StoreHelper;

class Recommend extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [
        'product_class' => null,
        'page' => 1,
        'per_page' => 20,
    ];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        // get user profile from session or cookie
        $makroSdk = app()->make('makroSdk');

        $user = AuthHelper::user();
        if ($user) {
            if (! empty($user['profile']['makro_card_id'])) {
                $this->config['makro_card_id'] = $user['profile']['makro_card_id'];
            }
        }

        $products = \Cache::tags(['global', 'lang_' . app()->getLocale(), 'store_' . StoreHelper::getCurrentStore(), 'store_price_' . StoreHelper::getCurrentStorePrice(), MakroHelper::memberIdentityCacheKey(), 'products.recommend'])->remember(md5(json_encode($this->config)), env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk) {
            try {
                $response = $makroSdk->product()->gerPersonalizeRecommend($this->config);

                if (! empty($response['data'])) {
                    return $response['data'];
                }
            } catch (\Exception $e) {

            }

            return null;
        });

        if (empty($products)) {
            $products = [];
        }

        return view('widgets.products.recommend', [
            'config' => $this->config,
            'products' => $products
        ]);
    }
}
