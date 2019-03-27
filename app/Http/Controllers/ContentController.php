<?php

namespace App\Http\Controllers;

use Breadcrumbs;
use MakroSdk\Exceptions\SDKException;
use Cache;

class ContentController extends BaseController
{
    public function show($slug)
    {
        $makroSdk = app()->make('makroSdk');

        $response = Cache::tags(['global', 'lang_' . app()->getLocale(), 'content.show'])->remember("content-{$slug}", env('CACHE_LIFE_TIME_IN_MINUTE', 5), function () use ($makroSdk, $slug) {
            try {
                $response = $makroSdk->content()->findBySlug($slug, ['status' => 'published']);

                if (!empty($response['data'])) {
                    return $response['data'];
                }
            } catch (\Exception $e) {
                if ($e instanceof SDKException) {
                    // abort(404, $e->getUserMessage());
                }
            }

            return null;
        });

        if (empty($response)) {
            abort(404);
        }

        $data['content'] = collect($response);

        $data['headerTitle'] = empty(array_get($data, 'content.seo_title')) ? array_get($data, 'content.name') : array_get($data, 'content.seo_title');
        $data['headerTitle'] .= ' - ' . trans('frontend.website_title');
        $data['seo_keywords'] = array_get($data, 'content.seo_keywords');
        $data['seo_description'] = array_get($data, 'content.seo_description');

        $this->addBreadcrumb('content', $data['content']['name'], route('contents.show', ['slug' => $slug]));
        return view('content.show', $data);
    }
}