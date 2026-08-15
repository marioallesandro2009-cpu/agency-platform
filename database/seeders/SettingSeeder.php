<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ["key" => "website_name", "value" => "ExodineWeb", "group" => "general"],
            ["key" => "tagline", "value" => "Jasa Website Profesional untuk Bisnis & UMKM", "group" => "general"],
            ["key" => "description", "value" => "Kami membantu bisnis Anda hadir online dengan website profesional, cepat, dan SEO-friendly.", "group" => "general"],
            ["key" => "logo", "value" => null, "group" => "general"],
            ["key" => "logo_dark", "value" => null, "group" => "general"],
            ["key" => "favicon", "value" => null, "group" => "general"],
            ["key" => "og_image", "value" => null, "group" => "general"],

            // Contact
            ["key" => "whatsapp_number", "value" => "6281234567890", "group" => "contact"],
            ["key" => "email", "value" => "hello@exodineweb.test", "group" => "contact"],
            ["key" => "phone", "value" => "021-1234567", "group" => "contact"],
            ["key" => "address", "value" => "Jakarta, Indonesia", "group" => "contact"],
            ["key" => "maps_url", "value" => null, "group" => "contact"],

            // Social
            ["key" => "instagram", "value" => null, "group" => "social"],
            ["key" => "tiktok", "value" => null, "group" => "social"],
            ["key" => "facebook", "value" => null, "group" => "social"],
            ["key" => "linkedin", "value" => null, "group" => "social"],
            ["key" => "youtube", "value" => null, "group" => "social"],

            // Branding
            ["key" => "primary_color", "value" => "#4d7c0f", "group" => "branding"],
            ["key" => "secondary_color", "value" => "#1a1a1a", "group" => "branding"],

            // Integrations
            ["key" => "ga_measurement_id", "value" => null, "group" => "integrations"],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(["key" => $setting["key"]], $setting);
        }

        // updateOrCreate() above bypasses Setting::set(), which is the only
        // place that clears the forever-cached grouped settings. Without this,
        // a stale cache from before seeding (e.g. an empty settings table)
        // would keep serving incomplete groups indefinitely.
        Cache::forget("settings");
        Cache::forget("settings.grouped");
    }
}