<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user()) {
            ActivityLogger::log(
                action: 'request.'.$request->method(),
                description: $request->method().' '.$request->path(),
                properties: [
                    'route' => optional($request->route())->getName(),
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'status' => $response->getStatusCode(),
                ],
                user: $request->user()
            );
        }

        return $response;
    }
}
