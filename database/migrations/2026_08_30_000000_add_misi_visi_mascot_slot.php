<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const KEY = "misi_visi_tentang_kami";

    public function up(): void
    {
        $now = now();

        DB::table("mascot_assets")->updateOrInsert(
            ["placement_key" => self::KEY],
            [
                "image_path" => null,
                "alt_text" => null,
                "caption" => null,
                "is_active" => true,
                "updated_at" => $now,
                "created_at" => $now,
            ]
        );
    }

    public function down(): void
    {
        DB::table("mascot_assets")->where("placement_key", self::KEY)->delete();
    }
};
