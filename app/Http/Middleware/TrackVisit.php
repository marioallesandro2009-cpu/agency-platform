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
        $response = $next($request);

        $isPartialReload = $request->header("X-Inertia-Partial-Component") !== null;

        if ($request->isMethod("get") && !$isPartialReload) {
            PageVisit::create([
                "path" => $request->path(),
                "ip_address" => $request->ip(),
                "session_id" => $request->session()->getId(),
                "user_agent" => substr((string) $request->userAgent(), 0, 255),
                "referrer" => substr((string) $request->header("referer"), 0, 255),
                "visit_date" => now()->toDateString(),
            ]);
        }

        return $response;
    }
}