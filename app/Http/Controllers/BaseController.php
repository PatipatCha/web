<?php

namespace App\Http\Controllers;

use App\Bootstrap\Helpers\StoreHelper;
use Breadcrumbs;

class BaseController extends Controller
{
    protected $currentBreadcrumb = null;

    public function __construct()
    {
        $this->addBreadcrumb('home.index', trans('frontend.home'), route('home.index'));

        /************ Main Mobile Menu ************/
        $locale = app()->getLocale();

        $slugs = [
            env('HOME_MENU_LEFT_1'),
            env('HOME_MENU_LEFT_2'),
        ];
        $response = \Cache::tags(['global', "lang_{$locale}"])->remember('mainMobileMenu_' . implode('_', $slugs), env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($slugs) {
            try {
                $makroSdk = app()->make('makroSdk');
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

        $mainMobileMenu['left_1'] = $menuResponse->where('slug', env('HOME_MENU_LEFT_1'))->first();
        $mainMobileMenu['left_2'] = $menuResponse->where('slug', env('HOME_MENU_LEFT_2'))->first();

        view()->share('mainMobileMenu', $mainMobileMenu);
        /************ END - Main Mobile Menu ************/
    }

    protected function addBreadcrumb($name, $title, $url)
    {
        $currentBreadcrumb = $this->currentBreadcrumb;

        Breadcrumbs::register($name, function ($breadcrumbs) use ($title, $url, $currentBreadcrumb) {
            if (!empty($currentBreadcrumb)) {
                $breadcrumbs->parent($currentBreadcrumb);
            }

            $breadcrumbs->push($title, $url);
        });

        $this->currentBreadcrumb = $name;

        view()->share('breadcrumbs', Breadcrumbs::render($this->currentBreadcrumb));
    }
}