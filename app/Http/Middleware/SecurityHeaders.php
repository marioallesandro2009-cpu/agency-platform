<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set("X-Frame-Options", "DENY");
        $response->headers->set("X-Content-Type-Options", "nosniff");
        $response->headers->set("Referrer-Policy", "strict-origin-when-cross-origin");
        $response->headers->set("Permissions-Policy", "geolocation=(), microphone=(), camera=()");

        /**
         * script-src keeps 'unsafe-inline' for the JSON-LD <script type="application/ld+json">
         * tags used for SEO structured data — those aren't executable, but CSP still governs
         * any <script> element. HTMLPurifier-sanitized content is the primary XSS defense;
         * this CSP is a second layer that still blocks loading scripts from other origins,
         * framing, and base-uri/object-src tricks.
         */
        /**
         * In local dev, the Vite dev server serves scripts (and a websocket for HMR)
         * from a separate origin/port, so CSP must allow that origin explicitly —
         * it's never included outside app()->environment('local').
         */
        $viteDevOrigins = app()->environment('local')
            ? ['http://localhost:5173', 'http://127.0.0.1:5173']
            : [];
        $viteDevWsOrigins = app()->environment('local')
            ? ['ws://localhost:5173', 'ws://127.0.0.1:5173']
            : [];

        /**
         * Local dev runs via `php artisan serve` on :8000, but APP_URL has no
         * port (see project memory: "APP_URL port mismatch breaks
         * Storage::url() during testing"). config/filesystems.php's public
         * disk builds its `url` from APP_URL, so Storage::url()/asset() calls
         * emit http://localhost/storage/... — a different origin than
         * http://localhost:8000 as far as CSP's `'self'` is concerned, which
         * silently blocked every media-library image. Rather than touch
         * APP_URL/.env (documented gotcha, out of scope here), explicitly
         * trust the request's actual origin for images in local dev only.
         */
        $localImgOrigins = app()->environment('local')
            ? [$request->getSchemeAndHttpHost(), 'http://localhost', 'http://127.0.0.1']
            : [];

        $response->headers->set("Content-Security-Policy", implode("; ", [
            "default-src 'self'",
            trim("script-src 'self' 'unsafe-inline' https://www.googletagmanager.com " . implode(' ', $viteDevOrigins)),
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net",
            "font-src 'self' https://fonts.bunny.net",
            trim("img-src 'self' data: https: " . implode(' ', $localImgOrigins)),
            trim("connect-src 'self' https://www.google-analytics.com https://*.google-analytics.com https://www.googletagmanager.com https://www.google.com " . implode(' ', [...$viteDevOrigins, ...$viteDevWsOrigins])),
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "object-src 'none'",
        ]));

        return $response;
    }
}
