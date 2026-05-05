<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\ActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log activities for non-admin users
        if ($request->user() && !$request->user()->hasRole(User::ROLE_ADMIN)) {
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
