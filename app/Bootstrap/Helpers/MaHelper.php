<?php

namespace App\Bootstrap\Helpers;
use Carbon\Carbon;

class MaHelper 
{
    public static $statusMaPage = 'active';
   
    public static function getMaPage(){

        //Define output
        $outputs = [
            'success' => false,
            'message' => [],
        ];

        $makroSdk = app()->make('makroSdk');
        
        $maData   = $makroSdk->config()->getMaintenancePage(['status' => 'active','name' => 'ma_page']);

        if (!empty($maData['data'])) {

            foreach ($maData['data'] as $data ) {

               if ($data['status'] == static::$statusMaPage) {

                    $outputs =  static::diff($data);

                }
            }
        }

        return $outputs;
    } 

     public static function diff($maData) {

        //Define output
        $outputs = [
            'success' => false,
            'message' => [],
        ];

        $now                    = new \DateTime();
        $thaiDate               = new ThaiDate;
        $thaiDate->buddhist_era = true;
        $minDate                = Carbon::parse($maData['start_datetime']);
        $beForTime              = $maData['value'];
        $now                    = Carbon::parse('now');

        $endTime = [
                    'end_datetime_th'   => $thaiDate->date('lที่ j F Y เวลา H:i', strtotime($maData['end_datetime'])),
                    'end_datetime_en'   => date("jS F, Y H:i a", strtotime($maData['end_datetime'])) 
                ];
     
        if ($minDate->diffInRealMinutes($now) <=  $beForTime ) {

            $outputs['success'] = true ;
            $outputs['message'] = $endTime;
            return $outputs ;
        }

         return $outputs ;

    }

}