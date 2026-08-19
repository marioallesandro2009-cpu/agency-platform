<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const OLD_KEY = "service_sidebar";

    private const NEW_KEYS = [
        "sidebar_jasa_umkm",
        "sidebar_jasa_company_profile",
        "sidebar_jasa_toko_online",
        "sidebar_jasa_web_development",
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $old = DB::table("mascot_assets")->where("placement_key", self::OLD_KEY)->first();

        // Existing "sidebar-jasa" data (if any) is copied into the first new
        // slot (UMKM) so nothing already uploaded in production is silently
        // dropped. The other three slots start empty — admins fill them in
        // per-service via /admin/maskot.
        foreach (self::NEW_KEYS as $index => $key) {
            $now = now();

            DB::table("mascot_assets")->updateOrInsert(
                ["placement_key" => $key],
                [
                    "image_path" => $index === 0 && $old ? $old->image_path : null,
                    "alt_text" => $index === 0 && $old ? $old->alt_text : null,
                    "caption" => $index === 0 && $old ? $old->caption : null,
                    "is_active" => true,
                    "updated_at" => $now,
                    "created_at" => $old->created_at ?? $now,
                ]
            );
        }

        if ($old) {
            DB::table("mascot_assets")->where("placement_key", self::OLD_KEY)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $umkm = DB::table("mascot_assets")->where("placement_key", self::NEW_KEYS[0])->first();

        DB::table("mascot_assets")->updateOrInsert(
            ["placement_key" => self::OLD_KEY],
            [
                "image_path" => $umkm->image_path ?? null,
                "alt_text" => $umkm->alt_text ?? null,
                "caption" => $umkm->caption ?? null,
                "is_active" => true,
                "updated_at" => now(),
                "created_at" => $umkm->created_at ?? now(),
            ]
        );

        DB::table("mascot_assets")->whereIn("placement_key", self::NEW_KEYS)->delete();
    }
};
