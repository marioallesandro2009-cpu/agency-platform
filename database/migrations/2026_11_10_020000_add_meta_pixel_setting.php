<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /** The admin settings form only shows keys that already have a row. */
    public function up(): void
    {
        Setting::firstOrCreate(["key" => "meta_pixel_id"], ["value" => null, "group" => "integrations"]);
        Cache::forget("settings");
        Cache::forget("settings.grouped");
    }

    public function down(): void
    {
        Setting::where("key", "meta_pixel_id")->delete();
        Cache::forget("settings");
        Cache::forget("settings.grouped");
    }
};
