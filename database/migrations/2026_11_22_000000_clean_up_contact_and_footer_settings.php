<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fixes two values that hurt credibility with business visitors. Each row
     * is only touched while it still holds the old text, so anything already
     * edited in the admin Settings page is kept.
     */
    public function up(): void
    {
        // National number without the leading 0 or country code, so tel: links never dialed correctly.
        DB::table("settings")
            ->where("key", "phone")
            ->where("value", "851-7788-6018")
            ->update(["value" => "+62 851-7788-6018"]);

        DB::table("settings")
            ->where("key", "footer_disclaimer")
            ->where("value", "like", "Beberapa elemen visual terinspirasi%")
            ->update(["value" => ""]);

        Cache::forget("settings");
        Cache::forget("settings.grouped");
    }

    /** The previous values are not kept, so this cannot be undone automatically. */
    public function down(): void
    {
    }
};
