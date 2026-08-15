<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects plain-HTTP requests to HTTPS in production only — local dev
 * (php artisan serve, XAMPP) has no TLS cert, so this must stay a no-op
 * outside app()->environment('production'). Skips the health-check route
 * so uptime monitors that hit it over HTTP don't just chase redirects.
 */
class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('production') && !$request->secure() && $request->path() !== 'up') {
            return redirect('https://'.$request->getHttpHost().$request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
