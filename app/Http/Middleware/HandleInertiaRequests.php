<?php

namespace App\Http\Middleware;

use App\Models\MascotAsset;
use App\Models\NavItem;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = "app";

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Every page (public included) gets this via Inertia's shared props, which
     * means it's visible in page source/API responses to anonymous visitors —
     * only groups that are genuinely meant to be public go here. Any future
     * "private" settings group (API secrets, SMTP credentials, etc.) must NOT
     * be added to this list; keep it in sync with SettingController::EDITABLE_KEYS.
     */
    private const PUBLIC_SETTING_GROUPS = ["general", "contact", "social", "branding", "integrations"];

    public function share(Request $request): array
    {
        $settingsGrouped = Setting::allGrouped();

        return [
            ...parent::share($request),
            "auth" => [
                "user" => $request->user(),
            ],
            // Every group is guaranteed present (defaulting to []) even if the
            // cached grouped settings are stale/incomplete, so the frontend
            // never has to deal with settings.<group> being undefined.
            "settings" => collect(self::PUBLIC_SETTING_GROUPS)
                ->mapWithKeys(fn (string $group) => [$group => $settingsGrouped[$group] ?? []])
                ->toArray(),
            "navServices" => Service::published()->ordered()->get(["id", "name", "slug"]),
            "navMenu" => NavItem::visible()->ordered()->get(["id", "label", "url"]),
            // Keyed by placement_key so any component can read
            // $page.props.mascots.<key>?.image_path without a dedicated
            // controller/query per page. image_path/alt_text/caption are
            // null until an admin sets them via /admin/maskot — every
            // consuming template must keep today's look when they're null.
            "mascots" => MascotAsset::where("is_active", true)->get()->keyBy("placement_key")
                ->map(fn (MascotAsset $m) => [
                    "image_path" => $m->image_path,
                    "alt_text" => $m->alt_text,
                    "caption" => $m->caption,
                ]),
            "flash" => [
                "success" => fn () => $request->session()->get("success"),
                "error" => fn () => $request->session()->get("error"),
            ],
            // Lets Vue-rendered inline <script> tags (JSON-LD in
            // PublicLayout/SeoHead/Breadcrumbs) match the nonce SecurityHeaders
            // put in the CSP header for this same request. See the 'csp-nonce'
            // singleton binding in AppServiceProvider::register().
            "cspNonce" => app('csp-nonce'),
        ];
    }
}