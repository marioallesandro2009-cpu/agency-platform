<?php

namespace Database\Seeders;

use App\Models\MaintenancePlan;
use Illuminate\Database\Seeder;

class MaintenancePlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                "name" => "Basic",
                "slug" => "basic",
                "price" => 100000,
                "price_yearly" => 1000000,
                "features" => [
                    "Cek uptime & monitoring",
                    "Backup rutin",
                    "Keamanan (security patch)",
                ],
                "cta_text" => "Tanya Lebih Lanjut",
                "order" => 1,
                "is_published" => true,
            ],
            [
                "name" => "Standard",
                "slug" => "standard",
                "price" => 200000,
                "price_yearly" => 2000000,
                "features" => [
                    "Semua benefit Basic",
                    "Update plugin/sistem",
                    "Edit konten (teks, gambar, harga produk)",
                    "Respon prioritas (1 hari kerja)",
                ],
                "cta_text" => "Tanya Lebih Lanjut",
                "order" => 2,
                "is_published" => true,
            ],
            [
                "name" => "Priority",
                "slug" => "priority",
                "price" => 350000,
                "price_yearly" => 3500000,
                "features" => [
                    "Semua benefit Standard",
                    "Perubahan section/layout ringan",
                    "Laporan performa bulanan",
                    "Respon prioritas tinggi (same-day, jam kerja)",
                ],
                "cta_text" => "Tanya Lebih Lanjut",
                "order" => 3,
                "is_published" => true,
            ],
        ];

        foreach ($plans as $plan) {
            MaintenancePlan::updateOrCreate(["slug" => $plan["slug"]], $plan);
        }
    }
}
