<?php

namespace App\Http\Controllers;

use Breadcrumbs;
use Illuminate\Http\Request;

class SubscribeController extends BaseController
{

    public function subscribe(Request $request)
    {
        $makroSdk = app()->make('makroSdk');

        $this->validate($request, [
            'email' => 'required|email|'
        ]);

        if (! empty($request['email'])) {
            try {
                $request['email'] = \MakroHelper::validateEmail($request['email']);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => __('frontend.please_enter_valid_email_format')
                ]);
            }
        }

        try {
            $response = $makroSdk->subscribe()->create($request['email']);
        } catch (\Exception $e) {

            return response()->json([
                'message' => __('frontend.subscribe_email_is_use')
            ]);
        }

        return response()->json($response);
    }
}
