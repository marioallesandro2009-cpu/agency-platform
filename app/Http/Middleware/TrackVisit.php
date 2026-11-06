<?php

namespace App\Http\Middleware;

use App\Models\PageVisit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Runs after the response has been sent to the visitor (PHP-FPM and
     * LiteSpeed both flush first), so the database insert no longer adds to
     * the time the visitor waits for the page.
     */
    public function terminate(Request $request, Response $response): void
    {
        $isPartialReload = $request->header("X-Inertia-Partial-Component") !== null;

        if (! $request->isMethod("get") || $isPartialReload) {
            return;
        }

        PageVisit::create([
            "path" => $request->path(),
            "ip_address" => $request->ip(),
            "session_id" => $request->hasSession() ? $request->session()->getId() : null,
            "user_agent" => substr((string) $request->userAgent(), 0, 255),
            "referrer" => substr((string) $request->header("referer"), 0, 255),
            "visit_date" => now()->toDateString(),
        ]);
    }
}
