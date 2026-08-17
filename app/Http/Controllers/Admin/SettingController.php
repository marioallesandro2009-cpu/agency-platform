<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SettingController extends Controller
{
    /**
     * Closed whitelist of editable settings, keyed by group. `Setting::allGrouped()`
     * is broadcast to every public page via HandleInertiaRequests — a settings
     * write endpoint that accepted an arbitrary group/key pair would let a future
     * (or compromised) staff account silently add a value that gets leaked to
     * every visitor. Keep this in sync with SettingSeeder and Admin/Settings/Index.vue.
     */
    private const EDITABLE_KEYS = [
        "general" => ["website_name", "tagline", "description", "logo", "logo_dark", "favicon", "og_image", "footer_disclaimer"],
        "contact" => ["whatsapp_number", "email", "phone", "address", "maps_url"],
        "social" => ["instagram", "tiktok", "facebook", "linkedin", "youtube"],
        "branding" => ["primary_color", "secondary_color"],
        "integrations" => ["ga_measurement_id"],
    ];

    public function index()
    {
        return Inertia::render("Admin/Settings/Index", [
            "settings" => Setting::allGrouped(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            "group" => ["required", "string", Rule::in(array_keys(self::EDITABLE_KEYS))],
            "values" => "required|array",
        ]);

        $allowedKeys = self::EDITABLE_KEYS[$data["group"]];

        foreach ($data["values"] as $key => $value) {
            if (!in_array($key, $allowedKeys, true)) {
                continue;
            }

            Setting::set($key, is_string($value) ? trim($value) : $value, $data["group"]);
        }

        return back()->with("success", "Pengaturan berhasil disimpan.");
    }
}