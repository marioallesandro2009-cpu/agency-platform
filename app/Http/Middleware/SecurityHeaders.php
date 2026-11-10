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
         * Same-origin window isolation. Safe to set unconditionally: nothing on
         * this site opens a cross-origin popup/tab that needs a window.opener
         * reference back (the WhatsApp links/widget are plain <a target="_blank"
         * rel="noopener"> — no window.open() or postMessage handshake — and GTM
         * doesn't need it either), so there's no feature this could break.
         */
        $response->headers->set("Cross-Origin-Opener-Policy", "same-origin");

        if (app()->environment('production')) {
            // 1 year, includes subdomains — safe to opt in since ForceHttps
            // already redirects every http:// request to https:// site-wide.
            // "preload" only takes effect once the domain is actually submitted
            // to https://hstspreload.org — adding the directive here is a
            // prerequisite for that submission, not a substitute for it.
            $response->headers->set("Strict-Transport-Security", "max-age=31536000; includeSubDomains; preload");
        }

        /**
         * script-src carries a per-request nonce (see the 'csp-nonce' singleton
         * binding in AppServiceProvider::register()) for the inline scripts we control — @routes/gtag init in app.blade.php, and
         * the JSON-LD <script type="application/ld+json"> tags Vue renders in
         * PublicLayout/SeoHead/Breadcrumbs via the "cspNonce" shared prop.
         * 'unsafe-inline' is kept alongside as a fallback token: CSP2+ browsers
         * ignore 'unsafe-inline' whenever a nonce is also present in the same
         * directive (per spec — this is the standard progressive-enhancement
         * pattern, not a loophole), so it costs nothing in any browser Lighthouse
         * tests with, while protecting against any inline script that isn't
         * (yet) covered by the nonce plumbing in older/uncommon browsers.
         */
        $nonce = app('csp-nonce');
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
            trim("script-src 'self' 'unsafe-inline' 'nonce-{$nonce}' https://www.googletagmanager.com " . implode(' ', $viteDevOrigins)),
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net",
            "font-src 'self' https://fonts.bunny.net",
            trim("img-src 'self' data: https: blob: " . implode(' ', $localImgOrigins)),
            trim("connect-src 'self' https://www.google-analytics.com https://*.google-analytics.com https://www.googletagmanager.com https://www.google.com " . implode(' ', [...$viteDevOrigins, ...$viteDevWsOrigins])),
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "object-src 'none'",
        ]));

        return $response;
    }
}
