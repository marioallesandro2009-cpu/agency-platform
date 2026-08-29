<?php

namespace Database\Seeders;

use App\Models\MascotAsset;
use Illuminate\Database\Seeder;

class MascotAssetSeeder extends Seeder
{
    public function run(): void
    {
        // image_path/alt_text/caption intentionally left null — filled in
        // later via /admin/maskot, never seeded with placeholder content.
        $keys = [
            "hero_tentang_kami",
            "whatsapp_widget",
            "sidebar_jasa_umkm",
            "sidebar_jasa_company_profile",
            "sidebar_jasa_toko_online",
            "sidebar_jasa_web_development",
            "cta_kontak",
            "kenalan_dekat_kami",
            "halaman_404",
            "empty_state_blog",
            "kontak_konfirmasi",
            "intro_tentang_kami",
            "misi_visi_tentang_kami",
        ];

        foreach ($keys as $key) {
            MascotAsset::firstOrCreate(["placement_key" => $key]);
        }
    }
}
