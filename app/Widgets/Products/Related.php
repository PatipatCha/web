<?php

namespace App\Widgets\Products;

use App\Bootstrap\Helpers\MakroHelper;
use Arrilot\Widgets\AbstractWidget;
use App\Bootstrap\Helpers\AuthHelper;
use App\Bootstrap\Helpers\StoreHelper;
use MakroSdk\Exceptions\SDKException;
use Cache;

class Related extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [
        'product_id' => null,
        'minimum' => 1,
        'show' => 6,
    ];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        //
        $makroSdk = app()->make('makroSdk');
        $id = $this->config['product_id'];
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
        if (!empty($relateProducts) && count($relateProducts) >= $this->config['minimum']) {
            foreach ($relateProducts as $relateProduct) {
                $products[$relateProduct['id']] = $relateProduct;
            }
        }

        return view('widgets.products.related', [
            'products' => collect($products),
            'show' => $this->config['show'],
        ]);
    }
}
