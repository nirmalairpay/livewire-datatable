<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;

class MeasureResponseTime
{
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        return $response;
    }

    public function terminate($request, $response)
    {
        if (defined('LARAVEL_START') and $request instanceof Request) {
            Log::channel('response_time')->info('------------- Response Time Measuring ------------------');
            Log::channel('response_time')->info('Request URI : '. $request->getRequestUri());
            Log::channel('response_time')->info('Request Method : '. $request->getMethod());
            // Log::channel('response_time')->info('Request: ' . json_encode($request->all()));
            // Log::channel('response_time')->info('Response: ' . json_encode($response));
            Log::channel('response_time')->info('Time Consumed: ' . microtime(true) - LARAVEL_START);
            Log::channel('response_time')->info('--------------------------------------------------------');
        }
    }
}
