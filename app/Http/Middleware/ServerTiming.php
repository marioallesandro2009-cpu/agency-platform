<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds "Server-Timing: app;dur=<ms>" so the time PHP spends on a page can be
 * read from the browser's DevTools (Network > Timing) or curl, separately
 * from network time. Only the total duration is exposed, nothing internal.
 */
class ServerTiming
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // LARAVEL_START is set in public/index.php; the request time covers
        // other entry points (tests, artisan serve).
        $start = defined('LARAVEL_START') ? LARAVEL_START : (float) $request->server('REQUEST_TIME_FLOAT');

        if ($start > 0) {
            $ms = max(0, round((microtime(true) - $start) * 1000, 1));
            $response->headers->set('Server-Timing', "app;dur={$ms}");
        }

        return $response;
    }
}
