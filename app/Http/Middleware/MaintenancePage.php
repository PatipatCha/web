<?php

namespace App\Http\Middleware;

use Closure;
use App\Bootstrap\Helpers\ThaiDate;
class MaintenancePage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //Get datas
        try {
            $makroSdk = app()->make('makroSdk');
            $maintenancepage = $makroSdk->config()->getMaintenancePage(['status' => 'active','name' => 'ma_page']);
        } catch (\Exception $e) {

        }

        if (!empty($maintenancepage['data'][0]['start_datetime']) && !empty($maintenancepage['data'][0]['end_datetime'])) {

            if (($maintenancepage['data'][0]['start_datetime'] <= date("Y-m-d H:i:s")) && ($maintenancepage['data'][0]['end_datetime'] > date("Y-m-d H:i:s"))) {
                $thaiDate = new ThaiDate;
                $thaiDate->buddhist_era = true;

                $data = [
                    'end_datetime_th'   => $thaiDate->date('lที่ j F Y เวลา H:i', strtotime($maintenancepage['data'][0]['end_datetime'])),
                    'end_datetime_en'   => date("jS F, Y H:i a", strtotime($maintenancepage['data'][0]['end_datetime'])),
                ];
                return response(view('maintenancepage.index', $data));
            }

        }

        return $next($request);
    }
}
