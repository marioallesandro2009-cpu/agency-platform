<?php

namespace App\Http\Middleware;

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
            "flash" => [
                "success" => fn () => $request->session()->get("success"),
                "error" => fn () => $request->session()->get("error"),
            ],
        ];
    }
}