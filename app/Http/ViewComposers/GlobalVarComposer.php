<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

class GlobalVarComposer
{
    protected $makroSdk;

    public function __construct()
    {
        $this->makroSdk = app()->make('makroSdk');
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $this->customStyle($view);
        $this->deliveryAndPickupMessages($view);
    }

    private function customStyle(View $view)
    {
        $cacheMinutes = env('CACHE_LIFE_TIME_IN_MINUTE', 5);

        $response = \Cache::tags(['global'])->remember("msStyleScriptConfig", $cacheMinutes, function () {
            try {
                $response = $this->makroSdk->config()->get(['config_type' => 'frontend', 'status' => 'active']);

                if (! empty($response['data']) && is_array($response['data'])) {
                    return $response;
                }
            } catch (\Exception $e) {
                return null;
            }
        });

        $msScript = null;
        $msStyle = null;
        if (! empty($response['data']) && is_array($response['data'])) {
            foreach ($response['data'] as $config) {
                if (strtolower($config['name']) == 'script' && ! empty($config['value'])) {
                    $msScript = $config['value'];
                } elseif (strtolower($config['name']) == 'css' && ! empty($config['value'])) {
                    $msStyle = $config['value'];
                }
            }
        }

        $view->with('msStyle', $msStyle);
        $view->with('msScript', $msScript);
    }

    private function deliveryAndPickupMessages(View $view)
    {
        $cacheMinutes = env('CACHE_LIFE_TIME_IN_MINUTE', 5);

        $response = \Cache::tags(['global'])->remember("deliveryMessage", $cacheMinutes, function () {
            try {
                $response = $this->makroSdk->config()->get(['config_type' => 'Setting', 'status' => 'active', 'code' => 'Delivery']);
                if (! empty($response['data']) && is_array($response['data'])) {
                    return $response;
                }
            } catch (\Exception $e) {
                return null;
            }
        });

        $shippingTitleMessage = '';
        if (! empty($response['data'])) {
            $data = head($response['data']);
            $shippingTitleMessage = array_get($data, 'name.' . app()->getLocale(), '');
        }

        $response = \Cache::tags(['global'])->remember("pickupMessage", $cacheMinutes, function () {
            try {
                $response = $this->makroSdk->config()->get(['config_type' => 'Setting', 'status' => 'active', 'code' => 'Pickup']);
                if (! empty($response['data']) && is_array($response['data'])) {
                    return $response;
                }
            } catch (\Exception $e) {
                return null;
            }
        });

        $pickupTitleMessage = '';
        if (! empty($response['data'])) {
            $data = head($response['data']);
            $pickupTitleMessage = array_get($data, 'name.' . app()->getLocale(), '');
        }

        $view->with('shippingTitleMessage', $shippingTitleMessage);
        $view->with('pickupTitleMessage', $pickupTitleMessage);
    }
}