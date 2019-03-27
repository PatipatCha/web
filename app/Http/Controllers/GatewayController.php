<?php

namespace App\Http\Controllers;

use App\Bootstrap\Helpers\MakroHelper;
use Illuminate\Support\Facades\Cache;
use Log;
use MakroSdk\Exceptions\SDKException;

class GatewayController extends BaseController
{
    public function __construct()
    {

    }

    public function truemoneyBackgroundProcess()
    {
        return $this->backgroundProcess('TMNCC');
    }

    public function backgroundProcess($driver)
    {
        $cacheKey = md5($driver . implode('-', request()->all()));

        if (Cache::has($cacheKey)) {
            return '';
        }

        Cache::put($cacheKey, 1, 1);

        $makroSdk = app()->make('makroSdk');

        //Log Start
        $logs = [
            'type' => 'info',
            'step' => 101,
            'driver' => $driver,
            'request' => request()->all()
        ];
        $this->writeGatewayLog('gateway', 'background', $logs);
        //Log End

        $backgroundResult = [];
        try {
            // Call API recheck TrueMoney
            $backgroundResult = $makroSdk->gateway()->backgroundResult($driver, request()->all());
        } catch (\Exception $e) {
            //Log Start
            if ($e instanceof SDKException) {
                $code = $e->getCode();
                $message = $e->getDeveloperMessage();
            } else {
                $code = null;
                $message = $e->getMessage();
            }

            $logs = [
                'type' => 'critical',
                'step' => 201,
                'driver' => $driver,
                'request' => request()->all(),
                'exception_code' => $code,
                'exception_message' => $message,
            ];
            $this->writeGatewayLog('gateway', 'background', $logs);
            //Log End
        }

        $orderNo = array_get($backgroundResult, 'data.order_no');

        if (empty($orderNo)) {
            //Log Start
            $logs = [
                'type' => 'critical',
                'step' => 202,
                'driver' => $driver,
                'request' => request()->all(),
                'exception_message' => 'Can\'t get order no from gateway response',
                'background_result' => $backgroundResult,
            ];
            $this->writeGatewayLog('gateway', 'background', $logs);
            //Log End
        } else {
            try {
                $order = $makroSdk->order()->getOrderDetail($orderNo, ['includes' => 'detail']);
            } catch (\Exception $e) {
                //Log Start
                if ($e instanceof SDKException) {
                    $code = $e->getCode();
                    $message = $e->getDeveloperMessage();
                } else {
                    $code = null;
                    $message = $e->getMessage();
                }

                $logs = [
                    'type' => 'critical',
                    'step' => 203,
                    'order_no' => $orderNo,
                    'exception_code' => $code,
                    'exception_message' => $message,
                ];
                $this->writeGatewayLog('gateway', 'background', $logs);
                //Log End
            }

            if (empty($order)) {
                //Log Start
                $logs = [
                    'type' => 'critical',
                    'step' => 204,
                    'order_no' => $orderNo,
                    'exception_message' => 'order is empty',
                ];
                $this->writeGatewayLog('gateway', 'background', $logs);
                //Log End
            } else {
                if (!empty($backgroundResult['data']['reference_id']) && !empty($backgroundResult['data']['status']) && in_array($backgroundResult['data']['status'], ['success', 'pending', 'failed', 'cancel', 'expired'])) {
                    $updateParameter = [];

                    if (!empty($backgroundResult['data']['status']) && $backgroundResult['data']['status'] == 'success') {
                        $updateParameter['PaymentStatus'] = 'PAID';
                    } else if (!empty($backgroundResult['data']['status']) && $backgroundResult['data']['status'] == 'pending') {
                        $updateParameter['PaymentStatus'] = 'PENDING';
                    } else if (!empty($backgroundResult['data']['status']) && $backgroundResult['data']['status'] == 'expired') {
                        $updateParameter['PaymentStatus'] = 'EXPIRED';
                    } else {
                        $updateParameter['PaymentStatus'] = 'FAIL';
                    }

                    //PaymentReference1 = payment_id for CC and for PayAtStore share order number
                    $updateParameter['PaymentReference1'] = $backgroundResult['data']['reference_id'];

                    //PaymentReference6 = Gateway
                    if (!empty($order) && !empty($order['detail']['payment']['payment_gateway'])) {
                        $updateParameter['PaymentReference6'] = $order['detail']['payment']['payment_gateway'];
                    }

                    //PaymentReference7 = Channel
                    if (!empty($backgroundResult['data']['payment_gateway_channel'])) {
                        $updateParameter['PaymentReference7'] = $backgroundResult['data']['payment_gateway_channel'];
                    } else if (!empty($order) && !empty($order['detail']['payment']['payment_gateway_code'])) {
                        $updateParameter['PaymentReference7'] = $order['detail']['payment']['payment_gateway_code'];
                    }

                    //PaymentReference8 = Agent
                    if (!empty($backgroundResult['data']['payment_gateway_agent'])) {
                        $updateParameter['PaymentReference8'] = $backgroundResult['data']['payment_gateway_agent'];
                    }

                    //PaymentReference9 = This field currenlty not in use and will be used for future reference

                    if (!empty($updateParameter)) {
                        try {
                            $makroSdk->order()->update($orderNo, $updateParameter);
                        } catch (\Exception $e) {
                            //Log Start
                            if ($e instanceof SDKException) {
                                $code = $e->getCode();
                                $message = $e->getDeveloperMessage();
                            } else {
                                $code = null;
                                $message = $e->getMessage();
                            }

                            $logs = [
                                'type' => 'critical',
                                'step' => 205,
                                'order_no' => $orderNo,
                                'update_parameter' => $updateParameter,
                                'exception_code' => $code,
                                'exception_message' => $message,
                            ];
                            $this->writeGatewayLog('gateway', 'background', $logs);
                            //Log End
                        }
                    }
                }

                if (!empty($backgroundResult['data']['status']) && $backgroundResult['data']['status'] == 'success') {
                    //clear cart
                    try {
                        $makroSdk->cart()->clear($order['store_id'], $order['member_id']);
                    } catch (\Exception $e) {
                        //Log Start
                        if ($e instanceof SDKException) {
                            $code = $e->getCode();
                            $message = $e->getDeveloperMessage();
                        } else {
                            $code = null;
                            $message = $e->getMessage();
                        }

                        $logs = [
                            'type' => 'error',
                            'step' => 206,
                            'storeId' => array_get($order, 'store_id'),
                            'memberId' => array_get($order, 'member_id'),
                            'exception_code' => $code,
                            'exception_message' => $message,
                        ];
                        $this->writeGatewayLog('gateway', 'background', $logs);
                        //Log End
                    }
                } else {
                    // delete order
                    /*try {
                        $makroSdk->order()->delete($order['ms_order_no']);
                    } catch (\Exception $e) {
                        Log::error('Gateway Background : Can\'t delete order', ['exception_message' => $e->getMessage(), 'orderNo' => $order['ms_order_no']]);
                    }*/

                    // cancel reserve
                    try {
                        $makroSdk->order()->cancelReserve($order['ms_reserve_id']);
                    } catch (\Exception $e) {
                        //Log Start
                        if ($e instanceof SDKException) {
                            $code = $e->getCode();
                            $message = $e->getDeveloperMessage();
                        } else {
                            $code = null;
                            $message = $e->getMessage();
                        }

                        $logs = [
                            'type' => 'error',
                            'step' => 207,
                            'reserve_id' => array_get($order, 'ms_reserve_id'),
                            'exception_code' => $code,
                            'exception_message' => $message,
                        ];
                        $this->writeGatewayLog('gateway', 'background', $logs);
                        //Log End
                    }
                }
            }
        }

        $uuid = MakroHelper::getUUID();
        $level = 'info';
        $activity = request()->fullUrl();
        $activity_name = 'PaymentGatewayBackground_' . $driver;
        $activity_message = json_encode(request()->all());

        if (empty($activity_message)) {
            $activity_message = json_encode([]);
        }

        try {
            MakroHelper::log($level, $activity, $activity_name, $activity_message, $uuid);
        } catch (\Exception $e) {

        }

        return array_get($backgroundResult, 'return_message');
    }

    private function writeGatewayLog($logName, $fileName, array $logs = [])
    {
        if (env('ENABLE_CUSTOM_GATEWAY_BACKGROUND_LOG')) {
            $date = date('Y-m-d');

            $logFIle = storage_path("logs/gateway/{$date}/{$fileName}.log");

            $logs = array_merge(['date_time' => \Carbon\Carbon::now()->toDateTimeString()], $logs);

            $view_log = new \Monolog\Logger('GATEWAY');
            $view_log->pushHandler(new \Monolog\Handler\StreamHandler($logFIle, \Monolog\Logger::INFO));
            $view_log->addInfo($logName, $logs);
            //Log End
        }
    }
}
