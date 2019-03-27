<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

class FooterComposer
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
        $locale = app()->getLocale();
        $cacheMinutes = env('CACHE_LIFE_TIME_IN_MINUTE', 5);
        $slugs = [
            env('FOOTER_MENU1_SLUG'),
            env('FOOTER_MENU2_SLUG'),
            env('FOOTER_MENU3_SLUG'),
        ];

        $response = \Cache::tags(['global', 'footer', "lang_{$locale}"])->remember('footerMenu_' . implode('_', $slugs), 180, function () use ($slugs) {
            try {
                $makroSdk = app()->make('makroSdk');
                $response = $makroSdk->group()->all(['slug' => implode(',', $slugs)]);

                if (! empty($response['data'])) {
                    return $response['data'];
                }
            } catch (\Exception $e) {

            }

            return null;
        });

        $footerMenuResponse = collect($response);

        $footerMenu['menu_1'] = $footerMenuResponse->where('slug', env('FOOTER_MENU1_SLUG'))->first();
        $footerMenu['menu_2'] = $footerMenuResponse->where('slug', env('FOOTER_MENU2_SLUG'))->first();
        $footerMenu['menu_3'] = $footerMenuResponse->where('slug', env('FOOTER_MENU3_SLUG'))->first();

        $view->with('footerMenu', $footerMenu);
    }
}