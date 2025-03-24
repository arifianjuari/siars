<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DebugAjaxRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log the request details
        Log::info('AJAX Request Details', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'headers' => $request->header(),
            'parameters' => $request->all(),
            'is_ajax' => $request->ajax(),
            'accepts_json' => $request->expectsJson(),
        ]);

        // Proceed with the request
        $response = $next($request);

        // Log the response status
        Log::info('AJAX Response Status: ' . $response->getStatusCode());

        return $response;
    }
}
