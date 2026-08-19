<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

/**
 * A manual hotfix script wrote the "admin_notification_email" row directly
 * to production without setting group="notifications" (the seeder's
 * intended value — see SettingSeeder). Setting::allGrouped() groups rows
 * by that column, so the row silently landed outside the "notifications"
 * tab and the admin UI rendered it as empty.
 */
return new class extends Migration
{
    public function up(): void
    {
        $setting = Setting::where("key", "admin_notification_email")->first();

        if ($setting) {
            $setting->update(["group" => "notifications"]);
        } else {
            Setting::create([
                "key" => "admin_notification_email",
                "value" => "contact@exodineweb.com",
                "group" => "notifications",
            ]);
        }

        Cache::forget("settings");
        Cache::forget("settings.grouped");
    }

    public function down(): void
    {
        //
    }
};
