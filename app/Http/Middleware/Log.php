<?php

namespace App\Http\Middleware;

use Closure;
use App\Bootstrap\Helpers\MakroHelper;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class Log
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $uuid = MakroHelper::getUUID();

        $level = 'info';
        $url = $request->fullUrl();
        $activity = $request->path();
        $activity_name = str_replace('.', '@', $request->route()->getName() . '_request_to_' . $url);
        // \Log::info('activity_name: '.$activity_name);
        $activity_message = json_encode($request->all());

        if (empty($activity_message)) {
            $activity_message = json_encode([]);
        }

        try {
            MakroHelper::log($level, $activity, $activity_name, $activity_message, $uuid);
        } catch (\Exception $e) {

        }

        $request->attributes->set('client_uuid', $uuid);
        $response = $next($request);

        $this->writeResponseLog($request, $response, $uuid);

        return $response;
    }

    protected function writeResponseLog(Request $request, $response, $uuid)
    {
        $url = $request->fullUrl();
        $level = 'info';
        $activity_message = 'HTTP/200 OK';

        if ($response->isClientError() || $response->isServerError()) {
            $level = 'error';
            $activity_message = 'HTTP ERROR';

        }

        $activity = $request->path();
        $activity_name = str_replace('.', '@', $request->route()->getName() . '_response_from_' . $url);

        try {
            MakroHelper::log($level, $activity, $activity_name, $activity_message, $uuid);
        } catch (\Exception $e) {

        }
    }
}
