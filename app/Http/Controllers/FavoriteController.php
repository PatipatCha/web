<?php

namespace App\Http\Controllers;

use App\Bootstrap\Helpers\AuthHelper;
use App\Bootstrap\Helpers\StoreHelper;
use Illuminate\Http\Request;

class FavoriteController extends BaseController
{
    protected $storeId = '1';
    protected $memberId = 'testuser1234';

    public function index(Request $request)
    {
        try {
            $makroSdk = app()->make('makroSdk');
            $data = [
                'store_id' => StoreHelper::getCurrentStore(),
                'member_id' => AuthHelper::getTokenId(),
                'per_page' => 1000000,
            ];
            $response = $makroSdk->favorite()->get($data);

        } catch (\Exception $e) {
            //Ajax
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }

            abort($e->getCode(), $e->getMessage());
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Successful',
            'data' => $response['data']
        ]);
    }

    protected function getItems()
    {
        try {
            $makroSdk = app()->make('makroSdk');
            $data = [
                'store_id' => $this->storeId,
                'member_id' => $this->memberId
            ];
            $response = $makroSdk->favorite()->get($data);

        } catch (\Exception $e) {
            throw $e;
        }

        return $response;
    }

    public function addItem(Request $request)
    {
        $this->validate($request, [
            'content_id' => 'required'
        ]);

        $response = null;
        try {
            $makroSdk = app()->make('makroSdk');
            $input = $request->input();
            $data = [
                'store_id' => StoreHelper::getCurrentStore(),
                'content_id' => $input['content_id'],
                'member_id' => AuthHelper::getTokenId()
            ];
            $response = $makroSdk->favorite()->add($data);
        } catch (\Exception $e) {
            $duplicate = false;
            $errors = $e->getErrors();
            foreach ($errors as $error) {
                if (preg_match('/duplicate/', $error['message'])) {
                    $duplicate = true;
                    break;
                }
            }

            try {
                $itemsResponse = $this->getItems();
            } catch (\Exception $e3) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e3->getMessage(),
                    'error_code' => $e3->getCode()
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
                'error_code' => $e->getCode(),
                'error_data' => [
                    'duplicate' => $duplicate
                ],
                'data' => $itemsResponse['data']
            ]);
        }

        return $this->index($request);
    }

    public function removeItem(Request $request)
    {
        $this->validate($request, [
            'content_id' => 'required'
        ]);

        try {
            $makroSdk = app()->make('makroSdk');
            $parameter = [
                'content_id' => $request->input('content_id')
            ];
            $response = $makroSdk->favorite()->remove($parameter);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
                'error_code' => $e->getCode()
            ]);
        }

        return $this->index($request);
    }

}